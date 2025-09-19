<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppraisalScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Skip scope if no authenticated user (for CLI/testing scenarios)
        if (!$user) {
            return;
        }

        // Get the user's roles
        $roles = $user->getRoleNames();
        $user_role = $roles->first();

        switch ($user_role) {
            case 'HR':
            case 'Executive Secretary':
            case 'Assistant Executive Secretary':
                // No constraints for HR and Executive roles - they can see all appraisals
                break;

            case 'Head of Division':
                $employeeId = DB::table('employees')->where('user_id', $user->id)->value('employee_id');
                if ($employeeId) {
                    // HOD can see:
                    // 1. Appraisals where they are the appraiser (need to approve)
                    // 2. Appraisals from employees in their department (department oversight)
                    // 3. Resubmitted appraisals where they are the appraiser (including those reset to 'Staff' stage)
                    $departmentId = DB::table('employees')->where('user_id', $user->id)->value('department_id');
                    
                    $builder->where(function ($query) use ($employeeId, $departmentId) {
                        // Show appraisals where HOD is the appraiser
                        $query->where('appraisals.appraiser_id', $employeeId);
                        
                        // OR show appraisals from employees in their department
                        if ($departmentId) {
                            $departmentEmployeeIds = DB::table('employees')
                                ->where('department_id', $departmentId)
                                ->pluck('employee_id');
                            $query->orWhereIn('appraisals.employee_id', $departmentEmployeeIds);
                        }
                    })
                    // Also include submitted drafts where HOD is appraiser (resubmissions)
                    ->orWhere(function ($query) use ($employeeId) {
                        $query->where('appraisals.appraiser_id', $employeeId)
                              ->whereExists(function ($subQuery) {
                                  $subQuery->select(DB::raw(1))
                                           ->from('appraisal_drafts')
                                           ->whereColumn('appraisal_drafts.appraisal_id', 'appraisals.appraisal_id')
                                           ->where('appraisal_drafts.is_submitted', true);
                              });
                    });
                } else {
                    // If no employee record, don't show anything
                    $builder->whereRaw('1 = 0');
                }
                break;

            case 'Staff':
                $employeeId = DB::table('employees')->where('user_id', $user->id)->value('employee_id');
                if ($employeeId) {
                    // Staff can only see their own appraisals
                    $builder->where('appraisals.employee_id', $employeeId);
                } else {
                    $builder->whereRaw('1 = 0');
                }
                break;

            default:
                // For unknown roles, don't show any appraisals
                $builder->whereRaw('1 = 0');
                break;
        }
    }
}
