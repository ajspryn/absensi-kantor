<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class EmployeeFactory extends Factory
{
     protected $model = Employee::class;

     public function definition(): array
     {
          return [
               'employee_id' => strtoupper($this->faker->bothify('EMP###')),
               'user_id' => User::factory(),
               'department_id' => \App\Models\Department::factory(),
               'position' => $this->faker->jobTitle(),
               'full_name' => $this->faker->name(),
               'phone' => $this->faker->phoneNumber(),
               'email' => $this->faker->unique()->safeEmail(),
               'address' => $this->faker->address(),
               'hire_date' => now()->subYears(1),
               'salary' => $this->faker->numberBetween(3000000, 15000000),
               'is_active' => true,
               'allow_remote_attendance' => false,
          ];
     }
}
