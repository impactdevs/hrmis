<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRoster extends Model
{
    // Specify the primary key
    protected $primaryKey = 'leave_roster_id';

    // Indicate that the primary key is not an auto-incrementing integer
    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';

    // The attributes that are mass assignable
    protected $fillable = [
        'leave_roster_id',
        'employee_id',
        'months',
        'year',
    ];

    // If you want to use casts for certain attributes
    protected $casts = [
        'months' => 'json',
    ];

    // Model boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leaveRoster) {
            $leaveRoster->leave_roster_id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    //a leave roster belongs to an employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    //get total leave days that an employee has taken
    public function totalLeaveDays()
    {
        //get the sum for all the month
        $months = $this->months;
        //current year
        $currentYear = Carbon::now()->year;
        $totalLeaveDays = 0;
        $leaveRosterMonths = $months[$currentYear] ?? [];
        foreach ($leaveRosterMonths as $leaveRosterMonth) {
            $totalLeaveDays += $leaveRosterMonth;
        }
        return $totalLeaveDays;
    }
}
