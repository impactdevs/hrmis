<?php

namespace App\Models;

use App\Models\Scopes\AppraisalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

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
        'recommendations',
        'panel_comment',
        'panel_recommendation',
        'overall_assessment',
        'executive_secretary_comments',
    ];

    protected $casts = [
        'appraisal_period_accomplishment' => 'array',
        'appraisal_start_date' => 'date',
        'appraisal_end_date' => 'date',
        'personal_attributes_assessment' => 'array',
        'performance_planning' => 'array',
        'appraisal_period_rate' => 'array',
        'appraisal_request_status' => 'array',

    ];

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
        return $this->belongsTo(Employee::class, 'employee_id')->withoutGlobalScopes();
    }

    public function appraiser()
    {
        return $this->belongsTo(Employee::class, 'appraiser_id', 'employee_id');
    }

    public function getIsAppraiseeAttribute()
    {
        if(auth()->user()->employee->employee_id == $this->employee_id){
            return true;
        }

        return false;
    }

    public function getIsAppraisorAttribute()
    {
        if(auth()->user()->employee->employee_id == $this->appraiser_id){
            return true;
        }

        return false;
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

}
