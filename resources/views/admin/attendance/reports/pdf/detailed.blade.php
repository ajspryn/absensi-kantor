<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Detail</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 5mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 7px;
            margin: 0;
            padding: 0;
        }

        h2 {
            font-size: 12px;
            margin: 5px 0;
            text-align: center;
        }

        p {
            font-size: 8px;
            margin: 2px 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 1px;
            text-align: center;
            word-break: break-word;
            font-size: 6px;
            line-height: 1.1;
        }

        th {
            background: #eee;
            font-size: 6px;
            font-weight: bold;
        }

        .no-col {
            width: 15px;
            font-size: 5px;
        }

        .name-col {
            width: 130px;
            text-align: left;
            font-size: 5px;
            line-height: 1.1;
        }

        .date-col {
            width: 30px;
            font-size: 5px;
            line-height: 1.1;
        }

        .rekap {
            width: 35px;
            font-weight: bold;
            font-size: 5px;
        }

        .status-hadir {
            background: #d4edda;
            color: #155724;
        }

        .status-terlambat {
            background: #fff3cd;
            color: #856404;
        }

        .status-absen {
            background: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }

        .status-izin {
            background: #cce5ff;
            color: #004085;
            font-weight: bold;
        }

        .status-cuti {
            background: #e2e3e5;
            color: #383d41;
            font-weight: bold;
        }

        .summary-table th {
            background: #007bff;
            color: #fff;
            font-size: 7px;
        }

        .summary-table td {
            font-weight: bold;
            font-size: 7px;
        }

        .summary-table .name-col {
            width: 100px;
            font-size: 6px;
        }

        /* Summary Dashboard Styles */
        .summary-dashboard {
            margin: 15px auto;
            page-break-inside: avoid;
            text-align: center;
            max-width: 90%;
        }

        .summary-row {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin-bottom: 10px;
            gap: 20px;
        }

        .summary-stats {
            width: 40%;
            margin: 0 auto;
        }

        .summary-chart {
            width: 40%;
            margin: 0 auto;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin: 0 auto;
            border-radius: 5px;
            overflow: hidden;
        }

        .stats-table th {
            background: #007bff;
            color: white;
            padding: 5px;
            text-align: center;
            font-size: 8px;
        }

        .stats-table td {
            padding: 4px;
            text-align: center;
            border: 1px solid #ddd;
            font-weight: bold;
        }

        .chart-container {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            margin: 0 auto;
            border-radius: 5px;
        }

        .chart-bar {
            display: block;
            margin: 3px auto;
            text-align: center;
            color: white;
            font-size: 7px;
            font-weight: bold;
            min-height: 25px;
            line-height: 25px;
            border-radius: 3px;
            max-width: 250px;
        }

        .bar-hadir {
            background: #28a745;
        }

        .bar-terlambat {
            background: #ffc107;
            color: #000;
        }

        .bar-absen {
            background: #dc3545;
        }

        .bar-izin {
            background: #17a2b8;
        }

        .bar-cuti {
            background: #6c757d;
        }

        .percentage-display {
            font-size: 8px;
            margin-top: 8px;
            text-align: center;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>
    <h2 style="text-align:center;">Laporan Absensi Detail</h2>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>

    @php
        // Hitung total statistik untuk summary
        $overallStats = ['H' => 0, 'T' => 0, 'A' => 0, 'I' => 0, 'C' => 0];
        $totalDays = 0;

        foreach ($employees as $employee) {
            $dates = [];
            $period = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            while ($period <= $end) {
                $dates[] = $period->format('Y-m-d');
                $period->addDay();
            }
            $totalDays = count($dates);

            foreach ($dates as $dateYmd) {
                $attendance = $attendances
                    ->filter(function ($a) use ($employee, $dateYmd) {
                        $attendanceDate = \Carbon\Carbon::parse($a->date)->format('Y-m-d');
                        return strval($a->employee_id) === strval($employee->id) && $attendanceDate === $dateYmd;
                    })
                    ->first();

                if ($attendance) {
                    if ($attendance->check_in) {
                        $start = $employee->workSchedule->start_time ?? '08:00:00';
                        $checkInTime = \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s');
                        $isLate = $checkInTime > $start;
                        $overallStats[$isLate ? 'T' : 'H']++;
                    } elseif ($attendance->status == 'izin') {
                        $overallStats['I']++;
                    } elseif ($attendance->status == 'cuti') {
                        $overallStats['C']++;
                    } else {
                        $overallStats['A']++;
                    }
                } else {
                    $overallStats['A']++;
                }
            }
        }

        $grandTotal = array_sum($overallStats);
        $employeeCount = count($employees);
    @endphp

    <!-- Summary Dashboard -->
    <div class="summary-dashboard">
        <div class="summary-row">
            <div class="summary-stats">
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="status-hadir">
                            <td>Hadir</td>
                            <td>{{ $overallStats['H'] }}</td>
                            <td>{{ $grandTotal > 0 ? number_format(($overallStats['H'] / $grandTotal) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr class="status-terlambat">
                            <td>Terlambat</td>
                            <td>{{ $overallStats['T'] }}</td>
                            <td>{{ $grandTotal > 0 ? number_format(($overallStats['T'] / $grandTotal) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr class="status-absen">
                            <td>Absen</td>
                            <td>{{ $overallStats['A'] }}</td>
                            <td>{{ $grandTotal > 0 ? number_format(($overallStats['A'] / $grandTotal) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr class="status-izin">
                            <td>Izin</td>
                            <td>{{ $overallStats['I'] }}</td>
                            <td>{{ $grandTotal > 0 ? number_format(($overallStats['I'] / $grandTotal) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr class="status-cuti">
                            <td>Cuti</td>
                            <td>{{ $overallStats['C'] }}</td>
                            <td>{{ $grandTotal > 0 ? number_format(($overallStats['C'] / $grandTotal) * 100, 1) : 0 }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="summary-chart">
                <div class="chart-container">
                    <h4 style="font-size: 9px; margin: 5px 0;">Grafik Distribusi Absensi</h4>
                    @php
                        $maxValue = max(array_values($overallStats));
                        $chartWidth = 200; // Total width untuk chart
                    @endphp

                    <div style="margin: 10px auto; max-width: 300px;">
                        @if ($overallStats['H'] > 0)
                            <div class="chart-bar bar-hadir" style="width: {{ ($overallStats['H'] / $maxValue) * $chartWidth }}px;">
                                H: {{ $overallStats['H'] }}
                            </div>
                        @endif
                        @if ($overallStats['T'] > 0)
                            <div class="chart-bar bar-terlambat" style="width: {{ ($overallStats['T'] / $maxValue) * $chartWidth }}px;">
                                T: {{ $overallStats['T'] }}
                            </div>
                        @endif
                        @if ($overallStats['A'] > 0)
                            <div class="chart-bar bar-absen" style="width: {{ ($overallStats['A'] / $maxValue) * $chartWidth }}px;">
                                A: {{ $overallStats['A'] }}
                            </div>
                        @endif
                        @if ($overallStats['I'] > 0)
                            <div class="chart-bar bar-izin" style="width: {{ ($overallStats['I'] / $maxValue) * $chartWidth }}px;">
                                I: {{ $overallStats['I'] }}
                            </div>
                        @endif
                        @if ($overallStats['C'] > 0)
                            <div class="chart-bar bar-cuti" style="width: {{ ($overallStats['C'] / $maxValue) * $chartWidth }}px;">
                                C: {{ $overallStats['C'] }}
                            </div>
                        @endif
                    </div>

                    <div class="percentage-display">
                        <strong>Total: {{ $grandTotal }} dari {{ $employeeCount }} pegawai × {{ $totalDays }} hari</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Ringkasan Rekap -->
    <table class="summary-table">
        <thead>
            <tr>
                <th class="no-col">No</th>
                <th class="name-col">Nama</th>
                <th>Hadir</th>
                <th>Terlambat</th>
                <th>Absen</th>
                <th>Izin</th>
                <th>Cuti</th>
                <th>Total Hari</th>
            </tr>
        </thead>
        <tbody>
            @php
                $showTotal = empty($employeeId ?? null);
                $totalRekap = ['H' => 0, 'T' => 0, 'A' => 0, 'I' => 0, 'C' => 0];
            @endphp
            @foreach ($employees as $i => $employee)
                @php
                    $rekap = ['H' => 0, 'T' => 0, 'A' => 0, 'I' => 0, 'C' => 0];
                    $dates = [];
                    $period = \Carbon\Carbon::parse($startDate);
                    $end = \Carbon\Carbon::parse($endDate);
                    while ($period <= $end) {
                        $dates[] = $period->format('Y-m-d');
                        $period->addDay();
                    }
                    foreach ($dates as $date) {
                        $dateYmd = $date;
                        $row = $attendances
                            ->filter(function ($a) use ($employee, $dateYmd) {
                                // Pastikan format tanggal sama persis
                                $attendanceDate = \Carbon\Carbon::parse($a->date)->format('Y-m-d');
                                return strval($a->employee_id) === strval($employee->id) && $attendanceDate === $dateYmd;
                            })
                            ->first();
                        // (debug lines removed)
                        if ($row) {
                            if ($row->check_in) {
                                $start = $employee->workSchedule->start_time ?? '08:00:00';
                                $checkInTime = \Carbon\Carbon::parse($row->check_in)->format('H:i:s');
                                $isLate = $checkInTime > $start;
                                $rekap[$isLate ? 'T' : 'H']++;
                            } elseif ($row->status == 'izin') {
                                $rekap['I']++;
                            } elseif ($row->status == 'cuti') {
                                $rekap['C']++;
                            } else {
                                $rekap['A']++;
                            }
                        } else {
                            // Tidak ada data absensi untuk tanggal ini
                            $rekap['A']++;
                        }
                    }
                    if ($showTotal) {
                        foreach ($rekap as $k => $v) {
                            $totalRekap[$k] += $v;
                        }
                    }
                @endphp
                <tr>
                    <td class="no-col">{{ $loop->iteration }}</td>
                    <td class="name-col">
                        <strong>{{ $employee->user->name ?? '-' }}</strong><br>
                        <small>{{ $employee->employee_id ?? '-' }}</small><br>
                        <small>{{ $employee->position_name ?? '-' }}</small>
                    </td>
                    <td style="background:#d4edda;color:#155724;">{{ $rekap['H'] }}</td>
                    <td style="background:#fff3cd;color:#856404;">{{ $rekap['T'] }}</td>
                    <td style="background:#f8d7da;color:#721c24;">{{ $rekap['A'] }}</td>
                    <td style="background:#cce5ff;color:#004085;">{{ $rekap['I'] }}</td>
                    <td style="background:#e2e3e5;color:#383d41;">{{ $rekap['C'] }}</td>
                    <td>{{ count($dates) }}</td>
                </tr>
            @endforeach
            @if ($showTotal)
                <tr>
                    <td colspan="2" style="background:#007bff;color:#fff;font-weight:bold;">TOTAL</td>
                    <td style="background:#d4edda;color:#155724;">{{ $totalRekap['H'] }}</td>
                    <td style="background:#fff3cd;color:#856404;">{{ $totalRekap['T'] }}</td>
                    <td style="background:#f8d7da;color:#721c24;">{{ $totalRekap['A'] }}</td>
                    <td style="background:#cce5ff;color:#004085;">{{ $totalRekap['I'] }}</td>
                    <td style="background:#e2e3e5;color:#383d41;">{{ $totalRekap['C'] }}</td>
                    <td>{{ count($dates) * count($employees) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Tabel Detail Harian -->
    <table>
        <thead>
            <tr>
                <th class="no-col">No</th>
                <th class="name-col">Nama</th>
                @foreach ($dates as $date)
                    <th class="date-col">{{ \Carbon\Carbon::parse($date)->format('j') }}</th>
                @endforeach
                <th>Rekap</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $i => $employee)
                @php
                    $rekap = ['H' => 0, 'T' => 0, 'A' => 0, 'I' => 0, 'C' => 0];
                @endphp
                <tr>
                    <td class="no-col">{{ $loop->iteration }}</td>
                    <td class="name-col">
                        <strong>{{ $employee->user->name ?? '-' }}</strong><br>
                        <small>{{ $employee->employee_id ?? '-' }}</small><br>
                    </td>
                    @foreach ($dates as $dateYmd)
                        @php
                            $row = $attendances
                                ->filter(function ($a) use ($employee, $dateYmd) {
                                    // Pastikan format tanggal sama persis
                                    $attendanceDate = \Carbon\Carbon::parse($a->date)->format('Y-m-d');
                                    return strval($a->employee_id) === strval($employee->id) && $attendanceDate === $dateYmd;
                                })
                                ->first();
                            if ($row) {
                                if ($row->check_in) {
                                    $start = $employee->workSchedule->start_time ?? '08:00:00';
                                    $checkInTime = \Carbon\Carbon::parse($row->check_in)->format('H:i:s');
                                    $isLate = $checkInTime > $start;
                                    $rekap[$isLate ? 'T' : 'H']++;
                                    $statusClass = $isLate ? 'status-terlambat' : 'status-hadir';
                                    $checkInDisplay = \Carbon\Carbon::parse($row->check_in)->format('H:i');
                                    $checkOutDisplay = $row->check_out ? \Carbon\Carbon::parse($row->check_out)->format('H:i') : '-';
                                    echo '<td class="' . $statusClass . '">' . $checkInDisplay . '<br>' . $checkOutDisplay . '</td>';
                                } elseif ($row->status == 'izin') {
                                    $rekap['I']++;
                                    echo '<td class="status-izin">IZIN</td>';
                                } elseif ($row->status == 'cuti') {
                                    $rekap['C']++;
                                    echo '<td class="status-cuti">CUTI</td>';
                                } else {
                                    $rekap['A']++;
                                    echo '<td class="status-absen">ABSEN</td>';
                                }
                            } else {
                                $rekap['A']++;
                                echo '<td class="status-absen">ABSEN</td>';
                            }
                        @endphp
                    @endforeach
                    <td class="rekap">
                        H:{{ $rekap['H'] }} T:{{ $rekap['T'] }}<br>A:{{ $rekap['A'] }} I:{{ $rekap['I'] }} C:{{ $rekap['C'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
