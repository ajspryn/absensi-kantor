@extends('layouts.app')

@section('title', 'Laporan Daily Activities - Departemen')

@section('content')
    <div class="container">
        <h1>Laporan Daily Activities - Departemen</h1>

        <form method="get" class="row g-2 mb-3">
            <div class="col-auto">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-auto">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-auto">
                <input type="text" name="employee_id" class="form-control" placeholder="Employee ID" value="{{ request('employee_id') }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Filter</button>
            </div>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Employee</th>
                    <th>Judul</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activities as $a)
                    <tr>
                        <td>{{ $a->date->format('Y-m-d') }}</td>
                        <td>{{ $a->employee->full_name ?? $a->employee->id }}</td>
                        <td>{{ $a->title }}</td>
                        <td>{{ $a->start_time ?? '-' }} - {{ $a->end_time ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.daily-activities.show', $a->id) }}" class="btn btn-sm btn-secondary">Lihat</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $activities->links() }}
    </div>
@endsection
