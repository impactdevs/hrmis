<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\HikvisionRawLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessHikvisionLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $logs = HikvisionRawLog::unprocessed()->get();

        if ($logs->isEmpty()) {
            return;
        }

        Log::info("ProcessHikvisionLogs: processing {$logs->count()} record(s)");

        foreach ($logs as $log) {
            $this->processLog($log);
        }
    }

    private function processLog(HikvisionRawLog $log): void
    {
        try {
            // -------------------------------------------------------
            // Step 1: Resolve employee.
            // The device sends staff_id with '/' stripped out.
            // e.g. SP/067 → SP067, AP/006 → AP006
            // We match by removing '/' from the stored staff_id and
            // comparing against the device's employee_id value.
            // -------------------------------------------------------
            $employee = Employee::whereRaw(
                "REPLACE(staff_id, '/', '') = ?",
                [$log->employee_id]
            )->first();

            if (!$employee) {
                $log->markFailed(
                    "No employee found for device ID: {$log->employee_id}"
                );
                Log::warning("ProcessHikvisionLogs: unmatched device ID {$log->employee_id}");
                return;
            }

            // -------------------------------------------------------
            // Step 2: Avoid duplicate attendance records.
            // The device pushes the same event multiple times (as seen
            // with SP115 appearing twice within minutes).
            // We deduplicate within a 1-minute window — two badges from
            // the same person within 60 seconds count as one event.
            // -------------------------------------------------------
            $exists = Attendance::where('staff_id', $employee->staff_id)
                ->whereBetween('access_date_and_time', [
                    $log->access_date_and_time->copy()->subMinute(),
                    $log->access_date_and_time->copy()->addMinute(),
                ])
                ->exists();

            if ($exists) {
                $log->markProcessed();
                Log::info("ProcessHikvisionLogs: duplicate skipped for {$employee->staff_id} at {$log->access_date_and_time}");
                return;
            }

            // -------------------------------------------------------
            // Step 3: Insert into attendances.
            // attendance_status is blank from device — we leave it out
            // since clock-in/out is derived from MIN/MAX of
            // access_date_and_time grouped by staff_id + access_date.
            // -------------------------------------------------------
            Attendance::create([
                'staff_id'             => $employee->staff_id,
                'access_date_and_time' => $log->access_date_and_time,
                'access_date'          => $log->access_date,
                'access_time'          => $log->access_time,
            ]);

            $log->markProcessed();

            Log::info("ProcessHikvisionLogs: recorded attendance for {$employee->staff_id} at {$log->access_date_and_time}");

        } catch (\Throwable $e) {
            $log->markFailed($e->getMessage());
            Log::error("ProcessHikvisionLogs: failed for log id {$log->id} — {$e->getMessage()}");
        }
    }
}