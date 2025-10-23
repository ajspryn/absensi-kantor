@extends('layouts.app')

@section('title', 'Riwayat Koreksi Absensi')


@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-14">Riwayat Koreksi Absensi</a>
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
        @forelse($corrections as $c)
            <div class="col-12">
                <div class="card card-style mb-2 shadow-sm border">
                    <div class="content py-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="font-14"><i class="bi bi-calendar-date me-1"></i>{{ $c->date->format('d M Y') }}</div>
                            <span class="badge bg-{{ $c->status === 'pending' ? 'warning text-dark' : ($c->status === 'manager_approved' ? 'info' : ($c->status === 'approved' ? 'success' : ($c->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                                {{ $c->status === 'pending' ? 'Menunggu Manager' : ($c->status === 'manager_approved' ? 'Menunggu HR' : ($c->status === 'approved' ? 'Disetujui & Diterapkan' : ($c->status === 'rejected' ? 'Ditolak' : ucfirst(str_replace('_', ' ', $c->status))))) }}
                            </span>
                            @if ($c->manager_approver_id)
                                <span class="badge bg-success ms-1">Manager <i class="bi bi-check-lg"></i></span>
                            @endif
                            @if ($c->hr_approver_id)
                                <span class="badge bg-primary ms-1">HR <i class="bi bi-check-lg"></i></span>
                            @endif
                        </div>
                        <div class="font-12 mb-1">Check-in: <span class="fw-bold">{{ optional($c->corrected_check_in)->format('H:i') ?? '-' }}</span> | Check-out: <span class="fw-bold">{{ optional($c->corrected_check_out)->format('H:i') ?? '-' }}</span></div>
                        <div class="font-12 mb-1"><strong>Alasan:</strong> {{ $c->reason }}</div>
                        @if ($c->attachment_path)
                            <div class="mb-1"><a href="{{ asset('storage/' . $c->attachment_path) }}" target="_blank" class="btn btn-xs btn-outline-secondary"><i class="bi bi-paperclip"></i> Lihat Lampiran</a></div>
                        @endif
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('employee.attendance.corrections.show', $c) }}" class="btn btn-xs btn-primary"><i class="bi bi-eye"></i> Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card card-style mb-2">
                    <div class="content py-3 text-center text-muted">
                        <i class="bi bi-info-circle me-1"></i> Belum ada pengajuan koreksi absensi.<br>
                        <a href="{{ route('employee.attendance.corrections.create') }}" class="btn btn-sm btn-primary mt-2"><i class="bi bi-plus-circle"></i> Ajukan Koreksi Sekarang</a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
