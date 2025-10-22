@extends('layouts.admin')

@section('title', 'Koreksi Absensi')

@section('content')
    @include('admin.partials.section-header', [
        'title' => 'Koreksi Absensi',
        'subtitle' => 'Kelola pengajuan koreksi absensi',
        'icon' => 'bi-pencil-square',
    ])

    @include('admin.partials.alerts')

    <div class="card card-style">
        <div class="content">
            <div class="row g-3 mb-3">
                <div class="col">
                    <div class="stats-card">
                        <div class="stats-value">{{ $stats['pending'] }}</div>
                        <div class="stats-label">Pending</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stats-card">
                        <div class="stats-value">{{ $stats['approved'] }}</div>
                        <div class="stats-label">Disetujui</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stats-card">
                        <div class="stats-value">{{ $stats['rejected'] }}</div>
                        <div class="stats-label">Ditolak</div>
                    </div>
                </div>
            </div>

            <form method="GET" class="card p-3 mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>
            </form>

            @forelse($corrections as $c)
                <div class="col">
                    <div class="entity-card h-100 shadow-sm border rounded">
                        <div class="entity-card-title d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-person-badge me-1"></i>{{ $c->employee?->full_name ?? $c->user?->name }}
                            </div>
                            <span class="badge bg-{{ $c->status === 'pending' ? 'warning text-dark' : ($c->status === 'manager_approved' ? 'info' : ($c->status === 'approved' ? 'success' : ($c->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                                {{ $c->status === 'pending' ? 'Menunggu Manager' : ($c->status === 'manager_approved' ? 'Menunggu HR' : ($c->status === 'approved' ? 'Disetujui' : ($c->status === 'rejected' ? 'Ditolak' : ucfirst(str_replace('_', ' ', $c->status))))) }}
                            </span>
                        </div>
                        <div class="entity-card-meta">Tanggal: {{ $c->date->format('d M Y') }}</div>
                        <div class="entity-card-body small">
                            <div>Masuk: <span class="fw-bold">{{ optional($c->original_check_in)->format('H:i') ?? '-' }}</span> → <span class="fw-bold">{{ optional($c->corrected_check_in)->format('H:i') ?? '-' }}</span></div>
                            <div>Pulang: <span class="fw-bold">{{ optional($c->original_check_out)->format('H:i') ?? '-' }}</span> → <span class="fw-bold">{{ optional($c->corrected_check_out)->format('H:i') ?? '-' }}</span></div>
                            <div class="mt-2 text-truncate"><strong>Alasan:</strong> {{ $c->reason }}</div>
                        </div>
                        <div class="entity-card-actions d-flex justify-content-end">
                            <a href="{{ route('admin.attendance-corrections.show', $c) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                @include('admin.partials.empty', ['title' => 'Belum ada pengajuan koreksi'])
            @endforelse

            <div class="mt-3">
                {{ $corrections->links() }}
            </div>
        </div>
    </div>
@endsection
