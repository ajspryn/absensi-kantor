<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Models\WorkSchedule;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
    /**
     * Show attendance page
     */
    public function index()
    {
        $employee = Auth::user()->employee;
        $todayAttendance = $employee ? $employee->getTodayAttendance() : null;

        return view('employee.attendance.index', compact('employee', 'todayAttendance'));
    }

    /**
     * Show office locations page
     */
    public function locations()
    {
        $officeLocations = OfficeLocation::where('is_active', true)->get();
        return view('employee.attendance.locations', compact('officeLocations'));
    }

    /**
     * Validate location before taking photo
     */
    public function validateLocation(Request $request)
    {
        $employee = $request->user()->employee;
        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found'], 404);
        }

        $todayAttendance = $employee->getTodayAttendance();
        if ($todayAttendance && $todayAttendance->check_in) {
            return response()->json(['error' => 'Anda sudah check in hari ini'], 400);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Check if remote attendance is allowed - prioritize individual employee setting
        $allowRemoteAttendance = $employee->allow_remote_attendance ?? AppSetting::getSetting('allow_remote_attendance', false);

        if ($allowRemoteAttendance) {
            // Remote attendance allowed - find nearest location for reference
            $nearestLocation = OfficeLocation::getNearestLocation($request->latitude, $request->longitude);

            return response()->json([
                'success' => true,
                'message' => 'Absen dimana saja diaktifkan. Silakan ambil foto untuk melanjutkan absen.',
                'location' => $nearestLocation ? [
                    'id' => $nearestLocation->id,
                    'name' => $nearestLocation->name . ' (Remote)',
                    'address' => 'Lokasi Remote'
                ] : [
                    'id' => null,
                    'name' => 'Lokasi Remote',
                    'address' => 'Absen dari lokasi remote'
                ],
                'is_remote' => true
            ]);
        }

        // Validate location against office locations (standard mode)
        $validLocation = OfficeLocation::getValidLocationFor($request->latitude, $request->longitude);
        if (!$validLocation) {
            return response()->json([
                'error' => 'Anda berada di luar area kantor yang diizinkan. Silakan pastikan Anda berada di lokasi kantor yang tepat.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lokasi valid. Silakan ambil foto untuk melanjutkan absen.',
            'location' => [
                'id' => $validLocation->id,
                'name' => $validLocation->name,
                'address' => $validLocation->address
            ],
            'is_remote' => false
        ]);
    }

    /**
     * Validate location for check out
     */
    public function validateLocationCheckOut(Request $request)
    {
        $employee = $request->user()->employee;
        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found'], 404);
        }

        $todayAttendance = $employee->getTodayAttendance();
        if (!$todayAttendance || !$todayAttendance->check_in) {
            return response()->json(['error' => 'Anda belum check in hari ini'], 400);
        }

        if ($todayAttendance && $todayAttendance->check_out) {
            return response()->json(['error' => 'Anda sudah check out hari ini'], 400);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Check if remote attendance is allowed - prioritize individual employee setting
        $allowRemoteAttendance = $employee->allow_remote_attendance ?? AppSetting::getSetting('allow_remote_attendance', false);

        if ($allowRemoteAttendance) {
            // Remote attendance allowed - find nearest location for reference
            $nearestLocation = OfficeLocation::getNearestLocation($request->latitude, $request->longitude);

            return response()->json([
                'success' => true,
                'message' => 'Absen dimana saja diaktifkan. Silakan ambil foto untuk melanjutkan absen keluar.',
                'location' => $nearestLocation ? [
                    'id' => $nearestLocation->id,
                    'name' => $nearestLocation->name . ' (Remote)',
                    'address' => 'Lokasi Remote'
                ] : [
                    'id' => null,
                    'name' => 'Lokasi Remote',
                    'address' => 'Absen dari lokasi remote'
                ],
                'is_remote' => true
            ]);
        }

        // Validate location against office locations (standard mode)
        $validLocation = OfficeLocation::getValidLocationFor($request->latitude, $request->longitude);
        if (!$validLocation) {
            return response()->json([
                'error' => 'Anda berada di luar area kantor yang diizinkan. Silakan pastikan Anda berada di lokasi kantor yang tepat.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lokasi valid. Silakan ambil foto untuk melanjutkan absen keluar.',
            'location' => [
                'id' => $validLocation->id,
                'name' => $validLocation->name,
                'address' => $validLocation->address
            ],
            'is_remote' => false
        ]);
    }

    /**
     * Check in attendance
     */
    public function checkIn(Request $request)
    {
        $employee = $request->user()->employee;
        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found'], 404);
        }

        $todayAttendance = $employee->getTodayAttendance();
        if ($todayAttendance && $todayAttendance->check_in) {
            return response()->json(['error' => 'Anda sudah check in hari ini'], 400);
        }

        // Validasi jadwal kerja aktif (relasi dan status)
        $activeSchedule = $employee->workSchedule;
        if (!$activeSchedule || !$activeSchedule->is_active) {
            return response()->json(['error' => 'Anda belum memiliki jadwal kerja aktif, silakan hubungi admin.'], 400);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:255',
        ]);

        // Re-validate location (double check) - prioritize per-employee setting
        $allowRemoteAttendance = $employee->allow_remote_attendance ?? AppSetting::getSetting('allow_remote_attendance', false);
        $validLocation = null;
        $isRemote = false;

        if ($allowRemoteAttendance) {
            // Remote attendance allowed
            $validLocation = OfficeLocation::getNearestLocation($request->latitude, $request->longitude);
            $isRemote = true;
        } else {
            // Standard location validation
            $validLocation = OfficeLocation::getValidLocationFor($request->latitude, $request->longitude);
            if (!$validLocation) {
                return response()->json([
                    'error' => 'Lokasi tidak valid. Silakan coba lagi dari lokasi kantor yang tepat.'
                ], 400);
            }
        }
        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance_photos', 'public');
        }

        // Create or update attendance
        $attendance = $todayAttendance ?: new Attendance();
        $attendance->employee_id = $employee->id;
        $attendance->date = today();
        $attendance->check_in = now();
        $attendance->latitude_in = $request->latitude;
        $attendance->longitude_in = $request->longitude;
        $attendance->office_location_id = $validLocation ? $validLocation->id : null;
        // persist the human-friendly location name in the column defined by the migration
        $attendance->location_name = $validLocation ? ($isRemote ? $validLocation->name . ' (Remote)' : $validLocation->name) : 'Lokasi Remote';
        $attendance->photo_in = $photoPath;
        $attendance->notes = $request->notes;
        $attendance->status = 'present';
        $attendance->work_schedule_id = $employee->work_schedule_id;
        $attendance->save();

        // Calculate schedule status for check-in
        $attendance->calculateScheduleStatus();

        return response()->json([
            'success' => true,
            'message' => 'Check in berhasil!',
            'attendance' => $attendance,
            'schedule_status' => $attendance->getScheduleStatusText()
        ]);
    }

    /**
     * Store check-out attendance
     */
    public function checkOut(Request $request)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found'], 404);
        }

        $todayAttendance = $employee->getTodayAttendance();

        // Validasi jadwal kerja aktif (relasi dan status)
        $activeSchedule = $employee->workSchedule;
        if (!$activeSchedule || !$activeSchedule->is_active) {
            return response()->json(['error' => 'Anda belum memiliki jadwal kerja aktif, silakan hubungi admin.'], 400);
        }

        if (!$todayAttendance || !$todayAttendance->check_in) {
            return response()->json(['error' => 'Anda belum check in hari ini'], 400);
        }

        if ($todayAttendance->check_out) {
            return response()->json(['error' => 'Anda sudah check out hari ini'], 400);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:255',
        ]);

        // Re-validate location (double check for check out) - prioritize per-employee setting
        $allowRemoteAttendance = $employee->allow_remote_attendance ?? AppSetting::getSetting('allow_remote_attendance', false);
        $validLocation = null;
        $isRemote = false;

        if ($allowRemoteAttendance) {
            // Remote attendance allowed
            $validLocation = OfficeLocation::getNearestLocation($request->latitude, $request->longitude);
            $isRemote = true;
        } else {
            // Standard location validation
            $validLocation = OfficeLocation::getValidLocationFor($request->latitude, $request->longitude);
            if (!$validLocation) {
                return response()->json([
                    'error' => 'Anda berada di luar area kantor yang diizinkan. Silakan pastikan Anda berada di lokasi kantor yang tepat.'
                ], 400);
            }
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance_photos', 'public');
        }

        // Update attendance
        $todayAttendance->check_out = now();
        $todayAttendance->latitude_out = $request->latitude;
        $todayAttendance->longitude_out = $request->longitude;
        $todayAttendance->photo_out = $photoPath;
        $todayAttendance->notes = $request->notes ? ($todayAttendance->notes . ' | ' . $request->notes) : $todayAttendance->notes;

        // Update office location name for check out (preserve human-friendly label)
        if ($isRemote) {
            if ($validLocation) {
                $todayAttendance->location_name = $validLocation->name . ' (Remote)';
            } else {
                $todayAttendance->location_name = 'Lokasi Remote';
            }
        }

        // Calculate working hours
        $todayAttendance->calculateWorkingHours();

        // Calculate schedule status for check-out
        $todayAttendance->calculateScheduleStatus();

        $todayAttendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Check out berhasil!',
            'attendance' => $todayAttendance,
            'schedule_status' => $todayAttendance->getScheduleStatusText()
        ]);
    }

    /**
     * Show attendance history
     */
    public function history(Request $request)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('employee.profile.complete');
        }

        $filterType = $request->get('filter_type', 'month');
        $month = $request->get('month', date('Y-m'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($filterType === 'date_range' && $startDate && $endDate) {
            // Validate dates
            try {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);

                // Ensure start date is not after end date
                if ($start->gt($end)) {
                    $temp = $start;
                    $start = $end;
                    $end = $temp;
                    $startDate = $start->format('Y-m-d');
                    $endDate = $end->format('Y-m-d');
                }

                // Get attendances with missing days
                $allAttendances = $employee->getAttendanceWithMissing($start->format('Y-m-d'), $end->format('Y-m-d'));
            } catch (\Exception $e) {
                // If date parsing fails, fallback to month filter
                $filterType = 'month';
                $start = Carbon::parse($month . '-01');
                $end = $start->copy()->endOfMonth();
                $allAttendances = $employee->getAttendanceWithMissing($start->format('Y-m-d'), $end->format('Y-m-d'));
            }
        } else {
            // Default month filter
            $start = Carbon::parse($month . '-01');
            $end = $start->copy()->endOfMonth();
            $allAttendances = $employee->getAttendanceWithMissing($start->format('Y-m-d'), $end->format('Y-m-d'));
        }

        // Manual pagination for mixed collection
        $page = $request->get('page', 1);
        $perPage = 20;
        $total = $allAttendances->count();

        $attendances = new \Illuminate\Pagination\LengthAwarePaginator(
            $allAttendances->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        // Preserve query parameters
        $attendances->appends($request->query());

        return view('employee.attendance.history', compact('employee', 'attendances', 'month', 'filterType', 'startDate', 'endDate'));
    }

    /**
     * Export attendance history to PDF
     */
    public function exportPdf(Request $request)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('employee.profile.complete');
        }

        $filterType = $request->get('filter_type', 'month');
        $month = $request->get('month', date('Y-m'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($filterType === 'date_range' && $startDate && $endDate) {
            // Validate dates
            try {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);

                // Ensure start date is not after end date
                if ($start->gt($end)) {
                    $temp = $start;
                    $start = $end;
                    $end = $temp;
                    $startDate = $start->format('Y-m-d');
                    $endDate = $end->format('Y-m-d');
                }

                // Get attendances with missing days
                $attendances = $employee->getAttendanceWithMissing($start->format('Y-m-d'), $end->format('Y-m-d'));
            } catch (\Exception $e) {
                // If date parsing fails, fallback to month filter
                $filterType = 'month';
                $start = Carbon::parse($month . '-01');
                $end = $start->copy()->endOfMonth();
                $attendances = $employee->getAttendanceWithMissing($start->format('Y-m-d'), $end->format('Y-m-d'));
            }
        } else {
            // Default month filter
            $start = Carbon::parse($month . '-01');
            $end = $start->copy()->endOfMonth();
            $attendances = $employee->getAttendanceWithMissing($start->format('Y-m-d'), $end->format('Y-m-d'));
        }

        // Generate filename
        if ($filterType === 'date_range' && $startDate && $endDate) {
            $filename = 'Laporan_Absensi_' . $employee->name . '_' .
                Carbon::parse($startDate)->format('d-m-Y') . '_sampai_' .
                Carbon::parse($endDate)->format('d-m-Y') . '.pdf';
        } else {
            $filename = 'Laporan_Absensi_' . $employee->name . '_' .
                Carbon::parse($month)->format('F_Y') . '.pdf';
        }

        // Load PDF view
        $pdf = Pdf::loadView('employee.attendance.pdf', compact('employee', 'attendances', 'month', 'filterType', 'startDate', 'endDate'));

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Set PDF options
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        // Download PDF
        return $pdf->download($filename);
    }

    /**
     * Get location for check in/out
     */
    public function getLocation()
    {
        return view('employee.attendance.location');
    }

    /**
     * Show employee work schedule
     */
    public function schedule()
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Get current active work schedule
        $currentSchedule = WorkSchedule::where('user_id', $user->id)
            ->where('is_active', true)
            ->effectiveOn()
            ->first();

        // Get all work schedules for this user
        $allSchedules = WorkSchedule::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get upcoming schedules
        $upcomingSchedules = WorkSchedule::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('effective_date', '>', Carbon::now())
            ->orderBy('effective_date')
            ->get();

        return view('employee.schedule.index', compact('employee', 'currentSchedule', 'allSchedules', 'upcomingSchedules'));
    }
}
