@extends('layouts.app')

@section('title', 'Detail Karyawan - Admin')

@section('header')
    <!-- Header -->
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('admin.employees.index') }}"><i class="bi bi-caret-left-fill font-11 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-13">Detail Karyawan</a>
        <a href="{{ route('admin.employees.edit', $employee) }}" class=""><i class="bi bi-pencil-fill font-13 color-highlight"></i></a>
    </div>
@endsection

@section('content')
    <!-- Employee Profile Card -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center">
                <div class="align-self-center">
                    <div class="position-relative">
                        <img src="{{ $employee->photo ? asset('storage/' . $employee->photo) : asset('template/images/avatars/5s.png') }}" width="80" height="80" class="rounded-circle me-3 border-4 border-{{ $employee->is_active ? 'green' : 'red' }}-dark">
                        <span class="position-absolute bottom-0 end-0 bg-{{ $employee->is_active ? 'green' : 'red' }}-dark border-2 border-white rounded-circle me-3" style="width: 24px; height: 24px;"></span>
                    </div>
                </div>
                <div class="align-self-center flex-grow-1">
                    <h1 class="font-700 font-20 mb-1">{{ $employee->full_name }}</h1>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-theme rounded-xl font-11 me-2" style="color:#fff !important;">{{ $employee->employee_id }}</span>
                        <span class="badge bg-highlight rounded-xl font-11" style="color:#fff !important;">{{ $employee->position_name ?? '-' }}</span>
                    </div>
                    <p class="mb-0 font-13 color-theme">
                        <i class="bi bi-building pe-1"></i>{{ optional($employee->department)->name ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Information -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-blue-light rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-info-circle color-blue-dark font-18"></i>
                </div>
                <h4 class="font-700 mb-0">Informasi Karyawan</h4>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="bg-gray-light rounded-s p-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-envelope color-blue-dark font-16 me-3"></i>
                            <div>
                                <h6 class="mb-0 font-13 color-theme">Email</h6>
                                <p class="mb-0 font-14 font-600 color-theme">{{ $employee->user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($employee->phone)
                    <div class="col-12 mb-3">
                        <div class="bg-gray-light rounded-s p-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-phone color-green-dark font-16 me-3"></i>
                                <div>
                                    <h6 class="mb-0 font-13 color-theme">No. Telepon</h6>
                                    <p class="mb-0 font-14 font-600 color-theme">{{ $employee->phone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-12 mb-3">
                    <div class="bg-gray-light rounded-s p-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-date color-orange-dark font-16 me-3"></i>
                            <div>
                                <h6 class="mb-0 font-13 color-theme">Tanggal Bergabung</h6>
                                <p class="mb-0 font-14 font-600 color-theme">{{ $employee->hire_date->format('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($employee->salary)
                    <div class="col-12 mb-3">
                        <div class="bg-gray-light rounded-s p-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-currency-dollar color-purple-dark font-16 me-3"></i>
                                <div>
                                    <h6 class="mb-0 font-13 color-theme">Gaji</h6>
                                    <p class="mb-0 font-14 font-600 color-theme">Rp {{ number_format($employee->salary, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-12">
                    <div class="bg-gray-light rounded-s p-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-{{ $employee->is_active ? 'check-circle' : 'x-circle' }} color-{{ $employee->is_active ? 'green' : 'red' }}-dark font-16 me-3"></i>
                            <div>
                                <h6 class="mb-0 font-13 color-theme">Status</h6>
                                <p class="mb-0 font-14 font-600 color-{{ $employee->is_active ? 'green' : 'red' }}-dark">
                                    {{ $employee->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance (if needed in future) -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-green-light rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-clock-history color-green-dark font-18"></i>
                </div>
                <h4 class="font-700 mb-0">Statistik Absensi</h4>
            </div>

            <div class="row text-center">
                <div class="col-4">
                    <div class="bg-blue-light p-3 rounded-s">
                        <i class="bi bi-calendar-check color-blue-dark font-20 d-block mb-2"></i>
                        <h6 class="mb-1 color-blue-dark font-600">Bulan Ini</h6>
                        <p class="mb-0 font-12 opacity-70">Coming Soon</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-green-light p-3 rounded-s">
                        <i class="bi bi-check-circle color-green-dark font-20 d-block mb-2"></i>
                        <h6 class="mb-1 color-green-dark font-600">Hadir</h6>
                        <p class="mb-0 font-12 opacity-70">Coming Soon</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-red-light p-3 rounded-s">
                        <i class="bi bi-x-circle color-red-dark font-20 d-block mb-2"></i>
                        <h6 class="mb-1 color-red-dark font-600">Tidak Hadir</h6>
                        <p class="mb-0 font-12 opacity-70">Coming Soon</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card card-style">
        <div class="content">
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-full rounded-s bg-highlight shadow-bg shadow-bg-s font-700 text-uppercase mb-2 w-100" style="background-color: #ff9800 !important; color: #fff !important; border: none !important;">
                        <i class="bi bi-pencil pe-2"></i>Edit
                    </a>
                </div>
                <div class="col-12 col-md-6">
                    <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')" class="d-inline w-100">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-full rounded-s bg-red-dark font-700 text-uppercase mb-2 w-100" style="background-color: #d32f2f !important; color: #fff !important; border: none !important;">
                            <i class="bi bi-trash pe-2"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
