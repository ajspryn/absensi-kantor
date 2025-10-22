@php
    // Props: title, subtitle (optional), icon (Bootstrap icon class), rightHtml (optional)
    $title = $title ?? 'Judul Halaman';
    $subtitle = $subtitle ?? null;
    $icon = $icon ?? 'bi bi-grid';
    $rightHtml = $rightHtml ?? null;
@endphp
<div class="card card-style shadow-m mb-4">
    <div class="content">
        <div class="d-flex align-items-center">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--admin-primary), #0b5ed7); color: white;">
                <i class="{{ $icon }}"></i>
            </div>
            <div class="ms-3">
                <h5 class="font-700 mb-1">{{ $title }}</h5>
                @if ($subtitle)
                    <p class="mb-0 font-11 opacity-70">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @if ($rightHtml)
            <div class="ms-3">{!! $rightHtml !!}</div>
        @endif
    </div>
</div>
