<?php

namespace App\Console\Commands;

use App\Models\Appraisal;
use App\Models\User;
use App\Notifications\AppraisalApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendAppraisalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appraisal:send-reminders {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deadline reminders for pending appraisals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('Running in dry-run mode - no notifications will be sent');
        }

        $this->info('Checking for appraisals requiring reminders...');

        // Get appraisals that need reminders
        $appraisalsNeedingReminders = Appraisal::withoutGlobalScopes()
            ->where('current_stage', '!=', 'Completed')
            ->whereNotNull('staff_deadline')
            ->get()
            ->filter(function ($appraisal) {
                return $appraisal->shouldSendReminder();
            });

        if ($appraisalsNeedingReminders->isEmpty()) {
            $this->info('No appraisals require reminders at this time.');
            return 0;
        }

        $this->info("Found {$appraisalsNeedingReminders->count()} appraisal(s) requiring reminders.");

        $sentCount = 0;
        $errorCount = 0;

        foreach ($appraisalsNeedingReminders as $appraisal) {
            try {
                $this->sendReminderForAppraisal($appraisal, $dryRun);
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for appraisal {$appraisal->appraisal_id}: {$e->getMessage()}");
                $errorCount++;
            }
        }

        $action = $dryRun ? 'would be sent' : 'sent';
        $this->info("Summary: {$sentCount} reminder(s) {$action}, {$errorCount} error(s)");
        
        return $errorCount > 0 ? 1 : 0;
    }

    /**
     * Send reminder for a specific appraisal
     */
    private function sendReminderForAppraisal(Appraisal $appraisal, bool $dryRun = false): void
    {
        $nextApproverRole = $appraisal->getNextApproverRole();
        
        if (!$nextApproverRole) {
            return; // No more approvers needed
        }

        // Get the next approver user
        $approverUser = null;
        
        if ($nextApproverRole === 'Head of Division') {
            // Get the appraiser (department head)
            $approverUser = $appraisal->appraiser->user ?? null;
        } else {
            // Get user by role
            $approverUser = User::role($nextApproverRole)->first();
        }

        if (!$approverUser) {
            throw new \Exception("No user found with role: {$nextApproverRole}");
        }

        $employee = $appraisal->employee;
        $deadlineStatus = $appraisal->deadline_status;
        
        $this->line("Sending reminder to {$approverUser->email} ({$nextApproverRole}) for {$employee->first_name} {$employee->last_name} - Status: {$deadlineStatus['message']}");
        
        if (!$dryRun) {
            // Send the notification
            Notification::send(
                $approverUser,
                new AppraisalApplication($appraisal, $employee->first_name, $employee->last_name)
            );
            
            // Mark reminder as sent
            $appraisal->markReminderSent();
        }
    }
}
