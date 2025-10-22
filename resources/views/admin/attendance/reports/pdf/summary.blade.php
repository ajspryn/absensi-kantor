<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Ringkasan Absensi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #333;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }

        .summary-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .summary-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .rate-excellent {
            color: #28a745;
            font-weight: bold;
        }

        .rate-good {
            color: #17a2b8;
            font-weight: bold;
        }

        .rate-warning {
            color: #ffc107;
            font-weight: bold;
        }

        .rate-danger {
            color: #dc3545;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        .department-section {
            margin-bottom: 30px;
        }

        .department-title {
            background-color: #e9ecef;
            padding: 10px;
            margin-bottom: 10px;
            font-weight: bold;
            border-left: 4px solid #007bff;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN RINGKASAN ABSENSI KARYAWAN</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i') }} WIB</p>
    </div>

    @php
        $groupedByDepartment = collect($employeeSummaries)->groupBy(function ($item) {
            return $item['employee']->department->name ?? '-';
        });
    @endphp

    @foreach ($groupedByDepartment as $departmentName => $employees)
        <div class="department-section">
            <div class="department-title">{{ $departmentName }}</div>

            <table class="summary-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">ID Karyawan</th>
                        <th width="25%">Nama</th>
                        <th width="8%">Hari Kerja</th>
                        <th width="8%">Hadir</th>
                        <th width="8%">Terlambat</th>
                        <th width="8%">Tidak Hadir</th>
                        <th width="10%">Tingkat Kehadiran</th>
                        <th width="13%">Jadwal Kerja</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $index => $summary)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $summary['employee']->employee_id }}</td>
                            <td style="text-align: left;">{{ $summary['employee']->user->name }}</td>
                            <td>{{ $summary['work_days'] }}</td>
                            <td style="color: #28a745;">{{ $summary['present_days'] }}</td>
                            <td style="color: #ffc107;">{{ $summary['late_days'] }}</td>
                            <td style="color: #dc3545;">{{ $summary['absent_days'] }}</td>
                            <td>
                                @php
                                    $rate = $summary['attendance_rate'];
                                    $class = 'rate-danger';
                                    if ($rate >= 95) {
                                        $class = 'rate-excellent';
                                    } elseif ($rate >= 85) {
                                        $class = 'rate-good';
                                    } elseif ($rate >= 75) {
                                        $class = 'rate-warning';
                                    }
                                @endphp
                                <span class="{{ $class }}">{{ $rate }}%</span>
                            </td>
                            <td style="font-size: 10px;">
                                @if ($summary['employee']->workSchedule)
                                    {{ \Carbon\Carbon::parse($summary['employee']->workSchedule->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($summary['employee']->workSchedule->end_time)->format('H:i') }}
                                @else
                                    Belum dijadwalkan
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    <!-- Department Summary Row -->
                    @php
                        $deptTotalWorkDays = $employees->sum('work_days');
                        $deptTotalPresent = $employees->sum('present_days');
                        $deptTotalLate = $employees->sum('late_days');
                        $deptTotalAbsent = $employees->sum('absent_days');
                        $deptAvgRate = $employees->avg('attendance_rate');
                    @endphp
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td colspan="3">TOTAL {{ $departmentName }}</td>
                        <td>{{ $deptTotalWorkDays }}</td>
                        <td style="color: #28a745;">{{ $deptTotalPresent }}</td>
                        <td style="color: #ffc107;">{{ $deptTotalLate }}</td>
                        <td style="color: #dc3545;">{{ $deptTotalAbsent }}</td>
                        <td>{{ round($deptAvgRate, 1) }}%</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    @if (count($employeeSummaries) == 0)
        <div style="text-align: center; padding: 50px; color: #666;">
            <h3>Tidak ada data karyawan</h3>
            <p>Tidak ada data karyawan yang sesuai dengan filter periode yang dipilih.</p>
        </div>
    @endif

    <!-- Overall Summary -->
    @if (count($employeeSummaries) > 0)
        <div style="margin-top: 30px; border-top: 2px solid #333; padding-top: 20px;">
            <h3>RINGKASAN KESELURUHAN</h3>
            @php
                $totalEmployees = count($employeeSummaries);
                $totalWorkDays = collect($employeeSummaries)->sum('work_days');
                $totalPresent = collect($employeeSummaries)->sum('present_days');
                $totalLate = collect($employeeSummaries)->sum('late_days');
                $totalAbsent = collect($employeeSummaries)->sum('absent_days');
                $overallRate = collect($employeeSummaries)->avg('attendance_rate');
            @endphp

            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa; font-weight: bold;">Total Karyawan</td>
                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center; font-weight: bold;">{{ $totalEmployees }}</td>
                    <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa; font-weight: bold;">Total Hari Kerja</td>
                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center; font-weight: bold;">{{ $totalWorkDays }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa; font-weight: bold;">Total Kehadiran</td>
                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center; color: #28a745; font-weight: bold;">{{ $totalPresent }}</td>
                    <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa; font-weight: bold;">Total Keterlambatan</td>
                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center; color: #ffc107; font-weight: bold;">{{ $totalLate }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa; font-weight: bold;">Total Ketidakhadiran</td>
                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center; color: #dc3545; font-weight: bold;">{{ $totalAbsent }}</td>
                    <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa; font-weight: bold;">Rata-rata Kehadiran</td>
                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center; font-weight: bold;">{{ round($overallRate, 1) }}%</td>
                </tr>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Absensi</p>
        <p>Total {{ count($employeeSummaries) }} karyawan ditemukan</p>
    </div>
</body>

</html>
