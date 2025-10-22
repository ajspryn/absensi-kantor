@extends('layouts.app')

@section('title', 'Analytics Karyawan - Admin')

@section('header')
    <!-- Header -->
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('admin.employees.index') }}"><i class="bi bi-caret-left-fill font-11 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-13">Analytics Karyawan</a>
        <a href="#" class="show-on-theme-light" data-toggle-theme><i class="bi bi-moon-fill font-13"></i></a>
    </div>
@endsection

@section('content')
    <!-- Overview Cards -->
    <div class="row mb-3">
        <div class="col-6">
            <div class="card card-style shadow-xl">
                <div class="content text-center py-3">
                    <h3 class="font-700 mb-0 color-highlight">{{ $analyticsData['total_employees'] }}</h3>
                    <p class="mb-0 font-11 color-theme">Total Karyawan</p>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-style shadow-xl">
                <div class="content text-center py-3">
                    <h3 class="font-700 mb-0 color-green-dark">{{ $analyticsData['active_employees'] }}</h3>
                    <p class="mb-0 font-11 color-theme">Karyawan Aktif</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-6">
            <div class="card card-style shadow-xl">
                <div class="content text-center py-3">
                    <h3 class="font-700 mb-0 color-red-dark">{{ $analyticsData['inactive_employees'] }}</h3>
                    <p class="mb-0 font-11 color-theme">Tidak Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-style shadow-xl">
                <div class="content text-center py-3">
                    <h3 class="font-700 mb-0 color-blue-dark">{{ $analyticsData['recent_hires']->count() }}</h3>
                    <p class="mb-0 font-11 color-theme">Baru (3 bulan)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Distribution -->
    <div class="card card-style mb-3">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                    <i class="bi bi-building color-white font-16"></i>
                </div>
                <div>
                    <h5 class="font-700 mb-0 font-15">Distribusi per Departemen</h5>
                    <p class="mb-0 font-11 color-white-50">Jumlah karyawan di setiap departemen</p>
                </div>
            </div>

            @if ($analyticsData['by_department']->isNotEmpty())
                @foreach ($analyticsData['by_department'] as $department => $count)
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-highlight rounded-circle me-3" style="width: 8px; height: 8px;"></div>
                            <span class="font-13">{{ $department ?: 'Tidak ada departemen' }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="font-700 font-14 color-highlight me-2">{{ $count }}</span>
                            <span class="font-11 color-theme">karyawan</span>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center color-theme opacity-70 mb-0">Belum ada data departemen</p>
            @endif
        </div>
    </div>

    <!-- Position Distribution -->
    <div class="card card-style mb-3">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                    <i class="bi bi-briefcase color-white font-16"></i>
                </div>
                <div>
                    <h5 class="font-700 mb-0 font-15">Distribusi per Posisi</h5>
                    <p class="mb-0 font-11 color-white-50">Jumlah karyawan di setiap posisi</p>
                </div>
            </div>

            @if ($analyticsData['by_position']->isNotEmpty())
                @foreach ($analyticsData['by_position'] as $position => $count)
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-green-dark rounded-circle me-3" style="width: 8px; height: 8px;"></div>
                            <span class="font-13">{{ $position ?: 'Tidak ada posisi' }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="font-700 font-14 color-green-dark me-2">{{ $count }}</span>
                            <span class="font-11 color-theme">karyawan</span>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center color-theme opacity-70 mb-0">Belum ada data posisi</p>
            @endif
        </div>
    </div>

    <!-- Role Distribution -->
    <div class="card card-style mb-3">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-red-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                    <i class="bi bi-shield-check color-white font-16"></i>
                </div>
                <div>
                    <h5 class="font-700 mb-0 font-15">Distribusi per Role</h5>
                    <p class="mb-0 font-11 color-white-50">Jumlah karyawan di setiap role</p>
                </div>
            </div>

            @if ($analyticsData['by_role']->isNotEmpty())
                @foreach ($analyticsData['by_role'] as $role => $count)
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-red-dark rounded-circle me-3" style="width: 8px; height: 8px;"></div>
                            <span class="font-13">{{ $role ?: 'Tidak ada role' }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="font-700 font-14 color-red-dark me-2">{{ $count }}</span>
                            <span class="font-11 color-theme">karyawan</span>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center color-theme opacity-70 mb-0">Belum ada data role</p>
            @endif
        </div>
    </div>

    <!-- Recent Hires -->
    @if ($analyticsData['recent_hires']->isNotEmpty())
        <div class="card card-style mb-3">
            <div class="content">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-orange-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-plus color-white font-16"></i>
                    </div>
                    <div>
                        <h5 class="font-700 mb-0 font-15">Karyawan Baru (3 Bulan Terakhir)</h5>
                        <p class="mb-0 font-11 color-white-50">Daftar karyawan yang baru bergabung</p>
                    </div>
                </div>

                @foreach ($analyticsData['recent_hires'] as $employee)
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            @if ($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" class="rounded-circle border-2 border-theme" style="width: 40px; height: 40px; object-fit: cover;" alt="Profile">
                            @else
                                <div class="bg-highlight rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person color-white font-14"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="font-600 mb-0 font-13">{{ $employee->full_name }}</h6>
                            <div class="font-11 color-theme opacity-80">
                                <span>{{ $employee->getFullPositionName() }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ \Carbon\Carbon::parse($employee->hire_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Export Options -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-purple-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                    <i class="bi bi-download color-white font-16"></i>
                </div>
                <div>
                    <h5 class="font-700 mb-0 font-15">Export Data</h5>
                    <p class="mb-0 font-11 color-white-50">Download laporan karyawan</p>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <a href="#" class="btn btn-full rounded-s bg-green-dark shadow-bg shadow-bg-s font-600 text-uppercase mb-2">
                        <i class="bi bi-file-earmark-excel pe-2"></i>Excel
                    </a>
                </div>
                <div class="col-6">
                    <a href="#" class="btn btn-full rounded-s bg-red-dark shadow-bg shadow-bg-s font-600 text-uppercase mb-2">
                        <i class="bi bi-file-earmark-pdf pe-2"></i>PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
