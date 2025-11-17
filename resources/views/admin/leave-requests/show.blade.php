@extends('layouts.admin')

@section('title', 'Detail Pengajuan Izin')

@section('header')
    @include('admin.header', [
        'title' => 'Detail Pengajuan Izin',
        'backUrl' => route('admin.leave-requests.index'),
    ])
@endsection

@section('content')
    @include('admin.partials.section-header', [
        'title' => 'Detail Pengajuan Izin',
        'subtitle' => 'Tinjau dan ambil keputusan',
        'icon' => 'bi-calendar-x',
    ])

    @include('admin.partials.alerts')

    <div class="card card-style shadow-m">
        <div class="content">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-2">{{ $leaveRequest->employee?->full_name ?? $leaveRequest->user?->name }}</h5>
                    <div class="mb-2"><strong>Tanggal:</strong> {{ $leaveRequest->start_date->format('Y-m-d') }} - {{ $leaveRequest->end_date->format('Y-m-d') }}</div>

                    <div class="mb-2"><strong>Status:</strong>
                        <span class="badge bg-{{ $leaveRequest->status === 'pending' ? 'warning text-dark' : ($leaveRequest->status === 'approved' ? 'info' : ($leaveRequest->status === 'verified' ? 'success' : ($leaveRequest->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                            {{ ucfirst($leaveRequest->status) }}
                        </span>
                    </div>

                    <div class="mb-2"><strong>Jenis Izin:</strong> {{ ucfirst($leaveRequest->type) }}</div>

                    <div class="mb-2"><strong>Alasan:</strong>
                        <div class="mt-1">{{ $leaveRequest->reason }}</div>
                    </div>

                    @if ($leaveRequest->attachment_path)
                        <div class="mb-2"><a href="{{ asset('storage/' . $leaveRequest->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-paperclip"></i> Lihat Lampiran</a></div>
                    @endif

                    @if ($leaveRequest->status === 'rejected' && $leaveRequest->rejected_reason)
                        <div class="mb-2"><strong>Alasan Penolakan:</strong>
                            <div class="alert alert-danger mt-1">{{ $leaveRequest->rejected_reason }}</div>
                        </div>
                    @endif
                </div>

                <div class="col-md-6">
                    <div class="mb-2"><strong>Persetujuan:</strong>
                        <div class="mt-1">{{ $leaveRequest->approver?->name ?? '-' }} @if ($leaveRequest->approved_at)
                                <small class="text-muted">({{ $leaveRequest->approved_at->format('Y-m-d H:i') }})</small>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2"><strong>Verifikasi:</strong>
                        <div class="mt-1">{{ $leaveRequest->verifier?->name ?? '-' }} @if ($leaveRequest->verified_at)
                                <small class="text-muted">({{ $leaveRequest->verified_at->format('Y-m-d H:i') }})</small>
                            @endif
                        </div>
                    </div>

                    @if ($leaveRequest->rejected_by_id)
                        <div class="mb-2"><strong>Ditolak Oleh:</strong>
                            <div class="mt-1">{{ $leaveRequest->rejectedBy?->name ?? '-' }} @if ($leaveRequest->rejected_at)
                                    <small class="text-muted">({{ $leaveRequest->rejected_at->format('Y-m-d H:i') }})</small>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($leaveRequest->status === 'pending' || $leaveRequest->status === 'approved')
        <div class="card card-style shadow-m">
            <div class="content">
                <h6 class="mb-3">Aksi</h6>
                <div class="row g-2">
                    @php
                        $isDeptManager = optional($leaveRequest->employee->department)->manager_id === auth()->id();
                        $isAdmin = auth()->user()->role && strtolower(auth()->user()->role->name) === 'admin';
                    @endphp

                    @if ($leaveRequest->status === 'pending' && auth()->user()->role && auth()->user()->role->hasPermission('leave.approve') && ($isDeptManager || $isAdmin) && auth()->id() !== $leaveRequest->user_id)
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('admin.leave-requests.approve', $leaveRequest) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100">Setujui</button>
                            </form>
                        </div>
                    @endif

                    @if ($leaveRequest->status === 'approved' && auth()->user()->role && auth()->user()->role->hasPermission('leave.verify') && auth()->id() !== $leaveRequest->user_id && auth()->id() !== $leaveRequest->approver_id)
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('admin.leave-requests.verify', $leaveRequest) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary w-100">Verifikasi</button>
                            </form>
                        </div>
                    @endif

                    @if (auth()->user()->role && (auth()->user()->role->hasPermission('leave.approve') || auth()->user()->role->hasPermission('leave.verify')) && auth()->id() !== $leaveRequest->user_id)
                        <div class="col-md-6">
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">Tolak</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

@endsection

@section('footer')
    <!-- Ensure modal and backdrop sit above offcanvas/sidebar -->
    <style>
        /* make modal and backdrop very top-most to avoid being covered by offcanvas */
        .modal {
            z-index: 20050 !important;
        }

        .modal-backdrop.show {
            z-index: 20040 !important;
        }
    </style>

    <!-- Reject Modal (moved to footer so it appears above offcanvas/sidebar) -->
    <div class="modal fade" id="rejectModal" tabindex="-1" style="z-index:20000;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Pengajuan Izin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.leave-requests.reject', $leaveRequest) }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan</label>
                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
