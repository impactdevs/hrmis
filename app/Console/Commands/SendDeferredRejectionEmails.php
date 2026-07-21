<?php

namespace App\Console\Commands;

use App\Mail\ApplicationStatusChangedMail;
use App\Models\JobApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDeferredRejectionEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-deferred-rejection-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send rejection emails for job applications whose job deadline has now passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $applications = JobApplication::query()
            ->where('status', JobApplication::STATUS_REJECTED)
            ->whereNull('status_notified_at')
            ->whereHas('companyJob', function ($query) {
                $query->whereNotNull('will_become_inactive_at')
                    ->where('will_become_inactive_at', '<=', now());
            })
            ->with('companyJob')
            ->get();

        $sent = 0;

        foreach ($applications as $application) {
            try {
                Mail::to($application->email)
                    ->send(new ApplicationStatusChangedMail($application, JobApplication::STATUS_PENDING));

                $application->forceFill(['status_notified_at' => now()])->saveQuietly();
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("Deferred rejection email failed for application #{$application->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$sent} deferred rejection email(s) out of {$applications->count()} due.");
    }
}
