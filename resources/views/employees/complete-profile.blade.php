@extends('layouts.app')

@section('title', 'Lengkapi Profil - Aplikasi Absensi')

@section('page-class', 'header-clear-medium mb-0 pb-0')

@push('styles')
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
@endpush

@section('header')
    <!-- Header -->
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a href="#" class="header-title color-theme font-13">Lengkapi Profil</a>
        <a href="#" class="show-on-theme-light" data-toggle-theme><i class="bi bi-moon-fill font-13"></i></a>
        <a href="#" class="show-on-theme-dark" data-toggle-theme><i class="bi bi-lightbulb-fill color-yellow-dark font-13"></i></a>
    </div>
@endsection

@section('content')
    <div class="card card-style">
        <div class="content mb-0">
            <h1 class="text-center font-800 font-24 mb-1">Lengkapi Profil</h1>
            <p class="text-center font-12 mt-n1 mb-3 color-theme opacity-70">
                Silakan lengkapi data profil Anda (Langkah <span id="currentStepText">1</span> dari 5)
            </p>

            @if ($errors->any())
                <div class="alert bg-red-dark alert-dismissible color-white rounded-s fade show pe-2 mb-3" role="alert">
                    <strong>Error:</strong>
                    <ul class="mb-0 mt-2 font-12">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="stepper-nav" id="stepperNav">
                <div class="stepper-item active" data-step="1"><i class="bi bi-person pe-1"></i> Pribadi</div>
                <div class="stepper-item" data-step="2"><i class="bi bi-geo-alt pe-1"></i> Kontak</div>
                <div class="stepper-item" data-step="3"><i class="bi bi-briefcase pe-1"></i> Pegawai</div>
                <div class="stepper-item" data-step="4"><i class="bi bi-people pe-1"></i> Keluarga</div>
                <div class="stepper-item" data-step="5"><i class="bi bi-journal pe-1"></i> Riwayat</div>
            </div>

            <form id="profileForm" method="POST" action="{{ route('employee.profile.store') }}" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- STEP 1: Pribadi & Foto -->
                <div class="step-content active" id="step1">
                    <div class="file-data text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <img id="image-data" src="{{ optional(auth()->user()->employee)->photo ? asset('storage/' . optional(auth()->user()->employee)->photo) : asset('template/images/avatars/5s.png') }}" class="img-fluid rounded-circle border-4 border-theme shadow-l" style="width: 110px; height: 110px; object-fit: cover;" alt="Profile Photo">
                            <div class="position-absolute bottom-0 end-0 bg-blue-dark rounded-circle d-flex align-items-center justify-content-center shadow-m" style="width: 36px; height: 36px; cursor: pointer;" onclick="document.querySelector('.upload-file').click()">
                                <i class="bi bi-camera-fill color-white font-16"></i>
                            </div>
                        </div>
                        <div class="mt-2 d-none">
                            <input type="file" name="photo" class="upload-file" accept="image/*">
                        </div>
                        <p class="font-11 color-theme opacity-50 mt-2 mb-0">Ketuk ikon kamera untuk mengubah foto</p>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-person-badge font-14"></i>
                        <input type="text" class="form-control rounded-s" id="employee_id" name="employee_id" placeholder="EMP001" value="{{ old('employee_id', optional(auth()->user()->employee)->employee_id) }}" required />
                        <label for="employee_id" class="form-label-always-active color-highlight">ID Karyawan *</label>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-person-circle font-14"></i>
                        <input type="text" class="form-control rounded-s" id="full_name" name="full_name" placeholder="Nama Lengkap" value="{{ old('full_name', optional(auth()->user()->employee)->full_name ?? auth()->user()->name) }}" required />
                        <label for="full_name" class="form-label-always-active color-highlight">Nama Lengkap *</label>
                    </div>

                    <div class="row mb-0">
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-gender-ambiguous font-14"></i>
                                <select name="gender" id="gender" class="form-control rounded-s" required>
                                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                    <option value="M" {{ old('gender', optional(auth()->user()->employee)->gender) === 'M' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="F" {{ old('gender', optional(auth()->user()->employee)->gender) === 'F' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <label for="gender" class="form-label-always-active color-highlight">Jenis Kelamin *</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-geo-alt font-14"></i>
                                <input type="text" name="birth_place" id="birth_place" class="form-control rounded-s" placeholder="Tempat Lahir" value="{{ old('birth_place', optional(auth()->user()->employee)->birth_place) }}" required />
                                <label for="birth_place" class="form-label-always-active color-highlight">Tempat Lahir *</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-calendar-date font-14"></i>
                                @php
                                    $empBirthDate = optional(auth()->user()->employee)->birth_date;
                                    $birthDateStr = $empBirthDate instanceof \Carbon\Carbon || $empBirthDate instanceof \DateTime ? $empBirthDate->format('Y-m-d') : ($empBirthDate ? date('Y-m-d', strtotime((string)$empBirthDate)) : '');
                                @endphp
                                <input type="date" name="birth_date" id="birth_date" class="form-control rounded-s" value="{{ old('birth_date', $birthDateStr) }}" required />
                                <label for="birth_date" class="form-label-always-active color-highlight">Tanggal Lahir *</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-card-text font-14"></i>
                                <input type="text" name="nik_ktp" id="nik_ktp" class="form-control rounded-s" placeholder="NIK / No. KTP" value="{{ old('nik_ktp', optional(auth()->user()->employee)->nik_ktp) }}" maxlength="20" required />
                                <label for="nik_ktp" class="form-label-always-active color-highlight">NIK / No. KTP *</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-people-fill font-14"></i>
                                <select name="marital_status" id="marital_status" class="form-control rounded-s" required>
                                    <option value="" disabled selected>Pilih Status Perkawinan</option>
                                    <option value="belum" {{ old('marital_status', optional(auth()->user()->employee)->marital_status) == 'belum' ? 'selected' : '' }}>Belum Menikah</option>
                                    <option value="menikah" {{ old('marital_status', optional(auth()->user()->employee)->marital_status) == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                    <option value="cerai_hidup" {{ old('marital_status', optional(auth()->user()->employee)->marital_status) == 'cerai_hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                    <option value="cerai_mati" {{ old('marital_status', optional(auth()->user()->employee)->marital_status) == 'cerai_mati' ? 'selected' : '' }}>Cerai Mati</option>
                                </select>
                                <label for="marital_status" class="form-label-always-active color-highlight">Status Perkawinan *</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-house-door-fill font-14"></i>
                                <select name="residence_status" id="residence_status" class="form-control rounded-s" required>
                                    <option value="" disabled selected>Status Tempat Tinggal</option>
                                    <option value="milik_pribadi" {{ old('residence_status', optional(auth()->user()->employee)->residence_status) == 'milik_pribadi' ? 'selected' : '' }}>Milik Pribadi</option>
                                    <option value="milik_orangtua" {{ old('residence_status', optional(auth()->user()->employee)->residence_status) == 'milik_orangtua' ? 'selected' : '' }}>Milik Orangtua</option>
                                    <option value="sewa_kontrak" {{ old('residence_status', optional(auth()->user()->employee)->residence_status) == 'sewa_kontrak' ? 'selected' : '' }}>Sewa / Kontrak</option>
                                    <option value="lainnya" {{ old('residence_status', optional(auth()->user()->employee)->residence_status) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                <label for="residence_status" class="form-label-always-active color-highlight">Status Tinggal *</label>
                            </div>
                        </div>
                    </div>


                    <div class="divider mt-4"></div>
                    <div class="d-flex mb-3">
                        <div class="align-self-center">
                            <h5 class="font-700 mb-0">Dokumen Pendukung</h5>
                            <p class="font-11 color-theme opacity-50 mb-0">Format JPG, PNG, atau PDF (Maks 4MB)</p>
                        </div>
                        <div class="align-self-center ms-auto">
                            <i class="bi bi-file-earmark-arrow-up font-20 color-highlight"></i>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <!-- KTP Upload -->
                        <div class="col-6">
                            <div class="card card-style mx-0 mb-3 text-center py-3 file-data-card shadow-0">
                                <i class="bi bi-file-earmark-person font-30 color-blue-dark"></i>
                                <h6 class="font-13 mt-2 mb-0">KTP *</h6>
                                <p class="font-10 opacity-50 mb-2">Identity Card</p>
                                <div class="upload-file-wrapper">
                                    <input type="file" name="ktp_file" class="upload-file-input" data-target="ktp-status" accept="image/*,.pdf" />
                                    <span class="btn btn-xxs font-600 rounded-s bg-highlight px-2">Pilih File</span>
                                </div>
                                <div id="ktp-status" class="font-9 mt-2 text-truncate px-2">
                                    @if(optional(auth()->user()->employee)->ktp_path)
                                        <span class="color-green-dark"><i class="bi bi-check-circle-fill"></i> Terunggah</span>
                                    @else
                                        <span class="opacity-40 italic">Belum ada file</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- KK Upload -->
                        <div class="col-6">
                            <div class="card card-style mx-0 mb-3 text-center py-3 file-data-card shadow-0">
                                <i class="bi bi-file-earmark-medical font-30 color-green-dark"></i>
                                <h6 class="font-13 mt-2 mb-0">KK *</h6>
                                <p class="font-10 opacity-50 mb-2">Family Card</p>
                                <div class="upload-file-wrapper">
                                    <input type="file" name="kk_file" class="upload-file-input" data-target="kk-status" accept="image/*,.pdf" />
                                    <span class="btn btn-xxs font-600 rounded-s bg-highlight px-2">Pilih File</span>
                                </div>
                                <div id="kk-status" class="font-9 mt-2 text-truncate px-2">
                                    @if(optional(auth()->user()->employee)->kk_path)
                                        <span class="color-green-dark"><i class="bi bi-check-circle-fill"></i> Terunggah</span>
                                    @else
                                        <span class="opacity-40 italic">Belum ada file</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Marriage Cert Upload -->
                        <div class="col-12" id="marriage_certificate_wrapper" style="display: none;">
                            <div class="card card-style mx-0 mb-3 text-center py-3 file-data-card shadow-0">
                                <i class="bi bi-journal-check font-30 color-red-dark"></i>
                                <h6 class="font-13 mt-2 mb-0">Buku Nikah *</h6>
                                <p class="font-10 opacity-50 mb-2">Marriage Certificate</p>
                                <div class="upload-file-wrapper">
                                    <input type="file" name="marriage_certificate_file" class="upload-file-input" data-target="marriage-status" accept="image/*,.pdf" />
                                    <span class="btn btn-xxs font-600 rounded-s bg-highlight px-3">Pilih File Buku Nikah</span>
                                </div>
                                <div id="marriage-status" class="font-9 mt-2 text-truncate px-2">
                                    @if(optional(auth()->user()->employee)->marriage_certificate_path)
                                        <span class="color-green-dark"><i class="bi bi-check-circle-fill"></i> Terunggah</span>
                                    @else
                                        <span class="opacity-40 italic">Belum ada file</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 mb-2">
                        <button type="button" class="btn btn-m gradient-blue shadow-bg shadow-bg-s rounded-s font-700 btn-next px-4">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- STEP 2: Kontak & Alamat -->
                <div class="step-content" id="step2">
                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-envelope font-14"></i>
                        <input type="email" class="form-control rounded-s" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" readonly />
                        <label for="email" class="form-label-always-active color-highlight">Alamat Email (Akun)</label>
                    </div>

                    <div class="row mb-0">
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-phone font-14"></i>
                                <input type="tel" class="form-control rounded-s" id="phone" name="phone" placeholder="021-... / 0812..." value="{{ old('phone', optional(auth()->user()->employee)->phone) }}" required />
                                <label for="phone" class="form-label-always-active color-highlight">Telepon Utama *</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-phone-vibrate font-14"></i>
                                <input type="tel" class="form-control rounded-s" id="mobile" name="mobile" placeholder="0812..." value="{{ old('mobile', optional(auth()->user()->employee)->mobile) }}" />
                                <label for="mobile" class="form-label-always-active color-highlight">No. HP (Alternatif)</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-house font-14"></i>
                        <textarea name="address_ktp" id="address_ktp" class="form-control rounded-s" rows="2" placeholder="Alamat lengkap sesuai KTP" required>{{ old('address_ktp', optional(auth()->user()->employee)->address_ktp) }}</textarea>
                        <label for="address_ktp" class="form-label-always-active color-highlight">Alamat KTP *</label>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-geo-alt font-14"></i>
                        <textarea name="address_domisili" id="address_domisili" class="form-control rounded-s" rows="2" placeholder="Kosongkan jika sama dengan KTP">{{ old('address_domisili', optional(auth()->user()->employee)->address_domisili) }}</textarea>
                        <label for="address_domisili" class="form-label-always-active color-highlight">Alamat Domisili</label>
                    </div>

                    <div class="d-flex justify-content-between mt-4 mb-2">
                        <button type="button" class="btn btn-m border-blue-dark color-blue-dark rounded-s font-700 btn-prev px-3"><i class="bi bi-arrow-left"></i></button>
                        <button type="button" class="btn btn-m gradient-blue shadow-bg shadow-bg-s rounded-s font-700 btn-next px-4">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- STEP 3: Pegawai & Lainnya -->
                <div class="step-content" id="step3">
                    @php
                        $currentDepartment = old('department_id', optional(auth()->user()->employee)->department_id);
                        $currentPosition = old('position_id', optional(auth()->user()->employee)->position_id);
                        if (isset($currentPositionId) && $currentPositionId) { $currentPosition = $currentPositionId; }
                        $workSchedules = $workSchedules ?? \App\Models\WorkSchedule::where('is_active', true)->get();
                    @endphp

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-building font-14"></i>
                        @if ($departments->isEmpty())
                            <select class="form-control rounded-s" id="department_id" name="department_id" disabled>
                                <option value="">(Belum ada departemen tersedia)</option>
                            </select>
                        @else
                            <select class="form-control rounded-s" id="department_id_display" disabled>
                                <option value="" disabled selected>Pilih Departemen</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ (string) $currentDepartment === (string) $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="department_id" value="{{ $currentDepartment }}">
                        @endif
                        <label for="department_id" class="form-label-always-active color-highlight">Departemen *</label>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-briefcase font-14"></i>
                        @if ($positions->isEmpty())
                            <select class="form-control rounded-s" id="position_id" name="position_id" disabled>
                                <option value="">(Belum ada posisi tersedia)</option>
                            </select>
                        @else
                            <select class="form-control rounded-s" id="position_id_display" disabled>
                                <option value="" disabled selected>Pilih Posisi</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}" {{ (string) $currentPosition === (string) $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="position_id" value="{{ $currentPosition }}">
                        @endif
                        <label for="position_id" class="form-label-always-active color-highlight">Posisi/Jabatan *</label>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-calendar-check font-14"></i>
                        @if ($workSchedules->isEmpty())
                            <select class="form-control rounded-s" id="work_schedule_id" name="work_schedule_id" disabled>
                                <option value="">(Belum ada jadwal kerja)</option>
                            </select>
                        @else
                            <select class="form-control rounded-s" id="work_schedule_id_display" disabled>
                                <option value="" selected>Sesuai Standar (Opsional)</option>
                                @foreach ($workSchedules as $ws)
                                    <option value="{{ $ws->id }}" {{ (string) old('work_schedule_id', optional(auth()->user()->employee)->work_schedule_id) === (string) $ws->id ? 'selected' : '' }}>{{ $ws->name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="work_schedule_id" value="{{ old('work_schedule_id', optional(auth()->user()->employee)->work_schedule_id) }}">
                        @endif
                        <label for="work_schedule_id" class="form-label-always-active color-highlight">Jadwal Kerja</label>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-calendar-date font-14"></i>
                        @php
                            $empHireDate = optional(auth()->user()->employee)->hire_date;
                            if ($empHireDate instanceof \Carbon\Carbon || $empHireDate instanceof \DateTime) {
                                $defaultHireDate = $empHireDate->format('Y-m-d');
                            } else {
                                try { $defaultHireDate = $empHireDate ? date('Y-m-d', strtotime((string)$empHireDate)) : date('Y-m-d'); } catch (\Throwable $t) { $defaultHireDate = date('Y-m-d'); }
                            }
                            $employeeHireDate = old('hire_date', $defaultHireDate);
                        @endphp
                        <input type="date" class="form-control rounded-s" id="hire_date_display" value="{{ $employeeHireDate }}" disabled />
                        <input type="hidden" name="hire_date" value="{{ $employeeHireDate }}">
                        <label for="hire_date" class="form-label-always-active color-highlight">Tanggal Bergabung *</label>
                    </div>

                    <hr class="mt-4 mb-4" style="opacity: 0.1">

                    <div class="row mb-0">
                        <div class="col-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-arrow-up-right-square font-14"></i>
                                <input type="number" name="height_cm" id="height_cm" class="form-control rounded-s" placeholder="Tinggi (cm)" value="{{ old('height_cm', optional(auth()->user()->employee)->height_cm) }}" required />
                                <label for="height_cm" class="form-label-always-active color-highlight">Tinggi (cm) *</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-arrow-down-right-square font-14"></i>
                                <input type="number" name="weight_kg" id="weight_kg" class="form-control rounded-s" placeholder="Berat (kg)" value="{{ old('weight_kg', optional(auth()->user()->employee)->weight_kg) }}" required />
                                <label for="weight_kg" class="form-label-always-active color-highlight">Berat (kg) *</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-bandaid font-14"></i>
                        <input type="text" name="degenerative_diseases" id="degenerative_diseases" class="form-control rounded-s" placeholder="Misal: Asma, Hipertensi (kosongkan jika tidak ada)" value="{{ old('degenerative_diseases', optional(auth()->user()->employee)->degenerative_diseases) }}" />
                        <label for="degenerative_diseases" class="form-label-always-active color-highlight">Penyakit Bawaan</label>
                    </div>

                    <div class="d-flex justify-content-between mt-4 mb-2">
                        <button type="button" class="btn btn-m border-blue-dark color-blue-dark rounded-s font-700 btn-prev px-3"><i class="bi bi-arrow-left"></i></button>
                        <button type="button" class="btn btn-m gradient-blue shadow-bg shadow-bg-s rounded-s font-700 btn-next px-4">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- STEP 4: Keluarga & Darurat -->
                <div class="step-content" id="step4">
                    <p class="font-12 mb-2 color-theme">Data kontak darurat sangat penting jika terjadi sesuatu pada Anda di tempat kerja.</p>

                    <h6 class="font-14 font-700 mb-2 mt-3 color-highlight">Kontak Darurat (Prioritas)</h6>
                    <div id="emergency-list">
                        @php
                            $emRows = old('emergency', null);
                            if (is_null($emRows)) {
                                $emRows = optional(auth()->user()->employee)->emergency_contact ?? [];
                            }
                            // Enforce minimum 2 rows for emergency contacts
                            while (count($emRows) < 2) {
                                $emRows[] = [];
                            }
                        @endphp
                        @if(!empty($emRows))
                            @foreach($emRows as $i => $row)
                                <div class="em-row repeater-card" data-index="{{ $i }}">
                                    <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-em shadow-bg shadow-bg-xs"><i class="bi bi-x-lg font-12"></i></button>
                                    <div class="row mb-0">
                                        <div class="col-12 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-person font-14"></i>
                                                <input type="text" name="emergency[{{ $i }}][name]" id="em_name_{{ $i }}" class="form-control rounded-s" placeholder="Nama Lengkap" value="{{ $row['name'] ?? $row->name ?? '' }}" required>
                                                <label for="em_name_{{ $i }}" class="form-label-always-active color-highlight">Nama Lengkap *</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-people font-14"></i>
                                                <input type="text" name="emergency[{{ $i }}][relation]" id="em_rel_{{ $i }}" class="form-control rounded-s" placeholder="Hubungan" value="{{ $row['relation'] ?? $row->relation ?? '' }}" required>
                                                <label for="em_rel_{{ $i }}" class="form-label-always-active color-highlight">Hubungan *</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-12 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-phone font-14"></i>
                                                <input type="tel" name="emergency[{{ $i }}][phone]" id="em_phone_{{ $i }}" class="form-control rounded-s" placeholder="Nomor Telepon" value="{{ $row['phone'] ?? $row->phone ?? '' }}" required>
                                                <label for="em_phone_{{ $i }}" class="form-label-always-active color-highlight">Nomor Telepon *</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-emergency" class="btn btn-full border-highlight color-highlight rounded-s mb-4 font-700"><i class="bi bi-plus-circle pe-2"></i>Tambah Kontak Darurat</button>

                    <h6 class="font-14 font-700 mb-2 mt-2 color-highlight">Susunan Keluarga</h6>
                    <div id="family-list">
                        @php
                            $familyRows = old('family', null);
                            if (is_null($familyRows)) {
                                $familyRows = optional(auth()->user()->employee)->family_structure ?? [];
                            }
                        @endphp
                        @if(!empty($familyRows))
                            @foreach($familyRows as $i => $row)
                                <div class="family-row repeater-card" data-index="{{ $i }}">
                                    <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-family shadow-bg shadow-bg-xs"><i class="bi bi-x-lg font-12"></i></button>
                                    <div class="row mb-0">
                                        <div class="col-12 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-person font-14"></i>
                                                <input type="text" name="family[{{ $i }}][name]" id="fam_name_{{ $i }}" class="form-control rounded-s" placeholder="Nama Lengkap" value="{{ $row['name'] ?? $row->name ?? '' }}">
                                                <label for="fam_name_{{ $i }}" class="form-label-always-active color-highlight">Nama Lengkap</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-people font-14"></i>
                                                <input type="text" name="family[{{ $i }}][relation]" id="fam_rel_{{ $i }}" class="form-control rounded-s" placeholder="Hubungan" value="{{ $row['relation'] ?? $row->relation ?? '' }}">
                                                <label for="fam_rel_{{ $i }}" class="form-label-always-active color-highlight">Hubungan</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-gender-ambiguous font-14"></i>
                                                <select name="family[{{ $i }}][gender]" id="fam_gender_{{ $i }}" class="form-control rounded-s">
                                                    <option value="M" {{ ($row['gender'] ?? $row->gender ?? '') == 'M' ? 'selected' : '' }}>Laki-laki</option>
                                                    <option value="F" {{ ($row['gender'] ?? $row->gender ?? '') == 'F' ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                                <label for="fam_gender_{{ $i }}" class="form-label-always-active color-highlight">Gender</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-calendar font-14"></i>
                                                <input type="number" name="family[{{ $i }}][age]" id="fam_age_{{ $i }}" class="form-control rounded-s" placeholder="Usia" value="{{ $row['age'] ?? $row->age ?? '' }}">
                                                <label for="fam_age_{{ $i }}" class="form-label-always-active color-highlight">Usia</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-family" class="btn btn-full border-highlight color-highlight rounded-s font-700"><i class="bi bi-plus-circle pe-2"></i>Tambah Keluarga</button>

                    <div class="d-flex justify-content-between mt-4 mb-2">
                        <button type="button" class="btn btn-m border-blue-dark color-blue-dark rounded-s font-700 btn-prev px-3"><i class="bi bi-arrow-left"></i></button>
                        <button type="button" class="btn btn-m gradient-blue shadow-bg shadow-bg-s rounded-s font-700 btn-next px-4">Lanjut <i class="bi bi-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- STEP 5: Pendidikan & Pelatihan -->
                <div class="step-content" id="step5">
                    <h6 class="font-14 font-700 mb-2 mt-2 color-highlight">Riwayat Pendidikan Terakhir</h6>
                    <div id="education-list">
                        @php
                            $eduRows = old('education', null);
                            if (is_null($eduRows)) {
                                $eduRows = optional(auth()->user()->employee)->education_history ?? [];
                            }
                        @endphp
                        @if(!empty($eduRows))
                            @foreach($eduRows as $i => $row)
                                <div class="edu-row repeater-card" data-index="{{ $i }}">
                                    <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-edu shadow-bg shadow-bg-xs"><i class="bi bi-x-lg font-12"></i></button>
                                    <div class="row mb-0">
                                        <div class="col-12 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-building font-14"></i>
                                                <input type="text" name="education[{{ $i }}][school_name]" id="edu_school_{{ $i }}" class="form-control rounded-s" placeholder="Nama Institusi" value="{{ $row['school_name'] ?? $row->school_name ?? '' }}">
                                                <label for="edu_school_{{ $i }}" class="form-label-always-active color-highlight">Institusi / Universitas</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-book font-14"></i>
                                                <input type="text" name="education[{{ $i }}][major]" id="edu_major_{{ $i }}" class="form-control rounded-s" placeholder="Jurusan" value="{{ $row['major'] ?? $row->major ?? '' }}">
                                                <label for="edu_major_{{ $i }}" class="form-label-always-active color-highlight">Jurusan / Prodi</label>
                                            </div>
                                        </div>
                                        <div class="col-7 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-geo-alt font-14"></i>
                                                <input type="text" name="education[{{ $i }}][city]" id="edu_city_{{ $i }}" class="form-control rounded-s" placeholder="Kota" value="{{ $row['city'] ?? $row->city ?? '' }}">
                                                <label for="edu_city_{{ $i }}" class="form-label-always-active color-highlight">Kota</label>
                                            </div>
                                        </div>
                                        <div class="col-5 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-calendar-check font-14"></i>
                                                <input type="number" name="education[{{ $i }}][start_year]" id="edu_year_{{ $i }}" class="form-control rounded-s" placeholder="Tahun" value="{{ $row['start_year'] ?? $row->start_year ?? '' }}">
                                                <label for="edu_year_{{ $i }}" class="form-label-always-active color-highlight">Tahun Lulus</label>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label class="font-10 font-800 text-uppercase color-highlight mb-1 opacity-70">Lampiran Ijazah</label>
                                            <div class="repeater-upload-area">
                                                <input type="file" name="education[{{ $i }}][certificate]" class="upload-file-input" data-target="edu-cert-{{ $i }}" accept="image/*,.pdf" />
                                                <i class="bi bi-cloud-arrow-up"></i>
                                                <span class="upload-title" id="edu-cert-{{ $i }}">
                                                    @if(isset($row['certificate_path']))
                                                        <span class="color-green-dark"><i class="bi bi-check-circle-fill"></i> Terunggah</span>
                                                    @else
                                                        Pilih Ijazah / Sertifikat
                                                    @endif
                                                </span>
                                                <span class="upload-sub">PDF atau Gambar (Maks 4MB)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-education" class="btn btn-full border-highlight color-highlight rounded-s mb-4 font-700"><i class="bi bi-plus-circle pe-2"></i>Tambah Pendidikan</button>

                    <h6 class="font-14 font-700 mb-2 mt-2 color-highlight">Riwayat Pelatihan / Sertifikasi</h6>
                    <div id="training-list">
                        @php
                            $trRows = old('training', null);
                            if (is_null($trRows)) {
                                $trRows = optional(auth()->user()->employee)->training_history ?? [];
                            }
                        @endphp
                        @if(!empty($trRows))
                            @foreach($trRows as $i => $row)
                                <div class="tr-row repeater-card" data-index="{{ $i }}">
                                    <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-tr shadow-bg shadow-bg-xs"><i class="bi bi-x-lg font-12"></i></button>
                                    <div class="row mb-0">
                                        <div class="col-12 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-award font-14"></i>
                                                <input type="text" name="training[{{ $i }}][course_name]" id="tr_name_{{ $i }}" class="form-control rounded-s" placeholder="Nama Pelatihan" value="{{ $row['course_name'] ?? $row->course_name ?? '' }}">
                                                <label for="tr_name_{{ $i }}" class="form-label-always-active color-highlight">Nama Pelatihan</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-patch-check font-14"></i>
                                                <input type="text" name="training[{{ $i }}][organizer]" id="tr_org_{{ $i }}" class="form-control rounded-s" placeholder="Penyelenggara" value="{{ $row['organizer'] ?? $row->organizer ?? '' }}">
                                                <label for="tr_org_{{ $i }}" class="form-label-always-active color-highlight">Penyelenggara</label>
                                            </div>
                                        </div>
                                        <div class="col-7 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-geo-alt font-14"></i>
                                                <input type="text" name="training[{{ $i }}][city]" id="tr_city_{{ $i }}" class="form-control rounded-s" placeholder="Kota" value="{{ $row['city'] ?? $row->city ?? '' }}">
                                                <label for="tr_city_{{ $i }}" class="form-label-always-active color-highlight">Kota</label>
                                            </div>
                                        </div>
                                        <div class="col-5 col-md-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-clock-history font-14"></i>
                                                <input type="text" name="training[{{ $i }}][duration]" id="tr_dur_{{ $i }}" class="form-control rounded-s" placeholder="Durasi" value="{{ $row['duration'] ?? $row->duration ?? '' }}">
                                                <label for="tr_dur_{{ $i }}" class="form-label-always-active color-highlight">Durasi</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="font-10 font-800 text-uppercase color-highlight mb-1 opacity-70">Lampiran Sertifikat</label>
                                            <div class="repeater-upload-area">
                                                <input type="file" name="training[{{ $i }}][certificate]" class="upload-file-input" data-target="tr-cert-{{ $i }}" accept="image/*,.pdf" />
                                                <i class="bi bi-patch-check"></i>
                                                <span class="upload-title" id="tr-cert-{{ $i }}">
                                                    @if(isset($row['certificate_path']))
                                                        <span class="color-green-dark"><i class="bi bi-check-circle-fill"></i> Terunggah</span>
                                                    @else
                                                        Pilih Sertifikat Pelatihan
                                                    @endif
                                                </span>
                                                <span class="upload-sub">PDF atau Gambar (Maks 4MB)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-training" class="btn btn-full border-highlight color-highlight rounded-s font-700"><i class="bi bi-plus-circle pe-2"></i>Tambah Pelatihan</button>

                    <hr class="mt-4 mb-4" style="opacity: 0.1">
                    <h6 class="font-14 font-700 mb-0 mt-2 color-highlight"><i class="bi bi-credit-card pe-1"></i> Riwayat Kredit / Pinjaman</h6>
                    <p class="font-11 mb-3 opacity-60">Opsional. Tambahkan jika Anda memiliki pinjaman aktif di lembaga keuangan.</p>
                    <div id="financing-list">
                        @php
                            $finRows = old('financing', null);
                            if (is_null($finRows)) {
                                $finRows = optional(auth()->user()->employee)->financing_history ?? [];
                            }
                        @endphp
                        @if(!empty($finRows))
                            @foreach($finRows as $i => $row)
                                <div class="fin-row repeater-card" data-index="{{ $i }}">
                                    <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-fin shadow-bg shadow-bg-xs"><i class="bi bi-x-lg font-12"></i></button>
                                    <div class="row mb-0">
                                        <div class="col-12 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-bank font-14"></i>
                                                <input type="text" name="financing[{{ $i }}][institution]" id="fin_inst_{{ $i }}" class="form-control rounded-s" placeholder="Nama Bank / Lembaga" value="{{ $row['institution'] ?? '' }}">
                                                <label for="fin_inst_{{ $i }}" class="form-label-always-active color-highlight">Nama Lembaga Keuangan</label>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-cash-stack font-14"></i>
                                                <input type="number" name="financing[{{ $i }}][plafond]" id="fin_plafond_{{ $i }}" class="form-control rounded-s" placeholder="Rp" value="{{ $row['plafond'] ?? '' }}">
                                                <label for="fin_plafond_{{ $i }}" class="form-label-always-active color-highlight">Plafond (Rp)</label>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-calendar-month font-14"></i>
                                                <input type="number" name="financing[{{ $i }}][monthly_installment]" id="fin_cicilan_{{ $i }}" class="form-control rounded-s" placeholder="Rp/bln" value="{{ $row['monthly_installment'] ?? '' }}">
                                                <label for="fin_cicilan_{{ $i }}" class="form-label-always-active color-highlight">Cicilan/Bulan (Rp)</label>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-0">
                                            <div class="form-custom form-label form-icon">
                                                <i class="bi bi-chat-text font-14"></i>
                                                <input type="text" name="financing[{{ $i }}][description]" id="fin_desc_{{ $i }}" class="form-control rounded-s" placeholder="Keterangan (opsional)" value="{{ $row['description'] ?? '' }}">
                                                <label for="fin_desc_{{ $i }}" class="form-label-always-active color-highlight">Keterangan</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-financing" class="btn btn-full border-highlight color-highlight rounded-s font-700 mb-3"><i class="bi bi-plus-circle pe-2"></i>Tambah Kredit / Pinjaman</button>

                    <div class="d-flex justify-content-between mt-4 mb-4">
                        <button type="button" class="btn btn-m border-blue-dark color-blue-dark rounded-s font-700 btn-prev px-3"><i class="bi bi-arrow-left"></i></button>
                        <button type="submit" class="btn btn-m gradient-green rounded-s font-700 shadow-bg shadow-bg-m px-4" @if ($departments->isEmpty() || $positions->isEmpty()) disabled @endif>
                            <i class="bi bi-check-circle pe-1"></i> Simpan Profil
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // === 1. FOTO PROFIL PREVIEW ===
        const uploadInput = document.querySelector('.upload-file');
        if (uploadInput) {
            uploadInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.getElementById('image-data');
                        if (img) img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // === 2. STEPPER & VALIDASI ===
        const steps = document.querySelectorAll('.step-content');
        const navItems = document.querySelectorAll('.stepper-item');
        const stepText = document.getElementById('currentStepText');
        let currentStep = 1;

        function showStep(stepIndex) {
            if (stepIndex > currentStep) {
                const currentSection = steps[currentStep - 1];
                const requiredInputs = currentSection.querySelectorAll('input[required]:not([type="file"]), select[required], textarea[required]');
                let isValid = true;

                requiredInputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.classList.add('border-red-dark');
                    } else {
                        input.classList.remove('border-red-dark');
                    }
                });

                if (!isValid) {
                    const invalid = currentSection.querySelector('input[required]:invalid, select[required]:invalid');
                    if(invalid) invalid.reportValidity();
                    return;
                }
            }

            steps.forEach(el => el.classList.remove('active'));
            navItems.forEach((el, index) => {
                el.classList.remove('active');
                if (index < stepIndex - 1) el.classList.add('completed');
            });

            currentStep = stepIndex;
            document.getElementById('step' + currentStep).classList.add('active');
            navItems[currentStep - 1].classList.add('active');
            stepText.textContent = currentStep;
            window.scrollTo(0, 0);
        }

        document.querySelectorAll('.btn-next').forEach(btn => btn.addEventListener('click', () => { if (currentStep < 5) showStep(currentStep + 1); }));
        document.querySelectorAll('.btn-prev').forEach(btn => btn.addEventListener('click', () => { if (currentStep > 1) showStep(currentStep - 1); }));

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
</script>
@endpush
