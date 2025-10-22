@extends('layouts.admin')

@section('title', 'Edit Posisi')


@section('content')
    @include('admin.partials.alerts')

    <div class="card card-style mb-3">
        <div class="content">
            <form action="{{ route('admin.positions.update', $position) }}" method="POST">
                @csrf
                @method('PUT')
                <h5 class="font-600 mb-3"><i class="fa fa-edit me-2"></i>Edit Posisi</h5>
                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-briefcase font-14"></i>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $position->name) }}" placeholder="Contoh: Software Engineer, HR Manager" required>
                            <label for="name" class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Nama Posisi <span class="text-danger">*</span></label>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- Note: department and level fields removed per UX request -->
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-file-text font-14"></i>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Deskripsi tugas dan tanggung jawab posisi ini">{{ old('description', $position->description) }}</textarea>
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
                                <input type="number" class="form-control @error('min_salary') is-invalid @enderror" name="min_salary" value="{{ old('min_salary', $position->min_salary) }}" placeholder="Gaji Minimum" min="0" step="1000">
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
                                <input type="number" class="form-control @error('max_salary') is-invalid @enderror" name="max_salary" value="{{ old('max_salary', $position->max_salary) }}" placeholder="Gaji Maximum" min="0" step="1000">
                                <label for="max_salary" class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Gaji Maksimum</label>
                                @error('max_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <div class="col-12">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-2"></i>Perbarui Posisi
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

    <!-- Employees with this Position -->
    @if ($position->employees->count() > 0)
        <div class="card card-style mt-3">
            <div class="card-header">
                <h5 class="mb-0">Karyawan dengan Posisi Ini ({{ $position->employees->count() }})</h5>
            </div>
            <div class="card-body">
                @foreach ($position->employees as $employee)
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $employee->user->name }}</h6>
                            <small class="text-muted">{{ $employee->employee_id }}</small>
                            @if ($employee->department)
                                <small class="text-muted"> - {{ optional($employee->department)->name ?? '-' }}</small>
                            @endif
                        </div>
                        <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-sm btn-primary text-white">
                            <i class="fa fa-eye"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameField = document.getElementById('name');
            if (nameField) nameField.focus();

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
