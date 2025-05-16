<?php

namespace App\Models\Scopes;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrainingScope implements Scope
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
            case 'Executive Secretary':
            case 'Assistant Executive Secretary':
                // No constraints for these roles
                break;

            case 'Head of Division':
                // Only show trainings related to the user's department
                $this->applyHeadOfDivisionScope($builder, $user);
                break;

            case 'Staff':
                // Show trainings based on user's roles, department, or position
                $this->applyStaffScope($builder, $user);
                break;

            default:
                // Default case if user doesn't match any known role
                $builder->whereRaw('1 = 0'); // This condition will always be false
                break;
        }
    }

    /**
     * Apply the scope for the Head of Division.
     */
    private function applyHeadOfDivisionScope(Builder $builder, $user): void
    {
        // Get the department ID and training categories
        $departmentId = Employee::where('user_id', $user->id)->value('department_id');

        if (!$departmentId) {
            return; // No department associated with the user, no trainings to show
        }

        // Get all training categories for the department
        $training_categories = DB::table('trainings')
            ->whereNotNull('training_category')
            ->pluck('training_category', 'training_id');

        // Prepare collections for easier processing
        $users_in_department = Employee::where('department_id', $departmentId)->get();
        $user_ids_in_department = $users_in_department->whereNotNull('user_id')->pluck('user_id');

        $users_trainings = [];

        foreach ($training_categories as $training_id => $training_category_json) {
            $training_category = json_decode($training_category_json);
            $users = explode(',', $training_category->users);
            $departments = explode(',', $training_category->departments);
            $positions = explode(',', $training_category->positions);

            // Check if user has direct access to the training
            if (in_array('0', $users) || in_array($user->id, $users)) {
                $users_trainings[] = $training_id;
            }

            // Check if the department matches
            if (in_array($departmentId, $departments)) {
                $users_trainings[] = $training_id;
            }

            // Check if any user in department matches the users or positions list
            foreach ($users_in_department as $user_in_department) {
                if (in_array($user_in_department->user_id, $users) || in_array($user_in_department->position_id, $positions)) {
                    $users_trainings[] = $training_id;
                }
            }
        }

        // Filter trainings based on the above logic
        $builder->whereIn('trainings.training_id', $users_trainings)->orWhereIn('trainings.user_id', $user_ids_in_department);
    }

    /**
     * Apply the scope for the Staff role.
     */
    private function applyStaffScope(Builder $builder, $user): void
    {
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return;
        }

        $training_categories = DB::table('trainings')
            ->whereNotNull('training_category')
            ->pluck('training_category', 'training_id');

        $users_trainings = [];

        foreach ($training_categories as $training_id => $training_category_json) {
            $training_category = json_decode($training_category_json);

            // If decoding fails, skip this record
            if (!$training_category) {
                continue;
            }

            $users = $training_category->users ?? '';
            $departments = $training_category->departments ?? '';
            $positions = $training_category->positions ?? '';

            // Show to all users if "All"
            if ($users === 'All') {
                $users_trainings[] = $training_id;
                continue;
            }

            $user_ids = array_filter(explode(',', $users));
            $department_ids = array_filter(explode(',', $departments));
            $position_ids = array_filter(explode(',', $positions));

            // Check if user ID, department, or position matches
            if (
                in_array((string)$user->id, $user_ids) ||
                in_array((string)$employee->department_id, $department_ids) ||
                in_array((string)$employee->position_id, $position_ids)
            ) {
                $users_trainings[] = $training_id;
            }
        }

        // Apply scope
        $builder->where(function ($query) use ($users_trainings, $user) {
            $query->whereIn('trainings.training_id', $users_trainings)
                ->orWhere('trainings.user_id', $user->id);
        });
    }
}
