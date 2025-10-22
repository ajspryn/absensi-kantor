@extends('layouts.admin')

@section('title', 'Data Absensi - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Data Absensi',
        'backUrl' => route('dashboard'),
    ])
@endsection

@section('content')
    <div class="card card-style bg-white shadow-xl mb-3">
        <div class="content">
            <h4 class="font-700 mb-2">Report Absensi Karyawan</h4>
            <p class="mb-2 color-theme">Daftar seluruh karyawan dan status absensi mereka.</p>
            <a href="{{ route('admin.attendance.export-pdf') }}" class="btn btn-sm btn-danger mb-3">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th>Nama</th>
                            <th>ID Karyawan</th>
                            <th>Departemen</th>
                            <th>Posisi</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Catatan Masalah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->employee->full_name }}</td>
                                <td>{{ $attendance->employee->employee_id }}</td>
                                <td>{{ $attendance->employee->department->name ?? '-' }}</td>
                                <td>{{ $attendance->employee->position ?? '-' }}</td>
                                <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}</td>
                                <td>{{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}</td>
                                <td>
                                    @if ($attendance->status == 'success')
                                        <span class="badge bg-success">Sukses</span>
                                    @elseif ($attendance->status == 'late')
                                        <span class="badge bg-warning text-dark">Terlambat</span>
                                    @elseif ($attendance->status == 'error')
                                        <span class="badge bg-danger">Error</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($attendance->error_message)
                                        <span class="text-danger">{{ $attendance->error_message }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Belum ada data absensi hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
