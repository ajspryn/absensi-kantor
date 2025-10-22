@extends('layouts.admin')

@section('title', 'Detail Lokasi Kantor')

@section('content')
    @include('admin.partials.alerts')
    <div class="card card-style mb-2" style="margin-top:8px;">
        <div class="card-body py-3 px-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h4 class="mb-1"><i class="fa fa-map-marker-alt me-2"></i>{{ $officeLocation->name }}</h4>
                    <span class="badge {{ $officeLocation->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $officeLocation->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <div>
                    <a href="{{ route('admin.office-locations.edit', $officeLocation) }}" class="btn btn-sm btn-primary rounded-s"><i class="fa fa-edit me-1"></i>Edit</a>
                    <a href="{{ route('admin.office-locations.index') }}" class="btn btn-sm btn-dark rounded-s"><i class="fa fa-arrow-left me-1"></i>Kembali</a>
                </div>
            </div>
            <div class="divider my-2"></div>
            <div class="mb-2">
                <i class="fa fa-map me-1 text-primary"></i>
                <strong>Alamat:</strong> {{ $officeLocation->address }}
            </div>
            <div class="mb-2">
                <i class="fa fa-globe me-1 text-info"></i>
                <strong>Koordinat:</strong> {{ $officeLocation->latitude }}, {{ $officeLocation->longitude }}
            </div>
            <div class="mb-2">
                <i class="fa fa-circle me-1 text-warning"></i>
                <strong>Radius:</strong> {{ $officeLocation->radius }} meter
            </div>
            @if ($officeLocation->description)
                <div class="divider my-2"></div>
                <p class="text-muted mb-0"><i class="fa fa-info-circle me-1"></i>{{ $officeLocation->description }}</p>
            @endif
        </div>
    </div>
@endsection
