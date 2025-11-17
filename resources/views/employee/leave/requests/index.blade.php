@extends('layouts.app')

@section('title', 'Riwayat Pengajuan Izin')

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-14">Riwayat Pengajuan Izin</a>
        <a href="{{ route('employee.leave.requests.create') }}" class="header-icon header-icon-1"><i class="bi bi-plus-lg font-18 color-theme"></i></a>
    </div>
@endsection

@section('sidebar')
    @include('employee.sidebar')
@endsection

@section('footer')
    @include('employee.footer')
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row g-2">
        @forelse($leaveRequests as $lr)
            <div class="col-12">
                <div class="card card-style mb-2 shadow-sm border">
                    <div class="content py-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="font-14"><i class="bi bi-calendar-date me-1"></i>{{ $lr->start_date->format('d M Y') }} - {{ $lr->end_date->format('d M Y') }}</div>
                            <span class="badge bg-{{ $lr->status === 'pending' ? 'warning text-dark' : ($lr->status === 'approved' ? 'info' : ($lr->status === 'verified' ? 'success' : ($lr->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                                {{ $lr->status === 'pending' ? 'Menunggu Approval' : ($lr->status === 'approved' ? 'Menunggu Verifikasi' : ($lr->status === 'verified' ? 'Disetujui' : ($lr->status === 'rejected' ? 'Ditolak' : ucfirst($lr->status)))) }}
                            </span>
                            @if ($lr->approver_id)
                                <span class="badge bg-success ms-1">Approved <i class="bi bi-check-lg"></i></span>
                            @endif
                            @if ($lr->verifier_id)
                                <span class="badge bg-primary ms-1">Verified <i class="bi bi-check-lg"></i></span>
                            @endif
                        </div>
                        <div class="font-12 color-theme opacity-60 mb-1">{{ $lr->type }} - {{ $lr->reason }}</div>
                        <a href="{{ route('employee.leave.requests.show', $lr) }}" class="btn btn-sm btn-primary w-100">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card card-style">
                    <div class="content text-center py-4">
                        <i class="bi bi-calendar-x font-48 color-theme opacity-50"></i>
                        <h4 class="mt-2">Belum ada pengajuan izin</h4>
                        <p class="mb-3">Ajukan izin pertama Anda untuk memulai.</p>
                        <a href="{{ route('employee.leave.requests.create') }}" class="btn btn-primary">Ajukan Izin</a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
