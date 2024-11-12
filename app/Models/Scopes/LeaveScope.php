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
            return; // If no user is authenticated, don't apply any scope
        }

        // Get the user's roles
        $roles = $user->getRoleNames();
        $user_role = $roles->first();

        switch ($user_role) {
            case 'Super Admin':
                // No constraints for Super Admin
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
            // Add logic if needed
            case 'Assistant Executive Secretary':
                // Add logic if needed
                break;

            case 'Staff':
                // Filter leaves by the user's ID
                $builder->where('leaves.user_id', $user->id);
                break;

            default:
                // Handle other roles if needed
                break;
        }
    }
}
