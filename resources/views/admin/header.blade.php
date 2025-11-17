@php
    // Props: $title, $backUrl, $menuTarget, $rightHtml
    $title = $title ?? (trim($__env->yieldContent('title')) ?: 'Admin');
    $backUrl = $backUrl ?? url()->previous();
    // default to the main menu id used by the app
    $menuTarget = $menuTarget ?? '#menu-main';
@endphp
<div class="header-bar header-fixed header-app header-bar-detached" style="backdrop-filter: saturate(180%) blur(8px);">
    <div class="d-flex align-items-center w-100">
        <a data-back-button href="{{ $backUrl }}" class="me-2"><i class="bi bi-caret-left-fill font-13 color-theme ps-2"></i></a>
        <div class="flex-grow-1">
            <a href="#" class="header-title color-theme font-13">{{ $title }}</a>
        </div>
        @if (!empty($rightHtml))
            {!! $rightHtml !!}
        @else
            <a href="#" data-bs-toggle="offcanvas" data-bs-target="{{ $menuTarget }}"><i class="gradient-blue shadow-bg shadow-bg-xs bi bi-list font-16"></i></a>
        @endif
    </div>
    <div class="mt-2 px-3 d-none" id="admin-subtitle-slot"></div>
</div>
