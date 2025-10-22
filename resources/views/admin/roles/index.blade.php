@extends('layouts.admin')

@section('title', 'Kelola Role - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Kelola Role',
        'backUrl' => route('dashboard'),
        'rightHtml' => auth()->user() && auth()->user()->canDo('roles.create') ? '<a href="' . route('admin.roles.create') . '"><i class="bi bi-shield-plus-fill font-13 color-highlight"></i></a>' : '',
    ])
@endsection

@section('content')
    @include('admin.partials.alerts')

    @include('admin.partials.section-header', [
        'title' => 'Role & Permissions',
        'subtitle' => 'Kelola role pengguna, level prioritas, dan hak akses',
        'icon' => 'bi bi-shield-lock',
    ])

    @include('admin.partials.filters', [
        'action' => route('admin.roles.index'),
        'method' => 'GET',
        'fields' => [['type' => 'text', 'name' => 'search', 'label' => 'Cari', 'placeholder' => 'Nama role atau deskripsi', 'col' => 12], ['type' => 'select', 'name' => 'active', 'label' => 'Status', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif'], 'col' => 6]],
        'submitLabel' => 'Terapkan',
    ])

    <!-- Statistics Cards -->
    <div class="card card-style shadow-m mb-4">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-purple rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-shield-check color-white font-18"></i>
                </div>
                <div>
                    <h4 class="font-700 mb-0 color-dark-dark">Statistik Role</h4>
                    <p class="mb-0 font-12 opacity-70">Ringkasan data role dan permissions</p>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-4">
                    <div class="bg-purple rounded-s p-3 text-center">
                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 35px; height: 35px;">
                            <i class="bi bi-shield-check color-purple font-14"></i>
                        </div>
                        <h6 class="font-700 mb-0 text-white">{{ $totalRoles }}</h6>
                        <p class="mb-0 font-9 text-white opacity-70">Total Role</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-green-dark rounded-s p-3 text-center">
                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 35px; height: 35px;">
                            <i class="bi bi-check-circle-fill color-green-dark font-14"></i>
                        </div>
                        <h6 class="font-700 mb-0 text-white">{{ $activeRoles }}</h6>
                        <p class="mb-0 font-9 text-white opacity-70">Aktif</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-blue-dark rounded-s p-3 text-center">
                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 35px; height: 35px;">
                            <i class="bi bi-people-fill color-blue-dark font-14"></i>
                        </div>
                        <h6 class="font-700 mb-0 text-white">{{ $totalUsers }}</h6>
                        <p class="mb-0 font-9 text-white opacity-70">User</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($roles->isEmpty())
        @include('admin.partials.empty', [
            'icon' => 'bi bi-shield-check',
            'title' => request()->has('search') ? 'Tidak Ada Hasil' : 'Belum Ada Role',
            'text' => request()->has('search') ? 'Tidak ditemukan role dengan kriteria yang dipilih' : 'Mulai dengan menambahkan role pertama untuk sistem Anda',
            'actionUrl' => !request()->has('search') && auth()->user() && auth()->user()->canDo('roles.create') ? route('admin.roles.create') : null,
            'actionLabel' => !request()->has('search') && auth()->user() && auth()->user()->canDo('roles.create') ? 'Tambah Role Pertama' : null,
            'actionIcon' => 'bi bi-shield-plus',
        ])
    @else
        <!-- Role List -->
        @foreach ($roles as $role)
            <div class="card card-style shadow-m mb-3 entity-card {{ $role->is_active ? 'active' : 'inactive' }}">
                <div class="content">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <div class="bg-purple rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-shield-check text-white font-18"></i>
                            </div>
                        </div>
                        <div class="col ps-0">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-1 gap-2">
                                <h6 class="font-700 mb-0 font-14">{{ $role->name }}</h6>
                                <div class="d-flex align-items-center gap-2">
                                    @if (!$role->is_active)
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                    @if ($role->is_default)
                                        <span class="badge bg-success">Default</span>
                                    @endif
                                    @if ($role->is_system_role)
                                        <span class="badge bg-warning text-dark">System</span>
                                    @endif
                                    <span class="badge bg-info">Level {{ $role->priority }}</span>
                                    <div class="dropdown">
                                        <button class="btn btn-s border-0 bg-dark text-white" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical font-16"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @canDo('roles.view')
                                            <li><a class="dropdown-item" href="{{ route('admin.roles.show', $role) }}"><i class="bi bi-eye pe-2"></i>Lihat Detail</a></li>
                                            @endCanDo
                                            @canDo('roles.edit')
                                            <li><a class="dropdown-item" href="{{ route('admin.roles.edit', $role) }}"><i class="bi bi-pencil pe-2"></i>Edit</a></li>
                                            @endCanDo
                                            @canDo('roles.edit')
                                            <li><a class="dropdown-item" href="{{ route('admin.roles.permissions', $role) }}"><i class="bi bi-key pe-2"></i>Permissions</a></li>
                                            @endCanDo
                                            @hasAnyPermission(['roles.edit', 'roles.assign'])
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                            @endHasAnyPermission
                                            @if (!$role->is_default)
                                                @canDo('roles.edit')
                                                <li>
                                                    <form action="{{ route('admin.roles.set-default', $role) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-star me-2"></i>Set Default
                                                        </button>
                                                    </form>
                                                </li>
                                                @endCanDo
                                            @endif
                                            @if (!$role->is_system_role)
                                                @canDo('roles.edit')
                                                <li>
                                                    <form action="{{ route('admin.roles.toggle-status', $role) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item">
                                                            @if ($role->is_active)
                                                                <i class="bi bi-eye-slash me-2"></i>Nonaktifkan
                                                            @else
                                                                <i class="bi bi-eye me-2"></i>Aktifkan
                                                            @endif
                                                        </button>
                                                    </form>
                                                </li>
                                                @endCanDo
                                            @endif
                                            @if ($role->canBeDeleted())
                                                @canDo('roles.delete')
                                                <li>
                                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus role ini?')" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash pe-2"></i>Hapus</button>
                                                    </form>
                                                </li>
                                                @endCanDo
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row font-11 color-theme opacity-80">
                                <div class="col-12 mb-1">
                                    <i class="bi bi-info-circle pe-1"></i>{{ $role->description ?? '-' }}
                                    <span class="mx-2">•</span>
                                    <i class="bi bi-key pe-1"></i>{{ $role->permission_count }} permissions
                                </div>
                                <div class="col-12 mb-1">
                                    <i class="bi bi-people pe-1"></i>{{ $role->users_count }} users
                                </div>
                                <div class="col-12">
                                    <i class="bi bi-sort-numeric-down pe-1"></i>Priority {{ $role->priority }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        @if ($roles->hasPages())
            <div class="card card-style bg-white">
                <div class="content">
                    <div class="row align-items-center g-2">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <p class="mb-0 font-11 color-theme text-center text-md-start">
                                Menampilkan {{ $roles->firstItem() }}-{{ $roles->lastItem() }} dari {{ $roles->total() }} role
                            </p>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex justify-content-center justify-content-md-end gap-2">
                                @if ($roles->onFirstPage())
                                    <span class="btn btn-s bg-theme rounded-s opacity-30"><i class="bi bi-chevron-left"></i></span>
                                @else
                                    <a href="{{ $roles->appends(request()->query())->previousPageUrl() }}" class="btn btn-s bg-highlight rounded-s"><i class="bi bi-chevron-left"></i></a>
                                @endif
                                @if ($roles->hasMorePages())
                                    <a href="{{ $roles->appends(request()->query())->nextPageUrl() }}" class="btn btn-s bg-highlight rounded-s"><i class="bi bi-chevron-right"></i></a>
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

    <!-- Add Button (Floating) -->
    <a href="{{ route('admin.roles.create') }}" class="btn bg-highlight rounded-circle shadow-bg shadow-bg-s position-fixed" style="bottom: 100px; right: 20px; width: 56px; height: 56px; z-index: 999;">
        <i class="bi bi-shield-plus color-white font-20"></i>
    </a>
@endsection
