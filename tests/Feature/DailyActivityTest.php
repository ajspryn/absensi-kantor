<?php

namespace Tests\Feature;

use App\Models\DailyActivity;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DailyActivityTest extends TestCase
{
    use RefreshDatabase;

    private function createEmployeeUserWithPermissions(array $permissions = ['daily_activities.create', 'daily_activities.view_own'], string $roleName = 'EmployeeRole'): array
    {
        $role = Role::factory()->create([
            'name' => $roleName,
            'permissions' => $permissions,
            'is_active' => true,
        ]);

        $user = User::factory()->create(['role_id' => $role->id]);

        $departmentId = DB::table('departments')->insertGetId([
            'name' => 'Dept ' . uniqid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $positionId = DB::table('positions')->insertGetId([
            'name' => 'Pos ' . uniqid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'employee_id' => 'EMP-' . str_pad((string) $user->id, 3, '0', STR_PAD_LEFT),
            'full_name' => 'Test Employee ' . $user->id,
            'department_id' => $departmentId,
            'position_id' => $positionId,
        ]);

        return [$user->fresh(), $employee->fresh()];
    }

    public function test_employee_can_create_daily_activity()
    {
        [$user] = $this->createEmployeeUserWithPermissions();

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

    public function test_daily_activity_index_shows_active_filter_label()
    {
        [$user, $employee] = $this->createEmployeeUserWithPermissions();

        DailyActivity::create([
            'employee_id' => $employee->id,
            'date' => '2026-07-20',
            'title' => 'Filtered Activity',
            'description' => 'Desc',
        ]);

        $this->actingAs($user)
            ->get(route('employee.daily-activities.index', [
                'start_date' => '2026-07-20',
                'end_date' => '2026-07-20',
            ]))
            ->assertOk()
            ->assertSee('Menampilkan: 2026-07-20')
            ->assertDontSee('Menampilkan: Hari ini');
    }

    public function test_employee_can_update_daily_activity()
    {
        [$user, $employee] = $this->createEmployeeUserWithPermissions();

        $activity = DailyActivity::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'title' => 'Original Activity',
            'description' => 'Old description',
            'tasks' => [['title' => 'Task 1', 'notes' => 'Old note']],
        ]);

        $this->actingAs($user)
            ->put(route('employee.daily-activities.update', $activity), [
                'date' => now()->toDateString(),
                'title' => 'Updated Activity',
                'description' => 'New description',
                'tasks' => [['title' => 'Task 1', 'notes' => 'New note']],
            ])
            ->assertRedirect(route('employee.daily-activities.show', $activity));

        $updatedActivity = DailyActivity::findOrFail($activity->id);
        $this->assertSame('Updated Activity', $updatedActivity->title);
        $this->assertSame('New description', $updatedActivity->description);
        $this->assertSame('New note', $updatedActivity->tasks[0]['notes']);
    }

    public function test_employee_can_update_daily_activity_task_completion()
    {
        [$user, $employee] = $this->createEmployeeUserWithPermissions();

        $activity = DailyActivity::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'title' => 'Task Activity',
            'tasks' => [['title' => 'Task 1', 'notes' => 'Note 1']],
        ]);

        $this->actingAs($user)
            ->patchJson(route('employee.daily-activities.tasks.update', [$activity, 0]), [
                'completed' => 1,
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'completed' => 1,
            ]);

        $this->assertEquals(1, DailyActivity::findOrFail($activity->id)->tasks[0]['completed']);
    }

    public function test_employee_can_add_daily_activity_attachments()
    {
        Storage::fake('public');
        [$user, $employee] = $this->createEmployeeUserWithPermissions();

        $activity = DailyActivity::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'title' => 'Attachment Activity',
        ]);

        $this->actingAs($user)
            ->post(route('employee.daily-activities.attachments.store', $activity), [
                'attachments' => [UploadedFile::fake()->image('proof.jpg')],
            ])
            ->assertRedirect();

        $storedActivity = DailyActivity::findOrFail($activity->id);
        $this->assertNotEmpty($storedActivity->attachments);
        $this->assertTrue(Storage::disk('public')->exists($storedActivity->attachments[0]));
    }

    public function test_employee_can_delete_daily_activity_and_attachments()
    {
        Storage::fake('public');
        [$user, $employee] = $this->createEmployeeUserWithPermissions();

        $storedPath = UploadedFile::fake()->image('delete-me.jpg')->store('daily_activity_attachments', 'public');

        $activity = DailyActivity::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'title' => 'Delete Activity',
            'attachments' => [$storedPath],
        ]);

        $this->actingAs($user)
            ->delete(route('employee.daily-activities.destroy', $activity))
            ->assertRedirect(route('employee.daily-activities.index'));

        $this->assertDatabaseMissing('daily_activities', ['id' => $activity->id]);
        $this->assertFalse(Storage::disk('public')->exists($storedPath));
    }
}
