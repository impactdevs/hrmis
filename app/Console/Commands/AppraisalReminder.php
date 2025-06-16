<?php

namespace App\Console\Commands;

use App\Models\Appraisal;
use App\Models\Contract;
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
        $this->end_of_contract();
        // $this->mid_financial_year();
    }

    //for mid financial year
    public function mid_financial_year()
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
            $user->notify(new \App\Notifications\AppraisalDueNotification('mid_financial_year'));
        }

        $this->info('Appraisal reminders sent to employees without appraisals for the current year.');
    }

    //for comfirmation
    public function comfirmation() {}

    //end of contract
    public function end_of_contract()
    {
        //get contracts that are past end_date
        // $contracts = Contract::wherePast('end_date')->get();
        $contractAppraisals = Appraisal::withoutGlobalScopes()->whereNotNull('contract_id')->pluck('contract_id')->toArray();
        // Get the most recent contract for the user that has not been appraised
        $contracts = Contract::withoutGlobalScopes()
            ->wherePast('end_date')
            ->whereNotIn('id', $contractAppraisals)
            ->orderBy('end_date', 'desc')
            ->get();
        foreach ($contracts as $contract) {
            $user = User::find($contract->employee->user_id);
            $user->notify(new \App\Notifications\AppraisalDueNotification('contract', $contract));
        }

        $this->info('Appraisal reminders sent to employees without whose contracts have expired');
    }
}
