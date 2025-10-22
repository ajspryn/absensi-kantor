@extends('layouts.admin')

@section('header')
    @include('admin.header', [
        'title' => 'Tambah Jadwal Kerja',
        'backUrl' => route('admin.work-schedules.index'),
    ])
@endsection

@section('content')
    <div class="content">
        <form action="{{ route('admin.work-schedules.store') }}" method="POST">
            @csrf
            @php($mode = 'create')
            @php($workSchedule = $workSchedule ?? null)
            @php($employees = $employees ?? ($allEmployees ?? []))

            @include('admin.work-schedules._form', compact('mode', 'workSchedule', 'employees'))
        </form>
    </div>
@endsection
