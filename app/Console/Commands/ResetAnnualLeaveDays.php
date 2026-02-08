<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ResetAnnualLeaveDays extends Command
{
    protected $signature = 'leave:reset-annual-days';
    protected $description = 'Resets entitled leave days for all employees at the start of each calendar year';

    public function handle()
    {
        $currentYear = Carbon::now()->year;
        $this->info("Running annual leave reset for year: {$currentYear}");

        $count = 0;

        Employee::query()
            ->whereNotNull('entitled_leave_days') // only employees who have entitlement defined
            ->chunk(100, function ($employees) use (&$count, $currentYear) {
                foreach ($employees as $employee) {
                    // Optional: store previous year's balance somewhere if needed
                    // $employee->previous_year_balance = $employee->leave_balance;
                    
                    // Reset to full entitlement
                    $employee->entitled_leave_days = $employee->default_entitled_days ?? 30; // fallback value
                    
                    // Optional: clear used days if your business rule is full reset
                    // Leave::where('employee_id', $employee->employee_id)
                    //     ->whereYear('start_date', '<', $currentYear)
                    //     ->update(['carried_forward' => false]); // or archive old leaves
                    
                    $employee->save();
                    
                    $count++;
                    
                    Log::info("Reset leave entitlement", [
                        'employee_id' => $employee->employee_id,
                        'new_entitled_days' => $employee->entitled_leave_days,
                        'year' => $currentYear,
                    ]);
                }
            });

        $this->info("Reset completed for {$count} employees.");
        
        // Optional: send summary email to HR
        // Mail::to('hr@example.com')->send(new AnnualLeaveResetSummary($count));
    }
}