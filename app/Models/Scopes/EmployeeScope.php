<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Auth;

class EmployeeScope implements Scope
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
                $departmentId = optional($user->employee)->department_id;

                if ($departmentId) {
                    $builder->where('employees.department_id', $departmentId);
                } else {
                    // If there's no department, don't show anything
                    $builder->whereRaw('1 = 0'); // This condition will always be false
                }
                break;

            case 'Executive Secretary':
                // Add logic if needed
                break;

            default:
                // Handle other roles if needed
                break;
        }
    }
}
