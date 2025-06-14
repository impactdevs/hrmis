<?php

namespace App\Console\Commands;

use App\Models\Appraisal;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Console\Command;

class AppraisalReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:appraisal-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remind employees to to start their appraisals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentYear = date('Y');

        // Get all employees
        $employees = Employee::withoutGlobalScopes()->get();

        // Get all appraisals for the current year
        $appraisals = Appraisal::withoutGlobalScopes()->whereYear('created_at', $currentYear)->get();

        // Map user IDs who already have an appraisal for the current year
        $userIdsWithAppraisal = $appraisals->pluck('user_id')->unique();

        // Get users (from employees) who do NOT have an appraisal for the current year
        $usersToNotify = $employees->whereNotIn('user_id', $userIdsWithAppraisal);

        foreach ($usersToNotify as $employee) {
            $user = User::find($employee->user_id);
            $user->notify(new \App\Notifications\AppraisalDueNotification());
        }

        $this->info('Appraisal reminders sent to employees without appraisals for the current year.');
    }
}
