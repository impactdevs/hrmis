<?php

namespace App\Models;

use App\Models\Scopes\AppraisalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Support\Facades\DB;

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
        'review_type',
        'review_type_other',
        'appraisal_start_date',
        'appraisal_end_date',
        'job_compatibility',
        'if_no_job_compatibility',
        'unanticipated_constraints',
        'personal_initiatives',
        'training_support_needs',
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
        'relevant_documents'
    ];

    protected $casts = [
        'appraisal_period_accomplishment' => 'array',
        'appraisal_start_date' => 'date',
        'appraisal_end_date' => 'date',
        'personal_attributes_assessment' => 'array',
        'performance_planning' => 'array',
        'appraisal_period_rate' => 'array',
        'appraisal_request_status' => 'array',
        'relevant_documents' => 'array',

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
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id')->withoutGlobalScopes();
    }

    public function appraiser()
    {
        return $this->belongsTo(Employee::class, 'appraiser_id', 'employee_id');
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
        $user = User::find(Employee::find($this->employee_id)->user_id);
        if ($user && $user->hasRole('Head of Division')) {
            $approvalFlow = ['Executive Secretary'];
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
        $draft = \Illuminate\Support\Facades\DB::table('appraisal_drafts')
            ->where('appraisal_id', $this->appraisal_id)->where('employee_id', auth()->user()->employee->employee_id)->where('is_submitted', false)
            ->exists();
        if ($draft) {
            return true;
        }

        return false;
    }



    public function getHasSomeDraftAttribute()
    {

        return \Illuminate\Support\Facades\DB::table('appraisal_drafts')
            ->where('appraisal_id', $this->appraisal_id)->where('is_submitted', false)
            ->exists();
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
        $draft = \Illuminate\Support\Facades\DB::table('appraisal_drafts')
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
}
