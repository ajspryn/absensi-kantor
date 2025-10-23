@extends('layouts.app')

@section('title', 'Detail Pengajuan Koreksi')

@section('header')
    @php
        if (\Illuminate\Support\Facades\Route::has('employee.attendance.corrections.index')) {
            $correctionsRouteUrl = route('employee.attendance.corrections.index');
        } elseif (\Illuminate\Support\Facades\Route::has('attendance.corrections.index')) {
            $correctionsRouteUrl = route('attendance.corrections.index');
        } else {
            $correctionsRouteUrl = url('/employee/attendance/corrections');
        }
    @endphp

    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ $correctionsRouteUrl }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-14">Detail Pengajuan Koreksi</a>
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
                    <div class="font-14"><i class="bi bi-calendar-date me-1"></i> {{ $correction->date->format('d M Y') }}</div>
                    <div class="font-12 text-muted">Diajukan: {{ $correction->created_at->format('Y-m-d H:i') }}</div>
                </div>
                <div>
                    <span class="badge bg-{{ $correction->status === 'pending' ? 'warning text-dark' : ($correction->status === 'manager_approved' ? 'info' : ($correction->status === 'approved' ? 'success' : ($correction->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                        {{ $correction->status === 'pending' ? 'Menunggu Manager' : ($correction->status === 'manager_approved' ? 'Menunggu HR' : ($correction->status === 'approved' ? 'Disetujui & Diterapkan' : ($correction->status === 'rejected' ? 'Ditolak' : ucfirst(str_replace('_', ' ', $correction->status))))) }}
                    </span>
                </div>
            </div>

            <div class="mb-2"><strong>Check-in Asli:</strong> {{ optional($correction->original_check_in)->format('H:i') ?? '-' }} </div>
            <div class="mb-2"><strong>Check-out Asli:</strong> {{ optional($correction->original_check_out)->format('H:i') ?? '-' }} </div>

            <div class="mb-2"><strong>Check-in Yang Diajukan:</strong> <span class="fw-bold">{{ optional($correction->corrected_check_in)->format('H:i') ?? '-' }}</span></div>
            <div class="mb-2"><strong>Check-out Yang Diajukan:</strong> <span class="fw-bold">{{ optional($correction->corrected_check_out)->format('H:i') ?? '-' }}</span></div>

            <div class="mb-2"><strong>Alasan:</strong>
                <div class="mt-1">{{ $correction->reason }}</div>
            </div>

            @if ($correction->attachment_path)
                <div class="mb-2">
                    <strong>Lampiran:</strong>
                    <div class="mt-1">
                        <a href="{{ asset('storage/' . $correction->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-paperclip"></i> Lihat Lampiran</a>
                        <small class="text-muted ms-2">(Klik untuk membuka)</small>
                    </div>
                </div>
            @endif

            <hr>

            <div class="mb-2"><strong>Persetujuan Manager:</strong>
                <div class="mt-1">{{ $correction->managerApprover?->name ?? '-' }} @if ($correction->manager_approved_at)
                        ({{ $correction->manager_approved_at->format('Y-m-d H:i') }})
                    @endif
                </div>
            </div>

            <div class="mb-2"><strong>Persetujuan HR:</strong>
                <div class="mt-1">{{ $correction->hrApprover?->name ?? '-' }} @if ($correction->hr_approved_at)
                        ({{ $correction->hr_approved_at->format('Y-m-d H:i') }})
                    @endif
                </div>
            </div>

            @if ($correction->status === 'rejected')
                <div class="alert alert-danger mt-3">Ditolak oleh {{ $correction->rejected_by_id }}: {{ $correction->rejected_reason }}</div>
            @endif

            <div class="d-flex gap-2 mt-3">
                <a href="{{ $correctionsRouteUrl }}" class="btn btn-sm btn-secondary">Kembali</a>
                @if ($correction->status === 'pending')
                    <a href="{{ route('employee.attendance.corrections.create') }}" class="btn btn-sm btn-primary">Ajukan Koreksi Lainnya</a>
                @endif
            </div>
        </div>
    </div>
@endsection
