@extends('layouts.admin')

@section('title', 'Detail Posisi')

@section('content')
    @include('admin.partials.alerts')
    <!-- Info Utama Posisi -->
    <div class="card card-style mb-2">
        <div class="card-body ">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h4 class="mb-1"><i class="fa fa-briefcase me-2"></i>{{ $position->name }}</h4>
                    <span class="badge bg-info me-2">Level {{ $position->level }}</span>
                    <span class="badge {{ $position->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $position->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <div>
                    <a href="{{ route('admin.positions.edit', $position) }}" class="btn btn-sm btn-primary rounded-s"><i class="fa fa-edit me-1"></i>Edit</a>
                    <a href="{{ route('admin.positions.index') }}" class="btn btn-sm btn-dark rounded-s"><i class="fa fa-arrow-left me-1"></i>Kembali</a>
                </div>
            </div>
            <div class="divider my-2"></div>
            <div class="row mb-2">
                <div class="col-12 col-md-6 mb-2">
                    <i class="fa fa-building me-1 text-primary"></i>
                    <strong>{{ optional($position->department)->name ?? '-' }}</strong>
                    @if ($position->department->manager)
                        <br><small class="text-muted">Manager: {{ $position->department->manager->user->name }}</small>
                    @endif
                </div>
                <div class="col-12 col-md-6 mb-2">
                    <i class="fa fa-users me-1 text-success"></i>
                    <strong>{{ $position->employees->count() }} Karyawan</strong>
                </div>
            </div>
            @if ($position->min_salary || $position->max_salary)
                <div class="row mb-2">
                    <div class="col-12">
                        <i class="fa fa-money-bill-wave me-1 text-warning"></i>
                        <small class="text-muted">Rentang Gaji</small>
                        <strong class="text-success ms-2">Rp {{ number_format($position->min_salary ?? 0) }} - Rp {{ number_format($position->max_salary ?? 0) }}</strong>
                    </div>
                </div>
            @endif
            @if ($position->description)
                <div class="divider my-2"></div>
                <p class="text-muted mb-0"><i class="fa fa-info-circle me-1"></i>{{ $position->description }}</p>
            @endif
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-3">
        <div class="col-6">
            <div class="card card-style">
                <div class="card-body text-center">
                    <i class="fa fa-users font-22 color-blue-dark mb-2"></i>
                    <h4 class="mb-1">{{ $position->employees->count() }}</h4>
                    <p class="mb-0 text-muted">Total Karyawan</p>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-style">
                <div class="card-body text-center">
                    <i class="fa fa-chart-line font-22 color-green-dark mb-2"></i>
                    <h4 class="mb-1">{{ $position->level }}</h4>
                    <p class="mb-0 text-muted">Level Posisi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Position Level Info -->
    <div class="card card-style mb-3">
        <div class="card-body">
            <h6 class="mb-3"><i class="fa fa-layer-group me-2"></i>Informasi Level</h6>
            <div class="row">
                <div class="col-12">
                    @php
                        $levelInfo = [
                            1 => ['name' => 'Entry Level', 'desc' => 'Posisi untuk fresh graduate atau pemula', 'color' => 'bg-info'],
                            2 => ['name' => 'Entry Level', 'desc' => 'Posisi dengan pengalaman minimal', 'color' => 'bg-info'],
                            3 => ['name' => 'Entry Level', 'desc' => 'Posisi dengan beberapa pengalaman', 'color' => 'bg-info'],
                            4 => ['name' => 'Mid Level', 'desc' => 'Posisi dengan pengalaman menengah', 'color' => 'bg-warning'],
                            5 => ['name' => 'Mid Level', 'desc' => 'Posisi dengan tanggungjawab lebih besar', 'color' => 'bg-warning'],
                            6 => ['name' => 'Mid Level', 'desc' => 'Posisi supervisi atau koordinasi', 'color' => 'bg-warning'],
                            7 => ['name' => 'Senior Level', 'desc' => 'Posisi manajerial atau expert', 'color' => 'bg-danger'],
                            8 => ['name' => 'Senior Level', 'desc' => 'Posisi kepemimpinan tinggi', 'color' => 'bg-danger'],
                            9 => ['name' => 'Senior Level', 'desc' => 'Posisi eksekutif atau direktur', 'color' => 'bg-danger'],
                        ];
                        $currentLevel = $levelInfo[$position->level] ?? ['name' => 'Unknown', 'desc' => 'Level tidak diketahui', 'color' => 'bg-secondary'];
                    @endphp

                    <div class="d-flex align-items-center">
                        <span class="badge {{ $currentLevel['color'] }} me-3">Level {{ $position->level }}</span>
                        <div>
                            <strong>{{ $currentLevel['name'] }}</strong>
                            <br><small class="text-muted">{{ $currentLevel['desc'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees List -->
    <div class="card card-style">
        <div class="card-header">
            <h5 class="mb-0">Karyawan dengan Posisi Ini</h5>
        </div>
        <div class="card-body">
            @forelse($position->employees as $employee)
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $employee->user->name }}</h6>
                        <p class="text-muted font-12 mb-1">{{ $employee->employee_id }}</p>

                        <div class="d-flex align-items-center gap-3">
                            @if ($employee->department)
                                <small class="text-primary">
                                    <i class="fa fa-building me-1"></i>{{ optional($employee->department)->name ?? '-' }}
                                </small>
                            @endif

                            <small class="text-muted">
                                <i class="fa fa-envelope me-1"></i>{{ $employee->user->email }}
                            </small>

                            @if ($employee->phone)
                                <small class="text-muted">
                                    <i class="fa fa-phone me-1"></i>{{ $employee->phone }}
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-dark btn-sm" type="button" data-bs-toggle="dropdown">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.employees.show', $employee) }}">
                                    <i class="fa fa-eye me-2"></i>Detail Karyawan
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.employees.edit', $employee) }}">
                                    <i class="fa fa-edit me-2"></i>Edit Karyawan
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fa fa-users font-30 text-muted mb-3"></i>
                    <p class="text-muted">Belum ada karyawan dengan posisi ini</p>
                    <a href="{{ route('admin.employees.create') }}?position_id={{ $position->id }}" class="btn btn-primary">
                        <i class="fa fa-plus me-2"></i>Tambah Karyawan
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Other Positions in Department -->
    @if ($position->department->positions->where('id', '!=', $position->id)->count() > 0)
        <div class="card card-style mt-3">
            <div class="card-header">
                <h5 class="mb-0">Posisi Lain di {{ optional($position->department)->name ?? '-' }}</h5>
            </div>
            <div class="card-body">
                @foreach ($position->department->positions->where('id', '!=', $position->id) as $otherPosition)
<div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h6 class="mb-0">
                    <a href="{{ route('admin.positions.show', $otherPosition) }}" class="text-decoration-none">
                        {{ $otherPosition->name }}
                    </a>
                </h6>
                <small class="text-muted">
                    Level {{ $otherPosition->level }} •
                    {{ $otherPosition->employees->count() }} karyawan
                    @if ($otherPosition->min_salary || $otherPosition->max_salary)
• Rp {{ number_format($otherPosition->min_salary ?? 0) }} - Rp {{ number_format($otherPosition->max_salary ?? 0) }}
@endif
                </small>
            </div>
            <span class="badge {{ $otherPosition->is_active ? 'bg-success' : 'bg-secondary' }}">
                {{ $otherPosition->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
@endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
    <script>
        // Auto-refresh for real-time updates
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                // Only refresh if page is visible
                location.reload();
            }
        }, 300000); // 5 minutes
    </script>
@endpush)
