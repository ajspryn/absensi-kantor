@extends('layouts.admin')

@section('title', 'Detail Koreksi Absensi')

@section('content')
    @include('admin.partials.section-header', [
        'title' => 'Detail Koreksi Absensi',
        'subtitle' => 'Tinjau dan ambil keputusan',
        'icon' => 'bi-pencil-square',
    ])

    @include('admin.partials.alerts')

    <div class="card p-3">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-2"><strong>Karyawan:</strong> {{ $attendanceCorrection->employee?->full_name ?? $attendanceCorrection->user?->name }}</div>
                <div class="mb-2"><strong>Tanggal:</strong> {{ $attendanceCorrection->date->format('Y-m-d') }}</div>
                <div class="mb-2"><strong>Status:</strong> <span class="badge bg-{{ $attendanceCorrection->status === 'pending' ? 'warning text-dark' : ($attendanceCorrection->status === 'approved' ? 'success' : 'secondary') }}">{{ ucfirst(str_replace('_', ' ', $attendanceCorrection->status)) }}</span></div>
                <div class="mb-2"><strong>Masuk:</strong> {{ optional($attendanceCorrection->original_check_in)->format('H:i') ?? '-' }} → <strong>{{ optional($attendanceCorrection->corrected_check_in)->format('H:i') ?? '-' }}</strong></div>
                <div class="mb-2"><strong>Pulang:</strong> {{ optional($attendanceCorrection->original_check_out)->format('H:i') ?? '-' }} → <strong>{{ optional($attendanceCorrection->corrected_check_out)->format('H:i') ?? '-' }}</strong></div>
                <div class="mb-2"><strong>Alasan:</strong> {{ $attendanceCorrection->reason }}</div>
                @if ($attendanceCorrection->attachment_path)
                    <div class="mb-2"><a href="{{ asset('storage/' . $attendanceCorrection->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Lampiran</a></div>
                @endif
            </div>
            <div class="col-md-6">
                <div class="mb-2"><strong>Persetujuan Manager:</strong> {{ $attendanceCorrection->managerApprover?->name ?? '-' }} @if ($attendanceCorrection->manager_approved_at)
                        ({{ $attendanceCorrection->manager_approved_at->format('Y-m-d H:i') }})
                    @endif
                </div>
                <div class="mb-2"><strong>Persetujuan HR:</strong> {{ $attendanceCorrection->hrApprover?->name ?? '-' }} @if ($attendanceCorrection->hr_approved_at)
                        ({{ $attendanceCorrection->hr_approved_at->format('Y-m-d H:i') }})
                    @endif
                </div>
                @if ($attendanceCorrection->status !== 'approved' && $attendanceCorrection->status !== 'rejected')
                    <div class="d-flex gap-2 mt-3">
                        <form method="POST" action="{{ route('admin.attendance-corrections.approve-manager', $attendanceCorrection) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">Setujui Manager</button>
                        </form>
                        <form method="POST" action="{{ route('admin.attendance-corrections.approve-hr', $attendanceCorrection) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary">Setujui HR & Terapkan</button>
                        </form>
                    </div>
                    <div class="mt-3">
                        <form method="POST" action="{{ route('admin.attendance-corrections.reject', $attendanceCorrection) }}" class="d-flex gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="reason" class="form-control" placeholder="Alasan penolakan" required>
                            <button type="submit" class="btn btn-outline-danger">Tolak</button>
                        </form>
                    </div>
                @elseif($attendanceCorrection->status === 'rejected')
                    <div class="alert alert-secondary mt-3">Ditolak oleh {{ $attendanceCorrection->rejected_by_id }}: {{ $attendanceCorrection->rejected_reason }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
