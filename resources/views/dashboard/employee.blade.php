@extends('layouts.app')

@section('title', 'Employee Dashboard - Aplikasi Absensi')

@section('header')
    <!-- Header -->
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-bs-toggle="offcanvas" data-bs-target="#menu-main" href="#"><i class="bi bi-list color-theme"></i></a>
        <a href="#" class="header-title color-theme">Dashboard</a>
        <a href="{{ route('employee.attendance.history') }}" class="header-icon"><i class="bi bi-clock-history color-theme font-16"></i></a>
        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#menu-notifications"><i class="bi bi-bell color-theme font-16"></i></a>
    </div>
@endsection

@section('footer')
    <!-- Footer Bar-->
    @include('employee.footer')
@endsection

@section('sidebar')
    <!-- Main Sidebar-->
    @include('employee.sidebar')

    <!-- Notifications Sidebar -->
    <div id="menu-notifications" class="offcanvas offcanvas-end offcanvas-detached rounded-m" style="width:280px;">
        <div class="content">
            <div class="d-flex pb-2">
                <div class="align-self-center">
                    <h1 class="mb-0">Notifikasi</h1>
                </div>
                <div class="align-self-center ms-auto">
                    <a href="#" class="ps-4" data-bs-dismiss="offcanvas">
                        <i class="bi bi-x color-red-dark font-26 line-height-xl"></i>
                    </a>
                </div>
            </div>
            <div class="divider mb-3"></div>

            <!-- Today's Status -->
            <div class="d-flex mb-3">
                <div class="align-self-center">
                    <div class="bg-green-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-calendar-check text-white font-14"></i>
                    </div>
                </div>
                <div class="align-self-center">
                    <h6 class="font-600 mb-0">Status Hari Ini</h6>
                    <p class="font-11 opacity-70 mb-0">
                        @if ($todayAttendance && $todayAttendance->check_in)
                            @if ($todayAttendance->check_out)
                                Absensi lengkap - {{ $todayAttendance->getWorkingHoursFormatted() }}
                            @else
                                Sudah check in - {{ $todayAttendance->check_in->format('H:i') }}
                            @endif
                        @else
                            Belum melakukan absensi
                        @endif
                    </p>
                </div>
            </div>

            <div class="divider my-2"></div>

            <!-- Monthly Stats -->
            <h6 class="font-600 mb-2">Statistik Bulan {{ now()->locale('id')->translatedFormat('F Y') }}</h6>
            <div class="row g-2 mb-3">
                <div class="col-4 text-center">
                    <h6 class="font-600 color-green-dark mb-0">{{ $weeklyStats['present'] }}</h6>
                    <p class="font-10 opacity-70 mb-0">Hadir</p>
                </div>
                <div class="col-4 text-center">
                    <h6 class="font-600 color-red-dark mb-0">{{ $weeklyStats['absent'] }}</h6>
                    <p class="font-10 opacity-70 mb-0">Alpha</p>
                </div>
                <div class="col-4 text-center">
                    <h6 class="font-600 color-blue-dark mb-0">{{ $weeklyStats['leave'] }}</h6>
                    <p class="font-10 opacity-70 mb-0">Cuti</p>
                </div>
            </div>

            <div class="divider my-2"></div>

            <!-- Quick Actions -->
            <h6 class="font-600 mb-2">Aksi Cepat</h6>

            <a href="{{ route('employee.attendance.index') }}" class="d-flex py-2" data-bs-dismiss="offcanvas">
                <div class="align-self-center">
                    <i class="bi bi-camera color-green-dark font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h6 class="pt-1 mb-0 font-14">Ambil Foto Absensi</h6>
                </div>
            </a>

            <a href="{{ route('employee.attendance.history') }}" class="d-flex py-2" data-bs-dismiss="offcanvas">
                <div class="align-self-center">
                    <i class="bi bi-clock-history color-blue-dark font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h6 class="pt-1 mb-0 font-14">Lihat Riwayat Lengkap</h6>
                </div>
            </a>

        </div>
    </div>
@endsection

@section('content')
    <!-- Welcome Card -->
    <div class="card card-style">
        <div class="content py-3">
            <div class="d-flex align-items-start">
                <div class="align-self-start me-3">
                    <div class="position-relative">
                        <img src="{{ $employee->photo ? asset('storage/' . $employee->photo) : asset('template/images/avatars/5s.png') }}" width="65" height="65" class="rounded-circle border-3 border-{{ $employee->is_active ? 'green' : 'gray' }}-dark">
                        <span class="position-absolute bottom-0 end-0 bg-{{ $employee->is_active ? 'green' : 'red' }}-dark border-3 border-white rounded-circle" style="width: 20px; height: 20px;"></span>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h1 class="font-700 font-16 mb-1">Selamat Datang!</h1>
                    <h2 class="font-600 font-14 mb-1 color-theme">{{ $employee->full_name }}</h2>

                    <p class="mb-1 font-10 opacity-70">
                        <i class="bi bi-building pe-2 color-theme"></i>{{ optional($employee->department)->name ?? '-' }}
                        <span class="badge bg-highlight rounded-xl font-8 px-2 py-1 ms-1">
                            {{ $employee->position_name ?? '-' }}
                        </span>
                    </p>

                    <div class="d-flex flex-wrap gap-1 mb-0">
                        <span class="badge bg-theme rounded-xl font-9 px-2 py-1">
                            <i class="bi bi-person-badge pe-1"></i>{{ $employee->employee_id }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="divider my-2"></div>
            <div class="d-flex align-items-center mb-2">
                <div class="align-self-center">
                    <i class="bi bi-calendar-today color-theme me-2 font-14"></i>
                </div>
                <div class="align-self-center flex-grow-1">
                    <p class="mb-0 font-12 font-600">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                    <p class="mb-0 font-10 opacity-70">
                        <i class="bi bi-clock pe-1"></i>{{ now()->format('H:i') }} WIB
                    </p>
                </div>
            </div>

            @if (!$todayAttendance || !$todayAttendance->check_in)
                <a href="{{ route('employee.attendance.index') }}" class="btn btn-full btn-l bg-green-dark text-uppercase font-600 rounded-s shadow-bg shadow-bg-s mb-0" style="min-height: 45px;">
                    <i class="bi bi-camera-fill pe-2 font-14"></i>Check In Sekarang
                </a>
            @elseif(!$todayAttendance->check_out)
                <a href="{{ route('employee.attendance.index') }}" class="btn btn-full btn-l bg-blue-dark text-uppercase font-600 rounded-s shadow-bg shadow-bg-s mb-0" style="min-height: 45px;">
                    <i class="bi bi-camera-fill pe-2 font-14"></i>Check Out Sekarang
                </a>
            @else
                <div class="alert bg-green-dark color-white rounded-s mb-0" role="alert">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-check-circle-fill pe-2 font-14"></i>
                        </div>
                        <div class="align-self-center">
                            <strong class="font-13">Absensi Hari Ini Lengkap!</strong><br>
                            <small class="font-10">Anda sudah melakukan check in dan check out</small>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Today's Attendance Status -->
    <div class="card card-style">
        <div class="content py-3">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                    <i class="bi bi-calendar-check text-white font-16"></i>
                </div>
                <div>
                    <h4 class="font-700 mb-0 font-15">Status Absensi Hari Ini</h4>
                    <p class="mb-0 font-11 opacity-70">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>

            @if ($todayAttendance)
                <div class="row g-2">
                    <div class="col-6">
                        <div class="rounded-s py-2 px-2 {{ $todayAttendance->check_in ? 'bg-green-dark border-green-dark' : 'bg-gray-dark border-grey-dark' }}">
                            <div class="content text-center py-2">
                                <i class="bi bi-box-arrow-in-right text-white font-20 d-block mb-1"></i>
                                <h6 class="text-white mb-1 font-10">Check In</h6>
                                <p class="text-white mb-0 font-10 font-600">
                                    @if ($todayAttendance->check_in)
                                        {{ $todayAttendance->check_in->setTimezone('Asia/Jakarta')->format('H:i') }}
                                    @else
                                        --:--
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="rounded-s py-2 px-2 {{ $todayAttendance->check_out ? 'bg-blue-dark border-blue-dark' : 'bg-gray-dark border-grey-dark' }}">
                            <div class="content text-center py-2">
                                <i class="bi bi-box-arrow-right text-white font-20 d-block mb-1"></i>
                                <h6 class="text-white mb-1 font-10">Check Out</h6>
                                <p class="text-white mb-0 font-10 font-600">
                                    @if ($todayAttendance->check_out)
                                        {{ $todayAttendance->check_out->setTimezone('Asia/Jakarta')->format('H:i') }}
                                    @else
                                        --:--
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($todayAttendance->check_in && $todayAttendance->check_out)
                    <div class="alert bg-green-dark color-white rounded-s mt-2 mb-2" role="alert">
                        <div class="d-flex">
                            <div class="align-self-center">
                                <i class="bi bi-check-circle-fill pe-2 font-14"></i>
                            </div>
                            <div class="align-self-center">
                                <strong class="font-12">Absensi Lengkap!</strong><br>
                                <small class="font-10">Total jam kerja: {{ $todayAttendance->getWorkingHoursFormatted() }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Access Buttons -->
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('employee.attendance.history') }}" class="btn btn-full btn-s bg-blue-dark text-white text-uppercase font-600 rounded-s" style="min-height: 40px;">
                                <i class="bi bi-clock-history pe-1 font-12"></i>Riwayat
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('employee.attendance.index') }}" class="btn btn-full btn-s bg-green-dark text-white text-uppercase font-600 rounded-s" style="min-height: 40px;">
                                <i class="bi bi-camera pe-1 font-12"></i>Absensi
                            </a>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-3">
                    <i class="bi bi-exclamation-triangle color-orange-dark font-30 d-block mb-2"></i>
                    <h5 class="color-orange-dark mb-1 font-14">Belum Absen Hari Ini</h5>
                    <p class="mb-0 font-10 opacity-70">Silakan lakukan check in untuk memulai hari kerja Anda</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Attendance History -->
    <div class="card card-style">
        <div class="content py-3">
            <div class="mb-2">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <h4 class="font-700 mb-2 font-15 flex-shrink-1">Riwayat Absensi (7 Hari Terakhir)</h4>
                    <a href="{{ route('employee.attendance.history') }}" class="btn btn-xs bg-blue-dark text-white rounded-s font-10 px-2 py-1 flex-shrink-0">
                        <i class="bi bi-arrow-right me-1 font-9"></i>Semua
                    </a>
                </div>
            </div>
            @if ($recentAttendances->count() > 0)
                @foreach ($recentAttendances as $attendance)
                    <div class="d-flex py-2">
                        <div class="align-self-center">
                            <div class="bg-{{ $attendance->check_in && $attendance->check_out ? 'green' : ($attendance->check_in ? 'orange' : 'red') }}-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="bi bi-calendar-check text-white font-12"></i>
                            </div>
                        </div>
                        <div class="align-self-center ps-3 flex-grow-1">
                            <h6 class="mb-1 font-13">{{ $attendance->date->format('d M Y') }}</h6>
                            <p class="mb-0 font-10 opacity-70">
                                @if ($attendance->check_in && $attendance->check_out)
                                    {{ $attendance->check_in->setTimezone('Asia/Jakarta')->format('H:i') }} - {{ $attendance->check_out->setTimezone('Asia/Jakarta')->format('H:i') }}
                                @elseif($attendance->check_in)
                                    Check In: {{ $attendance->check_in->setTimezone('Asia/Jakarta')->format('H:i') }}
                                @else
                                    Tidak Hadir
                                @endif
                            </p>
                        </div>
                        <div class="align-self-center">
                            <span class="badge bg-{{ $attendance->check_in && $attendance->check_out ? 'green' : ($attendance->check_in ? 'orange' : 'red') }}-dark font-9 px-2 py-1">
                                @if ($attendance->check_in && $attendance->check_out)
                                    Lengkap
                                @elseif($attendance->check_in)
                                    Belum Out
                                @else
                                    Tidak Hadir
                                @endif
                            </span>
                        </div>
                    </div>
                    @if (!$loop->last)
                        <div class="divider my-1"></div>
                    @endif
                @endforeach
            @else
                <div class="text-center py-3">
                    <i class="bi bi-calendar-x color-theme font-30 d-block mb-2"></i>
                    <h5 class="color-theme mb-1 font-14">Belum Ada Data</h5>
                    <p class="font-11 opacity-70 mb-0">Belum ada data absensi dalam 7 hari terakhir</p>
                </div>
            @endif
        </div>
    </div>
@endsection
