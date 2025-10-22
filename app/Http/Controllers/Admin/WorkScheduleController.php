<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkScheduleController extends Controller
{
    public function index()
    {
        // Only show template schedules (not assigned to users) - these are the main schedules
        $schedules = WorkSchedule::with('user')
            ->whereNull('user_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalSchedules = WorkSchedule::whereNull('user_id')->count(); // Only count template schedules
        $activeSchedules = WorkSchedule::whereNull('user_id')->where('is_active', true)->count();
        $employeesWithSchedule = WorkSchedule::whereNotNull('user_id')->distinct('user_id')->count('user_id');
        $flexibleSchedules = WorkSchedule::whereNull('user_id')->where('is_flexible', true)->count();

        return view('admin.work-schedules.index', compact(
            'schedules',
            'totalSchedules',
            'activeSchedules',
            'employeesWithSchedule',
            'flexibleSchedules'
        ));
    }

    public function create()
    {
        $employees = User::where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.work-schedules.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i|after:break_start_time',
            'work_days' => 'required|array|min:1',
            'work_days.*' => 'integer|between:0,6',
            'is_flexible' => 'boolean',
            'location_required' => 'boolean',
            'is_active' => 'boolean',
            'effective_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:effective_date',
            'overtime_threshold' => 'nullable|numeric|min:0',
            'late_tolerance' => 'nullable|integer|min:0|max:60'
        ]);

        // Calculate total hours
        $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
        $endTime = Carbon::createFromFormat('H:i', $validated['end_time']);
        $totalMinutes = $endTime->diffInMinutes($startTime);

        // Subtract break time if provided
        if (isset($validated['break_start_time']) && isset($validated['break_end_time'])) {
            $breakStart = Carbon::createFromFormat('H:i', $validated['break_start_time']);
            $breakEnd = Carbon::createFromFormat('H:i', $validated['break_end_time']);
            $breakMinutes = $breakEnd->diffInMinutes($breakStart);
            $totalMinutes -= $breakMinutes;
        }

        $validated['total_hours'] = $totalMinutes / 60;

        WorkSchedule::create($validated);

        return redirect()->route('admin.work-schedules.index')
            ->with('success', 'Jadwal kerja berhasil dibuat!');
    }

    public function show(WorkSchedule $workSchedule)
    {
        $workSchedule->load('user', 'attendances');

        return view('admin.work-schedules.show', compact('workSchedule'));
    }

    public function edit(WorkSchedule $workSchedule)
    {
        $employees = User::where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.work-schedules.edit', compact('workSchedule', 'employees'));
    }

    public function update(Request $request, WorkSchedule $workSchedule)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i|after:break_start_time',
            'work_days' => 'required|array|min:1',
            'work_days.*' => 'integer|between:0,6',
            'is_flexible' => 'boolean',
            'location_required' => 'boolean',
            'is_active' => 'boolean',
            'effective_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:effective_date',
            'overtime_threshold' => 'nullable|numeric|min:0',
            'late_tolerance' => 'nullable|integer|min:0|max:60'
        ]);

        // Calculate total hours
        $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
        $endTime = Carbon::createFromFormat('H:i', $validated['end_time']);
        $totalMinutes = $endTime->diffInMinutes($startTime);

        // Subtract break time if provided
        if (isset($validated['break_start_time']) && isset($validated['break_end_time'])) {
            $breakStart = Carbon::createFromFormat('H:i', $validated['break_start_time']);
            $breakEnd = Carbon::createFromFormat('H:i', $validated['break_end_time']);
            $breakMinutes = $breakEnd->diffInMinutes($breakStart);
            $totalMinutes -= $breakMinutes;
        }

        $validated['total_hours'] = $totalMinutes / 60;

        $workSchedule->update($validated);

        return redirect()->route('admin.work-schedules.index')
            ->with('success', 'Jadwal kerja berhasil diperbarui!');
    }

    public function destroy(WorkSchedule $workSchedule)
    {
        $workSchedule->delete();

        return redirect()->route('admin.work-schedules.index')
            ->with('success', 'Jadwal kerja berhasil dihapus!');
    }

    public function toggleStatus(WorkSchedule $workSchedule)
    {
        $workSchedule->update([
            'is_active' => !$workSchedule->is_active
        ]);

        $status = $workSchedule->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Jadwal kerja berhasil {$status}!");
    }

    public function assign()
    {
        $employees = User::whereHas('role', function ($query) {
            $query->whereJsonContains('permissions', 'employee.dashboard');
        })
            ->where('is_active', true)
            ->with(['workSchedules', 'employee'])
            ->orderBy('name')
            ->get();

        // Only get template schedules (without user_id) to avoid duplicates
        $schedules = WorkSchedule::where('is_active', true)
            ->whereNull('user_id')  // Only template schedules
            ->orderBy('name')
            ->get();

        return view('admin.work-schedules.assign', compact('employees', 'schedules'));
    }

    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.user_id' => 'required|exists:users,id',
            'assignments.*.schedule_id' => 'required|exists:work_schedules,id',
            'assignments.*.effective_date' => 'nullable|date',
        ]);

        $assignedCount = 0;
        $updatedCount = 0;

        DB::transaction(function () use ($validated, &$assignedCount, &$updatedCount) {
            foreach ($validated['assignments'] as $assignment) {
                $userId = $assignment['user_id'];
                $scheduleId = $assignment['schedule_id'];
                $effectiveDate = $assignment['effective_date'] ?? Carbon::now();

                // Check if user already has an active schedule
                $existingSchedule = WorkSchedule::where('user_id', $userId)
                    ->where('is_active', true)
                    ->first();

                if ($existingSchedule) {
                    // Deactivate existing schedule
                    $existingSchedule->update(['is_active' => false]);
                    $updatedCount++;
                }

                // Create new schedule assignment
                $templateSchedule = WorkSchedule::find($scheduleId);
                $newSchedule = $templateSchedule->replicate();
                $newSchedule->user_id = $userId;
                $newSchedule->effective_date = $effectiveDate;
                $newSchedule->is_active = true;
                $newSchedule->save();

                // Ensure total_hours is present; recalc if missing
                if (empty($newSchedule->total_hours)) {
                    $newSchedule->calculateTotalHours();
                }

                // Update employee's work_schedule_id
                $employee = \App\Models\Employee::where('user_id', $userId)->first();
                if ($employee) {
                    $employee->work_schedule_id = $newSchedule->id;
                    $employee->save();
                }

                $assignedCount++;
            }
        });

        $message = "Berhasil menugaskan jadwal ke {$assignedCount} karyawan";
        if ($updatedCount > 0) {
            $message .= " dan memperbarui {$updatedCount} jadwal yang sudah ada";
        }

        return redirect()->route('admin.work-schedules.index')
            ->with('success', $message . '!');
    }
}
