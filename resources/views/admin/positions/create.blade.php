@extends('layouts.admin')

@section('title', 'Tambah Posisi')

@section('content')
    @include('admin.partials.alerts')

    <div class="card card-style">
        <div class="content">
            <form action="{{ route('admin.positions.store') }}" method="POST">
                @csrf
                <h5 class="font-600 mb-3"><i class="fa fa-plus me-2"></i>Tambah Posisi</h5>
                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-briefcase font-14"></i>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Software Engineer, HR Manager" required>
                            <label for="name" class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Nama Posisi <span class="text-danger">*</span></label>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-file-text font-14"></i>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Deskripsi tugas dan tanggung jawab posisi ini">{{ old('description') }}</textarea>
                            <label for="description" class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Deskripsi</label>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if (\Illuminate\Support\Facades\Schema::hasColumn('positions', 'min_salary'))
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon mb-3">
                                <i class="bi bi-currency-dollar font-14"></i>
                                <input type="number" class="form-control @error('min_salary') is-invalid @enderror" name="min_salary" value="{{ old('min_salary') }}" placeholder="Gaji Minimum" min="0" step="1000">
                                <label for="min_salary" class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Gaji Minimum</label>
                                @error('min_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif

                    @if (\Illuminate\Support\Facades\Schema::hasColumn('positions', 'max_salary'))
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon mb-3">
                                <i class="bi bi-currency-dollar font-14"></i>
                                <input type="number" class="form-control @error('max_salary') is-invalid @enderror" name="max_salary" value="{{ old('max_salary') }}" placeholder="Gaji Maximum" min="0" step="1000">
                                <label for="max_salary" class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Gaji Maksimum</label>
                                @error('max_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <div class="col-12">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-2"></i>Simpan Posisi
                            </button>
                            <a href="{{ route('admin.positions.index') }}" class="btn btn-dark">
                                <i class="fa fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // No client-side validation for department or level here (fields removed by UX request).
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus name field
            const nameField = document.getElementById('name');
            if (nameField) nameField.focus();

            // Simple salary input formatting (if inputs exist)
            const minSalaryInput = document.querySelector('input[name="min_salary"]');
            const maxSalaryInput = document.querySelector('input[name="max_salary"]');
            [minSalaryInput, maxSalaryInput].forEach(input => {
                if (input) {
                    input.addEventListener('input', function() {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    });
                }
            });
        });
    </script>
@endpush
