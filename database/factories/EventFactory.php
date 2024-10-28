<?php

namespace Database\Factories;

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
            'category' => [$this->faker->word, $this->faker->word],
            'event_location' => $this->faker->city(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
