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
                <h6 class="font-600 mb-3 color-brown">
                    <i class="bi bi-person-badge me-2"></i>Profil Lengkap
                </h6>
                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-credit-card-2-front font-14"></i>
                            <input type="text" class="form-control rounded-xl" name="nik_ktp" value="{{ old('nik_ktp', $employee->nik_ktp) }}" placeholder="NIK / Nomor KTP" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">NIK / KTP</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-briefcase font-14"></i>
                            <input type="text" class="form-control rounded-xl" name="jabatan" value="{{ old('jabatan', $employee->jabatan) }}" placeholder="Jabatan (deskripsi)" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Jabatan (deskripsi)</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-geo-alt font-14"></i>
                            <textarea class="form-control rounded-xl" name="address_ktp" placeholder="Alamat sesuai KTP">{{ old('address_ktp', $employee->address_ktp) }}</textarea>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Alamat Sesuai KTP</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-house-door font-14"></i>
                            <textarea class="form-control rounded-xl" name="address_domisili" placeholder="Alamat Domisili">{{ old('address_domisili', $employee->address_domisili) }}</textarea>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Alamat Domisili</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-phone font-14"></i>
                            <input type="text" class="form-control rounded-xl" name="mobile" value="{{ old('mobile', $employee->mobile) }}" placeholder="HP / Mobile" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">HP / Mobile</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-gender-ambiguous font-14"></i>
                            <select class="form-select rounded-xl" name="gender">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="M" {{ old('gender', $employee->gender) == 'M' ? 'selected' : '' }}>Pria</option>
                                <option value="F" {{ old('gender', $employee->gender) == 'F' ? 'selected' : '' }}>Wanita</option>
                            </select>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Jenis Kelamin</label>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-arrows-expand font-14"></i>
                            <input type="number" class="form-control rounded-xl" name="height_cm" value="{{ old('height_cm', $employee->height_cm) }}" placeholder="Tinggi (cm)" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Tinggi (cm)</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-balance-scale font-14"></i>
                            <input type="number" class="form-control rounded-xl" name="weight_kg" value="{{ old('weight_kg', $employee->weight_kg) }}" placeholder="Berat (kg)" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Berat (kg)</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-heart font-14"></i>
                            <input type="text" class="form-control rounded-xl" name="hobby" value="{{ old('hobby', $employee->hobby) }}" placeholder="Hobby" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Hobby</label>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-geo-alt-fill font-14"></i>
                            <input type="text" class="form-control rounded-xl" name="birth_place" value="{{ old('birth_place', $employee->birth_place) }}" placeholder="Tempat Lahir" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Tempat Lahir</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-calendar2-date font-14"></i>
                            <input type="date" class="form-control rounded-xl" name="birth_date" value="{{ old('birth_date', optional($employee->birth_date)->format('Y-m-d')) }}" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Tanggal Lahir</label>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-person-lines-fill font-14"></i>
                            <select class="form-select rounded-xl" name="marital_status">
                                <option value="">-- Pilih Status Perkawinan --</option>
                                <option value="belum" {{ old('marital_status', $employee->marital_status) == 'belum' ? 'selected' : '' }}>Belum Menikah</option>
                                <option value="menikah" {{ old('marital_status', $employee->marital_status) == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                <option value="cerai_hidup" {{ old('marital_status', $employee->marital_status) == 'cerai_hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                <option value="cerai_mati" {{ old('marital_status', $employee->marital_status) == 'cerai_mati' ? 'selected' : '' }}>Cerai Mati</option>
                            </select>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Status Perkawinan</label>
                        </div>
                    </div>

                    <div class="col-6 col-md-6">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-house-door-fill font-14"></i>
                            <select class="form-select rounded-xl" name="residence_status">
                                <option value="">-- Pilih Status Tempat Tinggal --</option>
                                <option value="milik_sendiri" {{ old('residence_status', $employee->residence_status) == 'milik_sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                                <option value="milik_orang_tua" {{ old('residence_status', $employee->residence_status) == 'milik_orang_tua' ? 'selected' : '' }}>Milik Orang Tua</option>
                                <option value="sewa" {{ old('residence_status', $employee->residence_status) == 'sewa' ? 'selected' : '' }}>Sewa</option>
                                <option value="lainnya" {{ old('residence_status', $employee->residence_status) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Status Tempat Tinggal</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-clipboard-heart font-14"></i>
                            <textarea class="form-control rounded-xl" name="health_condition" placeholder="Kondisi Kesehatan dan catatan">{{ old('health_condition', $employee->health_condition) }}</textarea>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Kondisi Kesehatan</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-activity font-14"></i>
                            <textarea class="form-control rounded-xl" name="degenerative_diseases" placeholder="Penyakit degeneratif (pisahkan koma jika lebih dari satu)">{{ old('degenerative_diseases', $employee->degenerative_diseases) }}</textarea>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Penyakit Degeneratif / Kronis</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-journal-text font-14"></i>
                            <textarea class="form-control rounded-xl" name="education_history" placeholder="Riwayat pendidikan (format JSON atau teks)">{{ old('education_history', optional($employee)->education_history ? json_encode($employee->education_history, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Riwayat Pendidikan (JSON)</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-award font-14"></i>
                            <textarea class="form-control rounded-xl" name="training_history" placeholder="Kursus / Training (format JSON atau teks)">{{ old('training_history', optional($employee)->training_history ? json_encode($employee->training_history, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Kursus / Training (JSON)</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-people-fill font-14"></i>
                            <textarea class="form-control rounded-xl" name="family_structure" placeholder="Susunan keluarga (format JSON atau teks)">{{ old('family_structure', optional($employee)->family_structure ? json_encode($employee->family_structure, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Susunan Keluarga (JSON)</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-phone-vibrate font-14"></i>
                            <textarea class="form-control rounded-xl" name="emergency_contact" placeholder="Orang yang dapat dihubungi (format JSON atau teks)">{{ old('emergency_contact', optional($employee)->emergency_contact ? json_encode($employee->emergency_contact, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Kontak Darurat (JSON)</label>
                        </div>
                    </div>
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
