@extends('layouts.app')

@section('title', 'Daily Activities')

@section('content')
    <div class="container">
        <div class="card card-style mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="mb-0">Daily Activities</h1>
                    <a href="{{ route('employee.daily-activities.create') }}" class="btn btn-primary">Tambah Activity</a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activities as $a)
                                <tr>
                                    <td>{{ $a->date->format('Y-m-d') }}</td>
                                    <td>{{ $a->title }}</td>
                                    <td>{{ $a->start_time ?? '-' }} - {{ $a->end_time ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('employee.daily-activities.show', $a->id) }}" class="btn btn-sm btn-secondary">Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $activities->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-15">Daily Activities</a>
    </div>
@endsection

@section('footer')
    @include('employee.footer')
@endsection

@section('sidebar')
    @include('employee.sidebar')
@endsection
