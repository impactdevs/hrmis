<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;

class TrainingFactory extends Factory
{
    protected $model = \App\Models\Training::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // Fetch existing IDs from the respective models
        $userIds = User::pluck('id')->toArray();
        $departmentIds = Department::pluck('department_id')->toArray();
        $positionIds = Position::pluck('position_id')->toArray();

        return [
            'training_id' => Str::uuid(),
            'training_title' => $this->faker->sentence(4),
            'training_description' => $this->faker->paragraph(3),
            'training_location' => $this->faker->city(),
            'training_start_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'training_end_date' => $this->faker->dateTimeBetween('+1 month', '+2 months')->format('Y-m-d'),
            'training_category' => json_encode([
                'users' => implode(',', $this->faker->randomElements($userIds, min(2, count($userIds)))), // Pick random user IDs
                'departments' => implode(',', $this->faker->randomElements($departmentIds, min(2, count($departmentIds)))), // Pick random department IDs
                'positions' => implode(',', $this->faker->randomElements($positionIds, min(2, count($positionIds)))), // Pick random position IDs
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
