<?php

namespace App\Services;

use App\Models\Appraisal;
use App\Models\Employee;
use App\Models\User;
use App\Models\AppraisalHistory;
use App\Notifications\AppraisalApplication;
use App\Notifications\AppraisalApproval;
use App\Exceptions\AppraisalException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Models\Scopes\EmployeeScope;

class AppraisalService
{

    public function getUserRoleForApproval(User $user): string
    {
        if ($user->hasRole('HR')) {
            return 'HR';
        }
        if ($user->hasRole('Head of Division')) {
            return 'Head of Division';
        }
        if ($user->hasRole('Executive Secretary')) {
            return 'Executive Secretary';
        }
        if ($user->hasRole('Staff')) {
            return 'Staff';
        }
        return 'Unknown';
    }

    /**
     * Get next approver role
     */
    protected function getNextApproverRole(Appraisal $appraisal): ?string
    {
        if ($appraisal->current_stage === 'Completed') {
            return null;
        }

        return $appraisal->current_stage;
    }

    /**
     *
     *  approval or rejection of an appraisal
     */
    /**
     * Process approval or rejection of an appraisal
     */
    public function processApprovalDecision(Appraisal $appraisal, string $status, ?string $reason = null, ?User $approver = null): array
    {
        $approver = $approver ?? auth()->user();
        $userRole = $this->getUserRoleForApproval($approver);

        // ✅ Only HR and ES can approve/reject, not HOD
        if (!in_array($userRole, ['Head of Division', 'HR', 'Executive Secretary'])) {
            throw AppraisalException::unauthorizedRole("Your role ($userRole) cannot approve/reject appraisals. Only HR and Executive Secretary can make approval decisions.");
        }

        // Check if user has permission to approve at current stage
        if ($userRole !== $appraisal->current_stage) {
            throw AppraisalException::unauthorizedRole("Your role ($userRole) does not match the current stage ({$appraisal->current_stage}).");
        }

        $appraisalRequestStatus = $appraisal->appraisal_request_status ?: [];

        // Update status based on user's role
        if ($status === 'approved') {
            $appraisalRequestStatus[$userRole] = 'approved';
            $appraisal->rejection_reason = null;

            // Advance to next stage if approved
            $this->advanceStage($appraisal, $userRole);

            // Update approval status
            if ($appraisal->current_stage === 'Completed') {
                $appraisal->approval_status = 'approved';
            }

        } else {
            $appraisalRequestStatus[$userRole] = 'rejected';
            $appraisal->rejection_reason = $reason;
            $appraisal->approval_status = 'rejected'; // Set approval status to rejected

            // Reset to draft for resubmission - treat as fresh appraisal
            // DON'T clear the status here - we need to keep the rejection status for tracking
            $appraisal->is_draft = true;

            // Reset to appropriate stage for resubmission
            $appraisee = $appraisal->employee;
            $appraiseeUser = $appraisee?->user;

            if ($appraiseeUser && $appraiseeUser->hasRole('Head of Division')) {
                $appraisal->current_stage = 'Head of Division';
            } else {
                $appraisal->current_stage = 'Staff';
            }
        }

        $appraisal->appraisal_request_status = $appraisalRequestStatus;
        $appraisal->save();

        // Log action
        AppraisalHistory::logAction(
            $appraisal->appraisal_id,
            $status === 'approved' ? AppraisalHistory::ACTION_APPROVED : AppraisalHistory::ACTION_REJECTED,
            null,
            null,
            $reason,
            ['approver_role' => $userRole],
            $approver->employee->employee_id,
            $userRole
        );

        // Send notifications
        $this->sendApprovalNotifications($appraisal, $status, $approver);

        return [
            'success' => true,
            'message' => 'Appraisal ' . $status . ' successfully.',
            'current_stage' => $appraisal->current_stage,
            'next_approver' => $this->getNextApproverRole($appraisal)
        ];
    }




    protected function advanceStage(Appraisal $appraisal, string $currentRole)
    {
        // Get appraisee info to determine if they're a Head of Division
        $appraisee = $appraisal->employee;
        $appraiseeUser = $appraisee->user ?? null;
        $isAppraiseeHod = $appraiseeUser && $appraiseeUser->hasRole('Head of Division');

        // Determine next stage based on current role and appraisee status
        $nextRole = null;

        if ($isAppraiseeHod) {
            // HOD appraisee: Staff → Executive Secretary (skip HR)
            switch ($currentRole) {
                case 'Head of Division':
                    $nextRole = 'Executive Secretary';
                    break;
                case 'Executive Secretary':
                    $nextRole = null; // Completion
                    break;
            }
        } else {
            // Regular staff: Staff → Head of Division → HR → Executive Secretary
            switch ($currentRole) {
                case 'Staff':
                    $nextRole = 'Head of Division';
                    break;
                case 'Head of Division':
                    $nextRole = 'HR';
                    break;
                case 'HR':
                    $nextRole = 'Executive Secretary'; // HR approves to ES
                    break;
                case 'Executive Secretary':
                    $nextRole = null; // Completion
                    break;
            }
        }

        if ($nextRole) {
            $appraisal->current_stage = $nextRole;
        } else {
            $appraisal->current_stage = 'Completed';
            $appraisal->approval_status = 'approved'; // Set final approval status
        }

        $appraisal->save();
    }

    /**
     * Submit appraisal and send initial notifications
     */
    public function submitAppraisal(Appraisal $appraisal): void
    {
        // Get appraisee details
        $employeeAppraisee = Employee::withoutGlobalScope(EmployeeScope::class)
            ->where('email', auth()->user()->email)
            ->first();

        if (!$employeeAppraisee) {
            throw AppraisalException::noEmployeeRecord();
        }

        // Ensure employee has required name fields
        if (!$employeeAppraisee->first_name || !$employeeAppraisee->last_name) {
            throw new \Exception('Employee record is missing required name fields');
        }

        // Get appraiser
        $employeeAppraiser = Employee::withoutGlobalScope(EmployeeScope::class)
            ->find($appraisal->appraiser_id);

        // // Check if this is a resubmission after rejection
        // $hasRejectionHistory = AppraisalHistory::where('appraisal_id', $appraisal->appraisal_id)
        //     ->where('action', AppraisalHistory::ACTION_REJECTED)
        //     ->exists();

        // Always notify the appraiser first - they need to see resubmitted forms
        if ($employeeAppraiser && $employeeAppraiser->user) {
            Notification::send(
                $employeeAppraiser->user,
                new AppraisalApplication($appraisal, $employeeAppraisee->first_name, $employeeAppraisee->last_name)
            );
        }

        // Determine who to notify based on current stage
        $nextApproverRole = $appraisal->current_stage;

        if ($nextApproverRole && $nextApproverRole !== 'Completed') {
            $nextApprovers = User::role($nextApproverRole)->get();

            foreach ($nextApprovers as $nextApprover) {
                Notification::send(
                    $nextApprover,
                    new AppraisalApplication($appraisal, $employeeAppraisee->first_name, $employeeAppraisee->last_name)
                );
            }
        }

        // Log the submission action if not already logged
        $latestHistory = AppraisalHistory::where('appraisal_id', $appraisal->appraisal_id)
            ->where('action', 'submitted')
            ->latest('created_at')
            ->first();

        if (!$latestHistory) {
            AppraisalHistory::logAction(
                $appraisal->appraisal_id,
                AppraisalHistory::ACTION_SUBMITTED,
                null,
                $appraisal->current_stage,
                'Appraisal submitted and advanced to next stage',
                ['next_stage' => $nextApproverRole],
                $employeeAppraiser->employee_id ?? null,
                auth()->user()->getRoleNames()->first()
            );
        }
    }

    // /**
    //  * Create a new appraisal with proper initialization
    //  */
    // public function createAppraisal(User $user): array
    // {
    //     $role = $user->getRoleNames()->first();

    //     if (!in_array($role, ['Staff', 'Head of Division'])) {
    //         throw AppraisalException::unauthorizedRole();
    //     }

    //     if (!$user->employee) {
    //         throw AppraisalException::noEmployeeRecord();
    //     }

    //     if ($role === 'Staff') {
    //         return $this->createStaffAppraisal($user);
    //     } else {
    //         return $this->createHodAppraisal($user);
    //     }
    // }

    // /**
    //  * Create appraisal for staff member
    //  */
    // protected function createStaffAppraisal(User $user): array
    // {
    //     if (!$user->employee->department) {
    //         throw AppraisalException::noDepartmentAssigned();
    //     }

    //     if (!$user->employee->department->department_head) {
    //         throw AppraisalException::noDepartmentHead($user->employee->department->department_name ?? null);
    //     }

    //     $departmentHead = User::find($user->employee->department->department_head);
    //     if (!$departmentHead) {
    //         throw AppraisalException::noDepartmentHead();
    //     }

    //     $hasValidRole = $departmentHead->hasRole('Head of Division') || $departmentHead->hasRole('HR');
    //     if (!$hasValidRole) {
    //         $currentRole = $departmentHead->getRoleNames()->first();
    //         throw AppraisalException::invalidDepartmentHeadRole($currentRole);
    //     }

    //     if (!$departmentHead->employee) {
    //         throw AppraisalException::noDepartmentHead();
    //     }

    //     return [
    //         'appraiser_id' => $departmentHead->employee->employee_id,
    //         'employee_id' => $user->employee->employee_id,
    //         'current_stage' => 'Staff'
    //     ];
    // }

    /**
     * Handle appraisal withdrawal
     */
    public function withdrawAppraisal(Appraisal $appraisal, User $user): void
    {
        $appraisal->withdraw($user);

        // Notify appraisee so they know it's back to them
        $appraisee = Employee::withoutGlobalScope(EmployeeScope::class)
            ->find($appraisal->employee_id);

        if ($appraisee && $appraisee->user) {
            Notification::send(
                $appraisee->user,
                new AppraisalApproval($appraisal, 'withdrawn', $user->name, $this->getUserRoleForApproval($user))
            );
        }
    }


    /**
     * Get default appraisal data structure
     */
    public function getDefaultAppraisalData(array $baseData): array
    {
        return array_merge($baseData, [
            "appraisal_start_date" => null,
            "appraisal_end_date" => null,
            "if_no_job_compatibility" => null,
            "unanticipated_constraints" => null,
            "personal_initiatives" => null,
            "training_support_needs" => null,
            "appraisal_period_rate" => [
                [
                    "planned_activity" => null,
                    "output_results" => null,
                    "supervisee_score" => null,
                    "superviser_score" => null,
                ]
            ],
            "personal_attributes_assessment" => [
                "technical_knowledge" => ["appraisee_score" => null, "appraiser_score" => null, "agreed_score" => null],
                "commitment" => ["appraisee_score" => null, "appraiser_score" => null, "agreed_score" => null],
                "team_work" => ["appraisee_score" => null, "appraiser_score" => null, "agreed_score" => null],
                "productivity" => ["appraisee_score" => null, "appraiser_score" => null, "agreed_score" => null],
                "integrity" => ["appraisee_score" => null, "appraiser_score" => null, "agreed_score" => null],
                "flexibility" => ["appraisee_score" => null, "appraiser_score" => null, "agreed_score" => null],
                "attendance" => ["appraisee_score" => null, "appraiser_score" => null, "agreed_score" => null],
                "appearance" => ["appraisee_score" => null, "appraiser_score" => null, "agreed_score" => null],
                "interpersonal" => ["appraisee_score" => null, "appraiser_score" => null, "agreed_score" => null],
                "initiative" => ["appraisee_score" => null, "appraiser_score" => null, "agreed_score" => null]
            ],
            "performance_planning" => [
                [
                    "description" => null,
                    "performance_target" => null,
                    "target_date" => null,
                ]
            ],
            "employee_strength" => null,
            "employee_improvement" => null,
            "superviser_overall_assessment" => null,
            "recommendations" => null,
            "panel_comment" => null,
            "panel_recommendation" => null,
            "overall_assessment" => null,
            "executive_secretary_comments" => null,
            'is_draft' => true,
        ]);
    }

    /**
     * Check if user can edit appraisal
     */
    public function canUserEditAppraisal(Appraisal $appraisal, User $user): bool
    {
        $isAppraisee = $user->employee && $user->employee->employee_id === $appraisal->employee_id;

        if ($isAppraisee) {
            $status = $appraisal->appraisal_request_status ?? [];
            $appraiser = Employee::find($appraisal->appraiser_id);
            $appraiserUser = $appraiser ? User::find($appraiser->user_id) : null;
            $appraiserRole = $appraiserUser ? $this->getUserRoleForApproval($appraiserUser) : null;

            if ($user->hasRole('Head of Division')) {
                return !isset($status['Executive Secretary']) || $status['Executive Secretary'] === 'rejected';
            }

            if ($appraiserRole === 'HR') {
                return !isset($status['HR']) || $status['HR'] === 'rejected';
            } else {
                return !isset($status['Head of Division']) || $status['Head of Division'] === 'rejected';
            }
        }

        // Appraiser can always edit before approving
        return $user->employee && $user->employee->employee_id === $appraisal->appraiser_id;
    }


    /**
     * Check specifically if appraisee can edit (for clearer messaging)
     */
    public function canAppraiseeEdit(Appraisal $appraisal, User $user): bool
    {
        $isAppraisee = $user->employee && $user->employee->employee_id === $appraisal->employee_id;

        if (!$isAppraisee) {
            return false;
        }

        $status = $appraisal->appraisal_request_status ?? [];

        // Get the appraiser to determine their role
        $appraiserEmployee = Employee::withoutGlobalScope(EmployeeScope::class)
            ->find($appraisal->appraiser_id);
        $appraiserUser = $appraiserEmployee ? User::find($appraiserEmployee->user_id) : null;

        // If the employee is a Head of Division, they can edit until Executive Secretary reviews
        if ($user->hasRole('Head of Division')) {
            return !isset($status['Executive Secretary']) ||
                $status['Executive Secretary'] === null ||
                $status['Executive Secretary'] === 'rejected';
        }

        // For regular staff, check based on who their appraiser is
        // If appraiser is HR, check HR status; if HOD, check Head of Division status
        if ($appraiserUser && $appraiserUser->hasRole('HR')) {
            return !isset($status['HR']) ||
                $status['HR'] === null ||
                $status['HR'] === 'rejected';
        } else {
            return !isset($status['Head of Division']) ||
                $status['Head of Division'] === null ||
                $status['Head of Division'] === 'rejected';
        }
    }

    /**
     * Get the reason why appraisee cannot edit (for user feedback)
     */
    public function getAppraiseeEditRestrictionReason(Appraisal $appraisal, User $user): ?string
    {
        if ($this->canAppraiseeEdit($appraisal, $user)) {
            return null; // Can edit, no restriction
        }

        $isAppraisee = $user->employee && $user->employee->employee_id === $appraisal->employee_id;

        if (!$isAppraisee) {
            return "You are not the employee being appraised in this form.";
        }

        $status = $appraisal->appraisal_request_status ?? [];

        // Get the appraiser to determine their role
        $appraiserEmployee = Employee::withoutGlobalScope(EmployeeScope::class)
            ->find($appraisal->appraiser_id);
        $appraiserUser = $appraiserEmployee ? User::find($appraiserEmployee->user_id) : null;

        if ($user->hasRole('Head of Division')) {
            if (isset($status['Executive Secretary']) && $status['Executive Secretary'] === 'approved') {
                throw AppraisalException::appraiseeCannotEditAfterESReview();
            } elseif (isset($status['Executive Secretary']) && $status['Executive Secretary'] === 'rejected') {
                return "This appraisal has been rejected by the Executive Secretary. Please contact HR for guidance.";
            }
        } else {
            // Check based on appraiser role
            if ($appraiserUser && $appraiserUser->hasRole('HR')) {
                if (isset($status['HR']) && $status['HR'] === 'approved') {
                    throw AppraisalException::appraiseeCannotEditAfterSupervisorReview();
                } elseif (isset($status['HR']) && $status['HR'] === 'rejected') {
                    return "This appraisal has been rejected by HR. Please review their feedback and contact them for clarification.";
                }
            } else {
                if (isset($status['Head of Division']) && $status['Head of Division'] === 'approved') {
                    throw AppraisalException::appraiseeCannotEditAfterSupervisorReview();
                } elseif (isset($status['Head of Division']) && $status['Head of Division'] === 'rejected') {
                    return "This appraisal has been rejected by your supervisor. Please review their feedback and contact them for clarification.";
                }
            }
        }

        return "This appraisal is currently under review and cannot be edited.";
    }

    /**
     * Check if user can access attachments in appraisal
     */
    public function canUserAccessAttachments(Appraisal $appraisal, User $user): bool
    {
        // Check if the user is the appraisee (employee being appraised)
        $isAppraisee = $user->employee && $user->employee->employee_id === $appraisal->employee_id;

        if ($isAppraisee) {
            return true; // Appraisees can always view their own attachments
        }

        // Check if user is an appraiser - allow access after appraisal submission
        $isAppraiser = $user->employee && $user->employee->employee_id === $appraisal->appraiser_id;

        if ($isAppraiser) {
            // Appraisers can access attachments only after the appraisal has been submitted
            // Check if there's a submitted draft for this appraisal
            $hasSubmittedDraft = DB::table('appraisal_drafts')
                ->where('appraisal_id', $appraisal->appraisal_id)
                ->where('is_submitted', true)
                ->exists();

            return $hasSubmittedDraft;
        }

        // HR and Executive Secretary can access attachments for oversight
        if ($user->hasAnyRole(['HR', 'Executive Secretary'])) {
            return true;
        }

        // Head of Division can access attachments for employees under their supervision
        if ($user->hasRole('Head of Division')) {
            // Check if this appraisal has been submitted and they are in the approval flow
            $status = $appraisal->appraisal_request_status ?? [];
            $hasSubmittedDraft = DB::table('appraisal_drafts')
                ->where('appraisal_id', $appraisal->appraisal_id)
                ->where('is_submitted', true)
                ->exists();

            return $hasSubmittedDraft;
        }

        // Default: no access
        return false;
    }

    /**
     * Get attachment access status with detailed reason for UI feedback
     */
    public function getAttachmentAccessInfo(Appraisal $appraisal, User $user): array
    {
        $canAccess = $this->canUserAccessAttachments($appraisal, $user);
        $isAppraisee = $user->employee && $user->employee->employee_id === $appraisal->employee_id;
        $isAppraiser = $user->employee && $user->employee->employee_id === $appraisal->appraiser_id;

        if ($canAccess) {
            return [
                'can_access' => true,
                'reason' => null,
                'user_type' => $isAppraisee ? 'appraisee' : ($isAppraiser ? 'appraiser' : 'supervisor')
            ];
        }

        // Determine why access is denied
        if ($isAppraiser) {
            $hasSubmittedDraft = DB::table('appraisal_drafts')
                ->where('appraisal_id', $appraisal->appraisal_id)
                ->where('is_submitted', true)
                ->exists();

            if (!$hasSubmittedDraft) {
                return [
                    'can_access' => false,
                    'reason' => 'Attachments will be available after the appraisal is submitted by the employee.',
                    'user_type' => 'appraiser'
                ];
            }
        }

        return [
            'can_access' => false,
            'reason' => 'You do not have permission to view attachments for this appraisal.',
            'user_type' => 'unauthorized'
        ];
    }

    /**
     * Update appraisal draft status
     */
    /**
     * Update appraisal draft status
     */
    public function updateDraftStatus(Appraisal $appraisal, bool $isSubmitted, User $user): string
    {
        if ($isSubmitted) {
            // Final submission
            DB::table('appraisal_drafts')
                ->where('appraisal_id', $appraisal->appraisal_id)
                ->where('employee_id', $user->employee->employee_id)
                ->update(['is_submitted' => true]);

            // Check if this is a resubmission after rejection
            $hasRejection = !empty($appraisal->appraisal_request_status) &&
                collect($appraisal->appraisal_request_status)->contains(fn($s) => $s === 'rejected');

            if ($hasRejection) {
                // FOR RESUBMISSION: Clear rejection status and appraisal_request_status for fresh review
                // (History is preserved in AppraisalHistory)
                $appraisal->rejection_reason = null;
                $appraisal->appraisal_request_status = [];  // Reset status for fresh workflow
            } else {
                // FOR FRESH SUBMISSION: Clear any existing status
                $appraisal->appraisal_request_status = [];
            }

            $appraisal->is_draft = false;


            // Set appropriate stage based on appraisee role
            $appraisee = $appraisal->employee;
            $appraiseeUser = $appraisee?->user;

            if ($appraiseeUser && $appraiseeUser->hasRole('Head of Division')) {
                $appraisal->current_stage = 'Executive Secretary';
            } else {
                $appraisal->current_stage = 'Head of Division';
            }

            $appraisal->save();

            // Log submission or resubmission
            AppraisalHistory::logAction(
                $appraisal->appraisal_id,
                $hasRejection ? AppraisalHistory::ACTION_RESUBMITTED : AppraisalHistory::ACTION_SUBMITTED,
                null,
                $appraisal->current_stage,
                $hasRejection ? 'Appraisal resubmitted after rejection' : 'Appraisal submitted for review',
                null,
                $user->employee->employee_id,
                $user->getRoleNames()->first()
            );

            // Send notifications
            $this->submitAppraisal($appraisal);

            return $hasRejection ? "Appraisal resubmitted successfully." : "Appraisal submitted successfully.";
        } else {
            // Save as draft
            $appraisal->is_draft = true;
            $appraisal->save();

            $draftExists = DB::table('appraisal_drafts')
                ->where('appraisal_id', $appraisal->appraisal_id)
                ->where('employee_id', $user->employee->employee_id)
                ->exists();

            if (!$draftExists) {
                DB::table('appraisal_drafts')->insert([
                    'appraisal_id' => $appraisal->appraisal_id,
                    'employee_id' => $user->employee->employee_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return "Appraisal has been saved as a draft successfully.";
        }
    }

    /**
     * Send notifications after approval/rejection decision
     */
    protected function sendApprovalNotifications(Appraisal $appraisal, string $status, User $approver): void
    {
        try {
            $appraisee = Employee::withoutGlobalScope(EmployeeScope::class)
                ->find($appraisal->employee_id);

            if (!$appraisee || !$appraisee->user) {
                \Log::warning('Cannot send notification: Appraisee user not found', ['appraisal_id' => $appraisal->appraisal_id]);
                return;
            }

            // Send notification to appraisee
            Notification::send(
                $appraisee->user,
                new AppraisalApproval(
                    $appraisal,
                    $status,
                    $approver->name ?? 'Unknown',
                    $this->getUserRoleForApproval($approver)
                )
            );

            // If approved, notify next approver
            if ($status === 'approved' && $appraisal->current_stage !== 'Completed') {
                $nextApproverRole = $appraisal->current_stage;
                $nextApprover = User::role($nextApproverRole)->first();

                if ($nextApprover) {
                    Notification::send(
                        $nextApprover,
                        new AppraisalApplication(
                            $appraisal,
                            $appraisee->first_name ?? '',
                            $appraisee->last_name ?? ''
                        )
                    );
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send approval notifications', [
                'appraisal_id' => $appraisal->appraisal_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}