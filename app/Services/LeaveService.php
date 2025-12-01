<?php

namespace App\Services;

use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\LeaveHistory;
use App\Models\User;
use App\Notifications\LeaveApplied;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class LeaveService
{
    /**
     * Validate maternity leave restrictions
     */
    public function validateMaternityLeave(string $leaveTypeId, string $userId, string $startDate): ?string
    {
        // Get the maternity leave type
        $maternityLeaveType = LeaveType::where('leave_type_name', 'Maternity Leave')->first();

        if (!$maternityLeaveType || $leaveTypeId !== $maternityLeaveType->leave_type_id) {
            return null; // Not a maternity leave, no validation needed
        }

        $year = Carbon::parse($startDate)->year;

        $alreadyTaken = Leave::where('user_id', $userId)
            ->where('leave_type_id', $maternityLeaveType->leave_type_id)
            ->whereYear('start_date', $year)
            ->exists();

        if ($alreadyTaken) {
            return 'You cannot take maternity leave more than once in a year.';
        }

        return null;
    }


    /**
     * Validate paternity leave requirements
     */

    public function validatePaternityLeave(string $leaveTypeId, string $userId, string $startDate): ?string
    {
        // Get the paternity leave type
        $paternityLeaveType = LeaveType::where('leave_type_name', 'paternity Leave')->first();

        if (!$paternityLeaveType || $leaveTypeId !== $paternityLeaveType->leave_type_id) {
            return null; // Not a paternity leave, no validation needed
        }

        $year = Carbon::parse($startDate)->year;

        $alreadyTaken = Leave::where('user_id', $userId)
            ->where('leave_type_id', $paternityLeaveType->leave_type_id)
            ->whereYear('start_date', $year)
            ->exists();

        if ($alreadyTaken) {
            return 'You cannot take paternity leave more than once in a year.';
        }

        return null;
    }

    /**
     * Validate handover note requirements
     */
    public function validateHandoverNote(string $handoverNote, bool $hasFile): ?string
    {
        if (empty($handoverNote)) {
            return 'Handover note is required.';
        }

        // If note is longer than 1500 characters, require a file
        if (strlen($handoverNote) > 1500 && !$hasFile) {
            return 'Please upload a handover file if your note is long (over 1500 characters).';
        }

        return null;
    }

    /**
     * Create leave request with history tracking
     */
    public function createLeaveRequest(array $requestData, string $userId): Leave
    {
        // Set the user_id
        $requestData['user_id'] = $userId;

        // Create the leave record
        $leave = Leave::create($requestData);

        // Log the creation action
        LeaveHistory::logAction(
            $leave->leave_id,
            LeaveHistory::ACTION_CREATED,
            null,
            'draft',
            'Leave request created'
        );

        // Log the submission action
        LeaveHistory::logAction(
            $leave->leave_id,
            LeaveHistory::ACTION_SUBMITTED,
            'draft',
            'pending_approval',
            'Leave request submitted for approval'
        );

        if (isset($requestData['handover_note_file']) && $requestData['handover_note_file'] instanceof \Illuminate\Http\UploadedFile) {
        $path = $requestData['handover_note_file']->store('handover_notes', 'public');
        \Log::info('Handover file uploaded in service', [
            'original_name' => $requestData['handover_note_file']->getClientOriginalName(),
            'stored_path' => $path,
        ]);
        $requestData['handover_note_file'] = $path;
    }

        return $leave;
    }

    /**
     * Send notifications for new leave request
     * Only notifies the first approver in the chain (HR)
     */
    public function sendNotifications(Leave $leave): void
    {
        $user = User::find($leave->user_id);

        if (!$user || !$user->employee || !$user->employee->department) {
            return;
        }

        // Notify people who will stand in
        if (isset($leave->my_work_will_be_done_by['users'])) {
            $myWorkWillBeDoneBy = explode(',', $leave->my_work_will_be_done_by['users']);
            $doneBy = User::whereIn('id', $myWorkWillBeDoneBy)->get();
            Notification::send($doneBy, new LeaveApplied($leave, 1));
        }

        // Only notify HR users initially (first in the approval chain)
        $hrUsers = User::role('HR')->get();
        Notification::send($hrUsers, new LeaveApplied($leave, 3));
    }

    /**
     * Send notification to the next approver in the chain
     */
    public function sendNextApproverNotification(Leave $leave): void
    {
        $nextApprover = $leave->getNextApproverRole();
        if (!$nextApprover) {
            return; // No next approver
        }

        $user = User::find($leave->user_id);
        if (!$user || !$user->employee || !$user->employee->department) {
            return;
        }

        if ($nextApprover === 'Head of Division') {
            // Notify the specific Head of Department for the employee's department
            $department = $user->employee->department;
            if ($department && $department->department_head) {
                $hod = User::where('id', $department->department_head)->first();
                if ($hod && $hod->hasRole('Head of Division')) {
                    Notification::send($hod, new LeaveApplied($leave, 2));
                }
            }
        } elseif ($nextApprover === 'Executive Secretary') {
            // Notify Executive Secretary
            $esUsers = User::role('Executive Secretary')->get();
            Notification::send($esUsers, new LeaveApplied($leave, 4));
        }
    }

    /**
     * Approve or reject leave request with history tracking
     */
    public function approveOrRejectLeave(Leave $leave, string $status, ?string $reason = null, ?string $role = null): void
    {
        $user = auth()->user();
        $leaveRequestStatus = $leave->leave_request_status ?: [];

        $action = $status === 'approved' ? LeaveHistory::ACTION_APPROVED : LeaveHistory::ACTION_REJECTED;
        $comments = $status === 'rejected' ? $reason : 'Leave request approved';

        // Determine current stage from existing status
        $stageFrom = 'pending_approval';
        if (!empty($leaveRequestStatus)) {
            if (isset($leaveRequestStatus['HR']) && $leaveRequestStatus['HR'] === 'approved') {
                $stageFrom = 'hr_approved';
            }
            if (isset($leaveRequestStatus['Head of Division']) && $leaveRequestStatus['Head of Division'] === 'approved') {
                $stageFrom = 'hod_approved';
            }
        }

        $stageTo = $stageFrom; // default, will update based on role

        // Update status based on user role
        if ($user->hasRole('HR')) {
            $leaveRequestStatus['HR'] = $status;
            $stageTo = $status === 'approved' ? 'hr_approved' : 'hr_rejected';
        } elseif ($user->hasRole('Head of Division')) {
            $leaveRequestStatus['Head of Division'] = $status;
            $stageTo = $status === 'approved' ? 'hod_approved' : 'hod_rejected';
        } elseif ($user->hasRole('Executive Secretary')) {
            $leaveRequestStatus['Executive Secretary'] = $status;
            $stageTo = $status === 'approved' ? 'final_approved' : 'final_rejected';
        }

        // Update leave record (removed overall_status references)
        $leave->leave_request_status = $leaveRequestStatus;
        $leave->rejection_reason = $status === 'rejected' ? $reason : null;
        $leave->save();

        // Log the action
        LeaveHistory::logAction(
            $leave->leave_id,
            $action,
            $stageFrom,
            $stageTo,
            $comments
        );

        // If approved, send notification to the next approver
        // If rejected, do not continue the workflow
        if ($status === 'approved') {
            $this->sendNextApproverNotification($leave);
        }
        // Note: If rejected, workflow stops here - no further notifications
    }

    /**
     * Cancel leave request with history tracking
     */
    public function cancelLeave(Leave $leave, ?string $reason = null): void
    {
        $leave->is_cancelled = true;
        $leave->save();

        LeaveHistory::logAction(
            $leave->leave_id,
            LeaveHistory::ACTION_CANCELLED,
            null,
            'cancelled',
            $reason ?? 'Leave request cancelled'
        );
    }

    /**
     * Get leave history for display
     */
    public function getLeaveHistory(string $leaveId): array
    {
        return LeaveHistory::with('actor')
            ->forLeave($leaveId)
            ->get()
            ->map(function ($history) {
                return $history->timeline_data;
            })
            ->toArray();
    }

    /**
     * Check if user can edit leave request
     */
    public function canEditLeave(Leave $leave): bool
    {
        // User can only edit their own leave requests (handle type casting)
        if ((string) $leave->user_id !== (string) auth()->id()) {
            return false;
        }

        // Check if leave is still in draft or pending status
        $status = $leave->leave_request_status ?? [];

        // If any approval has been given, can't edit
        foreach ($status as $role => $roleStatus) {
            if ($roleStatus === 'approved') {
                return false;
            }
        }

        return true;
    }

    /**
     * Update leave request with history tracking
     */
    public function updateLeaveRequest(Leave $leave, array $requestData): Leave
    {
        $originalData = $leave->toArray();

        // Update the leave
        $leave->update($requestData);

        // Log the edit action
        LeaveHistory::logAction(
            $leave->leave_id,
            LeaveHistory::ACTION_EDITED,
            null,
            null,
            'Leave request updated',
            [
                'original_data' => $originalData,
                'updated_data' => $requestData
            ]
        );

        return $leave;
    }
}
