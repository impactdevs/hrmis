<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SalaryAdvance extends Model
{
    // Indicate that the primary key is not an auto-incrementing integer
    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';

    protected $fillable = [
        'amount_applied_for',
        'reasons',
        'repayment_start_date',
        'repayment_end_date',
        'date_of_contract_expiry',
        'net_monthly_pay',
        'outstanding_loan',
        'comments',
        'loan_request_status',
        'employee_id'
    ];

    protected $casts = [
        'loan_request_status' => 'array',
        'repayment_start_date' => 'date',
        'repayment_end_date' => 'date',
        'date_of_contract_expiry' => 'date'
    ];

    // Model boot method
    protected static function boot()
    {
        parent::boot();

        // Automatically generate a UUID when creating a new Employee
        static::creating(function ($salary_advance) {
            $salary_advance->id = (string) Str::uuid();
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id')->withoutGlobalScopes();
    }
}
