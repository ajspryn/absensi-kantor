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
<style>
    .step-content { display: none; }
    .step-content.active { display: block; animation: fadeIn 0.4s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateX(10px); } to { opacity: 1; transform: translateX(0); } }

    .stepper-nav { display: flex; overflow-x: auto; padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid rgba(0,0,0,0.05); scrollbar-width: none; }
    .stepper-nav::-webkit-scrollbar { display: none; }
    .stepper-item { flex: 0 0 auto; padding: 10px 15px; text-align: center; color: #a0a0a0; font-weight: 700; font-size: 12px; position: relative; transition: all 0.3s; cursor: pointer; }
    .stepper-item.active { color: #8CC152; border-bottom: 2px solid #8CC152; }
    .stepper-item.completed { color: #4A89DC; }

    .repeater-card {
        background: #ffffff;
        border-radius: 15px;
        padding: 20px;
        padding-top: 45px;
        margin-bottom: 20px;
        position: relative;
        border: 1px solid rgba(0,0,0,0.07);
        box-shadow: 0 3px 10px rgba(0,0,0,0.03);
    }
    .theme-dark .repeater-card {
        background: rgba(255,255,255,0.02);
        border-color: rgba(255,255,255,0.05);
        box-shadow: none;
    }
    .repeater-input-group { margin-bottom: 12px; }
    .repeater-input-group label {
        display: block;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--theme-highlight);
        margin-bottom: 4px;
        padding-left: 2px;
        opacity: 0.7;
    }

    .repeater-remove {
        position: absolute;
        top: 12px;
        right: 12px;
        border-radius: 8px !important;
        width: 28px;
        height: 28px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border: none;
    }

    .repeater-upload-area {
        position: relative;
        border: 1px dashed rgba(74, 137, 220, 0.3);
        background: rgba(74, 137, 220, 0.03);
        border-radius: 12px;
        padding: 12px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
    }
    .repeater-upload-area:hover {
        border-color: var(--theme-highlight);
        background: rgba(74, 137, 220, 0.08);
    }
    .repeater-upload-area i { font-size: 22px; color: var(--theme-highlight); display: block; margin-bottom: 2px; pointer-events: none; }
    .repeater-upload-area .upload-title { font-size: 11px; font-weight: 700; display: block; color: var(--theme-highlight); pointer-events: none; }
    .repeater-upload-area .upload-sub { font-size: 9px; opacity: 0.5; display: block; pointer-events: none; }
    .repeater-upload-area input[type=file] { position: absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer; z-index: 10; }

    .form-custom { margin-bottom: 20px !important; }
    .border-red-dark { border: 1px solid #d84558 !important; box-shadow: 0 0 5px rgba(216, 69, 88, 0.2) !important; }
    .file-data-card {
        border: 1px dashed rgba(0,0,0,0.1) !important;
        background: rgba(0,0,0,0.01) !important;
        transition: all 0.3s;
    }
    .file-data-card:hover { border-color: var(--theme-highlight) !important; background: rgba(0,0,0,0.03) !important; }
    .theme-dark .file-data-card { border-color: rgba(255,255,255,0.1) !important; background: rgba(255,255,255,0.02) !important; }
    .upload-file-wrapper { position: relative; overflow: hidden; display: inline-block; }
    .upload-file-wrapper input[type=file] { position: absolute; left: 0; top: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer; }
</style>
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

                    <div class="col-12 mb-3">

</div>

                    <div class="col-12 mb-3">

</div>

                    <div class="col-12 mb-3">

</div>

                    <div class="col-12 mb-3">

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
                <h6 class="font-600 mb-3 color-dark-dark">
                    <i class="bi bi-file-earmark-text me-2"></i>Dokumen Pendukung
                </h6>
                <div class="row g-2">
                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-file-pdf font-14"></i>
                            <input type="file" class="form-control rounded-xl" name="ktp_file" accept=".pdf,.jpg,.jpeg,.png" style="border:none; padding-top: 15px;" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Upload KTP (Ganti)</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-file-pdf font-14"></i>
                            <input type="file" class="form-control rounded-xl" name="kk_file" accept=".pdf,.jpg,.jpeg,.png" style="border:none; padding-top: 15px;" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Upload KK (Ganti)</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-file-pdf font-14"></i>
                            <input type="file" class="form-control rounded-xl" name="marriage_certificate_file" accept=".pdf,.jpg,.jpeg,.png" style="border:none; padding-top: 15px;" />
                            <label class="badge bg-theme text-white px-2 py-1 mb-1" style="font-size:13px;">Upload Surat Nikah (Ganti)</label>
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

        // === 3. REPEATER LOGIC & FULL TEMPLATES ===
        function makeIndex(container) {
            const rows = container.querySelectorAll(':scope > div');
            rows.forEach((row, idx) => {
                row.setAttribute('data-index', idx);
                row.querySelectorAll('input, select, textarea').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) input.setAttribute('name', name.replace(/\[\d+\]|\[__INDEX__\]/g, '['+idx+']'));

                    const oldId = input.getAttribute('id');
                    if (oldId) {
                        const newId = oldId.replace(/\d+|__INDEX__/g, idx);
                        input.setAttribute('id', newId);
                        const label = row.querySelector(`label[for="${oldId}"]`);
                        if (label) label.setAttribute('for', newId);
                    }

                    const target = input.getAttribute('data-target');
                    if (target) {
                        const newTarget = target.replace(/\d+|__INDEX__/g, idx);
                        input.setAttribute('data-target', newTarget);
                        const statusSpan = row.querySelector('.upload-title');
                        if (statusSpan) statusSpan.setAttribute('id', newTarget);
                    }
                });
            });
        }

        function addRow(listId, templateHtml) {
            const list = document.getElementById(listId);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = templateHtml.replace(/__INDEX__/g, list.children.length);
            list.appendChild(wrapper.firstElementChild);
            makeIndex(list);
        }

        //
        const eduTemplate = `<div class="edu-row repeater-card"><button type="button" class="btn btn-xxs bg-red-dark repeater-remove shadow-bg shadow-bg-xs"><i class="bi bi-x-lg font-12"></i></button><div class="row mb-0"><div class="col-12 col-md-6 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-building font-14"></i><input type="text" name="education[__INDEX__][school_name]" id="edu_school___INDEX__" class="form-control rounded-s" placeholder="Institusi"><label for="edu_school___INDEX__" class="form-label-always-active color-highlight">Institusi</label></div></div><div class="col-12 col-md-6 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-book font-14"></i><input type="text" name="education[__INDEX__][major]" id="edu_major___INDEX__" class="form-control rounded-s" placeholder="Jurusan"><label for="edu_major___INDEX__" class="form-label-always-active color-highlight">Jurusan</label></div></div><div class="col-7 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-geo-alt font-14"></i><input type="text" name="education[__INDEX__][city]" id="edu_city___INDEX__" class="form-control rounded-s" placeholder="Kota"><label for="edu_city___INDEX__" class="form-label-always-active color-highlight">Kota</label></div></div><div class="col-5 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-calendar font-14"></i><input type="number" name="education[__INDEX__][start_year]" id="edu_year___INDEX__" class="form-control rounded-s" placeholder="Tahun"><label for="edu_year___INDEX__" class="form-label-always-active color-highlight">Tahun Lulus</label></div></div><div class="col-12"><div class="repeater-upload-area"><input type="file" name="education[__INDEX__][certificate]" class="upload-file-input" data-target="edu-cert-__INDEX__" accept="image/*,.pdf" /><i class="bi bi-cloud-arrow-up"></i><span class="upload-title" id="edu-cert-__INDEX__">Pilih Ijazah</span><span class="upload-sub">PDF/Gambar (Maks 4MB)</span></div></div></div></div>`;

        const trTemplate = `<div class="tr-row repeater-card"><button type="button" class="btn btn-xxs bg-red-dark repeater-remove shadow-bg shadow-bg-xs"><i class="bi bi-x-lg font-12"></i></button><div class="row mb-0"><div class="col-12 col-md-6 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-award font-14"></i><input type="text" name="training[__INDEX__][course_name]" id="tr_name___INDEX__" class="form-control rounded-s" placeholder="Nama Pelatihan"><label for="tr_name___INDEX__" class="form-label-always-active color-highlight">Nama Pelatihan</label></div></div><div class="col-12 col-md-6 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-patch-check font-14"></i><input type="text" name="training[__INDEX__][organizer]" id="tr_org___INDEX__" class="form-control rounded-s" placeholder="Penyelenggara"><label for="tr_org___INDEX__" class="form-label-always-active color-highlight">Penyelenggara</label></div></div><div class="col-12"><div class="repeater-upload-area"><input type="file" name="training[__INDEX__][certificate]" class="upload-file-input" data-target="tr-cert-__INDEX__" accept="image/*,.pdf" /><i class="bi bi-patch-check"></i><span class="upload-title" id="tr-cert-__INDEX__">Pilih Sertifikat</span><span class="upload-sub">PDF/Gambar (Maks 4MB)</span></div></div></div></div>`;

        const famTemplate = `<div class="family-row repeater-card"><button type="button" class="btn btn-xxs bg-red-dark repeater-remove shadow-bg shadow-bg-xs"><i class="bi bi-x-lg font-12"></i></button><div class="row mb-0"><div class="col-12 col-md-6 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-person font-14"></i><input type="text" name="family[__INDEX__][name]" id="fam_name___INDEX__" class="form-control rounded-s" placeholder="Nama"><label for="fam_name___INDEX__" class="form-label-always-active color-highlight">Nama Lengkap</label></div></div><div class="col-6 col-md-6 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-people font-14"></i><input type="text" name="family[__INDEX__][relation]" id="fam_rel___INDEX__" class="form-control rounded-s" placeholder="Hubungan"><label for="fam_rel___INDEX__" class="form-label-always-active color-highlight">Hubungan</label></div></div><div class="col-6 col-md-6 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-gender-ambiguous font-14"></i><select name="family[__INDEX__][gender]" id="fam_gender___INDEX__" class="form-control rounded-s"><option value="M">Laki-laki</option><option value="F">Perempuan</option></select><label for="fam_gender___INDEX__" class="form-label-always-active color-highlight">Gender</label></div></div><div class="col-12 col-md-6 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-calendar font-14"></i><input type="number" name="family[__INDEX__][age]" id="fam_age___INDEX__" class="form-control rounded-s" placeholder="Usia"><label for="fam_age___INDEX__" class="form-label-always-active color-highlight">Usia</label></div></div></div></div>`;

        const emTemplate = `<div class="em-row repeater-card"><button type="button" class="btn btn-xxs bg-red-dark repeater-remove shadow-bg shadow-bg-xs"><i class="bi bi-x-lg font-12"></i></button><div class="row mb-0"><div class="col-12 col-md-6 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-person font-14"></i><input type="text" name="emergency[__INDEX__][name]" id="em_name___INDEX__" class="form-control rounded-s" placeholder="Nama" required><label for="em_name___INDEX__" class="form-label-always-active color-highlight">Nama Lengkap *</label></div></div><div class="col-6 col-md-6 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-people font-14"></i><input type="text" name="emergency[__INDEX__][relation]" id="em_rel___INDEX__" class="form-control rounded-s" placeholder="Hubungan" required><label for="em_rel___INDEX__" class="form-label-always-active color-highlight">Hubungan *</label></div></div><div class="col-6 col-md-12 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-phone font-14"></i><input type="tel" name="emergency[__INDEX__][phone]" id="em_phone___INDEX__" class="form-control rounded-s" placeholder="Telepon" required><label for="em_phone___INDEX__" class="form-label-always-active color-highlight">Nomor Telepon *</label></div></div></div></div>`;

        const finTemplate = `<div class="fin-row repeater-card"><button type="button" class="btn btn-xxs bg-red-dark repeater-remove shadow-bg shadow-bg-xs"><i class="bi bi-x-lg font-12"></i></button><div class="row mb-0"><div class="col-12 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-bank font-14"></i><input type="text" name="financing[__INDEX__][institution]" id="fin_inst___INDEX__" class="form-control rounded-s" placeholder="Bank"><label for="fin_inst___INDEX__" class="form-label-always-active color-highlight">Lembaga Keuangan</label></div></div><div class="col-6 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-cash-stack font-14"></i><input type="number" name="financing[__INDEX__][plafond]" id="fin_plafond___INDEX__" class="form-control rounded-s" placeholder="Rp"><label for="fin_plafond___INDEX__" class="form-label-always-active color-highlight">Plafond</label></div></div><div class="col-6 mb-2"><div class="form-custom form-label form-icon"><i class="bi bi-calendar font-14"></i><input type="number" name="financing[__INDEX__][monthly_installment]" id="fin_cicilan___INDEX__" class="form-control rounded-s" placeholder="Rp"><label for="fin_cicilan___INDEX__" class="form-label-always-active color-highlight">Cicilan</label></div></div></div></div>`;

        document.getElementById('add-education').addEventListener('click', () => addRow('education-list', eduTemplate));
        document.getElementById('add-training').addEventListener('click', () => addRow('training-list', trTemplate));
        document.getElementById('add-family').addEventListener('click', () => addRow('family-list', famTemplate));
        document.getElementById('add-emergency').addEventListener('click', () => addRow('emergency-list', emTemplate));
        document.getElementById('add-financing').addEventListener('click', () => addRow('financing-list', finTemplate));

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.repeater-remove');
            if (!btn) return;
            const row = btn.closest('.repeater-card');
            const list = row.parentElement;
            row.remove();
            makeIndex(list);
        });

        // === 4. UPLOAD STATUS & PREVIEW ===
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('upload-file-input')) {
                const input = e.target;
                const statusId = input.getAttribute('data-target');
                const statusEl = document.getElementById(statusId);
                if (statusEl && input.files[0]) {
                    statusEl.innerHTML = `<span class="color-green-dark font-600"><i class="bi bi-check-circle-fill"></i> ${input.files[0].name}</span>`;
                    const card = input.closest('.repeater-upload-area') || input.closest('.file-data-card');
                    if (card) { card.style.borderColor = '#8CC152'; card.style.backgroundColor = 'rgba(140, 193, 82, 0.05)'; }
                }
            }
        });

        const maritalSelect = document.getElementById('marital_status');
        if (maritalSelect) {
            maritalSelect.addEventListener('change', () => {
                document.getElementById('marriage_certificate_wrapper').style.display = (maritalSelect.value === 'menikah') ? 'block' : 'none';
            });
        }
    });
    });
</script>
    @endpush
@endsection
