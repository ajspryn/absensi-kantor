@extends('layouts.admin')

@section('title', 'Tambah Role')

@section('content')
    @include('admin.partials.alerts')
    <div class="card card-style shadow-m mb-4">
        <div class="content d-flex align-items-center justify-content-between">
            <div>
                <h3 class="font-700 mb-1 color-dark-dark"><i class="bi bi-shield-plus color-orange-dark me-2"></i>Tambah Role</h3>
                <p class="mb-0 font-12 opacity-70">Buat role baru dengan permissions</p>
            </div>
            <div>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-sm bg-theme text-dark rounded-s"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
            </div>
        </div>
    </div>

    <div class="card card-style shadow-m mb-4">
        <div class="content">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <!-- Role Name -->
                <div class="form-group mb-3">
                    <label for="name" class="form-label">Nama Role <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Manager, HR Staff" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Deskripsi role dan tanggung jawabnya">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Priority -->
                <div class="form-group mb-3">
                    <label for="priority" class="form-label">Priority Level <span class="text-danger">*</span></label>
                    <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                        <option value="">Pilih Priority</option>
                        @for ($i = 0; $i <= 100; $i += 10)
                            <option value="{{ $i }}" {{ old('priority') == $i ? 'selected' : '' }}>
                                {{ $i }}
                                @if ($i == 0)
                                    - Highest Priority
                                @elseif($i <= 30)
                                    - High Priority
                                @elseif($i <= 70)
                                    - Medium Priority
                                @else
                                    - Low Priority
                                @endif
                            </option>
                        @endfor
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Lower number = Higher priority. 0 adalah priority tertinggi</div>
                </div>

                <!-- Permissions -->
                <div class="form-group mb-4">
                    <label class="form-label">Permissions</label>
                    <div class="form-text mb-3">Pilih hak akses yang diberikan untuk role ini</div>

                    @foreach ($availablePermissions as $category => $permissions)
                        <div class="card border mb-3">
                            <div class="card-header bg-light">
                                <div class="form-check">
                                    <input class="form-check-input category-check" type="checkbox" id="category_{{ $category }}" data-category="{{ $category }}">
                                    <label class="form-check-label fw-bold" for="category_{{ $category }}">
                                        {{ ucfirst(str_replace('_', ' ', $category)) }}
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($permissions as $key => $label)
                                        <div class="col-12 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input permission-check" type="checkbox" name="permissions[]" value="{{ $key }}" id="permission_{{ $key }}" data-category="{{ $category }}" {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $key }}">
                                                    {{ $label }}
                                                    <small class="text-muted d-block">{{ $key }}</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @error('permissions')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status Options -->
                <div class="row mb-4">
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Role Aktif
                            </label>
                        </div>
                        <div class="form-text">Role aktif dapat digunakan untuk assign user</div>
                    </div>
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default">
                                Role Default
                            </label>
                        </div>
                        <div class="form-text">Role default untuk user baru</div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="row g-2">
                    <div class="col-6">
                        <button type="submit" class="btn btn-full rounded-s bg-highlight shadow-bg shadow-bg-s font-700 text-uppercase w-100">
                            <i class="bi bi-save me-2"></i>Simpan Role
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-full rounded-s btn-danger font-700 text-uppercase w-100">
                            <i class="bi bi-x-lg me-2"></i>Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus name field
            document.getElementById('name').focus();

            // Category checkbox functionality
            document.querySelectorAll('.category-check').forEach(categoryCheck => {
                categoryCheck.addEventListener('change', function() {
                    const category = this.dataset.category;
                    const permissionChecks = document.querySelectorAll(`.permission-check[data-category="${category}"]`);

                    permissionChecks.forEach(permissionCheck => {
                        permissionCheck.checked = this.checked;
                    });
                });
            });

            // Update category checkbox when individual permissions change
            document.querySelectorAll('.permission-check').forEach(permissionCheck => {
                permissionCheck.addEventListener('change', function() {
                    const category = this.dataset.category;
                    const categoryCheck = document.getElementById(`category_${category}`);
                    const permissionChecks = document.querySelectorAll(`.permission-check[data-category="${category}"]`);
                    const checkedPermissions = document.querySelectorAll(`.permission-check[data-category="${category}"]:checked`);

                    if (checkedPermissions.length === permissionChecks.length) {
                        categoryCheck.checked = true;
                        categoryCheck.indeterminate = false;
                    } else if (checkedPermissions.length > 0) {
                        categoryCheck.checked = false;
                        categoryCheck.indeterminate = true;
                    } else {
                        categoryCheck.checked = false;
                        categoryCheck.indeterminate = false;
                    }
                });
            });

            // Initialize category checkboxes state
            document.querySelectorAll('.category-check').forEach(categoryCheck => {
                const category = categoryCheck.dataset.category;
                const permissionChecks = document.querySelectorAll(`.permission-check[data-category="${category}"]`);
                const checkedPermissions = document.querySelectorAll(`.permission-check[data-category="${category}"]:checked`);

                if (checkedPermissions.length === permissionChecks.length) {
                    categoryCheck.checked = true;
                } else if (checkedPermissions.length > 0) {
                    categoryCheck.indeterminate = true;
                }
            });

            // Form validation
            const form = document.querySelector('form');
            const nameInput = document.getElementById('name');
            const prioritySelect = document.getElementById('priority');

            form.addEventListener('submit', function(e) {
                let errors = [];

                if (nameInput.value.trim().length < 2) {
                    errors.push('Nama role harus minimal 2 karakter');
                }

                if (!prioritySelect.value) {
                    errors.push('Pilih priority level');
                }

                if (errors.length > 0) {
                    e.preventDefault();
                    alert('Error:\n' + errors.join('\n'));
                    return false;
                }
            });

            // Character counter for description
            const descTextarea = document.getElementById('description');
            if (descTextarea) {
                const maxLength = 500;
                const counter = document.createElement('div');
                counter.className = 'form-text text-end';
                counter.id = 'desc-counter';
                descTextarea.parentNode.appendChild(counter);

                function updateCounter() {
                    const remaining = maxLength - descTextarea.value.length;
                    counter.textContent = `${descTextarea.value.length}/${maxLength} karakter`;
                    counter.className = remaining < 50 ? 'form-text text-end text-warning' : 'form-text text-end';
                }

                descTextarea.addEventListener('input', updateCounter);
                updateCounter();
            }
        });
    </script>
@endpush
