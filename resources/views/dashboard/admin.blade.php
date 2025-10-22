@extends('layouts.app')

@section('title', 'Admin Dashboard - Aplikasi Absensi')

@section('header')
    <!-- Header -->
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-bs-toggle="offcanvas" data-bs-target="#menu-main" href="#"><i class="bi bi-list color-theme"></i></a>
        <a href="#" class="header-title color-theme">Admin Dashboard</a>
        <a href="#" class="show-on-theme-light" data-toggle-theme><i class="bi bi-moon-fill font-13"></i></a>
        <a href="#" class="show-on-theme-dark" data-toggle-theme><i class="bi bi-lightbulb-fill color-yellow-dark font-13"></i></a>
    </div>
@endsection

@section('footer')
    <!-- Footer Bar-->
    <div id="footer-bar" class="footer-bar footer-bar-detached">
        <a href="{{ route('dashboard') }}" class="active-nav"><i class="bi bi-house-fill font-16"></i><span>Dashboard</span></a>
        <a href="{{ route('admin.employees.index') }}"><i class="bi bi-people-fill font-16"></i><span>Karyawan</span></a>
        <a href="{{ route('admin.departments.index') }}"><i class="bi bi-building font-16"></i><span>Departemen</span></a>
        <a href="{{ route('admin.settings.index') }}"><i class="bi bi-gear-fill font-16"></i><span>Settings</span></a>
        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#menu-main"><i class="bi bi-list"></i><span>Menu</span></a>
    </div>
@endsection

@section('sidebar')
    <!-- Main Sidebar-->
    <div id="menu-main" class="offcanvas offcanvas-start offcanvas-detached rounded-m" style="width:280px;">
        <div class="content">
            <div class="d-flex pb-2">
                <div class="align-self-center">
                    <h1 class="mb-0">Menu</h1>
                </div>
                <div class="align-self-center ms-auto">
                    <a href="#" class="ps-4" data-bs-dismiss="offcanvas">
                        <i class="bi bi-x color-red-dark font-26 line-height-xl"></i>
                    </a>
                </div>
            </div>
            <div class="divider mb-2"></div>

            <a href="{{ route('dashboard') }}" class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-house-door color-blue-dark font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h5 class="pt-1 mb-0">Dashboard</h5>
                </div>
            </a>

            <a href="{{ route('admin.employees.index') }}" class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-people color-green-dark font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h5 class="pt-1 mb-0">Kelola Karyawan</h5>
                </div>
            </a>

            <a href="{{ route('admin.departments.index') }}" class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-building color-brown-dark font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h5 class="pt-1 mb-0">Departemen</h5>
                </div>
            </a>

            <a href="{{ route('admin.positions.index') }}" class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-briefcase color-teal-dark font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h5 class="pt-1 mb-0">Posisi</h5>
                </div>
            </a>

            <a href="{{ route('admin.roles.index') }}" class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-shield-check text-purple font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h5 class="pt-1 mb-0">Role & Permissions</h5>
                </div>
            </a>

            <a href="{{ route('admin.office-locations.index') }}" class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-geo-alt text-purple font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h5 class="pt-1 mb-0">Lokasi Kantor</h5>
                </div>
            </a>

            <a href="{{ route('admin.work-schedules.index') }}" class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-calendar-week color-blue-dark font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h5 class="pt-1 mb-0">Jadwal Kerja</h5>
                </div>
            </a>

            <a href="{{ route('admin.password-reset.index') }}" class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-key color-orange-dark font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h5 class="pt-1 mb-0">Reset Password</h5>
                </div>
                @if ($pendingResetRequests->count() > 0)
                    <div class="align-self-center ms-auto">
                        <span class="badge bg-red-dark font-11">{{ $pendingResetRequests->count() }}</span>
                    </div>
                @endif
            </a>

            <div class="divider my-2"></div>
            <h6 class="font-600 font-12 opacity-70 mb-2">Pengaturan</h6>

            <a href="{{ route('admin.settings.index') }}" class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-gear color-blue-dark font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h5 class="pt-1 mb-0">Pengaturan Umum</h5>
                </div>
            </a>

            <div class="divider my-2"></div>
            <h6 class="font-600 font-12 opacity-70 mb-2">Absensi & Laporan</h6>

            <a href="{{ route('admin.attendance.index') }}" class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-calendar-check color-orange-dark font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h5 class="pt-1 mb-0">Data Absensi</h5>
                </div>
                <div class="align-self-center ms-auto">
                    <span class="badge bg-green-dark rounded-xl">Real-time</span>
                </div>
            </a>

            <a href="{{ route('admin.attendance.reports.index') }}" class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-graph-up color-blue-dark font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h5 class="pt-1 mb-0">Laporan Absensi</h5>
                </div>
                <div class="align-self-center ms-auto">
                    <span class="badge bg-blue-dark rounded-xl">Analytics</span>
                </div>
            </a>

            <div class="divider mb-2"></div>
            <div class="text-center pb-2">
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger w-100">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Welcome Card -->
    <div class="card card-style shadow-m mb-4">
        <div class="content">
            <div class="d-flex align-items-start">
                <div class="align-self-start me-3">
                    <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center shadow-m" style="width: 70px; height: 70px;">
                        <i class="bi bi-shield-check color-white font-36"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h1 class="font-800 font-18 mb-1 color-dark-dark">Selamat Datang, Admin!</h1>
                    <h2 class="font-600 font-14 mb-2 color-blue-dark">Dashboard Sistem Absensi</h2>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-blue-dark rounded-xl font-10 px-3 py-2 me-2">
                            <i class="bi bi-shield-check me-1"></i>Administrator
                        </span>
                        <span class="badge bg-green-dark rounded-xl font-10 px-3 py-2">
                            <i class="bi bi-circle-fill me-1 font-8"></i>Online
                        </span>
                    </div>
                </div>
            </div>
            <div class="divider my-3 opacity-50"></div>
            <div class="row g-0">
                <div class="col-6">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-today color-blue-dark me-2 font-16"></i>
                        <div>
                            <p class="mb-0 font-13 font-700 color-dark-dark">{{ now()->locale('id')->translatedFormat('l') }}</p>
                            <p class="mb-0 font-11 opacity-70">{{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center justify-content-end">
                        <i class="bi bi-clock color-green-dark me-2 font-16"></i>
                        <div class="text-end">
                            <p class="mb-0 font-13 font-700 color-dark-dark">{{ now()->format('H:i') }} WIB</p>
                            <p class="mb-0 font-11 opacity-70">Waktu Server</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="card card-style shadow-m mb-4">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-orange-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-graph-up-arrow color-white font-18"></i>
                </div>
                <div>
                    <h4 class="font-800 mb-0 font-16 color-dark-dark">Statistik Sistem</h4>
                    <p class="mb-0 font-12 opacity-70">Data real-time hari ini</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <div class="bg-blue-dark rounded-m p-3 text-center shadow-s">
                        <i class="bi bi-people-fill color-white font-24 d-block mb-2"></i>
                        <h3 class="font-800 mb-1 color-white">{{ $totalEmployees }}</h3>
                        <p class="mb-0 font-11 color-white opacity-80">Total Karyawan</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-green-dark rounded-m p-3 text-center shadow-s">
                        <i class="bi bi-building color-white font-24 d-block mb-2"></i>
                        <h3 class="font-800 mb-1 color-white">{{ $totalDepartments }}</h3>
                        <p class="mb-0 font-11 color-white opacity-80">Departemen</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-orange-dark rounded-m p-3 text-center shadow-s">
                        <i class="bi bi-calendar-check color-white font-24 d-block mb-2"></i>
                        <h3 class="font-800 mb-1 color-white">{{ $presentToday }}</h3>
                        <p class="mb-0 font-11 color-white opacity-80">Hadir Hari Ini</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-yellow-dark rounded-m p-3 text-center shadow-s">
                        <i class="bi bi-clock-history color-white font-24 d-block mb-2"></i>
                        <h3 class="font-800 mb-1 color-white">{{ $todayAttendances }}</h3>
                        <p class="mb-0 font-11 color-white opacity-80">Total Absensi</p>
                    </div>
                </div>
            </div>

            <div class="divider my-3 opacity-50"></div>

            <div class="row g-3">
                <div class="col-6">
                    <div class="bg-teal-dark rounded-m p-2 text-center">
                        <i class="bi bi-briefcase color-white font-18 d-block mb-1"></i>
                        <h5 class="font-700 mb-0 color-white">{{ $totalPositions }}</h5>
                        <p class="mb-0 font-11 color-white opacity-80">Posisi</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-blue-dark rounded-m p-2 text-center">
                        <i class="bi bi-shield-check color-white font-18 d-block mb-1"></i>
                        <h5 class="font-700 mb-0 color-white">{{ $totalRoles }}</h5>
                        <p class="mb-0 font-11 color-white opacity-80">Role</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card card-style shadow-m mb-4">
        <div class="content">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-red-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-lightning-fill color-white font-18"></i>
                </div>
                <div>
                    <h4 class="font-800 mb-0 font-16 color-dark-dark">Aksi Cepat</h4>
                    <p class="mb-0 font-12 opacity-70">Fitur yang paling sering digunakan</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <a href="{{ route('admin.employees.index') }}" class="card card-style bg-blue-dark border-0 h-100 text-decoration-none shadow-s">
                        <div class="content text-center py-3">
                            <i class="bi bi-people-fill color-white font-28 d-block mb-2"></i>
                            <h6 class="mb-1 color-white font-700 font-13">Kelola Karyawan</h6>
                            <p class="mb-0 font-11 color-white opacity-80">Tambah & edit data</p>
                        </div>
                    </a>
                </div>

                <div class="col-6">
                    <a href="{{ route('admin.attendance.reports.index') }}" class="card card-style bg-green-dark border-0 h-100 text-decoration-none shadow-s">
                        <div class="content text-center py-3">
                            <i class="bi bi-graph-up color-white font-28 d-block mb-2"></i>
                            <h6 class="mb-1 color-white font-700 font-13">Laporan Absensi</h6>
                            <p class="mb-0 font-11 color-white opacity-80">Analytics & export</p>
                        </div>
                    </a>
                </div>

                <div class="col-6">
                    <a href="{{ route('admin.office-locations.index') }}" class="card card-style bg-secondary border-0 h-100 text-white text-decoration-none shadow-s">
                        <div class="content text-center py-3">
                            <i class="bi bi-geo-alt color-white font-28 d-block mb-2"></i>
                            <h6 class="mb-1 color-white font-700 font-13">Lokasi Kantor</h6>
                            <p class="mb-0 font-11 color-white opacity-80">Multi-lokasi absen</p>
                        </div>
                    </a>
                </div>

                <div class="col-6">
                    <a href="{{ route('admin.work-schedules.index') }}" class="card card-style bg-orange-dark border-0 h-100 text-decoration-none shadow-s">
                        <div class="content text-center py-3">
                            <i class="bi bi-calendar-week color-white font-28 d-block mb-2"></i>
                            <h6 class="mb-1 color-white font-700 font-13">Jadwal Kerja</h6>
                            <p class="mb-0 font-11 color-white opacity-80">Shift & scheduling</p>
                        </div>
                    </a>
                </div>

                <div class="divider my-3 opacity-50"></div>
            </div>


            <div class="row g-2">
                <div class="col-6">
                    <a href="{{ route('admin.departments.index') }}" class="d-flex align-items-center p-2 bg-brown-dark rounded-m text-decoration-none">
                        <i class="bi bi-building color-white font-20 me-3"></i>
                        <div>
                            <h6 class="mb-0 color-white font-600 font-12">Departemen</h6>
                            <p class="mb-0 font-11 color-white opacity-80">Organisasi</p>
                        </div>
                    </a>
                </div>

                <div class="col-6">
                    <a href="{{ route('admin.positions.index') }}" class="d-flex align-items-center p-2 bg-teal-dark rounded-m text-decoration-none">
                        <i class="bi bi-briefcase color-white font-20 me-3"></i>
                        <div>
                            <h6 class="mb-0 color-white font-600 font-12">Posisi</h6>
                            <p class="mb-0 font-11 color-white opacity-80">Jabatan</p>
                        </div>
                    </a>
                </div>

                <div class="col-6">
                    <a href="{{ route('admin.roles.index') }}" class="d-flex align-items-center p-2 bg-yellow-dark rounded-m text-white text-decoration-none">
                        <i class="bi bi-shield-check color-white font-20 me-3"></i>
                        <div>
                            <h6 class="mb-0 color-white font-600 font-12">Role</h6>
                            <p class="mb-0 font-11 color-white opacity-80">Permissions</p>
                        </div>
                    </a>
                </div>

                <div class="col-6">
                    <a href="{{ route('admin.settings.index') }}" class="d-flex align-items-center p-2 bg-orange-dark rounded-m text-decoration-none">
                        <i class="bi bi-gear-fill color-white font-20 me-3"></i>
                        <div>
                            <h6 class="mb-0 color-white font-600 font-12">Settings</h6>
                            <p class="mb-0 font-11 color-white opacity-80">Konfigurasi</p>
                        </div>
                    </a>
                </div>
            </div>

            @if ($pendingResetRequests->count() > 0)
                <div class="divider my-3 opacity-50"></div>
                <a href="{{ route('admin.password-reset.index') }}" class="card card-style bg-red-dark border-0 text-decoration-none shadow-s">
                    <div class="content py-3">
                        <div class="d-flex align-items-center">
                            <div class="align-self-center">
                                <i class="bi bi-exclamation-triangle-fill color-white font-24 me-3"></i>
                            </div>
                            <div class="align-self-center flex-grow-1">
                                <h6 class="mb-1 color-white font-700 font-13">Permintaan Reset Password</h6>
                                <p class="mb-0 font-11 color-white opacity-80">{{ $pendingResetRequests->count() }} permintaan menunggu persetujuan</p>
                            </div>
                            <div class="align-self-center">
                                <span class="badge bg-white color-red-dark font-12 px-3 py-2">{{ $pendingResetRequests->count() }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    </div>

    <!-- Monitoring Aktivitas & Log Sistem -->
    <div class="card card-style shadow-m mb-4">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-brown-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-activity color-white font-18"></i>
                </div>
                <div>
                    <h4 class="font-800 mb-0 font-16 color-dark-dark">Monitoring Aktivitas & Log Sistem</h4>
                    <p class="mb-0 font-12 opacity-70">Riwayat aktivitas dan log sistem terbaru</p>
                </div>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered table-striped mb-0">
                    <thead class="bg-blue-dark text-white">
                        <tr>
                            <th class="font-12">Waktu</th>
                            <th class="font-12">User</th>
                            <th class="font-12">Aksi</th>
                            <th class="font-12">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activityLogs as $log)
                            <tr>
                                <td class="font-11">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="font-11">{{ $log->user->name ?? '-' }}</td>
                                <td class="font-11">{{ $log->action }}</td>
                                <td class="font-11">{{ $log->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center font-12">Belum ada aktivitas terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped mb-0">
                    <thead class="bg-brown-dark text-white">
                        <tr>
                            <th class="font-12">Waktu</th>
                            <th class="font-12">Event</th>
                            <th class="font-12">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($systemLogs as $log)
                            <tr>
                                <td class="font-11">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="font-11">{{ $log->event }}</td>
                                <td class="font-11">{{ $log->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center font-12">Belum ada log sistem terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
