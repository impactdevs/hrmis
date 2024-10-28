<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = \DB::table('employees')->pluck('employee_id'); // Assuming this table already exists and has data

        foreach ($employees as $employee_id) {
            // Set the attendance date to today
            $attendanceDate = Carbon::today();

            // Generate a random clock-in time within the last 12 hours
            $clockInTime = Carbon::now()->subHours(rand(0, 12))->format('H:i:s');

            DB::table('attendances')->insert([
                'attendance_id' => (string) Str::uuid(),
                'employee_id' => $employee_id,
                'attendance_date' => $attendanceDate,
                'clock_in' => $clockInTime,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
