@extends('layouts.app')

@section('title', 'Ajukan Koreksi Absensi')

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
        <a href="#" class="header-title color-theme font-14">Ajukan Koreksi Absensi</a>
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
    <div class="card card-style mb-3">
        <div class="content">
            <form method="POST" action="{{ route('employee.attendance.corrections.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Check-in yang benar (HH:MM)</label>
                        <input type="time" name="corrected_check_in" class="form-control" value="{{ old('corrected_check_in') }}">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Check-out yang benar (HH:MM)</label>
                        <input type="time" name="corrected_check_out" class="form-control" value="{{ old('corrected_check_out') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alasan Koreksi</label>
                    <textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Lampiran (opsional)</label>
                    <input type="file" name="attachment" class="form-control">
                    <small class="text-muted">Max 2 MB</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ $correctionsRouteUrl }}" class="btn btn-sm btn-light">Batal</a>
                    <button type="submit" class="btn btn-sm btn-primary">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
