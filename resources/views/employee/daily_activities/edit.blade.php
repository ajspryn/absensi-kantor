@extends('layouts.app')

@section('title', 'Edit Daily Activity')

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('employee.daily-activities.index') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-15">Edit Daily Activity</a>
    </div>
@endsection

@section('content')
    <div class="card card-style mb-3">
        <div class="content py-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h4 class="font-700 mb-0 font-15">Edit Daily Activity</h4>
                <a href="{{ route('employee.daily-activities.index') }}" class="btn btn-xs bg-gray-dark text-white rounded-s font-10 px-2 py-1">Kembali</a>
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

            <form method="post" action="{{ route('employee.daily-activities.update', $activity->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', $activity->date->format('Y-m-d')) }}" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Waktu Mulai</label>
                        <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $activity->start_time) }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Waktu Selesai</label>
                        <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $activity->end_time) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $activity->title) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control">{{ old('description', $activity->description) }}</textarea>
                </div>

                <div id="tasks-container" class="mb-3">
                    <label class="form-label">Tugas (Task) - tambahkan catatan untuk setiap task</label>
                    @php $tasks = $activity->tasks ?? []; @endphp
                    @if (count($tasks) > 0)
                        @foreach ($tasks as $i => $t)
                            <div class="task-row mb-2 d-flex gap-2 align-items-start">
                                <div class="flex-grow-1">
                                    <input type="text" name="tasks[{{ $i }}][title]" placeholder="Judul tugas" class="form-control mb-1" value="{{ $t['title'] ?? '' }}">
                                    <input type="text" name="tasks[{{ $i }}][notes]" placeholder="Catatan tugas" class="form-control" value="{{ $t['notes'] ?? '' }}">
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-danger remove-task" title="Hapus tugas">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="task-row mb-2 d-flex gap-2 align-items-start">
                            <div class="flex-grow-1">
                                <input type="text" name="tasks[0][title]" placeholder="Judul tugas" class="form-control mb-1">
                                <input type="text" name="tasks[0][notes]" placeholder="Catatan tugas" class="form-control">
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-danger remove-task" title="Hapus tugas">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <button type="button" id="add-task" class="btn btn-sm btn-secondary mb-3 w-100">Tambah Tugas</button>

                <div class="mb-3">
                    <label class="form-label">Attachments (tambahkan jika ingin)</label>
                    <input type="file" id="attachments-input" name="attachments[]" multiple class="form-control">
                    <div id="attachments-preview" class="row g-2 mt-2">
                        @if ($activity->attachments)
                            @foreach ($activity->attachments as $att)
                                <div class="col-4">
                                    <div class="card card-style p-1 text-center">
                                        @if (Str::endsWith($att, ['.jpg', '.jpeg', '.png']))
                                            <img src="{{ asset('storage/' . $att) }}" class="img-fluid rounded-s existing-attachment" style="max-height:120px; object-fit:cover; cursor:pointer;" data-src="{{ asset('storage/' . $att) }}" data-name="{{ basename($att) }}">
                                        @else
                                            <a href="{{ asset('storage/' . $att) }}" target="_blank" class="d-block font-12">{{ basename($att) }}</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <small class="text-muted">Allowed: jpg, png, pdf, doc, docx. Max 5MB each.</small>
                </div>

                <div>
                    <button class="btn btn-sm bg-blue-dark text-white w-100">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('sidebar')
    @include('employee.sidebar')
@endsection

@push('scripts')
    <script>
        // reuse small task JS from create; keep it simple
        function reindexTasks() {
            const container = document.getElementById('tasks-container');
            const rows = container.querySelectorAll('.task-row');
            rows.forEach((row, i) => {
                const inputs = row.querySelectorAll('input[type="text"]');
                if (inputs[0]) inputs[0].name = `tasks[${i}][title]`;
                if (inputs[1]) inputs[1].name = `tasks[${i}][notes]`;
                row.setAttribute('data-row-index', i);
            });
        }

        function attachRemoveHandlers() {
            document.querySelectorAll('.remove-task').forEach(function(btn) {
                btn.removeEventListener('click', handleRemoveClick);
                btn.addEventListener('click', handleRemoveClick);
            });
        }

        function handleRemoveClick(e) {
            const row = e.target.closest('.task-row');
            if (!row) return;
            row.remove();
            reindexTasks();
        }

        document.getElementById('add-task').addEventListener('click', function() {
            const container = document.getElementById('tasks-container');
            const index = container.querySelectorAll('.task-row').length;
            const div = document.createElement('div');
            div.className = 'task-row mb-2 d-flex gap-2 align-items-start';
            div.innerHTML = `
                <div class="flex-grow-1">
                    <input type="text" name="tasks[${index}][title]" placeholder="Judul tugas" class="form-control mb-1">
                    <input type="text" name="tasks[${index}][notes]" placeholder="Catatan tugas" class="form-control">
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-danger remove-task" title="Hapus tugas">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);
            attachRemoveHandlers();
            reindexTasks();
        });

        attachRemoveHandlers();
    </script>
@endpush

{{-- Image preview modal (shared with create/show) --}}
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0 text-center bg-black">
                <button type="button" class="btn-close btn-close-white position-absolute m-3 top-0 end-0" data-bs-dismiss="modal" aria-label="Close"></button>
                <img id="imagePreviewModalImg" src="" alt="Preview" class="img-fluid" style="max-height:100vh; width:auto;">
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.querySelectorAll('.existing-attachment').forEach(function(el) {
            el.addEventListener('click', function() {
                const src = el.getAttribute('data-src');
                openImageModal(src, el.getAttribute('data-name'));
            });
        });

        function openImageModal(src, title) {
            const img = document.getElementById('imagePreviewModalImg');
            img.src = src;
            const modalEl = document.getElementById('imagePreviewModal');
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        }
    </script>
@endpush
