<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class DailyActivityApprovalTest extends TestCase
{
     use RefreshDatabase;

     public function test_manager_can_approve_and_reject_activity()
     {
          // Setup manager with permission
          $manager = User::factory()->create();
          $roleId = \Illuminate\Support\Facades\DB::table('roles')->insertGetId([
               'name' => 'ManagerRole2',
               'permissions' => json_encode(['daily_activities.view_department', 'daily_activities.approve']),
               'is_active' => 1,
               'is_default' => 0,
               'priority' => 5,
               'created_at' => now(),
               'updated_at' => now(),
          ]);
          \Illuminate\Support\Facades\DB::table('users')->where('id', $manager->id)->update(['role_id' => $roleId]);
          // refresh the user model so the role_id and role relation are available to the controller
          $manager->refresh();

          // department and positions
          \Illuminate\Support\Facades\DB::table('departments')->insert(['id' => 3, 'name' => 'Dept 3', 'created_at' => now(), 'updated_at' => now()]);
          \Illuminate\Support\Facades\DB::table('positions')->insert(['id' => 3, 'name' => 'Pos 3', 'created_at' => now(), 'updated_at' => now()]);

          // manager employee
          $managerEmp = \Illuminate\Support\Facades\DB::table('employees')->insertGetId([
               'user_id' => $manager->id,
               'employee_id' => 'MGR-002',
               'full_name' => 'Manager2',
               'department_id' => 3,
               'position_id' => 3,
               'hire_date' => now()->format('Y-m-d'),
               'is_active' => 1,
               'allow_remote_attendance' => 0,
               'created_at' => now(),
               'updated_at' => now(),
          ]);

          // an employee in same dept and activity
          $empUser = \App\Models\User::factory()->create();
          $empId = \Illuminate\Support\Facades\DB::table('employees')->insertGetId([
               'user_id' => $empUser->id,
               'employee_id' => 'EMP-03',
               'full_name' => 'Emp Three',
               'department_id' => 3,
               'position_id' => 3,
               'hire_date' => now()->format('Y-m-d'),
               'is_active' => 1,
               'allow_remote_attendance' => 0,
               'created_at' => now(),
               'updated_at' => now(),
          ]);

          $activityId = \Illuminate\Support\Facades\DB::table('daily_activities')->insertGetId([
               'employee_id' => $empId,
               'date' => now()->format('Y-m-d'),
               'title' => 'To Approve',
               'description' => 'Desc',
               'tasks' => json_encode([['title' => 'task', 'notes' => 'note']]),
               'status' => 'submitted',
               'created_at' => now(),
               'updated_at' => now(),
          ]);

          $this->actingAs($manager);
          // Disable permission middleware for this feature test environment to exercise controller logic
          $this->withoutMiddleware([\App\Http\Middleware\PermissionMiddleware::class]);

          $res1 = $this->patch(route('admin.daily-activities.approve', $activityId));
          $res1->assertRedirect();
          $this->assertDatabaseHas('daily_activities', ['id' => $activityId, 'status' => 'approved']);

          $res2 = $this->patch(route('admin.daily-activities.reject', $activityId));
          $res2->assertRedirect();
          $this->assertDatabaseHas('daily_activities', ['id' => $activityId, 'status' => 'rejected']);
     }
}
