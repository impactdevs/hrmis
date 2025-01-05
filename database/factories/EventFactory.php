<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = \App\Models\Event::class; // Update the namespace to match your model

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'event_id' => (string) Str::uuid(),
            'event_start_date' => $this->faker->date(),
            'event_end_date' => $this->faker->date(),
            'event_title' => $this->faker->sentence(4),
            'event_description' => $this->faker->paragraph(3),
            'category' => json_encode([
                'users' => implode(',', $this->faker->randomElements(User::pluck('id')->toArray(), 2)),
                'departments' => implode(',', $this->faker->randomElements(Department::pluck('department_id')->toArray(), 2)),
                'positions' => implode(',', $this->faker->randomElements(Position::pluck('position_id')->toArray(), 2)),
            ]),
            'event_location' => $this->faker->city(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
