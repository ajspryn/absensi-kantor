@extends('layouts.admin')

@section('title', 'Detail Daily Activity (Manager)')

@section('header')
    @include('admin.header', [
        'title' => 'Detail Daily Activity',
        'backUrl' => route('admin.daily-activities.index'),
    ])
@endsection

@section('content')
    <div class="card card-style mb-3">
        <div class="content">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h1 class="mb-0">{{ $activity->title }}</h1>
                    <small class="text-muted">{{ $activity->employee->full_name ?? $activity->employee->id }} &middot; {{ $activity->date->format('Y-m-d') }}</small>
                </div>
                <div class="text-end">
                    @if (auth()->user() && auth()->user()->hasPermission('daily_activities.approve'))
                        <form method="post" action="{{ route('admin.daily-activities.approve', $activity->id) }}" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-success">Approve</button>
                        </form>
                        <form method="post" action="{{ route('admin.daily-activities.reject', $activity->id) }}" style="display:inline; margin-left:8px;">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-danger">Reject</button>
                        </form>
                    @endif
                </div>
            </div>

            <p><strong>Waktu:</strong> {{ $activity->start_time ?? '-' }} - {{ $activity->end_time ?? '-' }}</p>
            <p><strong>Deskripsi:</strong><br>{{ $activity->description }}</p>

            @if ($activity->tasks)
                <h3>Tugas</h3>
                <ul>
                    @foreach ($activity->tasks as $t)
                        <li><strong>{{ $t['title'] ?? '-' }}</strong> - {{ $t['notes'] ?? '' }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
