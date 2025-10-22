@php
    // Props: icon (bootstrap icon class), title, text, actionUrl, actionLabel, actionIcon (optional)
    $icon = $icon ?? 'bi bi-inbox';
    $title = $title ?? 'Tidak ada data';
    $text = $text ?? 'Belum ada data untuk ditampilkan.';
    $actionUrl = $actionUrl ?? null;
    $actionLabel = $actionLabel ?? null;
    $actionIcon = $actionIcon ?? null;
@endphp
<div class="card card-style shadow-m">
    <div class="content text-center py-5">
        <div class="bg-gray-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-4 shadow-s" style="width: 90px; height: 90px;">
            <i class="{{ $icon }} color-white font-40"></i>
        </div>
        <h4 class="font-700 mb-2">{{ $title }}</h4>
        <p class="color-theme opacity-70 mb-4 font-14">{{ $text }}</p>
        @if ($actionUrl && $actionLabel)
            <a href="{{ $actionUrl }}" class="btn btn-l bg-highlight text-uppercase font-700 rounded-s shadow-bg shadow-bg-s">
                @if ($actionIcon)
                    <i class="{{ $actionIcon }} pe-2 font-16"></i>
                @endif
                {{ $actionLabel }}
            </a>
        @endif
    </div>
</div>
