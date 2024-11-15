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

        // Get the user's roles
        $roles = $user->getRoleNames();
        $user_role = $roles->first();

        switch ($user_role) {
            case 'HR':
                // No constraints for HR
                break;

            case 'Head of Division':
                // Get the department_id of the authenticated user
                $departmentId = DB::table('employees')->where('user_id', $user->id)->value('department_id');
                if ($departmentId) {
                    $userIds = DB::table('employees')->where('department_id', $departmentId)->pluck('employee_id');
                    // Only show employees from the user's department
                    $builder->whereIn('appraisals.employee_id', $userIds);
                } else {
                    // If there's no department, don't show anything
                    $builder->whereRaw('1 = 0'); // This condition will always be false
                }
                break;

            case 'Staff':
                $employeeId = DB::table('employees')->where('user_id', $user->id)->value('employee_id');
                $builder->where('appraisals.employee_id', $employeeId);
                break;

            case 'Executive Secretary':
            // Add logic if needed
            case 'Assistant Executive Secretary':
                break;
            default:
                // Handle other roles if needed
                break;
        }
    }
}
