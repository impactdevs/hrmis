<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TrainingFactory extends Factory
{
    protected $model = \App\Models\Training::class; // Update the namespace to match your model

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'training_id' => Str::uuid(),
            'training_title' => $this->faker->sentence(4),
            'training_description' => $this->faker->paragraph(3),
            'training_location' => $this->faker->city(),
            'training_start_date' => $this->faker->date(),
            'training_end_date' => $this->faker->date(),
            'training_category' => [$this->faker->word, $this->faker->word],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
