@extends('layouts.admin')

@section('title', 'Ringkasan Laporan Absensi - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Ringkasan Laporan',
        'backUrl' => route('admin.attendance.reports.index', request()->all()),
    ])
@endsection

@section('content')
    <!-- Header Info -->
    <div class="card card-style bg-primary-dark text-white shadow-xl mb-3">
        <div class="content">
            <h4 class="text-black font-700 mb-2">Ringkasan Absensi Karyawan</h4>
            <p class="text-black opacity-80 mb-2">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </p>
            @if ($departmentId)
                @php $selectedDept = $departments->where('id', $departmentId)->first(); @endphp
                <p class="text-white opacity-80 mb-0">
                    Department: {{ $selectedDept->name ?? 'Semua Department' }}
                </p>
            @endif
        </div>
    </div>

    <!-- Filter -->
    <div class="card card-style bg-white shadow-xl mb-3">
        <div class="content">
            <form method="GET" action="{{ route('admin.attendance.reports.summary') }}" class="row g-3">
                <div class="col-6">
                    <label class="form-label font-600">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-6">
                    <label class="form-label font-600">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-8">
                    <label class="form-label font-600">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">-- Semua Department --</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ $departmentId == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Export Options -->
    <div class="card card-style bg-white shadow-xl mb-3">
        <div class="content">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.attendance.reports.export-pdf', array_merge(request()->all(), ['type' => 'summary'])) }}" class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('admin.attendance.reports.index', request()->all()) }}" class="btn btn-primary text-white">
                    <i class="bi bi-list-ul"></i> Lihat Detail
                </a>
            </div>
        </div>
    </div>

    <!-- Employee Summary Cards -->
    @foreach ($employeeSummaries as $summary)
        <div class="card card-style bg-white shadow-xl mb-3">
            <div class="content">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="flex-grow-1">
                        <h5 class="font-700 mb-1">{{ $summary['employee']->user->name }}</h5>
                        <p class="text-muted mb-0">
                            {{ $summary['employee']->employee_id }} •
                            {{ $summary['employee']->department->name ?? '-' }}
                        </p>
                        @if ($summary['employee']->workSchedule)
                            <small class="text-muted">
                                Jadwal: {{ \Carbon\Carbon::parse($summary['employee']->workSchedule->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($summary['employee']->workSchedule->end_time)->format('H:i') }}
                            </small>
                        @endif
                    </div>
                    <div class="text-end">
                        @php
                            $rateColor = 'danger';
                            if ($summary['attendance_rate'] >= 90) {
                                $rateColor = 'success';
                            } elseif ($summary['attendance_rate'] >= 75) {
                                $rateColor = 'warning';
                            }
                        @endphp
                        <span class="badge bg-{{ $rateColor }} fs-6">
                            {{ $summary['attendance_rate'] }}%
                        </span>
                    </div>
                </div>

                <!-- Statistics Row -->
                <div class="row text-center mb-3">
                    <div class="col-3">
                        <div class="bg-light rounded-s p-2">
                            <h6 class="font-700 mb-0 text-primary">{{ $summary['work_days'] }}</h6>
                            <small class="text-muted">Hari Kerja</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="bg-light rounded-s p-2">
                            <h6 class="font-700 mb-0 text-success">{{ $summary['present_days'] }}</h6>
                            <small class="text-muted">Hadir</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="bg-light rounded-s p-2">
                            <h6 class="font-700 mb-0 text-warning">{{ $summary['late_days'] }}</h6>
                            <small class="text-muted">Terlambat</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="bg-light rounded-s p-2">
                            <h6 class="font-700 mb-0 text-danger">{{ $summary['absent_days'] }}</h6>
                            <small class="text-muted">Tidak Hadir</small>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="progress mb-2" style="height: 8px;">
                    @php
                        $presentPercent = $summary['work_days'] > 0 ? ($summary['present_days'] / $summary['work_days']) * 100 : 0;
                        $absentPercent = $summary['work_days'] > 0 ? ($summary['absent_days'] / $summary['work_days']) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-success" style="width: {{ $presentPercent }}%"></div>
                    <div class="progress-bar bg-danger" style="width: {{ $absentPercent }}%"></div>
                </div>

                <div class="d-flex justify-content-between text-small">
                    <span class="text-success">
                        <i class="bi bi-circle-fill"></i> Hadir {{ round($presentPercent, 1) }}%
                    </span>
                    <span class="text-danger">
                        <i class="bi bi-circle-fill"></i> Tidak Hadir {{ round($absentPercent, 1) }}%
                    </span>
                </div>

                <!-- Quick Actions -->
                <div class="mt-3 pt-3 border-top">
                    <a href="{{ route('admin.attendance.reports.index', array_merge(request()->all(), ['employee_id' => $summary['employee']->id])) }}" class="btn btn-sm btn-primary text-white">
                        <i class="bi bi-eye"></i> Lihat Detail
                    </a>
                    <div class="mt-2">
                        @if (!empty($summary['last_check_in']) && $summary['last_check_in']->latitude_in && $summary['last_check_in']->longitude_in)
                            <div>
                                <small class="text-muted">Check In:&nbsp;</small>
                                <small>{{ number_format($summary['last_check_in']->latitude_in, 6) }}, {{ number_format($summary['last_check_in']->longitude_in, 6) }}</small>
                                <a href="https://maps.google.com/?q={{ $summary['last_check_in']->latitude_in }},{{ $summary['last_check_in']->longitude_in }}" target="_blank" class="ms-2">Lihat di Maps</a>
                            </div>
                        @endif

                        @if (!empty($summary['last_check_out']) && $summary['last_check_out']->latitude_out && $summary['last_check_out']->longitude_out)
                            <div>
                                <small class="text-muted">Check Out:&nbsp;</small>
                                <small>{{ number_format($summary['last_check_out']->latitude_out, 6) }}, {{ number_format($summary['last_check_out']->longitude_out, 6) }}</small>
                                <a href="https://maps.google.com/?q={{ $summary['last_check_out']->latitude_out }},{{ $summary['last_check_out']->longitude_out }}" target="_blank" class="ms-2">Lihat di Maps</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @if (count($employeeSummaries) == 0)
        <div class="card card-style bg-white shadow-xl mb-3">
            <div class="content text-center py-5">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <h5 class="mt-3 text-muted">Tidak ada data karyawan</h5>
                <p class="text-muted">Tidak ada karyawan yang sesuai dengan filter yang dipilih.</p>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        function exportSummaryPdf() {
            const params = new URLSearchParams(window.location.search);
            params.set('type', 'summary');

            const url = '{{ route('admin.attendance.reports.export-pdf') }}?' + params.toString();

            // Create a temporary link element and click it
            const link = document.createElement('a');
            link.href = url;
            link.download = '';
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection
