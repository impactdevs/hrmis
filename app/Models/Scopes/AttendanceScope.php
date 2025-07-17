<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;
use Auth;

class AttendanceScope implements Scope
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
                break;

            case 'Head of Division':
                // Get the department_id of the authenticated user
                $departmentId = DB::table('employees')->where('user_id', $user->id)->value('department_id');

                if ($departmentId) {
                    // Get staff IDs of employees in the specified department
                    $staffIds = DB::table('employees')
                        ->where('department_id', $departmentId)
                        ->pluck('staff_id'); // Assuming staff_id is stored in employees table

                    // Filter attendances by staff_id
                    $builder->whereIn('attendances.staff_id', $staffIds);
                }

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
