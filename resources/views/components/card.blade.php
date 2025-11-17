@php
    // Usage: @include('components.card', ['title' => 'Title', 'body' => '...', 'class' => 'mb-3', 'footer' => null])
    $cardClass = $class ?? '';
@endphp
<div class="card card-style rounded-m {{ $cardClass }}">
    @if (!empty($title))
        <div class="card-header p-3">
            <h5 class="mb-0">{!! $title !!}</h5>
        </div>
    @endif
    <div class="card-body p-3">
        {!! $body ?? ($slot ?? '') !!}
    </div>
    @if (!empty($footer))
        <div class="card-footer p-2">
            {!! $footer !!}
        </div>
    @endif
</div>
