@extends('layouts.admin')

@section('title', 'Edit Pengajuan Izin')

@section('header')
    @include('admin.header', [
        'title' => 'Edit Pengajuan Izin',
        'backUrl' => route('admin.leave-requests.index'),
    ])
@endsection

@section('content')
    @include('admin.partials.section-header', [
        'title' => 'Edit Pengajuan Izin',
        'subtitle' => 'Ubah data pengajuan izin',
        'icon' => 'bi-calendar-x',
    ])

    <div class="card card-style">
        <div class="content">
            <form action="{{ route('admin.leave-requests.update', $leaveRequest) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <div class="form-control">{{ $leaveRequest->employee?->full_name ?? $leaveRequest->user?->name }}</div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $leaveRequest->start_date->format('Y-m-d')) }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $leaveRequest->end_date->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Jenis</label>
                    <input type="text" name="type" class="form-control" value="{{ old('type', $leaveRequest->type) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Alasan</label>
                    <textarea name="reason" class="form-control" rows="4">{{ old('reason', $leaveRequest->reason) }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.leave-requests.show', $leaveRequest) }}" class="btn btn-light rounded-s me-2">Batal</a>
                    <button class="btn btn-primary rounded-s">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
