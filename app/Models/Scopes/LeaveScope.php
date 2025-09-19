<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;
use Auth;


class LeaveScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Get the currently authenticated user
        $user = Auth::user();

        if (!$user) {
            $builder->where('is_cancelled', false);
            return; // If no user is authenticated, don't apply any scope
        }

        // Get the user's roles
        $roles = $user->getRoleNames();
        $user_role = $roles->first();

        switch ($user_role) {
            case 'HR':
                // HR can see all leave requests (no constraints)
                break;

            case 'Head of Division':
                // Get the department_id of the authenticated user
                $departmentId = DB::table('employees')->where('user_id', $user->id)->value('department_id');
                if ($departmentId) {
                    // Only show leaves from the user's department by using leaves.user_id
                    $users = DB::table('employees')->where('department_id', $departmentId)->pluck('user_id');
                    $builder->whereIn('leaves.user_id', $users);
                } else {
                    // If there's no department, don't show anything
                    $builder->whereRaw('1 = 0'); // This condition will always be false
                }
                break;

            case 'Executive Secretary':
                // Executive Secretary can see all leave requests (no constraints)
                // This is needed for final approval stage
                break;

            case 'Assistant Executive Secretary':
                // Assistant Executive Secretary can see all leave requests (no constraints)
                break;

            case 'Staff':
                // Filter leaves by the user's ID - staff can only see their own leaves
                $builder->where('leaves.user_id', $user->id);
                break;

            default:
                // For unknown roles, show only their own leaves for security
                $builder->where('leaves.user_id', $user->id);
                break;
        }

        if ($user->hasAnyRole(['HR', 'Head of Division', 'Executive Secretary', 'Admin'])) {
        // Show all leaves (cancelled and non-cancelled) to approvers
        return; // No filtering for approvers
    } else {
        // For regular users, show only their own leaves (cancelled or not)
        // and non-cancelled leaves of others
        $builder->where(function($query) use ($user) {
            $query->where('is_cancelled', false)
                  ->orWhere('user_id', $user->id);
        });
    }
    }
}
