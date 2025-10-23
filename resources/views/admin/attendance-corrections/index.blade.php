@extends('layouts.admin')

@section('title', 'Koreksi Absensi')

@section('header')
    @include('admin.header', [
        'title' => 'Koreksi Absensi',
        // Use an existing admin route as the back URL
        'backUrl' => route('admin.attendance.index'),
    ])
@endsection

@section('content')
    @include('admin.partials.section-header', [
        'title' => 'Koreksi Absensi',
        'subtitle' => 'Kelola pengajuan koreksi absensi',
        'icon' => 'bi-pencil-square',
    ])

    @include('admin.partials.alerts')

    <!-- Stats Row -->
    <div class="card card-style shadow-m mb-3">
        <div class="content">
            <div class="row g-3 mb-2">
                <div class="col">
                    <div class="card card-style bg-light h-100 p-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                                <i class="bi bi-hourglass-split color-white font-16"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Pending</small>
                                <strong class="h4 mb-0">{{ $stats['pending'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card card-style bg-light h-100 p-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                                <i class="bi bi-check2-circle color-white font-16"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Disetujui</small>
                                <strong class="h4 mb-0">{{ $stats['approved'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card card-style bg-light h-100 p-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                                <i class="bi bi-x-circle color-white font-16"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Ditolak</small>
                                <strong class="h4 mb-0">{{ $stats['rejected'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <form method="GET" class="card card-style shadow-sm mb-3 p-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select rounded-xl" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.attendance-corrections.index') }}" class="btn btn-sm btn-light rounded-s">Reset</a>
            </div>
        </div>
    </form>

    <!-- Corrections Grid -->
    <div class="row g-3">
        @forelse($corrections as $c)
            <div class="col-12 col-md-6">
                <div class="card card-style shadow-sm h-100">
                    <div class="content">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="mb-0"><i class="bi bi-person-badge me-1"></i>{{ $c->employee?->full_name ?? $c->user?->name }}</h6>
                                <small class="text-muted">Tanggal: {{ $c->date->format('d M Y') }}</small>
                            </div>
                            <div>
                                <span class="badge {{ $c->status === 'pending' ? 'bg-warning text-dark' : ($c->status === 'manager_approved' ? 'bg-info' : ($c->status === 'approved' ? 'bg-success' : ($c->status === 'rejected' ? 'bg-danger' : 'bg-secondary'))) }}">
                                    {{ $c->status === 'pending' ? 'Menunggu Manager' : ($c->status === 'manager_approved' ? 'Menunggu HR' : ($c->status === 'approved' ? 'Disetujui' : ($c->status === 'rejected' ? 'Ditolak' : ucfirst(str_replace('_', ' ', $c->status))))) }}
                                </span>
                            </div>
                        </div>

                        <div class="row g-2 mb-2 small">
                            <div class="col-6">
                                <div class="text-muted">Masuk</div>
                                <div class="fw-bold">{{ optional($c->original_check_in)->format('H:i') ?? '-' }} → {{ optional($c->corrected_check_in)->format('H:i') ?? '-' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">Pulang</div>
                                <div class="fw-bold">{{ optional($c->original_check_out)->format('H:i') ?? '-' }} → {{ optional($c->corrected_check_out)->format('H:i') ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="mb-3 text-truncate"><strong>Alasan:</strong> {{ $c->reason }}</div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.attendance-corrections.show', $c) }}" class="btn btn-sm btn-primary rounded-s me-2"><i class="bi bi-eye me-1"></i>Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                @include('admin.partials.empty', ['title' => 'Belum ada pengajuan koreksi'])
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $corrections->links() }}
    </div>

@endsection
