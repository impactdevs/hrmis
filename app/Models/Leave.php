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
        'leave_roster_id',
        'leave_address',
        'phone_number',
        'other_contact_details',
        'handover_note_file',
        'handover_note',
        'is_cancelled',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'my_work_will_be_done_by' => 'array',
        'leave_request_status' => 'array',
        'is_cancelled' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leave) {
            $leave->leave_id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'leave_id';
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id', 'user_id');
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

    // Relationship to leave history
    public function history()
    {
        return $this->hasMany(LeaveHistory::class, 'leave_id', 'leave_id')->orderBy('created_at', 'desc');
    }

    // Helper method to get current leave status
    public function getCurrentStatus(): string
    {
        // Check if leave is cancelled first
        if ($this->is_cancelled) {
            return 'Cancelled';
        }

        $status = $this->leave_request_status ?? [];

        // If no status data at all, it's a new/draft request
        if (empty($status)) {
            return 'Submitted';
        }

        // Check for any rejections first
        foreach (['HR', 'Head of Division', 'Executive Secretary'] as $role) {
            if (isset($status[$role]) && $status[$role] === 'rejected') {
                return 'Rejected by ' . $role;
            }
        }

        // Check Executive Secretary (final approval)
        if (isset($status['Executive Secretary']) && $status['Executive Secretary'] === 'approved') {
            return 'Fully Approved';
        }

        // Check sequential approval status
        if (isset($status['Head of Division']) && $status['Head of Division'] === 'approved') {
            return 'Approved by Head of Division - Awaiting Executive Secretary';
        }

        if (isset($status['HR']) && $status['HR'] === 'approved') {
            return 'Approved by HR - Awaiting Head of Division';
        }

        // If we reach here, it's pending initial approval
        return 'Pending HR Approval';
    }

    // Helper method to get leave title for display
    public function getLeaveTitle(): string
    {
        $leaveType = $this->leaveCategory->leave_type_name ?? 'Leave';
        $employee = $this->employee;
        $employeeName = $employee ? "{$employee->first_name} {$employee->last_name}" : 'Unknown';

        return "{$leaveType} - {$employeeName}";
    }

    // Check if leave can be edited
    public function canBeEdited(): bool
    {
        // Must be authenticated to edit
        if (!auth()->check()) {
            return false;
        }

        // User can only edit their own leave requests (handle type casting)
        if ((string) $this->user_id !== (string) auth()->id()) {
            return false;
        }

        // Cannot edit cancelled leaves
        if ($this->is_cancelled) {
            return false;
        }

        // Check if leave is still in draft or pending status
        $status = $this->leave_request_status ?? [];

        // If any approval has been given, can't edit
        foreach ($status as $role => $roleStatus) {
            if ($roleStatus === 'approved') {
                return false;
            }
        }
        return true;
    }

    public function durationForLeave(array $publicHolidays = [])
    {
        return Carbon::parse($this->start_date)
            ->diffInDaysFiltered(function (Carbon $date) use ($publicHolidays) { // Now uses passed-in holidays
                return !$date->isWeekend() && !in_array($date->toDateString(), $publicHolidays);
            }, Carbon::parse($this->end_date));
    }

    /**
     * Get the next approver role based on current approvals
     * Sequential flow: Staff -> HR -> Head of Division -> Executive Secretary
     * If any approver rejects, workflow stops
     */
    public function getNextApproverRole(): ?string
    {
        $status = $this->leave_request_status ?? [];
        
        // IMPORTANT: If any approver has rejected, stop the workflow entirely
        foreach (['HR', 'Head of Division', 'Executive Secretary'] as $role) {
            if (isset($status[$role]) && $status[$role] === 'rejected') {
                return null; // Workflow stops on any rejection
            }
        }
        
        // Check if HR has approved, next is Head of Division
        if (isset($status['HR']) && $status['HR'] === 'approved') {
            if (!isset($status['Head of Division']) || $status['Head of Division'] === null) {
                return 'Head of Division';
            }
        }
        
        // Check if Head of Division has approved, next is Executive Secretary
        if (isset($status['Head of Division']) && $status['Head of Division'] === 'approved') {
            if (!isset($status['Executive Secretary']) || $status['Executive Secretary'] === null) {
                return 'Executive Secretary';
            }
        }
        
        // If no approvals yet, start with HR
        if (empty($status) || (!isset($status['HR']) || $status['HR'] === null)) {
            return 'HR';
        }
        
        // All approvals complete
        return null;
    }

    /**
     * Check if the current user can approve this leave request
     */
    public function canUserApprove(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Cannot approve if leave has been rejected by anyone
        if ($this->isRejected()) {
            return false;
        }

        $nextApprover = $this->getNextApproverRole();
        if (!$nextApprover) {
            return false; // All approvals complete or workflow stopped
        }

        return $user->hasRole($nextApprover);
    }

    /**
     * Get the current approval stage
     */
    public function getCurrentApprovalStage(): string
    {
        $status = $this->leave_request_status ?? [];
        
        // Check for rejections
        foreach (['HR', 'Head of Division', 'Executive Secretary'] as $role) {
            if (isset($status[$role]) && $status[$role] === 'rejected') {
                return 'rejected';
            }
        }
        
        // Check completion
        if (isset($status['Executive Secretary']) && $status['Executive Secretary'] === 'approved') {
            return 'completed';
        }
        
        // Check current stage
        if (isset($status['Head of Division']) && $status['Head of Division'] === 'approved') {
            return 'awaiting_executive_secretary';
        }
        
        if (isset($status['HR']) && $status['HR'] === 'approved') {
            return 'awaiting_head_of_division';
        }
        
        return 'awaiting_hr';
    }

    /**
     * Check if leave is fully approved
     */
    public function isFullyApproved(): bool
    {
        $status = $this->leave_request_status ?? [];
        return isset($status['HR']) && $status['HR'] === 'approved' &&
               isset($status['Head of Division']) && $status['Head of Division'] === 'approved' &&
               isset($status['Executive Secretary']) && $status['Executive Secretary'] === 'approved';
    }

    /**
     * Check if leave has been rejected
     */
    public function isRejected(): bool
    {
        $status = $this->leave_request_status ?? [];
        foreach (['HR', 'Head of Division', 'Executive Secretary'] as $role) {
            if (isset($status[$role]) && $status[$role] === 'rejected') {
                return true;
            }
        }
        return false;
    }

}
