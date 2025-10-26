<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Notifications\LeaveRequestDecision;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = LeaveRequest::with(['user', 'employee'])
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

        $leaveRequests = $query->paginate(15)->appends($request->query());

        $stats = [
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'verified' => LeaveRequest::where('status', 'verified')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.leave-requests.index', compact('leaveRequests', 'stats', 'status'));
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load(['user', 'employee', 'approver', 'verifier']);
        return view('admin.leave-requests.show', compact('leaveRequest'));
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        // Authorization enforced via route middleware 'permission:leave.approve'
        $user = Auth::user();

        // Prevent self-approval
        if ($leaveRequest->user_id === $user->id) {
            return redirect()->back()->with('error', 'Tidak dapat menyetujui pengajuan yang diajukan sendiri.');
        }

        if ($leaveRequest->status === LeaveRequest::STATUS_PENDING) {
            $leaveRequest->update([
                'status' => LeaveRequest::STATUS_APPROVED,
                'approver_id' => $user?->id,
                'approved_at' => Carbon::now(),
            ]);
        }

        return redirect()->back()->with('success', 'Pengajuan izin disetujui. Menunggu verifikasi.');
    }

    public function verify(LeaveRequest $leaveRequest)
    {
        // Authorization enforced via route middleware 'permission:leave.verify'
        $user = Auth::user();

        // Prevent self-verification
        if ($leaveRequest->user_id === $user->id) {
            return redirect()->back()->with('error', 'Tidak dapat memverifikasi pengajuan yang diajukan sendiri.');
        }

        if ($leaveRequest->status === LeaveRequest::STATUS_APPROVED) {
            $leaveRequest->update([
                'status' => LeaveRequest::STATUS_VERIFIED,
                'verifier_id' => $user?->id,
                'verified_at' => Carbon::now(),
            ]);

            // Notify the requester
            $leaveRequest->user?->notify(new LeaveRequestDecision($leaveRequest));
        }

        return redirect()->back()->with('success', 'Pengajuan izin diverifikasi dan disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $user = Auth::user();

        $leaveRequest->update([
            'status' => LeaveRequest::STATUS_REJECTED,
            'rejected_by_id' => $user->id,
            'rejected_reason' => $validated['reason'],
            'rejected_at' => Carbon::now(),
        ]);

        // Notify the requester
        $leaveRequest->user?->notify(new LeaveRequestDecision($leaveRequest));

        return redirect()->back()->with('success', 'Pengajuan izin ditolak.');
    }

    // Admin edit form
    public function edit(LeaveRequest $leaveRequest)
    {
        // Only allow admins to access edit form
        $user = Auth::user();
        if (!$user || !$user->role || strtolower($user->role->name) !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $leaveRequest->load(['user', 'employee']);
        return view('admin.leave-requests.edit', compact('leaveRequest'));
    }

    // Update leave request (admin)
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string|max:50',
            'reason' => 'required|string|min:3',
        ]);

        // Only admins can update via admin edit
        $user = Auth::user();
        if (!$user || !$user->role || strtolower($user->role->name) !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $leaveRequest->update([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'type' => $validated['type'],
            'reason' => $validated['reason'],
        ]);

        return redirect()->route('admin.leave-requests.show', $leaveRequest)->with('success', 'Pengajuan izin telah diperbarui.');
    }

    // Delete leave request
    public function destroy(LeaveRequest $leaveRequest)
    {
        // Only admins can delete leave requests via admin interface
        $user = Auth::user();
        if (!$user || !$user->role || strtolower($user->role->name) !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $leaveRequest->delete();
        return redirect()->route('admin.leave-requests.index')->with('success', 'Pengajuan izin berhasil dihapus.');
    }
}
