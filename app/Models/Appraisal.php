<?php

namespace App\Models;

use App\Models\Scopes\AppraisalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Support\Facades\DB;
use App\Models\AppraisalHistory;
use App\Models\Route;

#[ScopedBy([AppraisalScope::class])]
class Appraisal extends Model
{
    use HasFactory;

    // Specify the table if it doesn't follow Laravel's naming convention
    protected $table = 'appraisals';

    //primary key is employee_id
    // Specify the primary key
    protected $primaryKey = 'appraisal_id';

    // Indicate that the primary key is not an auto-incrementing integer
    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';

    // The attributes that are mass assignable
    protected $fillable = [
        'employee_id',
        'appraiser_id',
        'current_stage',
        'reminder_sent',
        'last_reminder_sent',
        'review_type',
        'review_type_other',
        'appraisal_start_date',
        'appraisal_end_date',
        'job_compatibility',
        'if_no_job_compatibility',
        'unanticipated_constraints',
        'personal_initiatives',
        'training_support_needs',
        'suggestions',
        'appraisal_period_rate',
        'personal_attributes_assessment',
        'performance_planning',
        'employee_strength',
        'employee_improvement',
        'superviser_overall_assessment',
        'panel_recommendations',
        'supervisor_recommendations',
        'panel_comment',
        'overall_assessment_and_comments',
        'panel_recommendation',
        'overall_assessment',
        'executive_secretary_comments',
        'contract_id',
        'relevant_documents',
        'rejection_reason',
        'qualifications',
        'is_draft',
        'approval_status'
    ];

    protected $casts = [
        'appraisal_period_accomplishment' => 'array',
        'appraisal_start_date' => 'date',
        'appraisal_end_date' => 'date',
        'last_reminder_sent' => 'datetime',
        'personal_attributes_assessment' => 'array',
        'performance_planning' => 'array',
        'appraisal_period_rate' => 'array',
        'appraisal_request_status' => 'array',
        'relevant_documents' => 'array',
        'attachments' => 'array',
        'suggestions' => 'array',
        'qualifications' => 'array',
        'appraisal_status' => 'array',
        'approval_status' => 'string'
    ];

    //define append


    // Model boot method
    protected static function boot()
    {
        parent::boot();

        // Automatically generate a UUID when creating a new Employee
        static::creating(function ($appraisal) {
            $appraisal->appraisal_id = (string) Str::uuid();
        });



        // Log creation
        static::created(function ($appraisal) {
            AppraisalHistory::logAction(
                $appraisal->appraisal_id,
                AppraisalHistory::ACTION_CREATED,
                null,
                $appraisal->current_stage,
                'Appraisal record created'
            );
        });

        // Log updates
        static::updated(function ($appraisal) {
            $changes = $appraisal->getChanges();

            // Log stage changes
            if (isset($changes['current_stage'])) {
                $original = $appraisal->getOriginal();
                AppraisalHistory::logAction(
                    $appraisal->appraisal_id,
                    AppraisalHistory::ACTION_STAGE_ADVANCED,
                    $original['current_stage'] ?? null,
                    $changes['current_stage'],
                    'Stage automatically advanced',
                    ['changes' => $changes]
                );
            }

            // Log general edits (exclude automatic stage changes)
            $significantChanges = array_diff_key($changes, ['current_stage' => '', 'updated_at' => '']);
            if (!empty($significantChanges)) {
                AppraisalHistory::logAction(
                    $appraisal->appraisal_id,
                    AppraisalHistory::ACTION_EDITED,
                    null,
                    null,
                    'Appraisal data updated',
                    ['changed_fields' => array_keys($significantChanges)]
                );
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id')->withoutGlobalScopes();
    }

    public function appraiser()
    {
        return $this->belongsTo(Employee::class, 'appraiser_id', 'employee_id');
    }

    public function history()
    {
        return $this->hasMany(AppraisalHistory::class, 'appraisal_id', 'appraisal_id')
            ->orderBy('created_at', 'desc');
    }


    public function getRouteKeyName()
    {
        return 'appraisal_id';
    }


    public function getIsAppraiseeAttribute()
    {
        // Check if the logged-in user is the appraisee
        if (auth()->user()->employee->employee_id == $this->employee_id) {

            // Optional: check if the appraisee is also a Head of Division
            if ($this->getTheAppraiseeIsHod()) {
                // You can return something special here if needed
                return true; // or return 'HOD' or some other indicator
            }

            return true;
        }

        return false;
    }

    public function getIsAppraisorAttribute()
    {
        if (auth()->user()->employee->employee_id == $this->appraiser_id) {
            return true;
        }

        return false;
    }

    public function getIsEsAttribute()
    {
        if (auth()->user()->hasRole('Executive Secretary')) {
            return true;
        }

        return false;
    }

    public function getTheAppraiseeIsHod()
    {
        // Fetch the employee without global scopes
        $employee = Employee::withoutGlobalScopes()->find($this->employee_id);

        if (!$employee || !$employee->user) {
            return false;
        }

        // Check if the appraisee's user has the 'Head of Division' role
        return $employee->user->hasRole('Head of Division');
    }


    /* enforce appraisal approval hierarchy
     *Staff creates appraisal
     *the next to approve is the supervisor(Head of Division)
     *the next to approve is HR
     *the next to and the last to approve is the Executive Secretary
     */
    public function getCurrentApproverAttribute()
    {
        // Expected approval flow in order
        $approvalFlow = ['Head of Division', 'HR', 'Executive Secretary'];

        //check if the employee who applied for appraisal is the Head of Division and set Head of Division as approved by default
        $employee = Employee::withoutGlobalScopes()->find($this->employee_id);
        if ($employee && $employee->user_id) {
            $user = User::find($employee->user_id);
            if ($user && $user->hasRole('Head of Division')) {
                $approvalFlow = ['Executive Secretary'];
            }
        }
        $status = $this->appraisal_request_status ?? [];

        foreach ($approvalFlow as $role) {
            // If the role is not set or false, it's the next approver
            if (empty($status[$role])) {
                return $role; // Return key as the role (e.g., 'hod', 'hr', 'executive_secretary')
            }
        }

        // All roles have approved
        return null;
    }

    public function getDocumentPath($doc)
    {
        return $doc['proof'] ? storage_path('app/public/' . $doc['proof']) : null;
    }


    public function getPreviousApproverAttribute()
    {
        $approvalFlow = ['Head of Division', 'HR', 'Executive Secretary'];

        $status = $this->appraisal_request_status ?? [];

        $previousApprover = null;

        foreach ($approvalFlow as $role) {
            if (!array_key_exists($role, $status)) {
                break;
            }
            $previousApprover = $role;
        }

        return $previousApprover; // null if no previous approver exists
    }

    public function getHasDraftAttribute()
    {
        // cehck in draft table using query builder
        $draft = DB::table('appraisal_drafts')
            ->where('appraisal_id', $this->appraisal_id)->where('employee_id', auth()->user()->employee->employee_id)->where('is_submitted', false)
            ->exists();
        if ($draft) {
            return true;
        }

        return false;
    }



    public function getHasSomeDraftAttribute()
    {

        return DB::table('appraisal_drafts')
            ->where('appraisal_id', $this->appraisal_id)->where('is_submitted', false)
            ->exists();
    }

    /**
     * Get the available stages for the appraisal workflow
     */
    public static function getAvailableStages(string $appraiserRole): array
    {
        return match ($appraiserRole) {
            'Executive Secretary' => ['Staff', 'Executive Secretary', 'Completed'],
            'HR' => ['Staff', 'HR', 'Executive Secretary', 'Completed'],
            'Head of Division' => ['Staff', 'Head of Division', 'Executive Secretary', 'Completed'],
            default => ['Staff', 'Head of Division', 'HR', 'Executive Secretary', 'Completed'],
        };
    }


    public function getIsResubmissionAttribute(): bool
    {
        $status = $this->appraisal_request_status ?? [];
        $hasRejection = collect($status)->contains(fn($s) => $s === 'rejected');
        $hasApproval = collect($status)->contains(fn($s) => $s === 'approved');

        return $hasRejection && !$hasApproval && $this->is_draft;
    }
    /**
     * Advance to the next stage based on current approvals
     */
    public function advanceStage(): bool
    {
        $appraiserRole = $this->appraiser->user->getRoleNames()->first();
        $stages = self::getAvailableStages($appraiserRole);
        $currentIndex = array_search($this->current_stage, $stages);

        if ($currentIndex === false || $currentIndex >= count($stages) - 1) {
            return false;
        }

        $nextStage = $stages[$currentIndex + 1];

        // Check if we can advance based on approval status
        if ($this->canAdvanceToStage($nextStage)) {
            $this->current_stage = $nextStage;
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Check if appraisal can advance to the given stage
     */
    public function canAdvanceToStage(string $stage): bool
    {
        $appraiserRole = $this->appraiser->user->getRoleNames()->first();
        $approvalFlow = self::getAvailableStages($appraiserRole);
        $status = $this->appraisal_request_status ?? [];

        $previousIndex = array_search($stage, $approvalFlow) - 1;
        if ($previousIndex < 0) {
            return true; // First stage always advanceable
        }

        $previousRole = $approvalFlow[$previousIndex];
        return isset($status[$previousRole]) && $status[$previousRole] === 'approved';
    }


    public function setAppraisalRequestStatusAttribute($value)
    {
        // If it's a string, convert it to an appropriate array
        if (is_string($value)) {
            if ($value === 'draft' || $value === '"draft"') {
                $this->attributes['appraisal_request_status'] = json_encode(['draft' => true]);
            } elseif ($value === 'submitted' || $value === '"submitted"') {
                $this->attributes['appraisal_request_status'] = json_encode([]);
            } else {
                $this->attributes['appraisal_request_status'] = json_encode([]);
            }
        } else {
            $this->attributes['appraisal_request_status'] = json_encode($value);
        }
    }

    public function getAppraisalRequestStatusAttribute($value)
    {
        // Handle both string and array values
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($value) ? $value : [];
    }

    /**
     * Get the next approver based on current stage
     */
    public function getNextApproverRole(): ?string
    {
        $appraiserRole = $this->appraiser->user->getRoleNames()->first();
        $approvalFlow = self::getAvailableStages($appraiserRole);
        $currentIndex = array_search($this->current_stage, $approvalFlow);

        if ($currentIndex === false || $currentIndex >= count($approvalFlow) - 2) {
            return null; // Last approver or completed
        }

        return $approvalFlow[$currentIndex + 1];
    }
    /**
     * Check if the appraisal is at the final stage
     */
    public function isCompleted(): bool
    {
        return $this->current_stage === 'Completed';
    }

    /**
     * Get stage progress percentage
     */
    public function getStageProgressAttribute(): int
    {
        $stages = self::getAvailableStages();
        $currentIndex = array_search($this->current_stage, $stages);

        if ($currentIndex === false) {
            return 0;
        }

        return (int) (($currentIndex / (count($stages) - 1)) * 100);
    }

    /**
     * look for draft users
     */
    public function getDraftUsersAttribute()
    {
        $roles = ['Executive Secretary', 'HR', 'Head of Division', 'Staff'];
        $currentUser = auth()->user();
        $currentRole = $currentUser->getRoleNames()->first();

        // Exclude the current user's role if it makes sense
        $filteredRoles = array_filter($roles, function ($role) use ($currentRole) {
            return $role !== $currentRole;
        });

        // Get employee_ids for each role except the current user's role
        $roleEmployeeIds = [];
        foreach ($filteredRoles as $role) {
            $user = \App\Models\User::role($role)->first();
            if ($user && $user->employee) {
                $roleEmployeeIds[$role] = $user->employee->employee_id;
            }
        }

        $drafts = DB::table('appraisal_drafts')
            ->where('appraisal_id', $this->appraisal_id)
            ->where('is_submitted', false) // Only consider drafts that are not submitted
            ->whereIn('employee_id', array_values($roleEmployeeIds))
            ->get();

        // Generate an array of role => true/false for draft presence
        $draftUsers = [];
        foreach ($roleEmployeeIds as $role => $employeeId) {
            $draftUsers[$role] = $drafts->contains('employee_id', $employeeId);
        }

        return $draftUsers;
    }

    public function getDraftWasSubmittedAttribute()
    {
        // Check if a draft exists for the current appraisal and user
        $draft = DB::table('appraisal_drafts')
            ->where('appraisal_id', $this->appraisal_id)
            ->where('employee_id', auth()->user()->employee->employee_id)
            ->first();

        // If a draft exists, return its submission status
        if ($draft) {
            return $draft->is_submitted;
        }

        // If no draft exists, return false
        return true;
    }

    /**
     * Check if appraisal can be withdrawn (hasn't been approved/rejected yet)
     */
    public function getCanBeWithdrawnAttribute()
    {
        $user = auth()->user();
        if (!$user || !$user->employee || $user->employee->employee_id !== $this->employee_id) {
            return false; // Only appraisee can withdraw their own appraisal
        }

        // Check if the appraisal is submitted
        $draft = DB::table('appraisal_drafts')
            ->where('appraisal_id', $this->appraisal_id)
            ->where('employee_id', $user->employee->employee_id)
            ->where('is_submitted', true)
            ->first();

        if (!$draft) {
            return false; // Not submitted, can't be withdrawn
        }

        // Check if any approver has made a final decision (approved/rejected)
        $status = $this->appraisal_request_status ?? [];

        // If any role has approved or rejected, it can't be withdrawn
        $hasFinalDecision = collect($status)->contains(fn($s) => in_array($s, ['approved', 'rejected']));

        return !$hasFinalDecision;
    }

    /**
     * Check if appraisal can be withdrawn
     */
    protected function canWithdrawAppraisal(Appraisal $appraisal, User $user): bool
    {
        // Appraisee can only withdraw their own appraisals
        $isAppraisee = $user->employee && $user->employee->employee_id === $appraisal->employee_id;

        if (!$isAppraisee) {
            return false;
        }

        // Can withdraw if appraisal is in draft or hasn't been approved by anyone yet
        $status = $appraisal->appraisal_request_status ?? [];

        // Allow withdrawal if draft or if no approvals have been given yet
        $hasApprovals = is_array($status) && collect($status)->contains(fn($s) => $s === 'approved');

        return $appraisal->is_draft || (!$hasApprovals && empty($status));
    }

    /**
     * Withdraw the appraisal submission
     */
    public function withdraw(User $user): void
    {
        \Log::info('Model withdraw method called', [
            'appraisal_id' => $this->appraisal_id,
            'user_id' => $user->id,
            'can_be_withdrawn' => $this->can_be_withdrawn,
            'current_stage' => $this->current_stage,
            'status' => $this->appraisal_request_status
        ]);

        if (!$this->can_be_withdrawn) {
            throw new \Exception('This appraisal cannot be withdrawn at this stage. It may have already been approved or rejected.');
        }

        $originalStage = $this->current_stage;

        // Reset to draft status
        $this->appraisal_request_status = [];
        $this->rejection_reason = null;
        $this->is_draft = true;

        // Reset to appropriate initial stage based on appraisee role
        $appraisee = $this->employee;
        $appraiseeUser = $appraisee?->user;

        if ($appraiseeUser && $appraiseeUser->hasRole('Head of Division')) {
            $this->current_stage = 'Head of Division';
        } else {
            $this->current_stage = 'Staff';
        }

        // Mark draft as not submitted in the drafts table
        DB::table('appraisal_drafts')
            ->where('appraisal_id', $this->appraisal_id)
            ->where('employee_id', $user->employee->employee_id)
            ->update(['is_submitted' => false, 'updated_at' => now()]);

        $this->save();

        // Log withdrawal action
        AppraisalHistory::logAction(
            $this->appraisal_id,
            AppraisalHistory::ACTION_WITHDRAWN,
            $originalStage,
            $this->current_stage,
            'Appraisal withdrawn by appraisee',
            ['note' => 'Appraisal withdrawn and reset to draft'],
            $user->employee->employee_id,
            $user->getRoleNames()->first()
        );
    }


    /**
     * Debug withdrawal permissions
     */
    public function getWithdrawalDebugInfoAttribute(): array
    {
        $user = auth()->user();
        $isAppraisee = $user && $user->employee && $user->employee->employee_id === $this->employee_id;

        $draft = DB::table('appraisal_drafts')
            ->where('appraisal_id', $this->appraisal_id)
            ->where('employee_id', $user->employee->employee_id)
            ->first();

        $status = $this->appraisal_request_status ?? [];
        $hasFinalDecision = collect($status)->contains(fn($s) => in_array($s, ['approved', 'rejected']));

        return [
            'is_appraisee' => $isAppraisee,
            'has_submitted_draft' => $draft && $draft->is_submitted,
            'has_final_decision' => $hasFinalDecision,
            'current_stage' => $this->current_stage,
            'appraisal_request_status' => $status,
            'can_be_withdrawn' => $isAppraisee && ($draft && $draft->is_submitted) && !$hasFinalDecision
        ];
    }

    /**
     * Reset current stage back to the appraisee (after rejection/withdrawal).
     */
    public function resetToAppraiseeStage(): void
    {
        // Simple reset - just mark as draft and let submission handle the stage
        $this->is_draft = true;

        // Reset to appropriate initial stage
        $appraisee = $this->employee;
        $appraiseeUser = $appraisee?->user;

        if ($appraiseeUser && $appraiseeUser->hasRole('Head of Division')) {
            $this->current_stage = 'Head of Division';
        } else {
            $this->current_stage = 'Staff';
        }

        $this->save();
    }

    /**
     * Debug current stage and approval status
     */
    public function getStageDebugInfoAttribute(): array
    {
        return [
            'current_stage' => $this->current_stage,
            'approval_status' => $this->approval_status,
            'appraisal_request_status' => $this->appraisal_request_status,
            'is_draft' => $this->is_draft,
            'employee_roles' => $this->employee->user->getRoleNames()->toArray() ?? [],
            'appraiser_roles' => $this->appraiser->user->getRoleNames()->toArray() ?? []
        ];
    }


    /**
     * Mark reminder as sent
     */
    public function markReminderSent(): void
    {
        $this->update([
            'reminder_sent' => true,
            'last_reminder_sent' => now()
        ]);
    }

    /**
     * Check if the current user is the appraisee (employee being appraised)
     */
    public function getIsCurrentUserAppraiseeAttribute(): bool
    {
        $user = auth()->user();
        return $user && $user->employee && $user->employee->employee_id === $this->employee_id;
    }

    /**
     * Check if the current user is the appraiser
     */
    public function getIsCurrentUserAppraiserAttribute(): bool
    {
        $user = auth()->user();
        return $user && $user->employee && $user->employee->employee_id === $this->appraiser_id;
    }

    public function getCanBeResubmittedAttribute(): bool
    {
        $user = auth()->user();
        if (!$user || !$user->employee || $user->employee->employee_id !== $this->employee_id) {
            return false;
        }

        $status = $this->appraisal_request_status ?? [];
        $hasRejection = collect($status)->contains(fn($s) => $s === 'rejected');

        return $hasRejection && $this->is_draft;
    }

    /**
     * Get edit status for the current user
     */
    public function getEditStatusForCurrentUserAttribute(): array
    {
        $user = auth()->user();
        if (!$user || !$user->employee) {
            return [
                'can_edit' => false,
                'reason' => 'No user session or employee record found',
                'user_role' => null
            ];
        }

        $isAppraisee = $user->employee->employee_id === $this->employee_id;
        $isAppraiser = $user->employee->employee_id === $this->appraiser_id;
        $status = $this->appraisal_request_status ?? [];
        $userRole = $user->getRoleNames()->first();

        if ($isAppraisee) {
            // Logic for appraisee edit permissions
            if ($userRole === 'Head of Division') {
                $canEdit = !isset($status['Executive Secretary']) ||
                    $status['Executive Secretary'] === null ||
                    $status['Executive Secretary'] === 'rejected';
                $reason = $canEdit ? null : 'This appraisal has been reviewed by the Executive Secretary';
            } else {
                // Get the appraiser to determine their role
                $appraiserEmployee = Employee::withoutGlobalScopes()->find($this->appraiser_id);
                $appraiserUser = $appraiserEmployee ? User::find($appraiserEmployee->user_id) : null;

                // Check based on appraiser role
                if ($appraiserUser && $appraiserUser->hasRole('HR')) {
                    $canEdit = !isset($status['HR']) ||
                        $status['HR'] === null ||
                        $status['HR'] === 'rejected';
                    $reason = $canEdit ? null : 'This appraisal has been reviewed by HR';
                } else {
                    $canEdit = !isset($status['Head of Division']) ||
                        $status['Head of Division'] === null ||
                        $status['Head of Division'] === 'rejected';
                    $reason = $canEdit ? null : 'This appraisal has been reviewed by your supervisor';
                }
            }

            return [
                'can_edit' => $canEdit,
                'reason' => $reason,
                'user_role' => 'appraisee',
                'stage_info' => "You can edit this appraisal until your " .
                    ($userRole === 'Head of Division' ? 'Executive Secretary' : 'supervisor') .
                    " reviews it."
            ];
        }

        if ($isAppraiser) {
            return [
                'can_edit' => true,
                'reason' => null,
                'user_role' => 'appraiser',
                'stage_info' => 'As the appraiser, you can edit this appraisal.'
            ];
        }

        // For other supervisory roles
        $canEdit = false;
        $reason = 'You do not have permission to edit this appraisal';

        if ($userRole === 'HR') {
            $canEdit = isset($status['Head of Division']) && $status['Head of Division'] === 'approved' &&
                (!isset($status['HR']) || $status['HR'] === null);
            $reason = $canEdit ? null : 'HR can only edit after Head of Division approval and before HR decision';
        } elseif ($userRole === 'Executive Secretary') {
            $canEdit = isset($status['HR']) && $status['HR'] === 'approved' &&
                (!isset($status['Executive Secretary']) || $status['Executive Secretary'] === null);
            $reason = $canEdit ? null : 'Executive Secretary can only edit after HR approval and before ES decision';
        }

        return [
            'can_edit' => $canEdit,
            'reason' => $reason,
            'user_role' => $userRole,
            'stage_info' => null
        ];
    }

    /**
     * Check if current user can access attachments
     */
    public function getCanAccessAttachmentsAttribute(): bool
    {
        $user = auth()->user();
        if (!$user || !$user->employee) {
            return false;
        }

        // Use the service to check attachment access permissions
        $appraisalService = app(\App\Services\AppraisalService::class);
        return $appraisalService->canUserAccessAttachments($this, $user);
    }

}