@extends('layouts.app')

@section('title', 'Laporan Daily Activities - Departemen')

@section('content')
    <div class="card card-style mb-3">
        <div class="content py-2">
            <form method="get" class="row g-2 mb-0">
                <div class="col-6 col-sm-3">
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-6 col-sm-3">
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-6 col-sm-3">
                    <select name="employee_id" class="form-control">
                        <option value="">Semua Karyawan</option>
                        @if(!empty($employees))
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ (string) request('employee_id') === (string) $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-6 col-sm-3">
                    <button class="btn btn-sm bg-highlight rounded-xs w-100">Filter</button>
                </div>
            </form>

            @if ($activities->count() > 0)
                <div class="mt-2">
                    <a href="{{ route('admin.daily-activities.export', request()->query()) }}" class="btn btn-sm bg-blue-dark rounded-xs text-uppercase font-600 w-100">
                        <i class="bi bi-file-earmark-csv font-10 pe-1"></i>Export CSV
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Mobile: stacked cards -->
    <div class="d-block d-sm-none">
        @foreach ($activities as $a)
            <div class="card card-style mb-2">
                <div class="content py-2">
                    <h6 class="mb-1 font-14">{{ $a->title }}</h6>
                    <div class="font-11 opacity-70">{{ $a->employee->full_name ?? $a->employee->id }} • {{ $a->date->format('Y-m-d') }}</div>
                    @php
                        $tasks = $a->tasks ?? [];
                        $totalTasks = count($tasks);
                        $completedTasks = collect($tasks)->filter(fn($t) => !empty($t['completed']) && ($t['completed'] == 1 || $t['completed'] === true))->count();
                        $status = $a->status ?? 'submitted';
                    @endphp
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <div class="font-11">{{ $totalTasks > 0 ? "${completedTasks}/${totalTasks} tugas selesai" : 'Belum ada tugas' }}</div>
                        <div class="ms-auto">
                            @if($status === 'approved')
                                <span class="badge bg-success">APPROVED</span>
                            @elseif($status === 'rejected')
                                <span class="badge bg-danger">REJECTED</span>
                            @else
                                <span class="badge bg-warning">SUBMITTED</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="font-11">{{ $a->start_time ?? '-' }} - {{ $a->end_time ?? '-' }}</div>
                        <a href="{{ route('admin.daily-activities.show', $a->id) }}" class="btn btn-xs bg-blue-dark text-white rounded-s font-10 px-3 py-2">Lihat</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Desktop: table list -->
    <div class="d-none d-sm-block">
        <div class="card card-style">
            <div class="content py-2">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Karyawan</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Tugas</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activities as $a)
                                @php
                                    $tasks = $a->tasks ?? [];
                                    $total = count($tasks);
                                    $completed = collect($tasks)->filter(fn($t) => !empty($t['completed']) && ($t['completed'] == 1 || $t['completed'] === true))->count();
                                    $status = $a->status ?? 'submitted';
                                @endphp
                                <tr>
                                    <td class="align-middle">{{ $a->title }}</td>
                                    <td class="align-middle">{{ $a->employee->full_name ?? $a->employee->id }}</td>
                                    <td class="align-middle">{{ $a->date->format('Y-m-d') }}</td>
                                    <td class="align-middle">{{ $a->start_time ?? '-' }} - {{ $a->end_time ?? '-' }}</td>
                                    <td class="align-middle">{{ $total > 0 ? "${completed}/${total}" : '-' }}</td>
                                    <td class="align-middle">
                                        @if($status === 'approved')
                                            <span class="badge bg-success">APPROVED</span>
                                        @elseif($status === 'rejected')
                                            <span class="badge bg-danger">REJECTED</span>
                                        @else
                                            <span class="badge bg-warning">SUBMITTED</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-end">
                                        <a href="{{ route('admin.daily-activities.show', $a->id) }}" class="btn btn-sm bg-blue-dark">Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-15">Laporan Daily Activities - Departemen</a>
    </div>
@endsection

@section('sidebar')
    @include('admin.sidebar')
@endsection
