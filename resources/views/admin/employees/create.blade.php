@extends('layouts.admin')

@section('title', 'Tambah Karyawan - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Tambah Karyawan',
        'backUrl' => route('admin.employees.index'),
    ])
@endsection

@section('content')
    @include('admin.partials.section-header', [
        'title' => 'Tambah Karyawan Baru',
        'subtitle' => 'Lengkapi data karyawan di bawah ini',
        'icon' => 'bi bi-person-plus-fill',
    ])

    <!-- Form Card -->
    <div class="card card-style shadow-m mb-3">
        <div class="content">
            @include('admin.partials.alerts')
            <form action="{{ route('admin.employees.store') }}" method="POST" id="employeeForm">
                @csrf

                <!-- Data Personal -->
                <div class="mb-4">
                    <h6 class="font-600 mb-3 color-blue-dark">
                        <i class="bi bi-person me-2"></i>Data Personal
                    </h6>

                    <div class="mb-3">
                        <label for="name" class="form-label font-600">Nama Lengkap <span class="color-red-dark">*</span></label>
                        <input type="text" class="form-control rounded-xl" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label font-600">Email <span class="color-red-dark">*</span></label>
                        <input type="email" class="form-control rounded-xl" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="employee_id" class="form-label font-600">ID Karyawan <span class="color-red-dark">*</span></label>
                        <input type="text" class="form-control rounded-xl" id="employee_id" name="employee_id" value="{{ old('employee_id') }}" placeholder="Contoh: EMP001" required>
                        @error('employee_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Data Organisasi -->
                <div class="mb-4">
                    <h6 class="font-600 mb-3 color-green-dark">
                        <i class="bi bi-building me-2"></i>Data Organisasi
                    </h6>

                    <div class="mb-3">
                        <label for="department_id" class="form-label font-600">Departemen <span class="color-red-dark">*</span></label>
                        <select class="form-select rounded-xl" id="department_id" name="department_id" required>
                            <option value="">-- Pilih Departemen --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="position_id" class="form-label font-600">Posisi <span class="color-red-dark">*</span></label>
                        <select class="form-select rounded-xl" id="position_id" name="position_id" required>
                            <option value="">-- Pilih Posisi --</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}" data-department="{{ $position->department_id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="work_schedule_id" class="form-label font-600">Jadwal Kerja <span class="color-red-dark">*</span></label>
                        <select class="form-select rounded-xl" id="work_schedule_id" name="work_schedule_id" required>
                            <option value="">-- Pilih Jadwal Kerja --</option>
                            @foreach ($workSchedules as $schedule)
                                <option value="{{ $schedule->id }}" {{ old('work_schedule_id') == $schedule->id ? 'selected' : '' }}>
                                    {{ $schedule->name }} ({{ $schedule->getWorkingHoursRange() }})
                                </option>
                            @endforeach
                        </select>
                        @error('work_schedule_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Data Akun -->
                <div class="mb-4">
                    <h6 class="font-600 mb-3 color-orange-dark">
                        <i class="bi bi-key me-2"></i>Data Akun
                    </h6>

                    <div class="mb-3">
                        <label for="password" class="form-label font-600">Password <span class="color-red-dark">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control rounded-start-xl" id="password" name="password" placeholder="Minimal 8 karakter" required>
                            <button class="btn btn-secondary rounded-end-xl" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Password harus minimal 8 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label font-600">Konfirmasi Password <span class="color-red-dark">*</span></label>
                        <input type="password" class="form-control rounded-xl" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row g-2 mt-4">
                    <div class="col-6">
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary rounded-xl w-100">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </a>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary rounded-xl w-100">
                            <i class="bi bi-check-circle me-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card card-style bg-blue-dark shadow-s mb-3">
        <div class="content">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle color-white font-20 me-3"></i>
                <div>
                    <h6 class="mb-1 color-white font-600">Informasi</h6>
                    <p class="mb-0 font-12 color-white opacity-80">
                        Pastikan data yang dimasukkan sudah benar. Karyawan akan menerima akun login setelah data tersimpan.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dependent select: positions by department
            const departmentSelect = document.getElementById('department_id');
            const positionSelect = document.getElementById('position_id');

            function setPositionOptions(options) {
                const oldSelected = '{{ old('position_id') }}';
                positionSelect.innerHTML = '<option value="">-- Pilih Posisi --</option>';
                options.forEach(function(pos) {
                    const opt = document.createElement('option');
                    opt.value = pos.id;
                    opt.textContent = pos.name + (pos.level ? ' (Level ' + pos.level + ')' : '');
                    if (String(oldSelected) === String(pos.id)) opt.selected = true;
                    positionSelect.appendChild(opt);
                });
            }

            async function loadPositionsByDepartment(deptId) {
                if (!deptId) {
                    // Reset to placeholder when no department selected
                    positionSelect.innerHTML = '<option value="">-- Pilih Posisi --</option>';
                    return;
                }
                try {
                    const url = '{{ route('admin.positions.by-department') }}' + '?department_id=' + encodeURIComponent(deptId);
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error('Gagal memuat posisi');
                    const data = await res.json();
                    setPositionOptions(data);
                } catch (e) {
                    console.error(e);
                }
            }

            // On change department, fetch positions
            if (departmentSelect) {
                departmentSelect.addEventListener('change', function() {
                    loadPositionsByDepartment(this.value);
                });
                // Initial load if old department exists
                const initialDept = departmentSelect.value || '{{ old('department_id') }}';
                if (initialDept) {
                    loadPositionsByDepartment(initialDept);
                }
            }

            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    if (type === 'text') {
                        eyeIcon.classList.remove('bi-eye');
                        eyeIcon.classList.add('bi-eye-slash');
                    } else {
                        eyeIcon.classList.remove('bi-eye-slash');
                        eyeIcon.classList.add('bi-eye');
                    }
                });
            }

            // Form validation
            const form = document.getElementById('employeeForm');
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const passwordConfirmation = document.getElementById('password_confirmation').value;

                if (password !== passwordConfirmation) {
                    alert('Password dan konfirmasi password tidak sama!');
                    return false;
                }

                if (password.length < 8) {
                    alert('Password harus minimal 8 karakter!');
                    return false;
                }
                // Jika valid, biarkan form submit
                return true;
            });
        });
    </script>
@endpush
