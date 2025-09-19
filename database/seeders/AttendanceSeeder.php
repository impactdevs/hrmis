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
        $employees = DB::table('employees')
                      ->select('employee_id', 'staff_id')
                      ->get();

        foreach ($employees as $employee) {
            // Generate a random clock-in time within the last 12 hours
            $accessDateTime = Carbon::now()->subHours(rand(0, 12));

            DB::table('attendances')->insert([
                'attendance_id' => (string) Str::uuid(),
                'staff_id' => $employee->staff_id,
                'access_date_and_time' => $accessDateTime,
                'access_date' => $accessDateTime->toDateString(),
                'access_time' => $accessDateTime->toTimeString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}