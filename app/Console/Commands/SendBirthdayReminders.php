<?php

namespace App\Console\Commands;

use App\Mail\BirthdayReminderForAdmin;
use App\Mail\BirthdayReminderForEmployee;
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
        // Get today's date
        $today = Carbon::today();
        // Get tomorrow's date for HR reminder
        $tomorrow = Carbon::tomorrow();

        // Get all employees whose birthdays are today
        $employeesToday = User::whereDate('date_of_birth', $today)->get();

        // Get all employees whose birthdays are tomorrow (for HR)
        $employeesTomorrow = User::whereDate('date_of_birth', $tomorrow)->get();

        // Send reminder to HR for tomorrow's birthdays
        foreach ($employeesTomorrow as $employee) {
            // Assuming you have a HR role or ID in the system
            $superAdmin = User::whereRole('super-admin')->first(); // Adjust as needed

            Mail::to($superAdmin->email)->send(new BirthdayReminderForAdmin($employee));
        }

        // Send reminder to employees for today's birthdays
        foreach ($employeesToday as $employee) {
            Mail::to($employee->email)->send(new BirthdayReminderForEmployee($employee));
        }

        $this->info('Birthday reminders sent successfully!');
    }
}
