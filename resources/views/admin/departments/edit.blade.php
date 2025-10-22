@extends('layouts.admin')

@section('title', 'Edit Departemen')

@push('styles')
    <style>
        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .form-switch .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }

        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
        }

        .page-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }

        .card-hover {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-lg {
            padding: 0.75rem 2rem;
            font-weight: 500;
        }

        .progress {
            border-radius: 10px;
        }

        .progress-bar {
            border-radius: 10px;
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }
    </style>
@endpush

@section('content')
    @include('admin.partials.alerts')
    <div class="card card-style shadow-m mb-4">
        <div class="content d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-pencil-square color-white font-18"></i>
                </div>
                <div>
                    <h3 class="font-700 mb-0 color-dark-dark">Edit Departemen</h3>
                    <p class="mb-0 font-12 opacity-70">Edit departemen: <strong>{{ $department->name }}</strong></p>
                </div>
            </div>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-sm bg-gray-dark color-white rounded-s">
                <i class="bi bi-arrow-left pe-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="card card-style shadow-m mb-4">
        <div class="content">
            <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Department Name -->
                <div class="form-custom form-label mb-3">
                    <label for="name" class="color-theme font-12 font-600 mb-1 d-block">Nama Departemen <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-s border-2 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $department->name) }}" placeholder="Contoh: IT, HR, Finance" required style="min-height: 45px;" />
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-custom form-label mb-3">
                    <label for="description" class="color-theme font-12 font-600 mb-1 d-block">Deskripsi</label>
                    <textarea class="form-control rounded-s border-2 @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Deskripsi singkat tentang departemen ini" style="min-height: 60px;">{{ old('description', $department->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Manager -->
                <div class="form-custom form-label mb-3">
                    <label for="manager_id" class="color-theme font-12 font-600 mb-1 d-block">Manager</label>
                    <select class="form-select rounded-s border-2 @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id" style="min-height: 45px;">
                        <option value="">Pilih Manager (Opsional)</option>
                        @foreach ($availableManagers as $employee)
                            @php $userId = $employee->user_id ?? optional($employee->user)->id; @endphp
                            <option value="{{ $userId }}" {{ old('manager_id', $department->manager_id) == $userId ? 'selected' : '' }}>
                                {{ optional($employee->user)->name ?? '(No user)' }} ({{ $employee->employee_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('manager_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if ($department->manager)
                        <div class="form-text text-success"><i class="bi bi-person-badge-fill me-1"></i>Manager saat ini: <strong>{{ optional($department->manager)->name ?? '(No name)' }}</strong></div>
                    @endif
                </div>

                <!-- Current Department Stats -->
                <div class="alert bg-info-dark rounded-s mb-3" role="alert">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-info-circle pe-2 font-14"></i>
                        </div>
                        <div class="align-self-center">
                            <strong class="font-12">Info:</strong><br>
                            <span class="font-11">Total Karyawan: <b>{{ $department->employees->count() }}</b> | Status: <span class="badge {{ $department->is_active ? 'bg-success-dark text-white' : 'bg-secondary-dark text-white' }}">{{ $department->is_active ? 'Aktif' : 'Nonaktif' }}</span></span>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="form-custom mb-3">
                    <div class="form-check form-switch d-flex align-items-center" style="min-height: 45px;">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                        <label class="form-check-label font-12 ms-2" for="is_active" style="margin-bottom:0;">
                            <i class="bi bi-toggle-on me-2 text-success"></i>Departemen Aktif
                        </label>
                    </div>
                    <div class="form-text mt-2">Departemen aktif dapat menerima karyawan baru</div>
                    @if ($department->employees->count() > 0 && !$department->is_active)
                        <div class="alert alert-warning mt-2 mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Departemen ini memiliki <strong>{{ $department->employees->count() }} karyawan</strong>
                        </div>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-full rounded-s btn-danger font-600 text-uppercase w-100" style="min-height: 45px;">
                            <i class="bi bi-x-circle pe-2"></i>Batal
                        </a>
                    </div>
                    <div class="col-6">
                        <button type="submit" class='btn btn-full rounded-s bg-highlight shadow-bg shadow-bg-s font-600 text-uppercase w-100' style="min-height: 45px;">
                            <i class="bi bi-check-circle pe-2"></i>Perbarui Departemen
                        </button>
                    </div>
                </div>
        </div>
        </form>
    </div>
    </div>

    <!-- Employees in Department -->
    @if ($department->employees->count() > 0)
        <div class="card card-style mt-4 shadow-sm">
            <div class="card-header bg-secondary-dark text-white">
                <h5 class="mb-0"><i class="fa fa-users me-2"></i>Karyawan di Departemen ({{ $department->employees->count() }})</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @foreach ($department->employees as $employee)
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100 card-hover">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ optional($employee->user)->name ?? '(No user)' }}</h6>
                                            <small class="text-muted">
                                                <i class="fa fa-id-badge me-1"></i>{{ $employee->employee_id }}
                                            </small>
                                            @if ($employee->getPositionName())
                                                <br><small class="text-primary">
                                                    <i class="fa fa-briefcase me-1"></i>{{ $employee->getPositionName() }}
                                                </small>
                                            @endif
                                        </div>
                                        @if ($employee->id == $department->manager_id)
                                            <span class="badge bg-success-dark text-white">
                                                <i class="fa fa-crown me-1"></i>Manager
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            const form = document.querySelector('form');
            const nameInput = document.getElementById('name');
            const isActiveCheckbox = document.getElementById('is_active');
            const submitBtn = form.querySelector('button[type="submit"]');

            // Real-time name validation
            nameInput.addEventListener('input', function() {
                const value = this.value.trim();
                if (value.length < 2) {
                    this.classList.add('is-invalid');
                    submitBtn.disabled = true;
                } else {
                    this.classList.remove('is-invalid');
                    submitBtn.disabled = false;
                }
            });

            form.addEventListener('submit', function(e) {
                // Validate name
                if (nameInput.value.trim().length < 2) {
                    e.preventDefault();
                    nameInput.classList.add('is-invalid');
                    nameInput.focus();
                    showToast('error', 'Nama departemen harus minimal 2 karakter');
                    return;
                }

                // Warning if deactivating department with employees
                @if ($department->employees->count() > 0)
                    if (!isActiveCheckbox.checked) {
                        if (!confirm('Departemen ini memiliki {{ $department->employees->count() }} karyawan. Yakin ingin menonaktifkan?')) {
                            e.preventDefault();
                            isActiveCheckbox.checked = true;
                            return;
                        }
                    }
                @endif

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Menyimpan...';
            });

            // Character counter for description
            const descTextarea = document.getElementById('description');
            if (descTextarea) {
                const maxLength = 500;
                const counterContainer = document.createElement('div');
                counterContainer.className = 'mt-1';

                const counter = document.createElement('div');
                counter.className = 'form-text text-end';
                counter.id = 'desc-counter';

                const progressBar = document.createElement('div');
                progressBar.className = 'progress mt-1';
                progressBar.style.height = '3px';

                const progressBarFill = document.createElement('div');
                progressBarFill.className = 'progress-bar';
                progressBar.appendChild(progressBarFill);

                counterContainer.appendChild(counter);
                counterContainer.appendChild(progressBar);
                descTextarea.parentNode.appendChild(counterContainer);

                function updateCounter() {
                    const currentLength = descTextarea.value.length;
                    const remaining = maxLength - currentLength;
                    const percentage = (currentLength / maxLength) * 100;

                    counter.textContent = `${currentLength}/${maxLength} karakter`;
                    progressBarFill.style.width = `${percentage}%`;

                    if (remaining < 50) {
                        counter.className = 'form-text text-end text-warning';
                        progressBarFill.className = 'progress-bar bg-warning';
                    } else if (remaining < 20) {
                        counter.className = 'form-text text-end text-danger';
                        progressBarFill.className = 'progress-bar bg-danger';
                    } else {
                        counter.className = 'form-text text-end text-muted';
                        progressBarFill.className = 'progress-bar bg-primary';
                    }
                }

                descTextarea.addEventListener('input', updateCounter);
                updateCounter();
            }

            // Status toggle enhancement
            isActiveCheckbox.addEventListener('change', function() {
                const label = this.nextElementSibling;
                const icon = label.querySelector('i');

                if (this.checked) {
                    icon.className = 'fa fa-toggle-on me-2 text-success';
                    label.innerHTML = icon.outerHTML + 'Departemen Aktif';
                } else {
                    icon.className = 'fa fa-toggle-off me-2 text-secondary';
                    label.innerHTML = icon.outerHTML + 'Departemen Nonaktif';
                }
            });

            // Toast notification function
            function showToast(type, message) {
                const toast = document.createElement('div');
                toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                toast.innerHTML = `
                    <i class="fa fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 5000);
            }
        });
    </script>
@endpush
