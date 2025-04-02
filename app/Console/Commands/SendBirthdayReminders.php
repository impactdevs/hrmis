<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Scopes\EmployeeScope;
use App\Models\User;
use App\Notifications\AdminBirthdayReminder;
use App\Notifications\BirthdayReminder;
use App\Notifications\BirthdayWish;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendBirthdayReminders extends Command
{
    // Command signature and description
    protected $signature = 'reminders:send-birthday';
    protected $description = 'Send birthday reminders to HR and employee';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Get today's month and day
        $todayMonthDay = Carbon::today()->format('m-d');
        // Get tomorrow's month and day for HR reminder
        $tomorrowMonthDay = Carbon::tomorrow()->format('m-d');

        // Get all employees whose birthdays are today (comparing month and day), bypassing global scopes
        $employeesToday = Employee::withoutGlobalScope(EmployeeScope::class) // Bypass the global scope
            ->whereRaw('DATE_FORMAT(date_of_birth, "%m-%d") = ?', [$todayMonthDay])
            ->get();

        // Get all employees whose birthdays are tomorrow (comparing month and day), bypassing global scopes
        $employeesTomorrow = Employee::withoutGlobalScope(EmployeeScope::class) // Bypass the global scope
            ->whereRaw('DATE_FORMAT(date_of_birth, "%m-%d") = ?', [$tomorrowMonthDay])
            ->get();

        //get all users and let them know about the birthday
        $employees = Employee::withoutGlobalScope(EmployeeScope::class)
            ->whereNotIn('employee_id', $employeesToday->pluck('employee_id')) // Extract IDs only for exclusion
            ->get();

        // Send reminders to employees for today's birthdays
        foreach ($employeesToday as $employee) {
            // Get the user associated with the employee
            $user = User::find($employee->user_id);

            if ($user) {
                // Send notification to employee (mail, database, and broadcast)
                $user->notify(new BirthdayWish($employee));  // Send to employee
            }
        }

        // Send reminders to HR for tomorrow's birthdays
        foreach ($employeesTomorrow as $employee) {
            // Get the HR user (assuming one HR user)
            $superAdmin = User::role('HR')->first();
            if ($superAdmin) {
                // Send notification to HR (mail, database, and broadcast)
                $superAdmin->notify(new AdminBirthdayReminder($employee));  // Send to HR
            }
        }

        // Send reminders to employees for today's birthdays
        foreach ($employees as $employee) {
            // Get the user associated with the employee
            $user = User::find($employee->user_id);

            if ($user) {
                foreach ($employeesToday as $employeeToday) {
                    // Get the user associated with the employee
        
                        // Send notification to employee (mail, database, and broadcast)
                        $user->notify(new BirthdayReminder($employeeToday));  // Send to employee
                }
                // Send notification to employee (mail, database, and broadcast)
                
            }
        }

        $this->info('Birthday reminders sent successfully!');
    }
}
