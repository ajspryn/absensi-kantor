<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $attendances = Attendance::with(['employee.department'])->get();

        return view('admin.attendance.index', compact('attendances'));
    }

    public function exportPdf(Request $request)
    {
        $attendances = Attendance::with(['employee.department'])->get();
        $pdf = Pdf::loadView('admin.attendance.pdf', compact('attendances'));

        return $pdf->download('laporan-absensi.pdf');
    }
}
