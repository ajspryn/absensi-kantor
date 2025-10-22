@extends('layouts.admin')

@section('title', 'Kelola Posisi - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Posisi',
        'backUrl' => route('dashboard'),
        'rightHtml' => '<a href="' . route('admin.positions.create') . '" class="me-1"><i class="bi bi-plus-circle-fill font-13 color-highlight"></i></a>',
    ])
@endsection

@section('content')
    @include('admin.partials.alerts')

    @include('admin.partials.section-header', [
        'title' => 'Posisi',
        'subtitle' => 'Kelola posisi jabatan dan struktur organisasi',
        'icon' => 'bi bi-briefcase',
    ])

    @include('admin.partials.filters', [
        'action' => route('admin.positions.index'),
        'method' => 'GET',
        'fields' => [['type' => 'select', 'name' => 'department_id', 'label' => 'Departemen', 'options' => $departmentsList ?? [], 'col' => 6], ['type' => 'select', 'name' => 'active', 'label' => 'Status', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif'], 'col' => 6]],
        'submitLabel' => 'Terapkan',
    ])

    <!-- Statistics Cards -->
    <div class="row mb-3">
        <div class="col-6">
            <div class="card card-style shadow-xl">
                <div class="content text-center py-3">
                    <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                        <i class="bi bi-briefcase color-white font-16"></i>
                    </div>
                    <h5 class="font-700 mb-0 color-green-dark">{{ $totalPositions }}</h5>
                    <p class="mb-0 font-10 color-theme">Total Posisi</p>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-style shadow-xl">
                <div class="content text-center py-3">
                    <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                        <i class="bi bi-check-circle color-white font-16"></i>
                    </div>
                    <h5 class="font-700 mb-0 color-blue-dark">{{ $activePositions }}</h5>
                    <p class="mb-0 font-10 color-theme">Aktif</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-6">
            <div class="card card-style shadow-xl">
                <div class="content text-center py-3">
                    <div class="bg-orange-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                        <i class="bi bi-people color-white font-16"></i>
                    </div>
                    <h5 class="font-700 mb-0 color-orange-dark">{{ $totalEmployees }}</h5>
                    <p class="mb-0 font-10 color-theme">Total Karyawan</p>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-style shadow-xl">
                <div class="content text-center py-3">
                    <div class="bg-purple-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                        <i class="bi bi-award color-white font-16"></i>
                    </div>
                    <h5 class="font-700 mb-0 color-purple-dark">{{ $positionsWithEmployees }}</h5>
                    <p class="mb-0 font-10 color-theme">Posisi Terisi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Position List -->
    @if ($positions->isEmpty())
        @include('admin.partials.empty', [
            'icon' => 'bi bi-briefcase',
            'title' => 'Belum Ada Posisi',
            'text' => 'Mulai dengan menambahkan posisi pekerjaan pertama',
            'actionUrl' => route('admin.positions.create'),
            'actionLabel' => 'Tambah Posisi Pertama',
            'actionIcon' => 'bi bi-plus-circle',
        ])
    @else
        @foreach ($positions as $position)
            <div class="card card-style shadow-xl mb-3 entity-card {{ $position->is_active ? 'active' : 'inactive' }}">
                <div class="content">
                    <div class="d-flex align-items-center">
                        <!-- Position Icon -->
                        <div class="me-3">
                            <div class="bg-{{ $position->is_active ? 'green' : 'gray' }}-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-briefcase color-white font-18"></i>
                            </div>
                        </div>

                        <!-- Position Info -->
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <h6 class="font-700 mb-0 font-14">{{ $position->name }}</h6>
                                <div class="d-flex align-items-center">
                                    @if ($position->is_active)
                                        <span class="badge bg-green-dark color-white font-10 rounded-xl">Aktif</span>
                                    @else
                                        <span class="badge bg-gray-dark color-white font-10 rounded-xl">Non-Aktif</span>
                                    @endif
                                    <div class="dropdown ms-2">
                                        <button class="btn btn-s border-0 bg-secondary text-white" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical color-theme font-16"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('admin.positions.show', $position) }}"><i class="bi bi-eye pe-2"></i>Lihat Detail</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.positions.edit', $position) }}"><i class="bi bi-pencil pe-2"></i>Edit</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.positions.toggle-status', $position) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item">
                                                        @if ($position->is_active)
                                                            <i class="bi bi-eye-slash pe-2"></i>Nonaktifkan
                                                        @else
                                                            <i class="bi bi-eye pe-2"></i>Aktifkan
                                                        @endif
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                @php
                                                    $confirmMessage = $position->employees_count > 0 ? 'Yakin hapus posisi ini? Semua karyawan akan dilepaskan (' . $position->employees_count . ' karyawan).' : 'Yakin hapus posisi ini?';
                                                    $btnLabel = $position->employees_count > 0 ? 'Hapus & Lepaskan Karyawan' : 'Hapus';
                                                @endphp
                                                <form action="{{ route('admin.positions.destroy', $position) }}" method="POST" onsubmit="return confirm('{{ $confirmMessage }}')" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash pe-2"></i>{{ $btnLabel }}</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            @if ($position->description)
                                <p class="mb-1 font-11 color-theme opacity-80">{{ Str::limit($position->description, 80) }}</p>
                            @endif

                            <div class="row font-11 color-theme opacity-80">
                                <div class="col-12 mb-1">
                                    <i class="bi bi-people pe-1"></i>{{ $position->employees_count }} karyawan
                                    @if ($position->department)
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-building pe-1 color-blue-dark"></i>
                                        <span class="color-blue-dark">{{ optional($position->department)->name ?? '-' }}</span>
                                    @endif
                                </div>
                                @if ($position->min_salary || $position->max_salary)
                                    <div class="col-12 mb-1">
                                        <i class="bi bi-currency-dollar pe-1 color-purple-dark"></i>
                                        <span class="color-purple-dark">
                                            @if ($position->min_salary && $position->max_salary)
                                                Rp {{ number_format($position->min_salary, 0, ',', '.') }} - Rp {{ number_format($position->max_salary, 0, ',', '.') }}
                                            @elseif($position->min_salary)
                                                Min: Rp {{ number_format($position->min_salary, 0, ',', '.') }}
                                            @else
                                                Max: Rp {{ number_format($position->max_salary, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <i class="bi bi-calendar pe-1"></i>Dibuat: {{ $position->created_at->format('d M Y') }}
                                    @if ($position->updated_at != $position->created_at)
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-pencil pe-1"></i>Update: {{ $position->updated_at->format('d M Y') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    @if ($positions->hasPages())
        <div class="card card-style">
            <div class="content">
                {{ $positions->appends(request()->query())->links('pagination.mobile') }}
            </div>
        </div>
    @endif

    <!-- Add Button (Floating) -->
    <a href="{{ route('admin.positions.create') }}" class="btn bg-highlight rounded-circle shadow-bg shadow-bg-s position-fixed" style="bottom: 100px; right: 20px; width: 56px; height: 56px; z-index: 999;">
        <i class="bi bi-plus color-white font-20"></i>
    </a>
@endsection
