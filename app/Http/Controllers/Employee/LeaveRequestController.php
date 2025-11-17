<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\LeaveRequestSubmitted;

class LeaveRequestController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $leaveRequests = LeaveRequest::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
        return view('employee.leave.requests.index', compact('leaveRequests'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();
        return view('employee.leave.requests.show', compact('leaveRequest'));
    }

    public function create()
    {
        return view('employee.leave.requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string',
            'reason' => 'required|string|min:5',
            'attachment' => 'nullable|file|max:2048',
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_requests', 'public');
        }

        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'employee_id' => $employee->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'attachment_path' => $attachmentPath,
            'status' => LeaveRequest::STATUS_PENDING,
        ]);

        // Notify approvers and verifiers
        $approvers = User::withPermission('leave.approve')->get();
        $verifiers = User::withPermission('leave.verify')->get();

        foreach ($approvers as $approver) {
            $approver->notify(new LeaveRequestSubmitted($leaveRequest));
        }

        foreach ($verifiers as $verifier) {
            $verifier->notify(new LeaveRequestSubmitted($leaveRequest));
        }

        return redirect()->route('employee.leave.requests.index')
            ->with('success', 'Pengajuan izin berhasil dikirim dan menunggu persetujuan.');
    }
}
