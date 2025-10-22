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
}
