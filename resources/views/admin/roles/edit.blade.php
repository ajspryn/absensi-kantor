@extends('layouts.admin')

@section('title', 'Edit Role')

@section('header')
    @include('admin.header', [
        'title' => 'Edit Role',
        'backUrl' => route('admin.roles.index'),
    ])
@endsection

@section('content')
    @include('admin.partials.alerts')
    <div class="card card-style bg-white shadow-xl mb-4">
        <div class="content py-4 px-4">
            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Role Name -->
                <div class="form-group mb-3">
                    <label for="name" class="form-label">Nama Role <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" placeholder="Contoh: Manager, HR Staff" required {{ $role->is_system_role ? 'readonly' : '' }}>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if ($role->is_system_role)
                        <div class="form-text text-warning">System role name cannot be changed</div>
                    @endif
                </div>

                <!-- Description -->
                <div class="form-group mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Deskripsi role dan tanggung jawabnya">{{ old('description', $role->description) }}</textarea>
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
                            <option value="{{ $i }}" {{ old('priority', $role->priority) == $i ? 'selected' : '' }}>
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
                </div>

                <!-- Current Role Stats -->
                <div class="card card-style bg-white mb-3">
                    <div class="content">
                        <h6 class="mb-2"><i class="bi bi-info-circle me-2"></i>Informasi Role</h6>
                        <div class="row">
                            <div class="col-6">
                                <small><strong>Total Users:</strong> {{ $role->users->count() }}</small>
                            </div>
                            <div class="col-6">
                                <small><strong>Permissions:</strong> {{ count($role->permissions ?? []) }}</small>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-6">
                                <small><strong>Status:</strong>
                                    <span class="badge {{ $role->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $role->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </small>
                            </div>
                            <div class="col-6">
                                <small><strong>Type:</strong>
                                    <span class="badge {{ $role->is_system_role ? 'bg-warning' : 'bg-primary' }}">
                                        {{ $role->is_system_role ? 'System' : 'Custom' }}
                                    </span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="form-group mb-4">
                    <label class="form-label">Permissions</label>
                    <div class="form-text mb-3">Pilih hak akses yang diberikan untuk role ini</div>

                    @foreach ($availablePermissions as $category => $permissions)
                        <div class="card card-style bg-white mb-3">
                            <div class="content">
                                <div class="form-check mb-2">
                                    <input class="form-check-input category-check" type="checkbox" id="category_{{ $category }}" data-category="{{ $category }}">
                                    <label class="form-check-label fw-bold" for="category_{{ $category }}">
                                        {{ ucfirst(str_replace('_', ' ', $category)) }}
                                    </label>
                                </div>
                                <div class="row">
                                    @foreach ($permissions as $key => $label)
                                        <div class="col-12 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input permission-check" type="checkbox" name="permissions[]" value="{{ $key }}" id="permission_{{ $key }}" data-category="{{ $category }}" {{ in_array($key, old('permissions', $role->permissions ?? [])) ? 'checked' : '' }}>
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
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $role->is_active) ? 'checked' : '' }} {{ $role->is_system_role ? 'disabled' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Role Aktif
                            </label>
                        </div>
                        @if ($role->is_system_role)
                            <div class="form-text text-warning">System role cannot be deactivated</div>
                        @else
                            <div class="form-text">Role aktif dapat digunakan untuk assign user</div>
                        @endif
                    </div>
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default', $role->is_default) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default">
                                Role Default
                            </label>
                        </div>
                        <div class="form-text">Role default untuk user baru</div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <button type="submit" class="btn btn-full btn gradient-orange shadow-bg shadow-bg-s rounded-s font-700 text-uppercase mb-2 w-100">
                            <i class="bi bi-save me-2"></i>Perbarui Role
                        </button>
                    </div>
                    <div class="col-12 col-md-6">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-full btn gradient-dark shadow-bg shadow-bg-s rounded-s font-700 text-uppercase mb-2 w-100">
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

                if (checkedPermissions.length === permissionChecks.length && permissionChecks.length > 0) {
                    categoryCheck.checked = true;
                } else if (checkedPermissions.length > 0) {
                    categoryCheck.indeterminate = true;
                }
            });

            // Form validation
            const form = document.querySelector('form');
            const nameInput = document.getElementById('name');
            const prioritySelect = document.getElementById('priority');
            const isActiveCheckbox = document.getElementById('is_active');

            form.addEventListener('submit', function(e) {
                let errors = [];

                if (nameInput.value.trim().length < 2) {
                    errors.push('Nama role harus minimal 2 karakter');
                }

                if (!prioritySelect.value) {
                    errors.push('Pilih priority level');
                }

                // Warning if deactivating role with users
                @if ($role->users->count() > 0)
                    if (!isActiveCheckbox.checked && !isActiveCheckbox.disabled) {
                        if (!confirm('Role ini memiliki {{ $role->users->count() }} users. Yakin ingin menonaktifkan?')) {
                            e.preventDefault();
                            isActiveCheckbox.checked = true;
                            return false;
                        }
                    }
                @endif

                if (errors.length > 0) {
                    e.preventDefault();
                    alert('Error:\n' + errors.join('\n'));
                    return false;
                }
            });
        });
    </script>
@endpush
