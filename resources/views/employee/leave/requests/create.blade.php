@extends('layouts.app')

@section('title', 'Ajukan Izin')

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('employee.leave.requests.index') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-14">Ajukan Izin</a>
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
            <form method="POST" action="{{ route('employee.leave.requests.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Izin</label>
                    <select name="type" class="form-control" required>
                        <option value="">Pilih Jenis Izin</option>
                        <option value="annual" {{ old('type') == 'annual' ? 'selected' : '' }}>Cuti Tahunan</option>
                        <option value="sick" {{ old('type') == 'sick' ? 'selected' : '' }}>Sakit</option>
                        <option value="personal" {{ old('type') == 'personal' ? 'selected' : '' }}>Pribadi</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alasan</label>
                    <textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Lampiran (opsional)</label>
                    <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">Maksimal 2MB, format: PDF, JPG, PNG</small>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ajukan Izin</button>
            </form>
        </div>
    </div>
@endsection
