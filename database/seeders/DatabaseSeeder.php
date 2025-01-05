<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionsDemoSeeder::class);

        $positions = [
            ['position_id' => Str::uuid(), 'position_name' => 'Manager'],
            ['position_id' => Str::uuid(), 'position_name' => 'Developer'],
            ['position_id' => Str::uuid(), 'position_name' => 'Designer'],
            ['position_id' => Str::uuid(), 'position_name' => 'Analyst'],
            ['position_id' => Str::uuid(), 'position_name' => 'Tester'],
        ];

        DB::table('positions')->insert($positions);

        $userIds = DB::table('users')->pluck('id')->toArray();

        $departments = [
            ['department_id' => Str::uuid(), 'department_name' => 'Human Resources', 'department_head' => $userIds[array_rand($userIds)]],
            ['department_id' => Str::uuid(), 'department_name' => 'Engineering', 'department_head' => $userIds[array_rand($userIds)]],
            ['department_id' => Str::uuid(), 'department_name' => 'Marketing', 'department_head' => $userIds[array_rand($userIds)]],
            ['department_id' => Str::uuid(), 'department_name' => 'Sales', 'department_head' => $userIds[array_rand($userIds)]],
            ['department_id' => Str::uuid(), 'department_name' => 'Finance', 'department_head' => $userIds[array_rand($userIds)]],
        ];

        DB::table('departments')->insert($departments);

        //leave types
        $leaveTypes = [
            ['leave_type_id' => Str::uuid(), 'leave_type_name' => 'Annual Leave'],
            ['leave_type_id' => Str::uuid(), 'leave_type_name' => 'Sick Leave'],
            ['leave_type_id' => Str::uuid(), 'leave_type_name' => 'Maternity Leave'],
            ['leave_type_id' => Str::uuid(), 'leave_type_name' => 'Paternity Leave'],
            ['leave_type_id' => Str::uuid(), 'leave_type_name' => 'Study Leave'],
        ];

        DB::table('leave_types')->insert($leaveTypes);


        $this->call(EmployeeSeeder::class);

        // $this->call(TrainingSeeder::class);

        // $this->call(EventsSeeder::class);

        $this->call(AttendanceSeeder::class);
    }
}
