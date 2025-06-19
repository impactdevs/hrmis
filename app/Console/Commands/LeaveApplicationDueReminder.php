<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class LeaveApplicationDueReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:leave-application-due-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a leave reminder for leave applications that are due within the next 3 days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // This command is intended to send reminders for leave applications that are due.
        // You can implement the logic to fetch leave applications and send reminders here.

        $this->info('Leave application due reminder command executed successfully.');

        // 1. Fetch leave rosters (schedules) that are within 3 days of their due date but have no leave.
        $dueDate = now()->addDays(3)->toDateString();
        $leaveRosters = \App\Models\LeaveRoster::whereDate('start_date', '<=', $dueDate)
            ->whereDoesntHave('leave')
            ->get();

        // 2. Send reminders to the respective users.
        foreach ($leaveRosters as $roster) {
            $user = User::find($roster->employee->user_id);
            if ($user && $user->email) {
                // Log the notification for debugging purposes
                Log::info('Sending leave application due reminder to user: ' . $user->email);
                // send a notification
                Notification::send($user, new \App\Notifications\LeaveApplicationDueReminder($roster));
            }
        }

        // For now, we will just output a message.
        $this->line('excecuted successfully');
    }
}

