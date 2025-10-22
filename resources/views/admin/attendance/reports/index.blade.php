@extends('layouts.admin')

@section('title', 'Laporan Absensi - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Laporan Absensi',
        'backUrl' => route('dashboard'),
    ])
@endsection

@section('content')
    <!-- Filter Card -->
    <div class="card card-style shadow-m mb-3">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-orange-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-funnel color-white font-18"></i>
                </div>
                <div>
                    <h4 class="font-700 mb-0 color-dark-dark">Filter Laporan</h4>
                    <p class="mb-0 font-12 opacity-70">Atur parameter laporan absensi</p>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.attendance.reports.index') }}" id="filterForm">
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label font-600">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label font-600">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label font-600">Karyawan</label>
                        <select name="employee_id" class="form-select">
                            <option value="">-- Semua Karyawan --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $employeeId == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->employee_id }} - {{ $employee->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
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
                    <div class="col-6">
                        <label class="form-label font-600">Status</label>
                        <select name="status" class="form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="present" {{ $status == 'present' ? 'selected' : '' }}>Hadir</option>
                            <option value="late" {{ $status == 'late' ? 'selected' : '' }}>Terlambat</option>
                            <option value="absent" {{ $status == 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.attendance.reports.index') }}" class="btn btn-secondary text-white me-2">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" id="exportPdfBtn" href="#" target="_blank">Export Detail PDF</a></li>
                                @push('scripts')
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const exportBtn = document.getElementById('exportPdfBtn');
                                            exportBtn.addEventListener('click', function(e) {
                                                e.preventDefault();
                                                const form = document.getElementById('filterForm');
                                                const formData = new FormData(form);
                                                const params = new URLSearchParams(formData);
                                                params.append('type', 'detailed');
                                                const url = '{{ route('admin.attendance.reports.export-pdf') }}?' + params.toString();
                                                window.open(url, '_blank');
                                            });
                                        });
                                    </script>
                                @endpush
                                <li><a class="dropdown-item" href="{{ route('admin.attendance.reports.summary', request()->all()) }}">Lihat Ringkasan</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="card card-style bg-white shadow-xl mb-3">
        <div class="content">
            <h5 class="font-700 mb-3">Statistik Periode</h5>
            <div class="row text-center">
                <div class="col-3">
                    <div class="bg-primary rounded-m p-2 mb-2">
                        <h4 class="text-white font-800 mb-0">{{ $stats['total'] }}</h4>
                    </div>
                    <p class="font-600 mb-0">Total</p>
                </div>
                <div class="col-3">
                    <div class="bg-success rounded-m p-2 mb-2">
                        <h4 class="text-white font-800 mb-0">{{ $stats['present'] }}</h4>
                    </div>
                    <p class="font-600 mb-0">Hadir</p>
                    <small class="text-success">{{ $stats['present_rate'] }}%</small>
                </div>
                <div class="col-3">
                    <div class="bg-warning rounded-m p-2 mb-2">
                        <h4 class="text-white font-800 mb-0">{{ $stats['late'] }}</h4>
                    </div>
                    <p class="font-600 mb-0">Terlambat</p>
                    <small class="text-warning">{{ $stats['late_rate'] }}%</small>
                </div>
                <div class="col-3">
                    <div class="bg-danger rounded-m p-2 mb-2">
                        <h4 class="text-white font-800 mb-0">{{ $stats['absent'] }}</h4>
                    </div>
                    <p class="font-600 mb-0">Tidak Hadir</p>
                    <small class="text-danger">{{ $stats['absent_rate'] }}%</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Data Table -->
    <div class="card card-style bg-white shadow-xl mb-3">
        <div class="content">
            <h5 class="font-700 mb-3">Data Absensi Detail</h5>

            @if ($attendances->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                        <thead class="bg-light">
                            <tr>
                                <th class="font-600">Tanggal</th>
                                <th class="font-600">Karyawan</th>
                                <th class="font-600">Department</th>
                                <th class="font-600">Masuk</th>
                                <th class="font-600">Keluar</th>
                                <th class="font-600">Jam Kerja</th>
                                <th class="font-600">Status</th>
                                <th class="font-600">Schedule Status</th>
                                <th class="font-600">Catatan</th>
                                <th class="font-600">Lokasi</th>
                                <th class="font-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendances as $attendance)
                                <tr>
                                    <td class="font-500">
                                        {{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <p class="font-600 mb-0">{{ $attendance->employee->user->name }}</p>
                                                <small class="text-muted">{{ $attendance->employee->employee_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="font-500">{{ $attendance->employee->department->name ?? '-' }}</td>
                                    <td class="font-500">
                                        @if ($attendance->check_in)
                                            <span class="text-success">{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}</span>
                                            @if ($attendance->employee->workSchedule)
                                                <br><small class="text-muted">Target: {{ \Carbon\Carbon::parse($attendance->employee->workSchedule->start_time)->format('H:i') }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="font-500">
                                        @if ($attendance->check_out)
                                            <span class="text-primary">{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="font-500">
                                        @php
                                            $minutes = max(0, (int) $attendance->working_hours);
                                            $hours = floor($minutes / 60);
                                            $minutes = $minutes % 60;
                                        @endphp
                                        {{ $hours }} jam {{ $minutes }} menit
                                    </td>
                                    <td>
                                        <span class="badge {{ $attendance->status == 'present' ? 'bg-success' : ($attendance->status == 'late' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {!! $attendance->getScheduleStatusBadge() !!} <small>{{ $attendance->getScheduleStatusText() }}</small>
                                    </td>
                                    <td>
                                        {{ $attendance->notes ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $attendance->location_name ?? '-' }}
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning mb-1 edit-attendance-btn" data-id="{{ $attendance->id }}">Edit</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $attendances->withQueryString()->links() }}
                </div>
        </div>
    </div>
    @include('admin.attendance.edit-modal')
@else
    <div class="text-center py-4">
        <i class="bi bi-inbox display-4 text-muted"></i>
        <h5 class="mt-3 text-muted">Tidak ada data absensi</h5>
        <p class="text-muted">Belum ada data absensi untuk periode dan filter yang dipilih.</p>
    </div>
    @endif
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        function exportReport(type) {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            params.append('type', type);

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

        // Edit Attendance Modal logic with AJAX
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-attendance-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const attendanceId = btn.getAttribute('data-id');
                    fetch(`/admin/attendance-reports/edit/${attendanceId}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('attendance_id').value = data.id;
                            // Format time from ISO string if available
                            document.getElementById('check_in').value = data.check_in ? new Date(data.check_in).toISOString().substring(11, 16) : '';
                            document.getElementById('check_out').value = data.check_out ? new Date(data.check_out).toISOString().substring(11, 16) : '';
                            var sidebar = new bootstrap.Offcanvas(document.getElementById('editAttendanceSidebar'));
                            sidebar.show();
                        });
                });
            });

            document.getElementById('editAttendanceForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                fetch('/admin/attendance-reports/update', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(async response => {
                        let data;
                        try {
                            data = await response.json();
                        } catch (err) {
                            alert('Gagal update absensi! (Format respons tidak valid)');
                            return;
                        }
                        if (response.ok && data.success) {
                            var sidebar = bootstrap.Offcanvas.getInstance(document.getElementById('editAttendanceSidebar'));
                            sidebar.hide();
                            alert(data.message || 'Absensi berhasil diupdate!');
                            location.reload();
                        } else {
                            alert(data.message || 'Gagal update absensi!');
                        }
                    })
                    .catch(error => {
                        alert('Gagal update absensi! (Network error)');
                    });
            });
        });
    </script>
@endpush
