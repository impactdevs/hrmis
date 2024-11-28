<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class LeaveRosterScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Get the currently authenticated user
        $user = Auth::user();

        if (!$user) {
            return; // If no user is authenticated, don't apply any scope
        }

        // Get the user's roles
        $roles = $user->getRoleNames();
        $user_role = $roles->first();

        switch ($user_role) {
            case 'HR':
            // No constraints for HR
            case 'Executive Secretary':
            // Add logic if needed
            case 'Assistant Executive Secretary':
                // Add logic if needed
                break;

            case 'Head of Division':
                // Get the department_id of the authenticated user
                $departmentId = DB::table('employees')->where('user_id', $user->id)->value('department_id');
                $employeeIds = DB::table('employees')->where('department_id', $departmentId)->pluck('employee_id');
                if ($departmentId) {
                    $builder->whereIn('leave_rosters.employee_id', $employeeIds);
                } else {
                    // If there's no department, don't show anything
                    $builder->whereRaw('1 = 0'); // This condition will always be false
                }
                break;
            case 'Staff':
                $departmentId = DB::table('employees')->where('user_id', $user->id)->value('employee_id');
                if ($departmentId) {
                    $builder->where('leave_rosters.employee_id', $departmentId);
                } else {
                    // If there's no department, don't show anything
                    $builder->whereRaw('1 = 0'); // This condition will always be false
                }
                break;
            default:
                // Filter leaves by the user's ID
                $builder->whereRaw('1 = 0'); // This condition will always be false
                break;


        }
    }
}
