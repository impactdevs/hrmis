<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class OutOfStationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Get the currently authenticated user
        $user = auth()->user();

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

                //get employees, pluck user_id
                $userIds = DB::table('employees')->where('department_id', $departmentId)->pluck('user_id');

                if ($departmentId) {
                    // Only show trainings from the user's department
                    $builder->whereIn('out_station_trainings.user_id', $userIds);
                } else {
                    // If there's no department, don't show anything
                    $builder->whereRaw('1 = 0'); // This condition will always be false
                }
                break;

            case 'Staff':
                $builder->where('out_station_trainings.user_id', $user->id);
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
