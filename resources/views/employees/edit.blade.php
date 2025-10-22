@extends('layouts.admin')

@section('title', 'Edit Karyawan - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Edit Karyawan',
        'backUrl' => route('admin.employees.show', $employee),
        'rightHtml' => '<a href="' . route('admin.employees.show', $employee) . '" class="me-1"><i class="bi bi-eye font-13 color-highlight"></i></a>',
    ])
@endsection

@section('content')
    @include('admin.partials.section-header', [
        'title' => 'Edit Karyawan',
        'subtitle' => 'Perbarui data karyawan di bawah ini',
        'icon' => 'bi bi-person-lines-fill',
    ])

    @include('admin.partials.alerts')

    <form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data" id="employeeEditForm">
        @csrf
        @method('PUT')
        <div class="card card-style mb-3">
            <div class="content">
                <h6 class="font-600 mb-3 color-blue-dark">
                    <i class="bi bi-person me-2"></i>Foto Profil
                </h6>
                <div class="text-center mb-3">
                    @if ($employee->photo)
                        <img src="{{ Storage::url($employee->photo) }}" alt="Photo" class="rounded-circle mb-2 border-4 border-highlight" style="width: 90px; height: 90px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-2 border-4 border-highlight" style="width: 90px; height: 90px;">
                            <i class="bi bi-person font-40 color-white-50"></i>
                        </div>
                    @endif
                    <label class="badge bg-highlight text-white px-3 py-2 mb-2" style="font-size:13px;">Ganti Foto (Opsional)</label>
                    <input type="file" class="btn bg-highlight text-white w-100" name="photo" accept="image/*" style="border:none;" />
                </div>
            </div>
        </div>
        <div class="card card-style mb-3">
            <div class="content">
                <h6 class="font-600 mb-3 color-green-dark">
                    <i class="bi bi-building me-2"></i>Data Organisasi
                </h6>
                <div class="row g-2">
                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-hash font-14"></i>
                            <input type="text" class="form-control rounded-xl" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" required placeholder="Nomor Karyawan" />
                            <label for="employee_id" class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Nomor Karyawan</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-person font-14"></i>
                            <input type="text" class="form-control rounded-xl" name="full_name" value="{{ old('full_name', $employee->full_name) }}" required placeholder="Nama Lengkap" />
                            <label for="full_name" class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Nama Lengkap</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-envelope font-14"></i>
                            <input type="email" class="form-control rounded-xl" name="email" value="{{ old('email', $employee->user->email) }}" required placeholder="Email" />
                            <label for="email" class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Email</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-briefcase font-14"></i>
                            <select class="form-select rounded-xl" id="position_id" name="position_id" required>
                                <option value="">-- Pilih Posisi --</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}" data-department="{{ $position->department_id }}" {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                @endforeach
                            </select>
                            <label for="position_id" class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Posisi/Jabatan</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-building font-14"></i>
                            <select class="form-select rounded-xl" id="department_id" name="department_id" required>
                                <option value="">-- Pilih Departemen --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                @endforeach
                            </select>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Departemen <span class="color-red-dark">*</span></label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-shield-check font-14"></i>
                            <select class="form-select rounded-xl" id="role_id" name="role_id" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $employee->user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <label for="role_id" class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Role Pengguna</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-calendar-date font-14"></i>
                            <input type="date" class="form-control rounded-xl" id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" required />
                            <label for="hire_date" class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Tanggal Bergabung</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-currency-dollar font-14"></i>
                            <input type="number" class="form-control rounded-xl" name="salary" value="{{ old('salary', $employee->salary) }}" min="0" placeholder="Gaji" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Gaji (Opsional)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-style mb-3">
            <div class="content">
                <h6 class="font-600 mb-3 color-orange-dark">
                    <i class="bi bi-key me-2"></i>Pengaturan Akun
                </h6>
                <div class="row g-2">
                    {{-- Hidden PK not needed; route provides the employee model. Removed duplicate name to avoid overwriting the employee number field. --}}
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-lock font-14"></i>
                            <input type="password" class="form-control rounded-xl" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Password Baru</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-lock font-14"></i>
                            <input type="password" class="form-control rounded-xl" name="password_confirmation" placeholder="Konfirmasi Password Baru" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Konfirmasi Password Baru</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-globe font-14"></i>
                            <select class="form-select rounded-xl" name="allow_remote_attendance">
                                <option value="0" {{ old('allow_remote_attendance', $employee->allow_remote_attendance) == '0' ? 'selected' : '' }}>Tidak - Harus di lokasi kantor</option>
                                <option value="1" {{ old('allow_remote_attendance', $employee->allow_remote_attendance) == '1' ? 'selected' : '' }}>Ya - Boleh absen dimana saja</option>
                            </select>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Izin Absen Remote</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-toggle-on font-14"></i>
                            <select class="form-select rounded-xl" name="is_active">
                                <option value="1" {{ old('is_active', $employee->user->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('is_active', $employee->user->is_active) == '0' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Status</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-style mb-3">
            <div class="content">
                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <button type="submit" class="btn btn-full rounded-xl bg-highlight shadow-bg shadow-bg-s font-700 text-uppercase mb-2 w-100">
                            <i class="bi bi-check-circle pe-2"></i>Update
                        </button>
                    </div>
                    <div class="col-12 col-md-6">
                        <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-full rounded-xl bg-theme shadow-bg shadow-bg-s font-700 text-uppercase mb-2 w-100">
                            <i class="bi bi-x-circle pe-2"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Dependent select: positions by department
                const departmentSelect = document.getElementById('department_id');
                const positionSelect = document.getElementById('position_id');

                function setPositionOptions(options) {
                    const oldSelected = '{{ old('position_id', $employee->position_id) }}';
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

                if (departmentSelect) {
                    departmentSelect.addEventListener('change', function() {
                        loadPositionsByDepartment(this.value);
                    });
                    const initialDept = departmentSelect.value || '{{ old('department_id', $employee->department_id) }}';
                    if (initialDept) {
                        loadPositionsByDepartment(initialDept);
                    }
                }

                // Form validation
                const form = document.getElementById('employeeEditForm');
                form.addEventListener('submit', function(e) {
                    const password = form.querySelector('input[name="password"]').value;
                    const passwordConfirmation = form.querySelector('input[name="password_confirmation"]').value;
                    if (password && password !== passwordConfirmation) {
                        alert('Password dan konfirmasi password tidak sama!');
                        e.preventDefault();
                        return false;
                    }
                    if (password && password.length < 8) {
                        alert('Password harus minimal 8 karakter!');
                        e.preventDefault();
                        return false;
                    }
                    return true;
                });
            });
        </script>
    @endpush
@endsection
