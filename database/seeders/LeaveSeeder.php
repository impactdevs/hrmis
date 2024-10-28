<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get valid employee IDs and leave type IDs from their respective tables
        $validEmployeeIds = DB::table('employees')->pluck('employee_id')->toArray(); // Assuming 'id' is the employee UUID field
        $validLeaveTypeIds = DB::table('leave_types')->pluck('leave_type_id')->toArray(); // Assuming 'id' is the leave type UUID field

        // Example data for leaves
        $leaves = [
            [
                'leave_id' => (string) Str::uuid(),
                'employee_id' => $validEmployeeIds[0] ?? null,
                'start_date' => '2024-10-01',
                'end_date' => '2024-10-05',
                'leave_type_id' => $validLeaveTypeIds[0] ?? null, // Use valid leave type ID
                'reason' => 'Vacation',
                'leave_request_status' => 'HR',
                'my_work_will_be_done_by' => json_encode(['colleague_1', 'colleague_2']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'leave_id' => (string) Str::uuid(),
                'employee_id' => $validEmployeeIds[1] ?? null,
                'start_date' => '2024-11-10',
                'end_date' => '2024-11-15',
                'leave_type_id' => $validLeaveTypeIds[1] ?? null, // Use valid leave type ID
                'reason' => 'Medical leave',
                'leave_request_status' => 'HR',
                'my_work_will_be_done_by' => json_encode(['colleague_3']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more leaves as needed
        ];

        // Filter out any leaves with invalid employee IDs or leave type IDs
        $leaves = array_filter($leaves, function ($leave) {
            return !is_null($leave['employee_id']) && !is_null($leave['leave_type_id']);
        });

        // Insert data into the leaves table
        DB::table('leaves')->insert($leaves);
    }
}
