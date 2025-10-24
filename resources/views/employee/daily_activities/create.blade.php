@extends('layouts.app')

@section('title', 'Buat Daily Activity')

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('employee.daily-activities.index') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-15">Buat Daily Activity</a>
    </div>
@endsection

@section('content')

    <div class="card card-style mb-3">
        <div class="content py-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h4 class="font-700 mb-0 font-15">Buat Daily Activity</h4>
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

            <form method="post" action="{{ route('employee.daily-activities.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Waktu Mulai</label>
                        <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Waktu Selesai</label>
                        <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}">
                    </div>
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
                </div>

                <button type="button" id="add-task" class="btn btn-sm btn-secondary mb-3 w-100">Tambah Tugas</button>

                <div class="mb-3">
                    <label class="form-label">Attachments</label>
                    <input type="file" id="attachments-input" name="attachments[]" multiple class="form-control">
                    <div id="attachments-preview" class="row g-2 mt-2"></div>
                    <small class="text-muted">Allowed: jpg, png, pdf, doc, docx. Max 5MB each.</small>
                </div>

                <div>
                    <button class="btn btn-sm bg-blue-dark text-white w-100">Simpan</button>
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
        function reindexTasks() {
            const container = document.getElementById('tasks-container');
            const rows = container.querySelectorAll('.task-row');
            rows.forEach((row, i) => {
                const title = row.querySelector('input[name^="tasks"][type="text"]');
                const notes = row.querySelectorAll('input[name^="tasks"][type="text"]')[1];
                if (title) title.name = `tasks[${i}][title]`;
                if (notes) notes.name = `tasks[${i}][notes]`;
                // update any data attributes if needed
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

        // initial attach
        attachRemoveHandlers();
    </script>

    <script>
        // Attachments preview and remove
        (function() {
            const input = document.getElementById('attachments-input');
            const preview = document.getElementById('attachments-preview');

            function renderPreviews(files) {
                preview.innerHTML = '';
                Array.from(files).forEach((file, i) => {
                    const col = document.createElement('div');
                    // responsive: two columns on xs, three on sm+
                    col.className = 'col-6 col-sm-4';

                    const card = document.createElement('div');
                    card.className = 'card card-style p-1 attachment-card';

                    const content = document.createElement('div');
                    content.className = 'content p-1 text-center';

                    if (file.type.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.className = 'attachment-thumb rounded-s';
                        img.alt = file.name;
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            img.src = e.target.result;
                            // open in modal on tap
                            img.addEventListener('click', function() {
                                openImageModal(e.target.result, file.name);
                            });
                        };
                        reader.readAsDataURL(file);
                        content.appendChild(img);
                    } else {
                        const icon = document.createElement('div');
                        icon.className = 'font-12 opacity-70 py-4';
                        icon.innerText = file.name;
                        content.appendChild(icon);
                    }

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm btn-danger attachment-remove';
                    removeBtn.innerHTML = '<i class="bi bi-trash"></i>';
                    removeBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        removeFileAtIndex(i);
                    });

                    card.appendChild(removeBtn);
                    card.appendChild(content);
                    col.appendChild(card);
                    preview.appendChild(col);
                });
            }

            function removeFileAtIndex(index) {
                const dt = new DataTransfer();
                const files = Array.from(input.files);
                files.forEach((f, i) => {
                    if (i !== index) dt.items.add(f);
                });
                input.files = dt.files;
                renderPreviews(input.files);
            }

            input && input.addEventListener('change', function() {
                renderPreviews(input.files);
            });
        })();
    </script>
@endpush

@push('styles')
    <style>
        /* Attachment preview styles */
        .attachment-card {
            position: relative;
            overflow: hidden;
        }

        .attachment-thumb {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: block;
        }

        .attachment-remove {
            position: absolute;
            top: 6px;
            right: 6px;
            z-index: 20;
            border-radius: 6px;
            padding: 6px 8px;
        }

        @media (max-width: 576px) {
            .attachment-thumb {
                height: 160px;
            }

            .attachment-remove {
                top: 8px;
                right: 8px;
            }
        }
    </style>
@endpush
{{-- Image preview modal --}}
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
        function openImageModal(src, title) {
            const img = document.getElementById('imagePreviewModalImg');
            img.src = src;
            // show modal
            const modalEl = document.getElementById('imagePreviewModal');
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        }
    </script>
@endpush
