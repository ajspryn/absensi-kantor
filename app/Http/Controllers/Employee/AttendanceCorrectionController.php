<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\AttendanceCorrectionSubmitted;

class AttendanceCorrectionController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $corrections = \App\Models\AttendanceCorrection::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
        return view('employee.attendance.corrections.index', compact('corrections'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $correction = \App\Models\AttendanceCorrection::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();
        return view('employee.attendance.corrections.show', compact('correction'));
    }

    public function create()
    {
        return view('employee.attendance.corrections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'corrected_check_in' => 'nullable|date_format:H:i',
            'corrected_check_out' => 'nullable|date_format:H:i',
            'reason' => 'required|string|min:5',
            'attachment' => 'nullable|file|max:2048',
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $validated['date'])
            ->first();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('corrections', 'public');
        }

        $originalCheckIn = $attendance?->check_in;
        $originalCheckOut = $attendance?->check_out;

        $correctedCheckIn = !empty($validated['corrected_check_in'])
            ? Carbon::parse($validated['date'] . ' ' . $validated['corrected_check_in'])
            : null;
        $correctedCheckOut = !empty($validated['corrected_check_out'])
            ? Carbon::parse($validated['date'] . ' ' . $validated['corrected_check_out'])
            : null;

        $correction = AttendanceCorrection::create([
            'user_id' => $user->id,
            'employee_id' => $employee->id,
            'attendance_id' => $attendance?->id,
            'date' => $validated['date'],
            'original_check_in' => $originalCheckIn,
            'original_check_out' => $originalCheckOut,
            'corrected_check_in' => $correctedCheckIn,
            'corrected_check_out' => $correctedCheckOut,
            'reason' => $validated['reason'],
            'attachment_path' => $attachmentPath,
            'status' => AttendanceCorrection::STATUS_PENDING,
        ]);
        // If the submitter has both approve and verify permissions, auto-approve and apply immediately
        $canApprove = $user->role && $user->role->is_active && $user->role->hasPermission('attendance.corrections.approve');
        $canVerify = $user->role && $user->role->is_active && $user->role->hasPermission('attendance.corrections.verify');

        if ($canApprove && $canVerify) {
            // mark as fully approved and set approver fields
            $now = Carbon::now();
            $correction->update([
                'status' => AttendanceCorrection::STATUS_APPROVED,
                'manager_approver_id' => $user->id,
                'manager_approved_at' => $now,
                'hr_approver_id' => $user->id,
                'hr_approved_at' => $now,
            ]);

            // Apply correction to Attendance (same logic as admin approveHr)
            if ($correction->attendance_id) {
                $att = $correction->attendance;
            } else {
                $att = Attendance::firstOrCreate([
                    'employee_id' => $correction->employee_id,
                    'date' => $correction->date,
                ], [
                    'status' => 'present',
                ]);
            }

            if ($correction->corrected_check_in) {
                $att->check_in = $correction->corrected_check_in;
            }
            if ($correction->corrected_check_out) {
                $att->check_out = $correction->corrected_check_out;
            }
            $att->save();

            // Recalculate derived fields
            $att->calculateWorkingHours();
            $att->calculateScheduleStatus();

            // Notify requester (they are the submitter) about approval
            $correction->user?->notify(new \App\Notifications\AttendanceCorrectionDecision($correction));
        } elseif (optional($user->role)->name && strtolower($user->role->name) === 'manager') {
            // If the submitter is a manager, auto-mark as manager-approved and notify HR only
            $correction->update([
                'status' => AttendanceCorrection::STATUS_MANAGER_APPROVED,
                'manager_approver_id' => $user->id,
                'manager_approved_at' => Carbon::now(),
            ]);

            $hrApprovers = User::withPermission('attendance.corrections.verify')->get();
            foreach ($hrApprovers as $approver) {
                $approver->notify(new AttendanceCorrectionSubmitted($correction));
            }
        } else {
            // Notify users with manager-approve permission
            $managerApprovers = User::withPermission('attendance.corrections.approve')->get();
            foreach ($managerApprovers as $approver) {
                $approver->notify(new AttendanceCorrectionSubmitted($correction));
            }

            // Notify users with HR-verify permission
            $hrApprovers = User::withPermission('attendance.corrections.verify')->get();
            foreach ($hrApprovers as $approver) {
                $approver->notify(new AttendanceCorrectionSubmitted($correction));
            }
        }

        return redirect()->route('attendance.corrections.index')
            ->with('success', 'Pengajuan koreksi absensi berhasil dikirim dan menunggu persetujuan.');
    }
}
