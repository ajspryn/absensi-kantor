<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Notifications\AttendanceCorrectionDecision;

class AttendanceCorrectionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = AttendanceCorrection::with(['user', 'employee', 'attendance'])
            ->orderBy('created_at', 'desc');

        // If current user is a manager (non-admin), restrict to their department
        $current = Auth::user();
        // If current user has role 'manager' but not 'admin', restrict to their department
        if ($current && $current->role && strtolower($current->role->name) === 'manager' && strtolower($current->role->name) !== 'admin') {
            $managerEmployee = $current->employee;
            if ($managerEmployee && $managerEmployee->department_id) {
                $deptId = $managerEmployee->department_id;
                $query->whereHas('employee', function ($q) use ($deptId) {
                    $q->where('department_id', $deptId);
                });
            } else {
                // If manager has no department assigned, show none
                $query->whereRaw('1 = 0');
            }
        }

        if ($status) {
            $query->where('status', $status);
        }

        $corrections = $query->paginate(15)->appends($request->query());

        $stats = [
            'pending' => AttendanceCorrection::where('status', 'pending')->count(),
            'approved' => AttendanceCorrection::where('status', 'approved')->count(),
            'rejected' => AttendanceCorrection::where('status', 'rejected')->count(),
        ];

        return view('admin.attendance-corrections.index', compact('corrections', 'stats', 'status'));
    }

    public function show(AttendanceCorrection $attendanceCorrection)
    {
        $attendanceCorrection->load(['user', 'employee', 'attendance', 'managerApprover', 'hrApprover']);
        return view('admin.attendance-corrections.show', compact('attendanceCorrection'));
    }

    public function approveManager(AttendanceCorrection $attendanceCorrection)
    {
        // Authorization enforced via route middleware 'permission:attendance.corrections.approve'
        $user = Auth::user();

        // Prevent self-approval: if submitter is this user (manager), disallow
        if ($attendanceCorrection->user_id === $user->id) {
            return redirect()->back()->with('error', 'Tidak dapat menyetujui pengajuan yang diajukan sendiri.');
        }

        // Only allow department manager (as recorded on the employee's department) or admin to perform manager approval
        $isAdmin = $user->role && strtolower($user->role->name) === 'admin';
        $deptManagerId = optional($attendanceCorrection->employee->department)->manager_id;
        if (!$isAdmin && $deptManagerId !== $user->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang manager untuk menyetujui pengajuan ini.');
        }

        if ($attendanceCorrection->status === AttendanceCorrection::STATUS_PENDING) {
            $attendanceCorrection->update([
                'status' => AttendanceCorrection::STATUS_MANAGER_APPROVED,
                'manager_approver_id' => $user?->id,
                'manager_approved_at' => Carbon::now(),
            ]);
        }

        return redirect()->back()->with('success', 'Koreksi disetujui oleh Manager. Menunggu persetujuan HR.');
    }

    public function approveHr(AttendanceCorrection $attendanceCorrection)
    {
        // Authorization enforced via route middleware 'permission:attendance.corrections.approve'
        $user = Auth::user();

        // Prevent self-approval: if submitter is this user, disallow HR approval
        if ($attendanceCorrection->user_id === $user->id) {
            return redirect()->back()->with('error', 'Tidak dapat memverifikasi pengajuan yang diajukan sendiri.');
        }

        if (in_array($attendanceCorrection->status, [AttendanceCorrection::STATUS_PENDING, AttendanceCorrection::STATUS_MANAGER_APPROVED])) {
            $attendanceCorrection->update([
                'status' => AttendanceCorrection::STATUS_APPROVED,
                'hr_approver_id' => $user?->id,
                'hr_approved_at' => Carbon::now(),
            ]);

            // Apply correction to Attendance record
            if ($attendanceCorrection->attendance_id) {
                $att = $attendanceCorrection->attendance;
            } else {
                $att = Attendance::firstOrCreate([
                    'employee_id' => $attendanceCorrection->employee_id,
                    'date' => $attendanceCorrection->date,
                ], [
                    'status' => 'present',
                ]);
            }

            if ($attendanceCorrection->corrected_check_in) {
                $att->check_in = $attendanceCorrection->corrected_check_in;
            }
            if ($attendanceCorrection->corrected_check_out) {
                $att->check_out = $attendanceCorrection->corrected_check_out;
            }
            $att->save();

            // Recalculate derived fields
            $att->calculateWorkingHours();
            $att->calculateScheduleStatus();

            // Notify requester about approval
            $attendanceCorrection->user?->notify(new AttendanceCorrectionDecision($attendanceCorrection));
        }

        return redirect()->back()->with('success', 'Koreksi disetujui oleh HR dan sudah diterapkan.');
    }

    public function reject(Request $request, AttendanceCorrection $attendanceCorrection)
    {
        $request->validate([
            'reason' => 'required|string|min:5'
        ]);

        $user = Auth::user();
        // Prevent self-rejection: submitter should not reject their own request
        if ($attendanceCorrection->user_id === $user->id) {
            return redirect()->back()->with('error', 'Tidak dapat menolak pengajuan yang diajukan sendiri.');
        }

        // Authorization enforced via route middleware 'permission:attendance.corrections.approve'

        $attendanceCorrection->update([
            'status' => AttendanceCorrection::STATUS_REJECTED,
            'rejected_by_id' => $user->id,
            'rejected_reason' => $request->reason,
            'rejected_at' => Carbon::now(),
        ]);

        // Notify requester about rejection
        $attendanceCorrection->user?->notify(new AttendanceCorrectionDecision($attendanceCorrection));

        return redirect()->back()->with('success', 'Pengajuan koreksi telah ditolak.');
    }

    // Admin edit form
    public function edit(AttendanceCorrection $attendanceCorrection)
    {
        // Only allow admins to access edit form
        $user = Auth::user();
        if (!$user || !$user->role || strtolower($user->role->name) !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $attendanceCorrection->load(['user', 'employee', 'attendance']);
        return view('admin.attendance-corrections.edit', compact('attendanceCorrection'));
    }

    // Update correction (admin)
    public function update(Request $request, AttendanceCorrection $attendanceCorrection)
    {
        $validated = $request->validate([
            'corrected_check_in' => 'nullable|date_format:H:i',
            'corrected_check_out' => 'nullable|date_format:H:i',
            'reason' => 'required|string|min:3',
        ]);

        // Only admins can update via admin edit
        $user = Auth::user();
        if (!$user || !$user->role || strtolower($user->role->name) !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $attendanceCorrection->update([
            'corrected_check_in' => $validated['corrected_check_in'] ?? null,
            'corrected_check_out' => $validated['corrected_check_out'] ?? null,
            'reason' => $validated['reason'],
        ]);

        return redirect()->route('admin.attendance-corrections.show', $attendanceCorrection)->with('success', 'Koreksi absensi telah diperbarui.');
    }

    // Delete correction
    public function destroy(AttendanceCorrection $attendanceCorrection)
    {
        // Only admins can delete corrections
        $user = Auth::user();
        if (!$user || !$user->role || strtolower($user->role->name) !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $attendanceCorrection->delete();
        return redirect()->route('admin.attendance-corrections.index')->with('success', 'Pengajuan koreksi berhasil dihapus.');
    }
}
