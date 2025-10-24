@extends('layouts.app')

@section('title', 'Detail Daily Activity')

@section('content')
    <div class="container">
        <h1>{{ $activity->title }}</h1>
        <p><strong>Tanggal:</strong> {{ $activity->date->format('Y-m-d') }}</p>
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

        <a href="{{ route('employee.daily-activities.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
@endsection

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('employee.daily-activities.index') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-15">Detail Daily Activity</a>
    </div>
@endsection

@section('footer')
    @include('employee.footer')
@endsection

@section('sidebar')
    @include('employee.sidebar')
@endsection
