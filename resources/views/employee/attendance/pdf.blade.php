<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Absensi - {{ $employee->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .header h2 {
            color: #6c757d;
            margin: 10px 0 0 0;
            font-size: 16px;
            font-weight: normal;
        }

        .employee-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .employee-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .employee-info td {
            padding: 5px 10px;
            border: none;
        }

        .employee-info .label {
            font-weight: bold;
            width: 30%;
            color: #495057;
        }

        .period-info {
            background-color: #e3f2fd;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .period-info strong {
            color: #1976d2;
            font-size: 14px;
        }

        .summary-stats {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }

        .stat-box:first-child {
            border-right: none;
            background-color: #d4edda;
        }

        .stat-box:nth-child(2) {
            border-right: none;
            background-color: #fff3cd;
        }

        .stat-box:nth-child(3) {
            border-right: none;
            background-color: #f1c2e7;
        }

        .stat-box:last-child {
            background-color: #f8d7da;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 11px;
            color: #6c757d;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .attendance-table th,
        .attendance-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }

        .attendance-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .attendance-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: white;
            display: inline-block;
        }

        .status-complete {
            background-color: #28a745;
        }

        .status-partial {
            background-color: #ffc107;
            color: #212529;
        }

        .status-absent {
            background-color: #dc3545;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN ABSENSI KARYAWAN</h1>
        <h2>{{ config('app.name', 'Sistem Absensi') }}</h2>
    </div>

    <!-- Employee Information -->
    <div class="employee-info">
        <table>
            <tr>
                <td class="label">Nama Karyawan:</td>
                <td>{{ $employee->full_name }}</td>
                <td class="label">Email:</td>
                <td>{{ $employee->email }}</td>
            </tr>
            <tr>
                <td class="label">ID Karyawan:</td>
                <td>{{ $employee->employee_id ?? '-' }}</td>
                <td class="label">Tanggal Cetak:</td>
                <td>{{ now()->setTimezone('Asia/Jakarta')->format('d F Y H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    <!-- Period Information -->
    <div class="period-info">
        @if (request('filter_type') == 'date_range' && request('start_date') && request('end_date'))
            <strong>
                Periode: {{ \Carbon\Carbon::parse(request('start_date'))->format('d F Y') }} -
                {{ \Carbon\Carbon::parse(request('end_date'))->format('d F Y') }}
            </strong>
        @else
            <strong>Periode: {{ \Carbon\Carbon::parse($month)->format('F Y') }}</strong>
        @endif
    </div>

    <!-- Summary Statistics -->
    <div class="summary-stats">
        <div class="stat-box">
            <div class="stat-number" style="color: #155724;">
                {{ $attendances->filter(function ($att) {return !empty($att->check_in) && !empty($att->check_out);})->count() }}
            </div>
            <div class="stat-label">Hadir Lengkap</div>
        </div>
        <div class="stat-box">
            <div class="stat-number" style="color: #856404;">
                {{ $attendances->filter(function ($att) {return !empty($att->check_in) && empty($att->check_out);})->count() }}
            </div>
            <div class="stat-label">Belum Check Out</div>
        </div>
        <div class="stat-box">
            <div class="stat-number" style="color: #d63384;">
                {{ $attendances->filter(function ($att) {return !empty($att->schedule_status) && in_array($att->schedule_status, ['late', 'late_early_leave']);})->count() }}
            </div>
            <div class="stat-label">Terlambat</div>
        </div>
        <div class="stat-box">
            <div class="stat-number" style="color: #721c24;">
                {{ $attendances->filter(function ($att) {return isset($att->is_missing) && $att->is_missing;})->count() }}
            </div>
            <div class="stat-label">Tidak Hadir</div>
        </div>
    </div>

    <!-- Attendance Table -->
    @if ($attendances->count() > 0)
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Total Jam</th>
                    <th>Status</th>
                    <th>Status Jadwal</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $index => $attendance)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>
                            @if (isset($attendance->is_missing) && $attendance->is_missing)
                                {{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}
                            @else
                                {{ $attendance->date->format('d/m/Y') }}
                            @endif
                        </td>
                        <td>
                            @if (isset($attendance->is_missing) && $attendance->is_missing)
                                {{ \Carbon\Carbon::parse($attendance->date)->format('l') }}
                            @else
                                {{ $attendance->date->format('l') }}
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if (isset($attendance->is_missing) && $attendance->is_missing)
                                -
                            @else
                                {{ $attendance->check_in ? $attendance->check_in->setTimezone('Asia/Jakarta')->format('H:i:s') : '-' }}
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if (isset($attendance->is_missing) && $attendance->is_missing)
                                -
                            @else
                                {{ $attendance->check_out ? $attendance->check_out->setTimezone('Asia/Jakarta')->format('H:i:s') : '-' }}
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if (isset($attendance->is_missing) && $attendance->is_missing)
                                -
                            @else
                                {{ $attendance->check_in && $attendance->check_out ? $attendance->getWorkingHoursFormatted() : '-' }}
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if (isset($attendance->is_missing) && $attendance->is_missing)
                                <span class="status-badge status-absent">Tidak Hadir</span>
                            @elseif ($attendance->check_in && $attendance->check_out)
                                <span class="status-badge status-complete">Lengkap</span>
                            @elseif($attendance->check_in)
                                <span class="status-badge status-partial">Belum Check Out</span>
                            @else
                                <span class="status-badge status-absent">Tidak Hadir</span>
                            @endif
                        </td>
                        <td style="text-align: center; font-size: 10px;">
                            @if (isset($attendance->is_missing) && $attendance->is_missing)
                                <span style="color: #6c757d;">Tidak Hadir</span>
                            @elseif ($attendance->schedule_status && $attendance->check_in)
                                @switch($attendance->schedule_status)
                                    @case('late')
                                        <span style="color: #d63384;">Terlambat {{ $attendance->late_minutes }}m</span>
                                    @break

                                    @case('early_leave')
                                        <span style="color: #0dcaf0;">Pulang Cepat {{ $attendance->early_leave_minutes }}m</span>
                                    @break

                                    @case('late_early_leave')
                                        <span style="color: #dc3545;">Terlambat {{ $attendance->late_minutes }}m<br>Pulang Cepat {{ $attendance->early_leave_minutes }}m</span>
                                    @break

                                    @default
                                        <span style="color: #198754;">Tepat Waktu</span>
                                @endswitch
                            @else
                                <span style="color: #6c757d;">-</span>
                            @endif
                        </td>
                        <td style="font-size: 10px;">
                            @if (isset($attendance->is_missing) && $attendance->is_missing)
                                -
                            @else
                                {{ $attendance->notes ? \Str::limit($attendance->notes, 30) : '-' }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 50px; color: #6c757d;">
            <h3>Tidak ada data absensi untuk periode yang dipilih</h3>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>
            Laporan ini digenerate secara otomatis pada {{ now()->setTimezone('Asia/Jakarta')->format('d F Y H:i:s') }} WIB<br>
            {{ config('app.name', 'Sistem Absensi') }} - Sistem Manajemen Absensi Karyawan
        </p>
    </div>
</body>

</html>
