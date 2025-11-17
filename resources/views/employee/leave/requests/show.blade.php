@extends('layouts.app')

@section('title', 'Detail Pengajuan Izin')

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('employee.leave.requests.index') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-14">Detail Pengajuan Izin</a>
    </div>
@endsection

@section('sidebar')
    @include('employee.sidebar')
@endsection

@section('footer')
    @include('employee.footer')
@endsection

@section('content')
    <div class="card card-style mb-3">
        <div class="content">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <div class="font-14"><i class="bi bi-calendar-date me-1"></i> {{ $leaveRequest->start_date->format('d M Y') }} - {{ $leaveRequest->end_date->format('d M Y') }}</div>
                    <div class="font-12 text-muted">Diajukan: {{ $leaveRequest->created_at->format('Y-m-d H:i') }}</div>
                </div>
                <div>
                    <span class="badge bg-{{ $leaveRequest->status === 'pending' ? 'warning text-dark' : ($leaveRequest->status === 'approved' ? 'info' : ($leaveRequest->status === 'verified' ? 'success' : ($leaveRequest->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                        {{ $leaveRequest->status === 'pending' ? 'Menunggu Approval' : ($leaveRequest->status === 'approved' ? 'Menunggu Verifikasi' : ($leaveRequest->status === 'verified' ? 'Disetujui' : ($leaveRequest->status === 'rejected' ? 'Ditolak' : ucfirst($leaveRequest->status)))) }}
                    </span>
                </div>
            </div>

            <div class="mb-2"><strong>Jenis Izin:</strong> {{ ucfirst($leaveRequest->type) }}</div>
            <div class="mb-2"><strong>Alasan:</strong> {{ $leaveRequest->reason }}</div>

            @if ($leaveRequest->attachment_path)
                <div class="mb-2">
                    <strong>Lampiran:</strong>
                    <a href="{{ Storage::url($leaveRequest->attachment_path) }}" target="_blank" class="btn btn-sm btn-primary ms-2">Lihat Lampiran</a>
                </div>
            @endif

            @if ($leaveRequest->status === 'rejected' && $leaveRequest->rejected_reason)
                <div class="mb-2">
                    <strong>Alasan Penolakan:</strong>
                    <div class="alert alert-danger">{{ $leaveRequest->rejected_reason }}</div>
                </div>
            @endif

            @if ($leaveRequest->approver)
                <div class="mb-2"><strong>Disetujui Oleh:</strong> {{ $leaveRequest->approver->name }} ({{ $leaveRequest->approved_at?->format('Y-m-d H:i') }})</div>
            @endif

            @if ($leaveRequest->verifier)
                <div class="mb-2"><strong>Diverifikasi Oleh:</strong> {{ $leaveRequest->verifier->name }} ({{ $leaveRequest->verified_at?->format('Y-m-d H:i') }})</div>
            @endif
        </div>
    </div>
@endsection
