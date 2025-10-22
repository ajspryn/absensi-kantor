<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;

class AttendanceFactory extends Factory
{
     protected $model = Attendance::class;

     public function definition(): array
     {
          $date = now();
          return [
               'employee_id' => Employee::factory(),
               'date' => $date->toDateString(),
               'check_in' => $date->copy()->setTime(8, 0)->format('H:i:s'),
               'check_out' => $date->copy()->setTime(17, 0)->format('H:i:s'),
               'status' => 'present',
               'working_hours' => 540, // 9 hours in minutes
          ];
     }
}
