<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class ManagerDailyActivityTest extends TestCase
{
     use RefreshDatabase;

     public function test_manager_can_view_department_activities()
     {
          // create manager user and role with permission
          $managerUser = User::factory()->create();
          $roleId = \Illuminate\Support\Facades\DB::table('roles')->insertGetId([
               'name' => 'ManagerRole',
               'permissions' => json_encode(['daily_activities.view_department']),
               'is_active' => 1,
               'is_default' => 0,
               'priority' => 5,
               'created_at' => now(),
               'updated_at' => now(),
          ]);
          \Illuminate\Support\Facades\DB::table('users')->where('id', $managerUser->id)->update(['role_id' => $roleId]);
          // refresh the user model so the role_id and role relation are available to the controller
          $managerUser->refresh();

          // create department, positions and employees
          \Illuminate\Support\Facades\DB::table('departments')->insert(['id' => 2, 'name' => 'Dept 2', 'created_at' => now(), 'updated_at' => now()]);
          \Illuminate\Support\Facades\DB::table('positions')->insert(['id' => 2, 'name' => 'Pos 2', 'created_at' => now(), 'updated_at' => now()]);

          $managerEmployeeId = \Illuminate\Support\Facades\DB::table('employees')->insertGetId([
               'user_id' => $managerUser->id,
               'employee_id' => 'MGR-001',
               'full_name' => 'Manager',
               'department_id' => 2,
               'position_id' => 2,
               'hire_date' => now()->format('Y-m-d'),
               'is_active' => 1,
               'allow_remote_attendance' => 0,
               'created_at' => now(),
               'updated_at' => now(),
          ]);

          // create an employee in same department and a daily activity
          $empUser = \App\Models\User::factory()->create();
          $employeeId = \Illuminate\Support\Facades\DB::table('employees')->insertGetId([
               'user_id' => $empUser->id,
               'employee_id' => 'EMP-02',
               'full_name' => 'Emp Two',
               'department_id' => 2,
               'position_id' => 2,
               'hire_date' => now()->format('Y-m-d'),
               'is_active' => 1,
               'allow_remote_attendance' => 0,
               'created_at' => now(),
               'updated_at' => now(),
          ]);

          \Illuminate\Support\Facades\DB::table('daily_activities')->insert([
               'employee_id' => $employeeId,
               'date' => now()->format('Y-m-d'),
               'title' => 'Report by emp2',
               'description' => 'Desc',
               'tasks' => json_encode([['title' => 't1', 'notes' => 'n1']]),
               'created_at' => now(),
               'updated_at' => now(),
          ]);

          $this->actingAs($managerUser);

          $response = $this->get(route('admin.daily-activities.index'));
          $response->assertStatus(200);
          $response->assertSee('Report by emp2');
     }
}
