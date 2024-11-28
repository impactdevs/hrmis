<?php

namespace App\Models;

use App\Models\Scopes\LeaveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

use Carbon\Carbon; // Make sure to import Carbon

#[ScopedBy([LeaveScope::class])]
class Leave extends Model
{
    use HasFactory;

    protected $table = 'leaves';
    protected $primaryKey = 'leave_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'leave_id',
        'user_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'reason',
        'leave_request_status',
        'my_work_will_be_done_by',
        'leave_roster_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'my_work_will_be_done_by' => 'array',
        'leave_request_status' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leave) {
            $leave->leave_id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function leaveCategory()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'leave_type_id');
    }

    public function remainingLeaveDays()
    {
        $currentDate = Carbon::now()->startOfDay(); // Set to start of the day
        $startDate = $this->start_date->startOfDay(); // Set to start of the day
        $endDate = $this->end_date->startOfDay(); // Set to start of the day

        // Check if the leave has not started
        if ($currentDate->isBefore($startDate)) {
            return "Leave has not started"; // Message for not started leave
        }

        // Check if the leave has ended
        if ($currentDate->isAfter($endDate)) {
            return 0; // No remaining days if the leave has ended
        }

        // Calculate the remaining days
        return $currentDate->diffInDays($endDate); // Remaining days
    }

    //every leave belongs to a leave roster
    public function leaveRoster()
    {
        return $this->belongsTo(LeaveRoster::class, 'leave_roster_id', 'leave_roster_id');
    }


}
