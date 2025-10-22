<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Department;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class AttendanceReportController extends Controller
{
    // Show attendance edit modal (returns JSON for modal)
    public function edit($id)
    {
        $attendance = \App\Models\Attendance::findOrFail($id);
        return response()->json($attendance);
    }

    // Update attendance from sidebar form
    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i'
        ]);

        try {
            $attendance = \App\Models\Attendance::findOrFail($request->attendance_id);

            // Update fields
            if ($request->check_in) {
                $attendance->check_in = \Carbon\Carbon::parse($attendance->date)->setTimeFromTimeString($request->check_in);
            } else {
                $attendance->check_in = null;
            }

            if ($request->check_out) {
                $attendance->check_out = \Carbon\Carbon::parse($attendance->date)->setTimeFromTimeString($request->check_out);
            } else {
                $attendance->check_out = null;
            }

            // Otomatisasi status dan jam kerja sesuai field dan enum database
            $employee = $attendance->employee;
            $workSchedule = $employee ? $employee->workSchedule : null;
            $startTime = $workSchedule ? $workSchedule->start_time : '08:00:00';

            // Default status
            $attendance->status = 'absent';

            if ($attendance->check_in) {
                $checkInTime = \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s');
                if ($checkInTime > $startTime) {
                    $attendance->status = 'late';
                } else {
                    $attendance->status = 'present';
                }
            }
            // Status hanya diatur otomatis, tidak dari request

            // Hitung jam kerja
            if ($attendance->check_in && $attendance->check_out) {
                $jamKerja = \Carbon\Carbon::parse($attendance->check_in)->diffInMinutes(\Carbon\Carbon::parse($attendance->check_out));
                $attendance->working_hours = $jamKerja; // field sesuai migrasi
            } else {
                $attendance->working_hours = null;
            }


            // Update working_hours dan schedule_status otomatis
            $attendance->calculateWorkingHours();
            $attendance->calculateScheduleStatus();

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil diupdate!',
                'data' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update absensi: ' . $e->getMessage()
            ], 500);
        }
    }
    public function index(Request $request)
    {
        // Get filter parameters
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');
        $departmentId = $request->get('department_id');
        $status = $request->get('status'); // present, late, absent

        // Build query
        $query = Attendance::with(['employee.user', 'employee.department', 'employee.workSchedule'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($status) {
            switch ($status) {
                case 'present':
                    $query->whereNotNull('check_in');
                    break;
                case 'late':
                    $query->whereNotNull('check_in')
                        ->whereRaw('check_in > TIME(COALESCE((SELECT start_time FROM work_schedules WHERE id = (SELECT work_schedule_id FROM employees WHERE id = attendances.employee_id)), "08:00:00"))');
                    break;
                case 'absent':
                    $query->whereNull('check_in');
                    break;
            }
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('check_in', 'asc')
            ->paginate(50);

        // Get filter options
        $employees = Employee::with('user')->orderBy('employee_id')->get();
        $departments = Department::orderBy('name')->get();

        // Calculate statistics
        $stats = $this->calculateStatistics($startDate, $endDate, $employeeId, $departmentId);

        return view('admin.attendance.reports.index', compact(
            'attendances',
            'employees',
            'departments',
            'startDate',
            'endDate',
            'employeeId',
            'departmentId',
            'status',
            'stats'
        ));
    }

    public function summary(Request $request)
    {
        // Get filter parameters
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $departmentId = $request->get('department_id');

        // Get employees with their attendance summary
        $employeesQuery = Employee::with(['user', 'department', 'workSchedule']);

        if ($departmentId) {
            $employeesQuery->where('department_id', $departmentId);
        }

        $employees = $employeesQuery->get();

        $employeeSummaries = [];

        foreach ($employees as $employee) {
            $attendanceQuery = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate]);

            $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $workDays = $this->getWorkDays($startDate, $endDate);

            $presentDays = $attendanceQuery->clone()->whereNotNull('check_in')->count();
            $lateDays = $attendanceQuery->clone()
                ->whereNotNull('check_in')
                ->whereRaw('check_in > TIME(COALESCE((SELECT start_time FROM work_schedules WHERE id = ?), "08:00:00"))', [$employee->work_schedule_id])
                ->count();
            $absentDays = $workDays - $presentDays;

            $attendanceRate = $workDays > 0 ? round(($presentDays / $workDays) * 100, 1) : 0;

            $employeeSummaries[] = [
                'employee' => $employee,
                'total_days' => $totalDays,
                'work_days' => $workDays,
                'present_days' => $presentDays,
                'late_days' => $lateDays,
                'absent_days' => $absentDays,
                'attendance_rate' => $attendanceRate
            ];
        }

        // Sort by attendance rate desc
        usort($employeeSummaries, function ($a, $b) {
            return $b['attendance_rate'] <=> $a['attendance_rate'];
        });

        $departments = Department::orderBy('name')->get();

        return view('admin.attendance.reports.summary', compact(
            'employeeSummaries',
            'departments',
            'startDate',
            'endDate',
            'departmentId'
        ));
    }

    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'detailed');

        if ($type === 'summary') {
            return $this->exportSummaryPdf($request);
        }

        return $this->exportDetailedPdf($request);
    }

    private function exportDetailedPdf(Request $request)
    {
        try {
            // Get same data as index method
            $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
            $employeeId = $request->get('employee_id');
            $departmentId = $request->get('department_id');
            $status = $request->get('status');

            $query = Attendance::with(['employee.user', 'employee.department', 'employee.workSchedule'])
                ->whereBetween('date', [$startDate, $endDate]);

            // Hanya tambahkan filter employee_id jika memang user memilih satu karyawan saja
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }

            // Filter departemen tetap boleh, karena rekap bisa per departemen
            if ($departmentId) {
                $query->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }

            // Untuk PDF, JANGAN filter status, ambil semua data absensi sesuai filter tanggal/karyawan/departemen

            // Untuk PDF, ambil semua data absensi tanpa paginasi
            $attendances = $query->orderBy('date', 'desc')->orderBy('check_in', 'asc')->get();
            // Ambil semua karyawan sesuai filter
            $employeesQuery = Employee::with(['user', 'department', 'workSchedule']);
            if ($employeeId) {
                $employeesQuery->where('id', $employeeId);
            }
            if ($departmentId) {
                $employeesQuery->where('department_id', $departmentId);
            }
            $employees = $employeesQuery->orderBy('employee_id')->get();
            $stats = $this->calculateStatistics($startDate, $endDate, $employeeId, $departmentId);

            $pdf = Pdf::loadView('admin.attendance.reports.pdf.detailed', compact(
                'attendances',
                'employees',
                'startDate',
                'endDate',
                'stats'
            ));

            $filename = 'laporan-absensi-detail-' . $startDate . '-to-' . $endDate . '.pdf';

            $pdf->setPaper('A4', 'landscape');

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response('PDF Error: ' . $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile())
                ->header('Content-Type', 'text/plain');
        }
    }

    private function exportSummaryPdf(Request $request)
    {
        // Get summary data (similar to summary method)
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $departmentId = $request->get('department_id');

        // Get employees with their attendance summary
        $employeesQuery = Employee::with(['user', 'department', 'workSchedule']);

        if ($departmentId) {
            $employeesQuery->where('department_id', $departmentId);
        }

        $employees = $employeesQuery->get();

        $employeeSummaries = [];

        foreach ($employees as $employee) {
            $attendanceQuery = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate]);

            $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $workDays = $this->getWorkDays($startDate, $endDate);

            $presentDays = $attendanceQuery->clone()->whereNotNull('check_in')->count();
            $lateDays = $attendanceQuery->clone()
                ->whereNotNull('check_in')
                ->whereRaw('check_in > TIME(COALESCE((SELECT start_time FROM work_schedules WHERE id = ?), "08:00:00"))', [$employee->work_schedule_id])
                ->count();
            $absentDays = $workDays - $presentDays;

            $attendanceRate = $workDays > 0 ? round(($presentDays / $workDays) * 100, 1) : 0;

            $employeeSummaries[] = [
                'employee' => $employee,
                'total_days' => $totalDays,
                'work_days' => $workDays,
                'present_days' => $presentDays,
                'late_days' => $lateDays,
                'absent_days' => $absentDays,
                'attendance_rate' => $attendanceRate
            ];
        }

        // Sort by attendance rate desc
        usort($employeeSummaries, function ($a, $b) {
            return $b['attendance_rate'] <=> $a['attendance_rate'];
        });

        try {
            $pdf = Pdf::loadView('admin.attendance.reports.pdf.summary', compact(
                'employeeSummaries',
                'startDate',
                'endDate'
            ));

            $filename = 'laporan-absensi-ringkasan-' . $startDate . '-to-' . $endDate . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    private function calculateStatistics($startDate, $endDate, $employeeId = null, $departmentId = null)
    {
        $query = Attendance::whereBetween('date', [$startDate, $endDate]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $total = $query->count();
        $present = $query->clone()->whereNotNull('check_in')->count();
        $absent = $query->clone()->whereNull('check_in')->count();
        $late = $query->clone()
            ->whereNotNull('check_in')
            ->whereRaw('check_in > TIME(COALESCE((SELECT start_time FROM work_schedules WHERE id = (SELECT work_schedule_id FROM employees WHERE id = attendances.employee_id)), "08:00:00"))')
            ->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'present_rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            'absent_rate' => $total > 0 ? round(($absent / $total) * 100, 1) : 0,
            'late_rate' => $present > 0 ? round(($late / $present) * 100, 1) : 0
        ];
    }

    private function getWorkDays($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $workDays = 0;

        while ($start->lte($end)) {
            // Exclude weekends (Saturday = 6, Sunday = 0)
            if (!in_array($start->dayOfWeek, [0, 6])) {
                $workDays++;
            }
            $start->addDay();
        }

        return $workDays;
    }
}
