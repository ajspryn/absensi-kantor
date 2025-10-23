<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;

class AttendanceCorrectionFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_submit_correction()
    {
        Notification::fake();
        $role = Role::factory()->create(['name' => 'employee', 'permissions' => ['attendance.corrections.request']]);
        $user = User::factory()->create(['role_id' => $role->id]);
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $attendance = Attendance::factory()->create(['employee_id' => $employee->id, 'date' => now()->toDateString()]);

        $this->actingAs($user)
            ->post(route('employee.attendance.corrections.store'), [
                'date' => now()->toDateString(),
                'corrected_check_in' => '08:00',
                'corrected_check_out' => '17:00',
                'reason' => 'Lupa absen',
                'attachment' => UploadedFile::fake()->image('lampiran.jpg'),
            ])
            ->assertRedirect(route('attendance.corrections.index'));

        $this->assertDatabaseHas('attendance_corrections', [
            'user_id' => $user->id,
            'employee_id' => $employee->id,
            'reason' => 'Lupa absen',
            'status' => AttendanceCorrection::STATUS_PENDING,
        ]);
    }

    public function test_manager_can_approve_correction()
    {
        Notification::fake();
        $managerRole = Role::factory()->create(['name' => 'manager', 'permissions' => ['attendance.corrections.approve']]);
        $manager = User::factory()->create(['role_id' => $managerRole->id]);
        $department = Department::factory()->create(['manager_id' => $manager->id]);
        $employee = Employee::factory()->create(['department_id' => $department->id]);
        $correction = AttendanceCorrection::factory()->create(['employee_id' => $employee->id, 'status' => AttendanceCorrection::STATUS_PENDING]);

        $this->actingAs($manager)
            ->patch(route('admin.attendance-corrections.approve-manager', $correction))
            ->assertRedirect();

        $this->assertDatabaseHas('attendance_corrections', [
            'id' => $correction->id,
            'status' => AttendanceCorrection::STATUS_MANAGER_APPROVED,
            'manager_approver_id' => $manager->id,
        ]);
    }

    public function test_hr_can_approve_and_update_attendance()
    {
        Notification::fake();
    $hrRole = Role::factory()->create(['name' => 'hr', 'permissions' => ['attendance.corrections.verify']]);
        $hr = User::factory()->create(['role_id' => $hrRole->id]);
        $employee = Employee::factory()->create();
        $attendance = Attendance::factory()->create(['employee_id' => $employee->id, 'date' => now()->toDateString()]);
        $correction = AttendanceCorrection::factory()->create([
            'employee_id' => $employee->id,
            'attendance_id' => $attendance->id,
            'corrected_check_in' => now()->setTime(8, 0),
            'corrected_check_out' => now()->setTime(17, 0),
            'status' => AttendanceCorrection::STATUS_MANAGER_APPROVED,
        ]);

        $this->actingAs($hr)
            ->patch(route('admin.attendance-corrections.approve-hr', $correction))
            ->assertRedirect();

        $this->assertDatabaseHas('attendance_corrections', [
            'id' => $correction->id,
            'status' => AttendanceCorrection::STATUS_APPROVED,
            'hr_approver_id' => $hr->id,
        ]);
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'check_in' => now()->setTime(8, 0),
            'check_out' => now()->setTime(17, 0),
        ]);
    }

    public function test_correction_can_be_rejected()
    {
        Notification::fake();
        $managerRole = Role::factory()->create(['name' => 'manager', 'permissions' => ['attendance.corrections.approve']]);
        $manager = User::factory()->create(['role_id' => $managerRole->id]);
        $employee = Employee::factory()->create();
        $correction = AttendanceCorrection::factory()->create(['employee_id' => $employee->id, 'status' => AttendanceCorrection::STATUS_PENDING]);

        $this->actingAs($manager)
            ->patch(route('admin.attendance-corrections.reject', $correction), [
                'reason' => 'Tidak valid',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attendance_corrections', [
            'id' => $correction->id,
            'status' => AttendanceCorrection::STATUS_REJECTED,
            'rejected_reason' => 'Tidak valid',
        ]);
    }
}
