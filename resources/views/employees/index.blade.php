@extends('layouts.app')

@section('title', 'Kelola Karyawan - Admin')

@section('header')
    <!-- Header -->
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-caret-left-fill font-11 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-13">Kelola Karyawan</a>
        <div class="d-flex">
            @canDo('employees.create')
            <a href="{{ route('admin.employees.create') }}" class=""><i class="bi bi-person-plus-fill font-13 color-highlight"></i></a>
            @endCanDo
        </div>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <div class="content">
            <div class="alert bg-green-dark alert-dismissible color-white rounded-s fade show pe-2 mb-3" role="alert">
                <strong>Success:</strong> {{ session('success') }}
                <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="content">
            <div class="alert bg-red-dark alert-dismissible color-white rounded-s fade show pe-2 mb-3" role="alert">
                <strong>Error:</strong> {{ session('error') }}
                <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="card card-style shadow-m mb-4">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-highlight rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-people-fill color-white font-18"></i>
                </div>
                <div>
                    <h4 class="font-700 mb-0 color-dark-dark">Statistik Karyawan</h4>
                    <p class="mb-0 font-12 opacity-70">Ringkasan data dan status karyawan</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-4">
                    <div class="bg-highlight-dark rounded-s p-3 text-center">
                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 35px; height: 35px;">
                            <i class="bi bi-people color-highlight font-14"></i>
                        </div>
                        <h6 class="font-700 mb-0 color-white">{{ $stats['total'] }}</h6>
                        <p class="mb-0 font-9 color-white opacity-70">Total</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-green-dark rounded-s p-3 text-center">
                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 35px; height: 35px;">
                            <i class="bi bi-check-circle color-green-dark font-14"></i>
                        </div>
                        <h6 class="font-700 mb-0 color-white">{{ $stats['active'] }}</h6>
                        <p class="mb-0 font-9 color-white opacity-70">Aktif</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-blue-dark rounded-s p-3 text-center">
                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 35px; height: 35px;">
                            <i class="bi bi-briefcase color-blue-dark font-14"></i>
                        </div>
                        <h6 class="font-700 mb-0 color-white">{{ $stats['with_position'] }}</h6>
                        <p class="mb-0 font-9 color-white opacity-70">Ada Posisi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card card-style shadow-m">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-orange-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-tools color-white font-18"></i>
                </div>
                <div>
                    <h4 class="font-700 mb-0 color-dark-dark">Aksi Karyawan</h4>
                    <p class="mb-0 font-12 opacity-70">Kelola dan tambah data karyawan</p>
                </div>
            </div>

            <div class="row g-2">
                @canDo('employees.create')
                <div class="col-6">
                    <a href="{{ route('admin.employees.create') }}" class="btn btn-full rounded-s gradient-green text-uppercase font-600 shadow-bg shadow-bg-s">
                        <i class="bi bi-person-plus-fill me-2"></i>Tambah Karyawan
                    </a>
                </div>
                @endCanDo
                @canDo('employees.create')
                <div class="col-6">
                    <a href="{{ route('admin.employees.import') }}" class="btn btn-full rounded-s gradient-blue text-uppercase font-600 shadow-bg shadow-bg-s">
                        <i class="bi bi-file-earmark-excel me-2"></i>Import Excel
                    </a>
                </div>
                @endCanDo
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card card-style shadow-m">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-funnel color-white font-18"></i>
                </div>
                <div>
                    <h4 class="font-700 mb-0 color-dark-dark">Filter & Pencarian</h4>
                    <p class="mb-0 font-12 opacity-70">Cari dan filter data karyawan</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.employees.index') }}">
                <!-- Search -->
                <div class="form-custom form-label form-icon mb-3">
                    <i class="bi bi-search font-14"></i>
                    <input type="text" class="form-control rounded-s" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau ID karyawan..." style="min-height: 45px;" />
                    <label class="color-theme font-11">Pencarian</label>
                </div>

                <!-- Filter Row -->
                <div class="row">
                    <div class="col-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-building font-14"></i>
                            <select class="form-control rounded-s" name="department_id" style="min-height: 45px;">
                                <option value="">Semua Departemen</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="color-theme font-11">Departemen</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-briefcase font-14"></i>
                            <select class="form-control rounded-s" name="position_id" style="min-height: 45px;">
                                <option value="">Semua Posisi</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="color-theme font-11">Posisi</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-shield-check font-14"></i>
                            <select class="form-control rounded-s" name="role_id" style="min-height: 45px;">
                                <option value="">Semua Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="color-theme font-11">Role</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-power font-14"></i>
                            <select class="form-control rounded-s" name="status" style="min-height: 45px;">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            <label class="color-theme font-11">Status</label>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <button type="submit" class="btn btn-full rounded-s bg-dark shadow-bg shadow-bg-s font-600 text-uppercase w-100 mb-2 mb-md-0">
                            <i class="bi bi-funnel pe-2"></i>Filter
                        </button>
                    </div>
                    <div class="col-12 col-md-6">
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-full rounded-s bg-dark shadow-bg shadow-bg-s font-600 text-uppercase w-100">
                            <i class="bi bi-x-circle pe-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($employees->isEmpty())
        <div class="card card-style">
            <div class="content text-center py-5">
                <div class="bg-blue-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                    <i class="bi bi-people color-blue-dark font-50"></i>
                </div>
                <h4 class="font-700 mb-2">
                    @if (request()->hasAny(['search', 'department_id', 'position_id', 'role_id', 'status']))
                        Tidak Ada Hasil
                    @else
                        Belum Ada Karyawan
                    @endif
                </h4>
                <p class="color-theme opacity-70 mb-4 font-14">
                    @if (request()->hasAny(['search', 'department_id', 'position_id', 'role_id', 'status']))
                        Tidak ditemukan karyawan dengan kriteria yang dipilih
                    @else
                        Mulai dengan menambahkan karyawan pertama untuk sistem absensi Anda
                    @endif
                </p>
                @if (!request()->hasAny(['search', 'department_id', 'position_id', 'role_id', 'status']))
                    <a href="{{ route('admin.employees.create') }}" class="btn btn-l bg-highlight text-uppercase font-700 rounded-s shadow-bg shadow-bg-s">
                        <i class="bi bi-person-plus pe-2 font-16"></i>Tambah Karyawan Pertama
                    </a>
                @endif
            </div>
        </div>
    @else
        <!-- Employee List -->
        @foreach ($employees as $employee)
            <div class="card card-style shadow-xl mb-3">
                <div class="content">
                    <div class="row g-2 align-items-center">
                        <!-- Photo -->
                        <div class="col-auto">
                            @if ($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" class="rounded-circle border-2 border-theme" style="width: 48px; height: 48px; object-fit: cover;" alt="Profile">
                            @else
                                <div class="bg-highlight rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="bi bi-person color-white font-18"></i>
                                </div>
                            @endif
                        </div>
                        <!-- Employee Info -->
                        <div class="col ps-0">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-1 gap-2">
                                <h6 class="font-700 mb-0 font-14">{{ $employee->full_name }}</h6>
                                <div class="d-flex align-items-center gap-2">
                                    {!! $employee->getStatusBadge() !!}
                                    <div class="dropdown">
                                        <button class="btn btn-s border-0 bg-dark text-white" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical font-16"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('admin.employees.show', $employee) }}"><i class="bi bi-eye pe-2"></i>Lihat Detail</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.employees.edit', $employee) }}"><i class="bi bi-pencil pe-2"></i>Edit</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Yakin hapus karyawan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash pe-2"></i>Hapus</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row font-11 color-theme opacity-80">
                                <div class="col-12 mb-1">
                                    <i class="bi bi-person-badge pe-1"></i>{{ $employee->employee_id }}
                                    <span class="mx-2">•</span>
                                    <i class="bi bi-envelope pe-1"></i>{{ $employee->user->email }}
                                </div>
                                <div class="col-12 mb-1">
                                    <i class="bi bi-briefcase pe-1"></i>{{ $employee->getFullPositionName() }}
                                </div>
                                <div class="col-12">
                                    <i class="bi bi-shield-check pe-1"></i>{{ $employee->getRoleName() }}
                                    @if ($employee->phone)
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-telephone pe-1"></i>{{ $employee->phone }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        @if ($employees->hasPages())
            <div class="card card-style">
                <div class="content">
                    <div class="row align-items-center g-2">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <p class="mb-0 font-11 color-theme text-center text-md-start">
                                Menampilkan {{ $employees->firstItem() }}-{{ $employees->lastItem() }} dari {{ $employees->total() }} karyawan
                            </p>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex justify-content-center justify-content-md-end gap-2">
                                @if ($employees->onFirstPage())
                                    <span class="btn btn-s bg-theme rounded-s opacity-30"><i class="bi bi-chevron-left"></i></span>
                                @else
                                    <a href="{{ $employees->appends(request()->query())->previousPageUrl() }}" class="btn btn-s bg-highlight rounded-s"><i class="bi bi-chevron-left"></i></a>
                                @endif
                                @if ($employees->hasMorePages())
                                    <a href="{{ $employees->appends(request()->query())->nextPageUrl() }}" class="btn btn-s bg-highlight rounded-s"><i class="bi bi-chevron-right"></i></a>
                                @else
                                    <span class="btn btn-s bg-theme rounded-s opacity-30"><i class="bi bi-chevron-right"></i></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

@endsection
