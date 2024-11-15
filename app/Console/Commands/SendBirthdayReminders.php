<?php

namespace App\Console\Commands;

use App\Mail\BirthdayReminderForAdmin;
use App\Mail\BirthdayReminderForEmployee;
use App\Models\Employee;
use App\Models\Scopes\EmployeeScope;
use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

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

        // Send reminder to HR for tomorrow's birthdays
        foreach ($employeesTomorrow as $employee) {
            // Get the HR user (you may have more than one HR, this gets the first one)
            $superAdmin = User::whereRole('HR')->first(); // Adjust role as needed

            if ($superAdmin) {
                // Send email to HR
                Mail::to($superAdmin->email)->send(new BirthdayReminderForAdmin($employee));
            }
        }

        // Send reminder to employees for today's birthdays
        foreach ($employeesToday as $employee) {
            // Get the user associated with the employee
            $user = User::find($employee->user_id);

            if ($user) {
                // Send email to employee
                Mail::to($user->email)->send(new BirthdayReminderForEmployee($user));
            }
        }

        $this->info('Birthday reminders sent successfully!');
    }
}
