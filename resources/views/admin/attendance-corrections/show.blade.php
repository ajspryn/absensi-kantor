@extends('layouts.admin')

@section('title', 'Detail Koreksi Absensi')

@section('header')
    @include('admin.header', [
        'title' => 'Detail Koreksi Absensi',
        'backUrl' => route('admin.attendance-corrections.index'),
    ])
@endsection

@section('content')
    @include('admin.partials.section-header', [
        'title' => 'Detail Koreksi Absensi',
        'subtitle' => 'Tinjau dan ambil keputusan',
        'icon' => 'bi-pencil-square',
    ])

    @include('admin.partials.alerts')

    <div class="card card-style shadow-m">
        <div class="content">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-2">{{ $attendanceCorrection->employee?->full_name ?? $attendanceCorrection->user?->name }}</h5>
                    <div class="mb-2"><strong>Tanggal:</strong> {{ $attendanceCorrection->date->format('Y-m-d') }}</div>

                    <div class="mb-2"><strong>Status:</strong>
                        <span class="badge bg-{{ $attendanceCorrection->status === 'pending' ? 'warning text-dark' : ($attendanceCorrection->status === 'manager_approved' ? 'info' : ($attendanceCorrection->status === 'approved' ? 'success' : ($attendanceCorrection->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                            {{ $attendanceCorrection->status === 'pending' ? 'Menunggu Manager' : ($attendanceCorrection->status === 'manager_approved' ? 'Menunggu HR' : ($attendanceCorrection->status === 'approved' ? 'Disetujui' : ($attendanceCorrection->status === 'rejected' ? 'Ditolak' : ucfirst(str_replace('_', ' ', $attendanceCorrection->status))))) }}
                        </span>
                    </div>

                    <div class="mb-2"><strong>Masuk:</strong> <span class="fw-bold">{{ optional($attendanceCorrection->original_check_in)->format('H:i') ?? '-' }}</span> → <strong>{{ optional($attendanceCorrection->corrected_check_in)->format('H:i') ?? '-' }}</strong></div>
                    <div class="mb-2"><strong>Pulang:</strong> <span class="fw-bold">{{ optional($attendanceCorrection->original_check_out)->format('H:i') ?? '-' }}</span> → <strong>{{ optional($attendanceCorrection->corrected_check_out)->format('H:i') ?? '-' }}</strong></div>

                    <div class="mb-2"><strong>Alasan:</strong>
                        <div class="mt-1">{{ $attendanceCorrection->reason }}</div>
                    </div>

                    @if ($attendanceCorrection->attachment_path)
                        <div class="mb-2"><a href="{{ asset('storage/' . $attendanceCorrection->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-paperclip"></i> Lihat Lampiran</a></div>
                    @endif
                </div>

                <div class="col-md-6">
                    <div class="mb-2"><strong>Persetujuan Manager:</strong>
                        <div class="mt-1">{{ $attendanceCorrection->managerApprover?->name ?? '-' }} @if ($attendanceCorrection->manager_approved_at)
                                <small class="text-muted">({{ $attendanceCorrection->manager_approved_at->format('Y-m-d H:i') }})</small>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2"><strong>Persetujuan HR:</strong>
                        <div class="mt-1">{{ $attendanceCorrection->hrApprover?->name ?? '-' }} @if ($attendanceCorrection->hr_approved_at)
                                <small class="text-muted">({{ $attendanceCorrection->hr_approved_at->format('Y-m-d H:i') }})</small>
                            @endif
                        </div>
                    </div>

                    @if ($attendanceCorrection->status !== 'approved' && $attendanceCorrection->status !== 'rejected')
                        <div class="d-flex gap-2 mt-3">
                            {{-- Manager approval: only for users with approve permission who are department managers (or admin), not the submitter, and when status is pending --}}
                            @php
                                $isDeptManager = optional($attendanceCorrection->employee->department)->manager_id === auth()->id();
                            @endphp
                            @if (auth()->user()->hasPermission('attendance.corrections.approve') && $isDeptManager && $attendanceCorrection->status === 'pending' && auth()->id() !== $attendanceCorrection->user_id)
                                <form method="POST" action="{{ route('admin.attendance-corrections.approve-manager', $attendanceCorrection) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">Setujui Manager</button>
                                </form>
                            @endif

                            {{-- HR approval: only for users with verify permission, not the submitter, and when status is manager_approved --}}
                            @if (auth()->user()->hasPermission('attendance.corrections.verify') && $attendanceCorrection->status === 'manager_approved' && auth()->id() !== $attendanceCorrection->user_id)
                                <form method="POST" action="{{ route('admin.attendance-corrections.approve-hr', $attendanceCorrection) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-primary">Setujui HR & Terapkan</button>
                                </form>
                            @endif
                        </div>

                        @if ((auth()->user()->hasPermission('attendance.corrections.approve') || auth()->user()->hasPermission('attendance.corrections.verify')) && auth()->id() !== $attendanceCorrection->user_id && in_array($attendanceCorrection->status, ['pending', 'manager_approved']))
                            <div class="mt-3">
                                <form method="POST" action="{{ route('admin.attendance-corrections.reject', $attendanceCorrection) }}" class="d-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="reason" class="form-control form-control-sm" placeholder="Alasan penolakan" required>
                                    <button type="submit" class="btn btn-sm btn-danger">Tolak</button>
                                </form>
                            </div>
                        @endif
                    @elseif($attendanceCorrection->status === 'rejected')
                        <div class="alert alert-secondary mt-3">Ditolak oleh {{ $attendanceCorrection->rejectedBy?->name ?? $attendanceCorrection->rejected_by_id }}: {{ $attendanceCorrection->rejected_reason }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
