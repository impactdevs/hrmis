<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Training::factory()->count(100)->create(); // Create 10 training records
    }
}
