<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'employee_id' => (string) Str::uuid(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'title' => $this->faker->jobTitle,
            'staff_id' => $this->faker->unique()->numerify('STAFF####'),
            'position_id' => Position::inRandomOrder()->value('position_id'), // Get a random position_id
            'nin' => $this->faker->unique()->numerify('NIN#######'),
            'date_of_entry' => $this->faker->date(),
            'contract_expiry_date' => $this->faker->date(),
            'department_id' => Department::inRandomOrder()->value('department_id'), // Get a random department_id
            'nssf_no' => $this->faker->unique()->numerify('NSSF####'),
            'home_district' => $this->faker->city,
            'tin_no' => $this->faker->unique()->numerify('TIN####'),
            'job_description' => $this->faker->paragraph,
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->unique()->phoneNumber,
            'next_of_kin' => $this->faker->name,
            'passport_photo' => null,
            'date_of_birth' => $this->faker->date(),
        ];
    }
}
