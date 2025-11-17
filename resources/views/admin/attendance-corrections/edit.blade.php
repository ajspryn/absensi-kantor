@extends('layouts.admin')

@section('title', 'Edit Koreksi Absensi')

@section('header')
    @include('admin.header', [
        'title' => 'Edit Koreksi Absensi',
        'backUrl' => route('admin.attendance-corrections.index'),
    ])
@endsection

@section('content')
    @include('admin.partials.section-header', [
        'title' => 'Edit Koreksi Absensi',
        'subtitle' => 'Ubah data koreksi absensi',
        'icon' => 'bi-pencil-square',
    ])

    <div class="card card-style">
        <div class="content">
            <form action="{{ route('admin.attendance-corrections.update', $attendanceCorrection) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <div class="form-control">{{ $attendanceCorrection->employee?->full_name ?? $attendanceCorrection->user?->name }}</div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label">Corrected Check In (HH:MM)</label>
                        <input type="time" name="corrected_check_in" class="form-control" value="{{ old('corrected_check_in', optional($attendanceCorrection->corrected_check_in)->format('H:i')) }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Corrected Check Out (HH:MM)</label>
                        <input type="time" name="corrected_check_out" class="form-control" value="{{ old('corrected_check_out', optional($attendanceCorrection->corrected_check_out)->format('H:i')) }}">
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Alasan</label>
                    <textarea name="reason" class="form-control" rows="4">{{ old('reason', $attendanceCorrection->reason) }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.attendance-corrections.show', $attendanceCorrection) }}" class="btn btn-light rounded-s me-2">Batal</a>
                    <button class="btn btn-primary rounded-s">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
