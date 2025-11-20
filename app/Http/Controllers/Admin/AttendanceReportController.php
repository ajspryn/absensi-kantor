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
use Illuminate\Support\Facades\DB;

class AttendanceReportController extends Controller
{
    // Show attendance edit modal (returns JSON for modal)
    public function edit($id)
    {
        $attendance = \App\Models\Attendance::findOrFail($id);

        // Format data for frontend form inputs
        $data = [
            'id' => $attendance->id,
            'date' => $attendance->date->format('Y-m-d'),
            'check_in' => $attendance->check_in ? $attendance->check_in->format('H:i') : null,
            'check_out' => $attendance->check_out ? $attendance->check_out->format('H:i') : null,
        ];

        return response()->json($data);
    }

    // Update attendance from sidebar form
    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i'
        ]);

        try {
            $attendance = \App\Models\Attendance::findOrFail($request->attendance_id);

            // Check for duplicate attendance on the new date (excluding current record)
            $existingAttendance = \App\Models\Attendance::where('employee_id', $attendance->employee_id)
                ->where('date', $request->date)
                ->where('id', '!=', $request->attendance_id)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan sudah memiliki absensi pada tanggal ' . $request->date
                ], 422);
            }

            // Update date first
            $attendance->date = $request->date;

            // Update fields
            if ($request->check_in) {
                $attendance->check_in = \Carbon\Carbon::parse($request->date)->setTimeFromTimeString($request->check_in);
            } else {
                $attendance->check_in = null;
            }

            if ($request->check_out) {
                $attendance->check_out = \Carbon\Carbon::parse($request->date)->setTimeFromTimeString($request->check_out);
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

            // Save the attendance record first
            $attendance->save();

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

        // Normalize dates: ensure valid Y-m-d and startDate <= endDate
        try {
            $sd = Carbon::parse($startDate)->format('Y-m-d');
            $ed = Carbon::parse($endDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $sd = now()->startOfMonth()->format('Y-m-d');
            $ed = now()->endOfMonth()->format('Y-m-d');
        }
        if ($sd > $ed) {
            // swap
            [$sd, $ed] = [$ed, $sd];
        }
        $startDate = $sd;
        $endDate = $ed;
        $employeeId = $request->get('employee_id');
        $departmentId = $request->get('department_id');
        $status = $request->get('status'); // present, late, absent

        // Build query
        $query = Attendance::with(['employee.user', 'employee.department', 'employee.workSchedule'])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);

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
            ->paginate(50)->withQueryString();

        // Get filter options
        $employeesQuery = Employee::with('user')->orderBy('employee_id');
        if ($departmentId) {
            $employeesQuery->where('department_id', $departmentId);
        }
        $employees = $employeesQuery->get();
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

        // Normalize dates
        try {
            $sd = Carbon::parse($startDate)->format('Y-m-d');
            $ed = Carbon::parse($endDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $sd = now()->startOfMonth()->format('Y-m-d');
            $ed = now()->endOfMonth()->format('Y-m-d');
        }
        if ($sd > $ed) {
            [$sd, $ed] = [$ed, $sd];
        }
        $startDate = $sd;
        $endDate = $ed;
        $departmentId = $request->get('department_id');

        // Get employees with their attendance summary
        $employeesQuery = Employee::with(['user', 'department', 'workSchedule']);

        if ($departmentId) {
            $employeesQuery->where('department_id', $departmentId);
        }

        $employees = $employeesQuery->get();

        $employeeSummaries = [];

        foreach ($employees as $employee) {
            // Build list of scheduled dates for this employee according to their work schedule
            $dates = [];
            $period = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            while ($period->lte($end)) {
                $isWorkDay = false;
                if ($employee->workSchedule) {
                    // WorkSchedule::isWorkDay expects a date param
                    $isWorkDay = $employee->workSchedule->isWorkDay($period->format('Y-m-d'));
                } else {
                    // Default: exclude weekends (Sunday=0, Saturday=6)
                    $isWorkDay = !in_array($period->dayOfWeek, [0, 6]);
                }

                if ($isWorkDay) {
                    $dates[] = $period->format('Y-m-d');
                }

                $period->addDay();
            }

            $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $workDays = count($dates);

            if ($workDays > 0) {
                // Compare only date part because DB may store datetime
                $presentDays = Attendance::where('employee_id', $employee->id)
                    ->whereIn(DB::raw('date(date)'), $dates)
                    ->whereNotNull('check_in')
                    ->count();

                $startTime = $employee->workSchedule->start_time ?? '08:00:00';
                $lateDays = Attendance::where('employee_id', $employee->id)
                    ->whereIn(DB::raw('date(date)'), $dates)
                    ->whereNotNull('check_in')
                    ->whereRaw('TIME(check_in) > ?', [$startTime])
                    ->count();
            } else {
                $presentDays = 0;
                $lateDays = 0;
            }

            $absentDays = max(0, $workDays - $presentDays);

            $attendanceRate = $workDays > 0 ? round(($presentDays / $workDays) * 100, 1) : 0;

            // Get last check-in and check-out attendances for this employee within the period (if any)
            $lastIn = Attendance::where('employee_id', $employee->id)
                ->whereIn(DB::raw('date(date)'), $dates)
                ->whereNotNull('check_in')
                ->orderBy('date', 'desc')
                ->first();

            $lastOut = Attendance::where('employee_id', $employee->id)
                ->whereIn(DB::raw('date(date)'), $dates)
                ->whereNotNull('check_out')
                ->orderBy('date', 'desc')
                ->first();

            $employeeSummaries[] = [
                'employee' => $employee,
                'total_days' => $totalDays,
                'work_days' => $workDays,
                'present_days' => $presentDays,
                'late_days' => $lateDays,
                'absent_days' => $absentDays,
                'attendance_rate' => $attendanceRate,
                'last_check_in' => $lastIn,
                'last_check_out' => $lastOut
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

            // Normalize dates
            try {
                $sd = Carbon::parse($startDate)->format('Y-m-d');
                $ed = Carbon::parse($endDate)->format('Y-m-d');
            } catch (\Exception $e) {
                $sd = now()->startOfMonth()->format('Y-m-d');
                $ed = now()->endOfMonth()->format('Y-m-d');
            }
            if ($sd > $ed) {
                [$sd, $ed] = [$ed, $sd];
            }
            $startDate = $sd;
            $endDate = $ed;
            $employeeId = $request->get('employee_id');
            $departmentId = $request->get('department_id');
            $status = $request->get('status');

            $query = Attendance::with(['employee.user', 'employee.department', 'employee.workSchedule'])
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate);

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
            // However, if user requested a specific status filter, apply same logic as index
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

        // Normalize dates
        try {
            $sd = Carbon::parse($startDate)->format('Y-m-d');
            $ed = Carbon::parse($endDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $sd = now()->startOfMonth()->format('Y-m-d');
            $ed = now()->endOfMonth()->format('Y-m-d');
        }
        if ($sd > $ed) {
            [$sd, $ed] = [$ed, $sd];
        }
        $startDate = $sd;
        $endDate = $ed;
        $departmentId = $request->get('department_id');

        // Get employees with their attendance summary
        $employeesQuery = Employee::with(['user', 'department', 'workSchedule']);

        if ($departmentId) {
            $employeesQuery->where('department_id', $departmentId);
        }

        $employees = $employeesQuery->get();

        $employeeSummaries = [];

        foreach ($employees as $employee) {
            // Build scheduled dates for this employee according to their work schedule
            $dates = [];
            $period = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            while ($period->lte($end)) {
                $isWorkDay = false;
                if ($employee->workSchedule) {
                    $isWorkDay = $employee->workSchedule->isWorkDay($period->format('Y-m-d'));
                } else {
                    $isWorkDay = !in_array($period->dayOfWeek, [0, 6]);
                }

                if ($isWorkDay) {
                    $dates[] = $period->format('Y-m-d');
                }

                $period->addDay();
            }

            $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $workDays = count($dates);

            if ($workDays > 0) {
                $presentDays = Attendance::where('employee_id', $employee->id)
                    ->whereIn(DB::raw('date(date)'), $dates)
                    ->whereNotNull('check_in')
                    ->count();

                $startTime = $employee->workSchedule->start_time ?? '08:00:00';
                $lateDays = Attendance::where('employee_id', $employee->id)
                    ->whereIn(DB::raw('date(date)'), $dates)
                    ->whereNotNull('check_in')
                    ->whereRaw('TIME(check_in) > ?', [$startTime])
                    ->count();
            } else {
                $presentDays = 0;
                $lateDays = 0;
            }
            $absentDays = max(0, $workDays - $presentDays);

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
        // Build employees list according to filters
        $employeesQuery = Employee::query();
        if ($employeeId) {
            $employeesQuery->where('id', $employeeId);
        }
        if ($departmentId) {
            $employeesQuery->where('department_id', $departmentId);
        }

        $employees = $employeesQuery->get();

        // Total expected work days = number of employees * work days in period
        $workDays = $this->getWorkDays($startDate, $endDate);
        $totalExpected = count($employees) * $workDays;

        $present = 0;
        $late = 0;

        foreach ($employees as $employee) {
            // Count distinct attendance days with check_in for this employee
            $presentCount = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->whereNotNull('check_in')
                ->count();

            $present += $presentCount;

            // Count late occurrences for this employee (compare check_in time to employee start_time)
            $startTime = $employee->workSchedule->start_time ?? '08:00:00';
            $lateCount = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->whereNotNull('check_in')
                ->whereRaw('TIME(check_in) > ?', [$startTime])
                ->count();

            $late += $lateCount;
        }

        $absent = max(0, $totalExpected - $present);

        return [
            'total' => $totalExpected,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'present_rate' => $totalExpected > 0 ? round(($present / $totalExpected) * 100, 1) : 0,
            'absent_rate' => $totalExpected > 0 ? round(($absent / $totalExpected) * 100, 1) : 0,
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
