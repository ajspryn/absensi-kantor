@extends('layouts.admin')

@section('title', 'Pengajuan Izin')

@section('header')
    @include('admin.header', [
        'title' => 'Pengajuan Izin',
        'backUrl' => route('dashboard'),
    ])
@endsection

@section('content')
    @include('admin.partials.section-header', [
        'title' => 'Pengajuan Izin',
        'subtitle' => 'Kelola pengajuan izin karyawan',
        'icon' => 'bi-calendar-x',
    ])

    @include('admin.partials.alerts')

    <!-- Filter Form -->
    <div class="card card-style shadow-m mb-3">
        <div class="content">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <!-- Leave Requests Grid (matching attendance corrections style) -->
    <div class="row g-3">
        @forelse($leaveRequests as $lr)
            <div class="col-12 col-md-6">
                <div class="card card-style shadow-sm h-100">
                    <div class="content">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="mb-0"><i class="bi bi-person-badge me-1"></i>{{ $lr->employee?->full_name ?? $lr->user?->name }}</h6>
                                <small class="text-muted">Tanggal: {{ $lr->start_date->format('d M Y') }} - {{ $lr->end_date->format('d M Y') }}</small>
                            </div>
                            <div>
                                <span class="badge {{ $lr->status === 'pending' ? 'bg-warning text-dark' : ($lr->status === 'approved' ? 'bg-info' : ($lr->status === 'verified' ? 'bg-success' : ($lr->status === 'rejected' ? 'bg-danger' : 'bg-secondary'))) }}">
                                    {{ $lr->status === 'pending' ? 'Pending' : ($lr->status === 'approved' ? 'Approved' : ($lr->status === 'verified' ? 'Verified' : ($lr->status === 'rejected' ? 'Rejected' : ucfirst($lr->status)))) }}
                                </span>
                            </div>
                        </div>

                        <div class="row g-2 mb-2 small">
                            <div class="col-6">
                                <div class="text-muted">Jenis</div>
                                <div class="fw-bold">{{ ucfirst($lr->type) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">Diajukan</div>
                                <div class="fw-bold">{{ $lr->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>

                        <div class="mb-3 text-truncate"><strong>Alasan:</strong> {{ $lr->reason }}</div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.leave-requests.show', $lr) }}" class="btn btn-sm btn-primary rounded-s me-2"><i class="bi bi-eye me-1"></i>Detail</a>
                            @if (auth()->user() && auth()->user()->isAdmin())
                                <a href="{{ route('admin.leave-requests.edit', $lr) }}" class="btn btn-sm btn-secondary rounded-s me-2"><i class="bi bi-pencil me-1"></i>Edit</a>
                                <form action="{{ route('admin.leave-requests.destroy', $lr) }}" method="POST" onsubmit="return confirm('Hapus pengajuan izin ini?');">
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
                @include('admin.partials.empty', ['title' => 'Tidak ada pengajuan izin'])
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $leaveRequests->appends(request()->query())->links() }}
    </div>
@endsection
