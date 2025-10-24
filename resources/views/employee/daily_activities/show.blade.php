@extends('layouts.app')

@section('title', 'Detail Daily Activity')

@section('content')
    <div class="card card-style">
        <div class="content py-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h4 class="font-700 mb-0 font-15">{{ $activity->title }}</h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('employee.daily-activities.edit', $activity->id) }}" class="btn btn-xs bg-warning text-white rounded-s font-10 px-2 py-1">Edit</a>

                    <form method="POST" action="{{ route('employee.daily-activities.destroy', $activity->id) }}" onsubmit="return confirm('Yakin ingin menghapus activity ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs bg-danger text-white rounded-s font-10 px-2 py-1">Hapus</button>
                    </form>

                    <a href="{{ route('employee.daily-activities.index') }}" class="btn btn-xs bg-gray-dark text-white rounded-s font-10 px-2 py-1">Kembali</a>
                </div>
            </div>

            <div class="mb-2">
                <p class="mb-1"><strong>Tanggal:</strong> {{ $activity->date->format('Y-m-d') }}</p>
                <p class="mb-1"><strong>Waktu:</strong> {{ $activity->start_time ?? '-' }} - {{ $activity->end_time ?? '-' }}</p>
            </div>

            <div class="divider my-2"></div>

            <div class="mb-2">
                <h6 class="font-600 mb-1">Deskripsi</h6>
                <p class="font-12 mb-0">{{ $activity->description }}</p>
            </div>

            @if ($activity->tasks)
                <div class="divider my-2"></div>
                <h6 class="font-600 mb-2">Tugas</h6>
                <div class="list-group">
                    @foreach ($activity->tasks as $index => $t)
                        <label class="d-flex align-items-center py-2 gap-2 task-row form-check" data-task-index="{{ $index }}">
                            <input class="form-check-input task-toggle me-2" type="checkbox" data-task-index="{{ $index }}" {{ !empty($t['completed']) && ($t['completed'] == 1 || $t['completed'] === true) ? 'checked' : '' }}>
                            <div class="flex-grow-1">
                                <strong class="font-13">{{ $t['title'] ?? '-' }}</strong>
                                <div class="font-11 opacity-70">{{ $t['notes'] ?? '' }}</div>
                            </div>
                        </label>
                        @if (!$loop->last)
                            <div class="divider my-1"></div>
                        @endif
                    @endforeach
                </div>
            @endif

            <div class="divider my-2"></div>
            <h6 class="font-600 mb-2">Lampiran</h6>
            <div class="mb-2">
                @if ($activity->attachments && count($activity->attachments) > 0)
                    <div class="row g-2 mb-2">
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
                    </div>
                @else
                    <div class="font-12 opacity-70">Belum ada lampiran</div>
                @endif
            </div>

            {{-- Flash messages (success / errors) --}}
            @if (session('success'))
                <div class="alert alert-success mt-2">{{ session('success') }}</div>
            @endif

            @if ($errors && $errors->any())
                <div class="alert alert-danger mt-2">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>

    <div class="card card-style mb-3">
        <div class="content py-2">
            <h6 class="font-600 mb-2">Tambah Foto Kegiatan</h6>
            <form method="post" action="{{ route('employee.daily-activities.attachments.store', $activity->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    <input type="file" name="attachments[]" multiple accept="image/*" class="form-control">
                </div>
                <div>
                    <button class="btn btn-s bg-blue-dark text-white rounded-s w-100">Tambah Foto</button>
                </div>
            </form>
        </div>
    </div>
@endsection
{{-- image preview modal --}}
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
@push('scripts')
    <script>
        (function() {
            const tasksBase = "{{ url('employee/daily-activities') }}";
            document.querySelectorAll('.task-toggle').forEach(function(cb) {
                cb.addEventListener('change', function(e) {
                    const index = e.target.getAttribute('data-task-index');
                    const activityId = {{ $activity->id }};
                    const completed = e.target.checked ? 1 : 0;

                    fetch(`${tasksBase}/${activityId}/tasks/${index}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                completed: completed
                            })
                        }).then(async r => {
                            if (!r.ok) {
                                // try to read json error
                                let json = {};
                                try {
                                    json = await r.json();
                                } catch (e) {}
                                throw new Error(json.error || 'HTTP ' + r.status);
                            }
                            return r.json();
                        })
                        .then(data => {
                            if (!data.success) {
                                alert('Gagal memperbarui task');
                                e.target.checked = !e.target.checked;
                            }
                        }).catch(err => {
                            console.error(err);
                            alert('Terjadi kesalahan saat memperbarui task');
                            e.target.checked = !e.target.checked;
                        });
                });
            });
        })();
    </script>
@endpush

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('employee.daily-activities.index') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-15">Detail Daily Activity</a>
    </div>
@endsection

@push('styles')
    <style>
        /* increase checkbox tappable area on mobile */
        @media (max-width: 576px) {
            .form-check-input {
                width: 22px;
                height: 22px;
            }

            .btn-xs {
                padding: 10px 12px;
                font-size: 14px;
            }
        }
    </style>
@endpush

@section('sidebar')
    @include('employee.sidebar')
@endsection
