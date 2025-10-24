@extends('layouts.admin')

@section('title', 'Detail Daily Activity (Manager)')

@section('header')
    @include('admin.header', [
        'title' => 'Detail Daily Activity',
        'backUrl' => route('admin.daily-activities.index'),
    ])
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.manager-attachment').forEach(function(el) {
            el.addEventListener('click', function() {
                const src = el.getAttribute('data-src');
                const img = document.getElementById('imagePreviewModalImg');
                img.src = src;
                const modalEl = document.getElementById('imagePreviewModal');
                const bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
            });
        });
    </script>
@endpush

{{-- shared modal for manager view --}}
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

@section('content')
    <div class="card card-style mb-3">
        <div class="content">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h1 class="mb-0">{{ $activity->title }}</h1>
                    <small class="text-muted">{{ $activity->employee->full_name ?? $activity->employee->id }} &middot; {{ $activity->date->format('Y-m-d') }}</small>
                </div>
            </div>

            <p><strong>Waktu:</strong> {{ $activity->start_time ?? '-' }} - {{ $activity->end_time ?? '-' }}</p>
            <p>
                <strong>Status:</strong>
                @php
                    $status = $activity->status ?? 'submitted';
                    $badgeClass = 'badge bg-warning';
                    if ($status === 'approved') {
                        $badgeClass = 'badge bg-success';
                    }
                    if ($status === 'rejected') {
                        $badgeClass = 'badge bg-danger';
                    }
                @endphp
                <span class="{{ $badgeClass }} text-uppercase">{{ $status }}</span>
            </p>
            <p><strong>Deskripsi:</strong><br>{{ $activity->description }}</p>

            @if ($activity->tasks)
                <h3>Tugas</h3>
                <ul class="list-group mb-0">
                    @foreach ($activity->tasks as $t)
                        @php
                            $completed = !empty($t['completed']) && ($t['completed'] == 1 || $t['completed'] === true);
                        @endphp
                        <li class="list-group-item d-flex align-items-start">
                            <div class="form-check me-3 mt-1">
                                <input class="form-check-input" type="checkbox" disabled {{ $completed ? 'checked' : '' }}>
                            </div>
                            <div class="flex-grow-1">
                                <strong class="{{ $completed ? 'text-decoration-line-through' : '' }}">{{ $t['title'] ?? '-' }}</strong>
                                @if (!empty($t['notes']))
                                    <div class="font-12 opacity-70">{{ $t['notes'] }}</div>
                                @endif
                            </div>
                            @if ($completed)
                                <span class="badge bg-success ms-2">Selesai</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
            <div class="divider my-2"></div>
            <h3>Lampiran</h3>
            @if ($activity->attachments && count($activity->attachments) > 0)
                <div class="row g-2 mb-2">
                    @foreach ($activity->attachments as $att)
                        <div class="col-6 col-sm-4">
                            <div class="card card-style p-1 text-center">
                                <div class="content p-1">
                                    @if (Str::endsWith($att, ['.jpg', '.jpeg', '.png']))
                                        <img src="{{ asset('storage/' . $att) }}" class="img-fluid rounded-s manager-attachment" style="max-height:140px; object-fit:cover; cursor:pointer;" data-src="{{ asset('storage/' . $att) }}" data-name="{{ basename($att) }}">
                                    @else
                                        <a href="{{ asset('storage/' . $att) }}" target="_blank" class="d-block font-12">{{ basename($att) }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="font-12 opacity-70">Belum ada lampiran</div>
            @endif
            @if (auth()->user() && auth()->user()->hasPermission('daily_activities.approve'))
                <div class="row g-2 mt-2">
                    <div class="col-12">
                        <form method="post" action="{{ route('admin.daily-activities.reject', $activity->id) }}" onsubmit="return confirm('Yakin ingin menolak activity ini?');">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-danger w-100">Tolak</button>
                        </form>
                    </div>
                    <div class="col-12">
                        <form method="post" action="{{ route('admin.daily-activities.approve', $activity->id) }}" onsubmit="return confirm('Yakin ingin menyetujui activity ini?');">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-success w-100">Setujui</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
