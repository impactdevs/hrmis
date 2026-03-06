<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ResetAnnualLeaveDays extends Command
{
    protected $signature = 'leave:reset-annual-days';
    protected $description = 'Resets entitled leave days for all employees at the start of each calendar year';

    public function handle()
    {
        $currentYear = Carbon::now()->year;
        $this->info("Running annual leave reset for year: {$currentYear}");

        $count = 0;

        Employee::query()
            ->whereNotNull('entitled_leave_days') // only employees who have entitlement defined
            ->chunk(100, function ($employees) use (&$count, $currentYear) {
                foreach ($employees as $employee) {
                    // Reset entitled_leave_days to the employee's personal default.
                    // If default_entitled_days is not set, fall back to current entitled_leave_days, then 30.
                    $resetTo = $employee->default_entitled_days ?? $employee->entitled_leave_days ?? 30;

                    $employee->entitled_leave_days = $resetTo;
                    $employee->save();

                    $count++;

                    Log::info("Reset leave entitlement", [
                        'employee_id' => $employee->employee_id,
                        'reset_to' => $resetTo,
                        'year' => $currentYear,
                    ]);
                }
            });

        $this->info("Reset completed for {$count} employees.");
    }
}