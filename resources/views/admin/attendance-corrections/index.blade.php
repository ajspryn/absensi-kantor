@extends('layouts.admin')

@section('title', 'Koreksi Absensi')

@section('header')
    @include('admin.header', [
        'title' => 'Koreksi Absensi',
        // Use an existing admin route as the back URL
        'backUrl' => route('dashboard'),
    ])
@endsection

@section('content')
    @include('admin.partials.section-header', [
        'title' => 'Koreksi Absensi',
        'subtitle' => 'Kelola pengajuan koreksi absensi',
        'icon' => 'bi-pencil-square',
    ])

    @include('admin.partials.alerts')

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
                            @if (auth()->user() && auth()->user()->isAdmin())
                                <a href="{{ route('admin.attendance-corrections.edit', $c) }}" class="btn btn-sm btn-secondary rounded-s me-2"><i class="bi bi-pencil me-1"></i>Edit</a>
                                <form action="{{ route('admin.attendance-corrections.destroy', $c) }}" method="POST" onsubmit="return confirm('Hapus pengajuan koreksi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger rounded-s"><i class="bi bi-trash me-1"></i>Hapus</button>
                                </form>
                            @endif
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
