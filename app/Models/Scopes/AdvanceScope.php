<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdvanceScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
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
                break;

            case 'Head of Division':
                // Get the department_id of the authenticated user
                $isFinanceDept = false;
                if (
                    $user->employee &&
                    $user->employee->department &&
                    $user->employee->department->department_name === 'FINANCE AND ADMINISTRATION (F&A)'
                ) {
                    $isFinanceDept = true;
                }

                if ($isFinanceDept) {
                    // don't constrain in any way

                } else {
                    // If there's no department or not FINANCE DEPARTMENT, don't show anything
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
                $builder->where('salary_advances.employee_id', $user->employee->employee_id);
                break;

            default:
                // Handle other roles if needed
                break;
        }
    }
}
