@extends('layouts.app')

@section('title', 'Riwayat Absensi - Aplikasi Absensi')

@section('header')
    <!-- Header -->
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-14">Riwayat Absensi</a>
        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#filter-menu"><i class="bi bi-funnel font-16 color-highlight"></i></a>
    </div>
@endsection

@section('sidebar')
    @include('employee.sidebar')
@endsection

@section('footer')
    @include('employee.footer')
@endsection

@section('content')
    <!-- Employee Info & Stats -->
    @if (auth()->user() && auth()->user()->hasPermission('attendance.corrections.request'))
        <div class="card card-style">
            <div class="content py-2 d-flex justify-content-between align-items-center">
                <div class="font-12">Perlu koreksi jam masuk/pulang?</div>
                <a href="{{ route('employee.attendance.corrections.create') }}" class="btn btn-s btn-primary rounded-s">
                    <i class="bi bi-pencil-square me-1"></i>Ajukan Koreksi
                </a>
            </div>
        </div>
    @endif
    <div class="card card-style">
        <div class="content py-3">
            <div class="d-flex">
                <div class="align-self-center">
                    <img src="{{ $employee->photo ? asset('storage/' . $employee->photo) : asset('template/images/avatars/5s.png') }}" width="50" height="50" class="rounded-circle me-3 border-2 border-theme">
                </div>
                <div class="align-self-center flex-grow-1">
                    <h5 class="mb-1 font-15">{{ $employee->full_name }}</h5>
                    <p class="mb-0 font-11 opacity-70">{{ $employee->employee_id }}</p>
                </div>
                <div class="align-self-center text-center">
                    @if (request('filter_type') == 'date_range' && request('start_date') && request('end_date'))
                        <h6 class="mb-0 color-highlight font-14">
                            {{ \Carbon\Carbon::parse(request('start_date'))->format('d M') }} -
                            {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}
                        </h6>
                    @else
                        <h6 class="mb-0 color-highlight font-14">{{ \Carbon\Carbon::parse($month)->format('M Y') }}</h6>
                    @endif
                    <p class="mb-0 font-10 opacity-70">{{ $attendances->total() }} hari</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Summary -->
    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="card card-style">
                <div class="content text-center py-3">
                    <i class="bi bi-check-circle-fill color-green-dark font-28 d-block mb-2"></i>
                    <h4 class="font-700 mb-0 font-20">{{ $attendances->filter(function ($att) {return !empty($att->check_in) && !empty($att->check_out);})->count() }}</h4>
                    <p class="mb-0 font-11 opacity-70">Hadir Lengkap</p>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-style">
                <div class="content text-center py-3">
                    <i class="bi bi-clock-history color-orange-dark font-28 d-block mb-2"></i>
                    <h4 class="font-700 mb-0 font-20">{{ $attendances->filter(function ($att) {return !empty($att->check_in) && empty($att->check_out);})->count() }}</h4>
                    <p class="mb-0 font-11 opacity-70">Belum Check Out</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="card card-style">
                <div class="content text-center py-3">
                    <i class="bi bi-x-circle-fill color-red-dark font-28 d-block mb-2"></i>
                    <h4 class="font-700 mb-0 font-20">{{ $attendances->filter(function ($att) {return isset($att->is_missing) && $att->is_missing;})->count() }}</h4>
                    <p class="mb-0 font-11 opacity-70">Tidak Hadir</p>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-style">
                <div class="content text-center py-3">
                    <i class="bi bi-clock-fill color-yellow-dark font-28 d-block mb-2"></i>
                    <h4 class="font-700 mb-0 font-20">{{ $attendances->filter(function ($att) {return !empty($att->schedule_status) && $att->schedule_status === 'late';})->count() }}</h4>
                    <p class="mb-0 font-11 opacity-70">Terlambat</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Month -->
    <div class="card card-style">
        <div class="content py-2">
            <h6 class="font-600 mb-2">Filter Periode</h6>
            <form method="GET" action="{{ route('employee.attendance.history') }}" id="filterForm">
                <!-- Filter Type Selection -->
                <div class="form-custom form-label mb-3">
                    <select class="form-select rounded-xs" name="filter_type" onchange="toggleFilterType()" id="filterType">
                        <option value="month" {{ request('filter_type', 'month') == 'month' ? 'selected' : '' }}>Filter Bulan</option>
                        <option value="date_range" {{ request('filter_type') == 'date_range' ? 'selected' : '' }}>Filter Rentang Tanggal</option>
                    </select>
                    <label class="color-theme font-12">Jenis Filter</label>
                </div>

                <!-- Month Filter -->
                <div id="monthFilter" style="{{ request('filter_type', 'month') == 'month' ? '' : 'display: none;' }}">
                    <div class="form-custom form-label form-icon mb-0">
                        <i class="bi bi-calendar-month font-13"></i>
                        <input type="month" class="form-control rounded-xs" name="month" value="{{ request('month', $month) }}" onchange="this.form.submit()" />
                        <label class="color-theme font-12">Pilih Bulan</label>
                    </div>
                </div>

                <!-- Date Range Filter -->
                <div id="dateRangeFilter" style="{{ request('filter_type') == 'date_range' ? '' : 'display: none;' }}">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-calendar-event font-13"></i>
                                <input type="date" class="form-control rounded-xs" name="start_date" value="{{ request('start_date') }}" />
                                <label class="color-theme font-12">Dari Tanggal</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-calendar-event font-13"></i>
                                <input type="date" class="form-control rounded-xs" name="end_date" value="{{ request('end_date') }}" />
                                <label class="color-theme font-12">Sampai Tanggal</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-sm bg-highlight rounded-xs text-uppercase font-600 w-100">
                                <i class="bi bi-search font-10 pe-1"></i>Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Export PDF Button -->
            @if ($attendances->count() > 0)
                <div class="mt-3">
                    <a href="{{ route('employee.attendance.export-pdf', request()->query()) }}" class="btn btn-sm bg-blue-dark rounded-xs text-uppercase font-600 w-100">
                        <i class="bi bi-file-earmark-pdf font-10 pe-1"></i>Export PDF
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Attendance List -->
    @if ($attendances->count() > 0)
        @foreach ($attendances as $attendance)
            <div class="card card-style">
                <div class="content py-3">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <div class="bg-{{ $attendance->check_in && $attendance->check_out ? 'green' : ($attendance->check_in ? 'orange' : 'red') }}-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-calendar-check text-white font-16"></i>
                            </div>
                        </div>
                        <div class="align-self-center ps-3 flex-grow-1">
                            @if (isset($attendance->is_missing) && $attendance->is_missing)
                                <h6 class="mb-0 font-14">{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</h6>
                                <p class="mb-0 font-10 opacity-70">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</p>
                            @else
                                <h6 class="mb-0 font-14">{{ $attendance->date->format('d M Y') }}</h6>
                                <p class="mb-0 font-10 opacity-70">{{ $attendance->date->format('l') }}</p>
                            @endif
                            @if ($attendance->notes)
                                <p class="mb-0 font-9 color-blue-dark">{{ Str::limit($attendance->notes, 40) }}</p>
                            @endif
                        </div>
                        <div class="align-self-center text-end">
                            @if ($attendance->check_in && $attendance->check_out)
                                <span class="badge bg-green-dark mb-1 font-9">Lengkap</span><br>
                                <small class="font-9 opacity-70">
                                    {{ $attendance->check_in->setTimezone('Asia/Jakarta')->format('H:i') }} - {{ $attendance->check_out->setTimezone('Asia/Jakarta')->format('H:i') }}<br>
                                    <strong class="font-10">{{ $attendance->getWorkingHoursFormatted() }}</strong>
                                </small>
                                <!-- Schedule Status -->
                                @if ($attendance->schedule_status && $attendance->schedule_status !== 'on_time')
                                    <br>
                                    <div class="mt-1">
                                        @php
                                            $statusClass = match ($attendance->schedule_status) {
                                                'late' => 'bg-warning text-dark',
                                                'early_leave' => 'bg-info text-dark',
                                                'late_early_leave' => 'bg-danger text-white',
                                                'absent' => 'bg-secondary text-white',
                                                default => 'bg-success text-white',
                                            };
                                            $statusText = isset($attendance->is_missing) && $attendance->is_missing ? 'Tidak Hadir' : $attendance->getScheduleStatusText();
                                        @endphp
                                        <span class="badge {{ $statusClass }} font-8">{{ $statusText }}</span>
                                    </div>
                                @endif
                            @elseif($attendance->check_in)
                                <span class="badge bg-orange-dark mb-1 font-9">Belum Check Out</span><br>
                                <small class="font-9 opacity-70">
                                    Check In: {{ $attendance->check_in->setTimezone('Asia/Jakarta')->format('H:i') }}
                                </small>
                                <!-- Schedule Status for partial attendance -->
                                @if ($attendance->schedule_status && $attendance->schedule_status !== 'on_time')
                                    <br>
                                    <div class="mt-1">
                                        @php
                                            $statusClass = $attendance->schedule_status === 'late' ? 'bg-warning text-dark' : 'bg-success text-white';
                                            $statusText = isset($attendance->is_missing) && $attendance->is_missing ? 'Tidak Hadir' : $attendance->getScheduleStatusText();
                                        @endphp
                                        <span class="badge {{ $statusClass }} font-8">{{ $statusText }}</span>
                                    </div>
                                @endif
                            @else
                                @if (isset($attendance->is_missing) && $attendance->is_missing)
                                    <span class="badge bg-secondary mb-1 font-9">Tidak Hadir</span><br>
                                    <small class="font-9 opacity-70">Hari Kerja</small>
                                @else
                                    <span class="badge bg-red-dark mb-1 font-9">Tidak Hadir</span><br>
                                    <small class="font-9 opacity-70">-</small>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Detail Toggle -->
                    @if ($attendance->check_in && !isset($attendance->is_missing))
                        <div class="divider my-2"></div>
                        <a href="#" class="d-block" data-bs-toggle="collapse" data-bs-target="#detail-{{ $attendance->id ?? 'missing-' . \Str::slug($attendance->date) }}">
                            <div class="d-flex">
                                <div class="align-self-center">
                                    <i class="bi bi-chevron-down font-10 color-theme"></i>
                                </div>
                                <div class="align-self-center ps-2">
                                    <small class="color-theme font-10">Lihat Detail</small>
                                </div>
                            </div>
                        </a>

                        <div class="collapse" id="detail-{{ $attendance->id ?? 'missing-' . \Str::slug($attendance->date) }}">
                            <div class="mt-2">
                                <div class="row g-2">
                                    @if ($attendance->check_in)
                                        <div class="col-6">
                                            <h6 class="font-10 mb-1">Check In:</h6>
                                            <p class="font-9 mb-0">{{ $attendance->check_in->setTimezone('Asia/Jakarta')->format('H:i:s') }}</p>
                                            @if ($attendance->photo_in)
                                                <img src="{{ asset('storage/' . $attendance->photo_in) }}" class="img-fluid rounded-s mt-1" style="max-height: 70px;" onclick="showPhoto('{{ asset('storage/' . $attendance->photo_in) }}', 'Check In')" data-bs-toggle="modal" data-bs-target="#photoModal">
                                            @endif
                                        </div>
                                    @endif

                                    @if ($attendance->check_out)
                                        <div class="col-6">
                                            <h6 class="font-10 mb-1">Check Out:</h6>
                                            <p class="font-9 mb-0">{{ $attendance->check_out->setTimezone('Asia/Jakarta')->format('H:i:s') }}</p>
                                            @if ($attendance->photo_out)
                                                <img src="{{ asset('storage/' . $attendance->photo_out) }}" class="img-fluid rounded-s mt-1" style="max-height: 70px;" onclick="showPhoto('{{ asset('storage/' . $attendance->photo_out) }}', 'Check Out')" data-bs-toggle="modal" data-bs-target="#photoModal">
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                @if ($attendance->latitude_in && $attendance->longitude_in)
                                    <div class="mt-2">
                                        <h6 class="font-10 mb-1">Lokasi:</h6>
                                        @if ($attendance->location_name)
                                            <p class="font-9 mb-1 color-blue-dark">
                                                <i class="bi bi-building pe-1 font-8"></i>{{ $attendance->location_name }}
                                            </p>
                                        @endif
                                        <p class="font-9 mb-0 opacity-70">
                                            {{ number_format($attendance->latitude_in, 6) }}, {{ number_format($attendance->longitude_in, 6) }}
                                            <a href="https://maps.google.com/?q={{ $attendance->latitude_in }},{{ $attendance->longitude_in }}" target="_blank" class="color-highlight font-9">
                                                <i class="bi bi-geo-alt-fill font-8"></i> Maps
                                            </a>
                                        </p>
                                    </div>
                                @endif

                                <!-- Schedule Status Detail -->
                                @if ($attendance->schedule_status)
                                    <div class="mt-2">
                                        <h6 class="font-10 mb-1">Status Jadwal Kerja:</h6>
                                        @php
                                            $statusClass = match ($attendance->schedule_status) {
                                                'late' => 'color-orange-dark',
                                                'early_leave' => 'color-blue-dark',
                                                'late_early_leave' => 'color-red-dark',
                                                'absent' => 'color-red-dark',
                                                default => 'color-green-dark',
                                            };
                                            $statusText = isset($attendance->is_missing) && $attendance->is_missing ? 'Tidak Hadir' : $attendance->getScheduleStatusText();
                                        @endphp
                                        <p class="font-9 mb-0 {{ $statusClass }}">
                                            <i class="bi bi-clock pe-1 font-8"></i>{{ $statusText }}
                                        </p>
                                        @if ($attendance->late_minutes > 0)
                                            <small class="font-8 opacity-70">Terlambat: {{ $attendance->late_minutes }} menit</small>
                                        @endif
                                        @if ($attendance->early_leave_minutes > 0)
                                            <small class="font-8 opacity-70 d-block">Pulang cepat: {{ $attendance->early_leave_minutes }} menit</small>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        @if ($attendances->hasPages())
            <div class="card card-style">
                <div class="content py-3">
                    {{ $attendances->appends(request()->query())->links('pagination.mobile') }}
                </div>
            </div>
        @endif
    @else
        <div class="card card-style">
            <div class="content text-center py-4">
                <i class="bi bi-calendar-x color-theme opacity-30 font-40 d-block mb-2"></i>
                <h4 class="font-16">Belum Ada Data Absensi</h4>
                @if (request('filter_type') == 'date_range' && request('start_date') && request('end_date'))
                    <p class="color-theme opacity-70 font-12">
                        Belum ada data absensi untuk periode
                        {{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}
                    </p>
                @else
                    <p class="color-theme opacity-70 font-12">Belum ada data absensi untuk bulan {{ \Carbon\Carbon::parse($month)->format('F Y') }}</p>
                @endif
                <a href="{{ route('employee.attendance.index') }}" class="btn btn-s bg-highlight rounded-s text-uppercase font-600">
                    <i class="bi bi-camera pe-2 font-10"></i>Mulai Absensi
                </a>
            </div>
        </div>
    @endif

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title font-14" id="photoModalLabel">Foto Absensi</h6>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <img id="modalPhoto" src="" class="img-fluid rounded-s" style="max-width: 100%;">
                </div>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        function showPhoto(photoUrl, title) {
            document.getElementById('photoModalLabel').textContent = 'Foto ' + title;
            document.getElementById('modalPhoto').src = photoUrl;
        }

        function toggleFilterType() {
            const filterType = document.getElementById('filterType').value;
            const monthFilter = document.getElementById('monthFilter');
            const dateRangeFilter = document.getElementById('dateRangeFilter');

            if (filterType === 'month') {
                monthFilter.style.display = '';
                dateRangeFilter.style.display = 'none';
                // Clear date range inputs
                document.querySelector('input[name="start_date"]').value = '';
                document.querySelector('input[name="end_date"]').value = '';
            } else {
                monthFilter.style.display = 'none';
                dateRangeFilter.style.display = '';
                // Clear month input
                document.querySelector('input[name="month"]').value = '';
            }
        }
    </script>
@endpush
