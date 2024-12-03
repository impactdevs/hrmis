<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use Log;

class UploadEmployees extends Controller
{
    public function process_csv_for_arrears()
    {
        ini_set('max_execution_time', 2000);
        ini_set('memory_limit', '-1');
        //get column names from the csv
        $file = public_path('assets/employee_records/employees.csv');
        $csv = array_map('str_getcsv', file($file));

        //set excution time to 5 minutes
        for ($i = 2; $i < count($csv); $i++) {
            try {
                $user = DB::table('users')->where('email', $csv[$i][4])->doesntExist();
                if ($user) {
                    $password = Str::random(10);
                    $user = new User();
                    $user->email = $csv[$i][4];
                    $user->name = $csv[$i][2] . ' ' . $csv[$i][3];
                    $user->password = Hash::make($password);
                    $user->save();
                    $user->assignRole('Staff'); // Ensure the role exists

                    $data = [
                        'staff_id' => $csv[$i][0],
                        'title' => $csv[$i][1] ?? 'null',
                        'first_name' => $csv[$i][2],
                        'last_name' => $csv[$i][3],
                        'email' => $csv[$i][4],
                        'user_id' => $user->id,
                    ];

                    //create employee record
                    $createEmployee = Employee::create($data);

                    //if the employee is created, add the text password to the csv file
                    if ($createEmployee) {
                        $file = fopen(public_path('assets/employee_records/employees_created.csv'), 'a');
                        fputcsv($file, [$csv[$i][0], $csv[$i][1], $csv[$i][2], $csv[$i][3], $csv[$i][4], $password]);
                        fclose($file);

                        Log::info('Employee created: ' . $csv[$i][0]);
                    }
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }
}
