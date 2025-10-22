@php
    // Props: action (form action), method (GET/POST), fields (array of [type,name,label,options?,value?,placeholder?]), submitLabel
    $action = $action ?? url()->current();
    $method = strtoupper($method ?? 'GET');
    $fields = $fields ?? [];
    $submitLabel = $submitLabel ?? 'Filter';
@endphp
<div class="card card-style shadow-m mb-4">
    <div class="content">
        <form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" class="row g-2">
            @if ($method !== 'GET')
                @csrf
            @endif
            @foreach ($fields as $field)
                @php
                    $type = $field['type'] ?? 'text';
                    $name = $field['name'] ?? '';
                    $label = $field['label'] ?? '';
                    $value = $field['value'] ?? request($name);
                    $placeholder = $field['placeholder'] ?? '';
                    $options = $field['options'] ?? [];
                    $col = $field['col'] ?? 6;
                @endphp
                <div class="col-12 col-md-{{ $col }}">
                    <label class="form-label font-600">{{ $label }}</label>
                    @if ($type === 'select')
                        <select name="{{ $name }}" class="form-select">
                            <option value="">-- Semua --</option>
                            @foreach ($options as $optValue => $optLabel)
                                <option value="{{ $optValue }}" {{ (string) $value === (string) $optValue ? 'selected' : '' }}>{{ $optLabel }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="{{ $type }}" name="{{ $name }}" class="form-control" value="{{ $value }}" placeholder="{{ $placeholder }}" />
                    @endif
                </div>
            @endforeach
            <div class="col-12 col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 quick-action-btn">
                    <i class="bi bi-filter me-2"></i>{{ $submitLabel }}
                </button>
            </div>
        </form>
    </div>
</div>
