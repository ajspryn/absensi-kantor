@extends('layouts.admin')

@section('header')
    @include('admin.header', [
        'title' => 'Edit Jadwal Kerja',
        'backUrl' => route('admin.work-schedules.index'),
    ])
@endsection

@section('content')
    <div class="content">
        <form action="{{ route('admin.work-schedules.update', $workSchedule->id) }}" method="POST">
            @csrf
            @method('PUT')

            @php($mode = 'edit')
            @php($employees = $employees ?? ($allEmployees ?? []))

            @include('admin.work-schedules._form', compact('mode', 'workSchedule', 'employees'))
        </form>
    </div>
@endsection
