<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Employee;

class DailyActivityTest extends TestCase
{
     use RefreshDatabase;

     public function test_employee_can_create_daily_activity()
     {
          $user = User::factory()->create();

          // create a role that has daily activity permissions and assign to user
          $roleId = \Illuminate\Support\Facades\DB::table('roles')->insertGetId([
               'name' => 'EmployeeRole',
               'permissions' => json_encode(['daily_activities.create', 'daily_activities.view_own']),
               'is_active' => 1,
               'is_default' => 0,
               'priority' => 10,
               'created_at' => now(),
               'updated_at' => now(),
          ]);

          \Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->update(['role_id' => $roleId]);
          // ensure department and position exist for middleware
          \Illuminate\Support\Facades\DB::table('departments')->insert(['id' => 1, 'name' => 'Dept 1', 'created_at' => now(), 'updated_at' => now()]);
          \Illuminate\Support\Facades\DB::table('positions')->insert(['id' => 1, 'name' => 'Pos 1', 'created_at' => now(), 'updated_at' => now()]);

          $employee = Employee::factory()->create([
               'user_id' => $user->id,
               'employee_id' => 'EMP-001',
               'full_name' => 'Test Employee',
               'department_id' => 1,
               'position_id' => 1,
          ]);

          $this->actingAs($user);

          $response = $this->post(route('employee.daily-activities.store'), [
               'date' => now()->format('Y-m-d'),
               'title' => 'Test Activity',
               'description' => 'Desc',
               'tasks' => [['title' => 'Task 1', 'notes' => 'Note']],
          ]);

          $response->assertRedirect(route('employee.daily-activities.index'));
          $this->assertDatabaseHas('daily_activities', ['title' => 'Test Activity']);
     }
}
