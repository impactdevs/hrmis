<?php

namespace App\Models;

use App\Models\Scopes\LeaveRosterScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;


#[ScopedBy([LeaveRosterScope::class])]
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
        'start_date',
        'end_date',
        'booking_approval_status',
        'leave_title',
        'rejection_reason'
    ];

    // If you want to use casts for certain attributes
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
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

    //check if the leave roster has been approved, if pending, return null, if rejected, false, if approved, true
    public function isApproved()
    {
        if ($this->booking_approval_status == 'Approved') {
            return true;
        } elseif ($this->booking_approval_status == 'Rejected') {
            return false;
        } else {
            return null;
        }
    }

}
