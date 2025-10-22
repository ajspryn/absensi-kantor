@extends('layouts.admin')

@section('title', 'Edit Lokasi Kantor')

@section('content')
    @include('admin.partials.alerts')
    <div class="card card-style mb-2" style="margin-top:8px;">
        <div class="card-body py-3 px-3">
            <form action="{{ route('admin.office-locations.update', $officeLocation) }}" method="POST">
                @csrf
                @method('PUT')
                <h4 class="mb-3"><i class="fa fa-map-marker-alt me-2"></i>Edit Lokasi Kantor</h4>
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $officeLocation->name) }}" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Alamat <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $officeLocation->address) }}" required>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 mb-2">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', $officeLocation->latitude) }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', $officeLocation->longitude) }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="radius" class="form-label">Radius (meter)</label>
                        <input type="number" class="form-control" id="radius" name="radius" value="{{ old('radius', $officeLocation->radius) }}" min="1">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $officeLocation->description) }}</textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $officeLocation->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Lokasi Aktif</label>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-s"><i class="fa fa-save me-2"></i>Simpan Perubahan</button>
                    <a href="{{ route('admin.office-locations.show', $officeLocation) }}" class="btn btn-dark rounded-s"><i class="fa fa-arrow-left me-2"></i>Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
