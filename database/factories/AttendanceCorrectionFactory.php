<?php

namespace Database\Factories;

use App\Models\AttendanceCorrection;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\User;

class AttendanceCorrectionFactory extends Factory
{
     protected $model = AttendanceCorrection::class;

     public function definition(): array
     {
          $date = now();
          return [
               'user_id' => User::factory(),
               'employee_id' => Employee::factory(),
               'attendance_id' => Attendance::factory(),
               'date' => $date->toDateString(),
               'original_check_in' => null,
               'original_check_out' => null,
               'corrected_check_in' => $date->copy()->setTime(8, 0),
               'corrected_check_out' => $date->copy()->setTime(17, 0),
               'reason' => $this->faker->sentence(),
               'status' => AttendanceCorrection::STATUS_PENDING,
          ];
     }
}
