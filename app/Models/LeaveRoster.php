<?php

namespace App\Models;

use App\Models\Scopes\LeaveRosterScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'leave_title',
        'rejection_reason',
    ];

    // If you want to use casts for certain attributes
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Model boot method
    protected static function boot()
    {
        parent::boot();

        // Automatically generate a UUID for the primary key when creating a new leave roster
        static::creating(function ($leaveRoster) {
            $leaveRoster->leave_roster_id = (string) Str::uuid();
        });
    }

    // A leave roster belongs to an employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // A leave roster belongs to 0 or 1 leave
    public function leave()
    {
        return $this->belongsTo(Leave::class, 'leave_roster_id', 'leave_roster_id')->withDefault();
    }
}
