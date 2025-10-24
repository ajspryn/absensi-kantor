@extends('layouts.app')

@section('title', 'Buat Daily Activity')

@section('content')
    <div class="container">
    @section('header')
        <div class="header-bar header-fixed header-app header-bar-detached">
            <a data-back-button href="{{ route('employee.daily-activities.index') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
            <a href="#" class="header-title color-theme font-15">Buat Daily Activity</a>
        </div>
    @endsection

    <div class="card card-style mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="mb-0">Buat Daily Activity</h1>
                <a href="{{ route('employee.daily-activities.index') }}" class="btn btn-secondary">Kembali</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" action="{{ route('employee.daily-activities.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div id="tasks-container" class="mb-3">
                    <label class="form-label">Tugas (Task) - tambahkan catatan untuk setiap task</label>
                    <div class="task-row mb-2">
                        <input type="text" name="tasks[0][title]" placeholder="Judul tugas" class="form-control mb-1">
                        <input type="text" name="tasks[0][notes]" placeholder="Catatan tugas" class="form-control">
                    </div>
                </div>

                <button type="button" id="add-task" class="btn btn-sm btn-outline-secondary mb-3">Tambah Tugas</button>

                <div class="mb-3">
                    <label class="form-label">Attachments</label>
                    <input type="file" name="attachments[]" multiple class="form-control">
                    <small class="text-muted">Allowed: jpg, png, pdf, doc, docx. Max 5MB each.</small>
                </div>

                <div>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @section('footer')
        @include('employee.footer')
    @endsection

    @section('sidebar')
        @include('employee.sidebar')
    @endsection
</div>

<script>
    document.getElementById('add-task').addEventListener('click', function() {
        const container = document.getElementById('tasks-container');
        const index = container.querySelectorAll('.task-row').length;
        const div = document.createElement('div');
        div.className = 'task-row mb-2';
        div.innerHTML = '<input type="text" name="tasks[' + index + '][title]" placeholder="Judul tugas" class="form-control mb-1">\n                <input type="text" name="tasks[' + index + '][notes]" placeholder="Catatan tugas" class="form-control">';
        container.appendChild(div);
    });
</script>
@endsection
