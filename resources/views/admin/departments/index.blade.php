@extends('layouts.admin')

@section('title', 'Kelola Departemen - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Departemen',
        'backUrl' => route('dashboard'),
        'rightHtml' => '<a href="' . route('admin.departments.create') . '" class="me-1"><i class="bi bi-plus-circle-fill font-13 color-highlight"></i></a>',
    ])
@endsection

@section('content')
    @include('admin.partials.alerts')

    @include('admin.partials.section-header', [
        'title' => 'Departemen',
        'subtitle' => 'Kelola struktur departemen dan manager',
        'icon' => 'bi bi-building',
    ])

    @include('admin.partials.filters', [
        'action' => route('admin.departments.index'),
        'method' => 'GET',
        'fields' => [['type' => 'select', 'name' => 'active', 'label' => 'Status', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif'], 'col' => 6]],
        'submitLabel' => 'Terapkan',
    ])

    <!-- Statistics Cards -->
    <div class="card card-style shadow-m mb-4">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-graph-up-arrow color-white font-18"></i>
                </div>
                <div>
                    <h4 class="font-700 mb-0 color-dark-dark">Statistik Departemen</h4>
                    <p class="mb-0 font-12 opacity-70">Ringkasan data departemen dan karyawan</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <div class="bg-blue-light rounded-s p-3 text-center">
                        <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                            <i class="bi bi-building color-white font-16"></i>
                        </div>
                        <h5 class="font-700 mb-0 color-blue-dark">{{ $totalDepartments }}</h5>
                        <p class="mb-0 font-10 color-blue-dark opacity-70">Total Departemen</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-green-light rounded-s p-3 text-center">
                        <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                            <i class="bi bi-check-circle color-white font-16"></i>
                        </div>
                        <h5 class="font-700 mb-0 color-green-dark">{{ $activeDepartments }}</h5>
                        <p class="mb-0 font-10 color-green-dark opacity-70">Departemen Aktif</p>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-6">
                    <div class="bg-orange-light rounded-s p-3 text-center">
                        <div class="bg-orange-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                            <i class="bi bi-people color-white font-16"></i>
                        </div>
                        <h5 class="font-700 mb-0 color-orange-dark">{{ $totalEmployees }}</h5>
                        <p class="mb-0 font-10 color-orange-dark opacity-70">Total Karyawan</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-purple-light rounded-s p-3 text-center">
                        <div class="bg-purple-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                            <i class="bi bi-person-badge color-white font-16"></i>
                        </div>
                        <h5 class="font-700 mb-0 color-purple-dark">{{ $departmentsWithManager }}</h5>
                        <p class="mb-0 font-10 color-purple-dark opacity-70">Ada Manager</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department List -->
    @if ($departments->isEmpty())
        @include('admin.partials.empty', [
            'icon' => 'bi bi-building',
            'title' => 'Belum Ada Departemen',
            'text' => 'Mulai dengan menambahkan departemen pertama untuk mengorganisir karyawan',
            'actionUrl' => route('admin.departments.create'),
            'actionLabel' => 'Tambah Departemen Pertama',
            'actionIcon' => 'bi bi-plus-circle',
        ])
    @else
        @foreach ($departments as $department)
            <div class="card card-style shadow-m mb-3 entity-card {{ $department->is_active ? 'active' : 'inactive' }}">
                <div class="content">
                    <div class="d-flex align-items-center">
                        <!-- Department Icon -->
                        <div class="me-3">
                            <div class="bg-{{ $department->is_active ? 'blue' : 'gray' }}-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-building color-white font-18"></i>
                            </div>
                        </div>

                        <!-- Department Info -->
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <h6 class="font-700 mb-0 font-14">{{ $department->name }}</h6>
                                <div class="d-flex align-items-center">
                                    @if ($department->is_active)
                                        <span class="badge bg-green-dark color-white font-10 rounded-xl">Aktif</span>
                                    @else
                                        <span class="badge bg-gray-dark color-white font-10 rounded-xl">Non-Aktif</span>
                                    @endif
                                    <div class="dropdown ms-2">
                                        <button class="btn btn-s border-0 bg-secondary text-white" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical color-theme font-16"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('admin.departments.show', $department) }}"><i class="bi bi-eye pe-2"></i>Lihat Detail</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.departments.edit', $department) }}"><i class="bi bi-pencil pe-2"></i>Edit</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.departments.toggle-status', $department) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item">
                                                        @if ($department->is_active)
                                                            <i class="bi bi-eye-slash pe-2"></i>Nonaktifkan
                                                        @else
                                                            <i class="bi bi-eye pe-2"></i>Aktifkan
                                                        @endif
                                                    </button>
                                                </form>
                                            </li>
                                            @if ($department->employees_count == 0)
                                                <li>
                                                    <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" onsubmit="return confirm('Yakin hapus departemen ini?')" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash pe-2"></i>Hapus</button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            @if ($department->description)
                                <p class="mb-1 font-11 color-theme opacity-80">{{ Str::limit($department->description, 80) }}</p>
                            @endif

                            <div class="row font-11 color-theme opacity-80">
                                <div class="col-12 mb-1">
                                    <i class="bi bi-people pe-1"></i>{{ $department->employees_count }} karyawan
                                    @if ($department->manager && optional($department->manager)->name)
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-person-badge pe-1 color-green-dark"></i>
                                        <span class="color-green-dark">Manager: {{ optional($department->manager)->name }}</span>
                                    @else
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-exclamation-triangle pe-1 color-orange-dark"></i>
                                        <span class="color-orange-dark">Belum ada manager</span>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <i class="bi bi-calendar pe-1"></i>Dibuat: {{ $department->created_at->format('d M Y') }}
                                    @if ($department->updated_at != $department->created_at)
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-pencil pe-1"></i>Update: {{ $department->updated_at->format('d M Y') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    @if ($departments->hasPages())
        <div class="card card-style">
            <div class="content">
                {{ $departments->links() }}
            </div>
        </div>
    @endif

    <!-- Add Button (Floating) -->
    <a href="{{ route('admin.departments.create') }}" class="btn bg-highlight rounded-circle shadow-bg shadow-bg-s position-fixed" style="bottom: 100px; right: 20px; width: 56px; height: 56px; z-index: 999;">
        <i class="bi bi-plus color-white font-20"></i>
    </a>
@endsection
