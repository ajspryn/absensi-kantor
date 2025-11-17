@extends('layouts.app')

@section('title', 'Daily Activities')

@section('content')
    <div class="card card-style mb-3">
        <div class="content py-2">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h4 class="font-700 mb-0 font-15">Daily Activities</h4>
                <small class="color-theme opacity-70">Menampilkan: Hari ini</small>
            </div>
            <a href="{{ route('employee.daily-activities.create') }}" class="btn btn-sm bg-blue-dark">Tambah Activity</a>
        </div>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Mobile: card list -->
    <div class="d-block">
        @foreach ($activities as $a)
            @php
                $tasks = $a->tasks ?? [];
                $total = count($tasks);
                $completed = collect($tasks)->filter(fn($t) => !empty($t['completed']) && ($t['completed'] == 1 || $t['completed'] === true))->count();
                $status = $a->status ?? 'submitted';
            @endphp
            <div class="card card-style mb-2">
                <div class="content py-2 d-flex align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-start justify-content-between">
                            <h6 class="mb-1 font-14">{{ $a->title }}</h6>
                            <div>
                                @if ($status === 'approved')
                                    <span class="badge bg-success">APPROVED</span>
                                @elseif($status === 'rejected')
                                    <span class="badge bg-danger">REJECTED</span>
                                @else
                                    <span class="badge bg-warning">SUBMITTED</span>
                                @endif
                            </div>
                        </div>

                        <div class="font-11 opacity-70">{{ $a->date->format('Y-m-d') }} • {{ $completed > 0 ? "$completed/$total selesai" : '-' }}</div>
                        <div class="font-11 mt-1">{{ $a->start_time ?? '-' }} - {{ $a->end_time ?? '-' }}</div>
                    </div>
                    <div class="ms-2">
                        <a href="{{ route('employee.daily-activities.show', $a->id) }}" class="btn btn-sm bg-blue-dark">Lihat</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($activities->hasPages())
        <div class="card card-style">
            <div class="content py-3">
                {{ $activities->appends(request()->query())->links('pagination.mobile') }}
            </div>
        </div>
    @endif
@endsection

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-15">Daily Activities</a>
    </div>
@endsection

@section('sidebar')
    @include('employee.sidebar')
@endsection
