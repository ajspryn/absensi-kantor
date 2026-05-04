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
    
    .repeater-card { background: rgba(0,0,0,0.02); border-radius: 12px; padding: 15px; padding-top: 35px; margin-bottom: 15px; position: relative; border: 1px solid rgba(0,0,0,0.05); }
    .repeater-remove { position: absolute; top: 10px; right: 10px; border-radius: 6px !important; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; }
    .theme-dark .repeater-card { background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.05); }
    
    .form-custom { margin-bottom: 15px !important; }
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
                        <label for="employee_id" class="color-theme font-12">ID Karyawan *</label>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-person-circle font-14"></i>
                        <input type="text" class="form-control rounded-s" id="full_name" name="full_name" placeholder="Nama Lengkap" value="{{ old('full_name', optional(auth()->user()->employee)->full_name ?? auth()->user()->name) }}" required />
                        <label for="full_name" class="color-theme font-12">Nama Lengkap *</label>
                    </div>

                    <div class="row mb-0">
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-gender-ambiguous font-14"></i>
                                <select name="gender" id="gender" class="form-control rounded-s">
                                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                    <option value="M" {{ old('gender', optional(auth()->user()->employee)->gender) === 'M' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="F" {{ old('gender', optional(auth()->user()->employee)->gender) === 'F' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <label for="gender" class="color-theme font-12">Jenis Kelamin</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-geo-alt font-14"></i>
                                <input type="text" name="birth_place" id="birth_place" class="form-control rounded-s" placeholder="Tempat Lahir" value="{{ old('birth_place', optional(auth()->user()->employee)->birth_place) }}" />
                                <label for="birth_place" class="color-theme font-12">Tempat Lahir</label>
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
                                <input type="date" name="birth_date" id="birth_date" class="form-control rounded-s" value="{{ old('birth_date', $birthDateStr) }}" />
                                <label for="birth_date" class="color-theme font-12">Tanggal Lahir</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-card-text font-14"></i>
                                <input type="number" name="nik_ktp" id="nik_ktp" class="form-control rounded-s" placeholder="NIK / No. KTP" value="{{ old('nik_ktp', optional(auth()->user()->employee)->nik_ktp) }}" />
                                <label for="nik_ktp" class="color-theme font-12">NIK KTP</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-people-fill font-14"></i>
                                <select name="marital_status" id="marital_status" class="form-control rounded-s">
                                    <option value="" disabled selected>Pilih Status Perkawinan</option>
                                    <option value="belum" {{ old('marital_status', optional(auth()->user()->employee)->marital_status) == 'belum' ? 'selected' : '' }}>Belum Menikah</option>
                                    <option value="menikah" {{ old('marital_status', optional(auth()->user()->employee)->marital_status) == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                    <option value="cerai_hidup" {{ old('marital_status', optional(auth()->user()->employee)->marital_status) == 'cerai_hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                    <option value="cerai_mati" {{ old('marital_status', optional(auth()->user()->employee)->marital_status) == 'cerai_mati' ? 'selected' : '' }}>Cerai Mati</option>
                                </select>
                                <label for="marital_status" class="color-theme font-12">Status Perkawinan</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-house-door-fill font-14"></i>
                                <select name="residence_status" id="residence_status" class="form-control rounded-s">
                                    <option value="" disabled selected>Status Tempat Tinggal</option>
                                    <option value="milik_pribadi" {{ old('residence_status', optional(auth()->user()->employee)->residence_status) == 'milik_pribadi' ? 'selected' : '' }}>Milik Pribadi</option>
                                    <option value="milik_orangtua" {{ old('residence_status', optional(auth()->user()->employee)->residence_status) == 'milik_orangtua' ? 'selected' : '' }}>Milik Orangtua</option>
                                    <option value="sewa_kontrak" {{ old('residence_status', optional(auth()->user()->employee)->residence_status) == 'sewa_kontrak' ? 'selected' : '' }}>Sewa / Kontrak</option>
                                    <option value="lainnya" {{ old('residence_status', optional(auth()->user()->employee)->residence_status) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                <label for="residence_status" class="color-theme font-12">Status Tinggal</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-heart-pulse font-14"></i>
                        <input type="text" name="health_condition" id="health_condition" class="form-control rounded-s" placeholder="Kondisi Kesehatan (singkat)" value="{{ old('health_condition', optional(auth()->user()->employee)->health_condition) }}" />
                        <label for="health_condition" class="color-theme font-12">Kondisi Kesehatan</label>
                    </div>

                    <button type="button" class="btn btn-full bg-blue-dark rounded-s text-uppercase font-700 mt-3 btn-next">Selanjutnya <i class="bi bi-arrow-right ms-2"></i></button>
                </div>

                <!-- STEP 2: Kontak & Alamat -->
                <div class="step-content" id="step2">
                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-envelope font-14"></i>
                        <input type="email" class="form-control rounded-s" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" readonly />
                        <label for="email" class="color-theme font-12">Alamat Email (Akun)</label>
                    </div>

                    <div class="row mb-0">
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-phone font-14"></i>
                                <input type="tel" class="form-control rounded-s" id="phone" name="phone" placeholder="021-... / 0812..." value="{{ old('phone', optional(auth()->user()->employee)->phone) }}" />
                                <label for="phone" class="color-theme font-12">Telepon Utama</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-phone-vibrate font-14"></i>
                                <input type="tel" class="form-control rounded-s" id="mobile" name="mobile" placeholder="0812..." value="{{ old('mobile', optional(auth()->user()->employee)->mobile) }}" />
                                <label for="mobile" class="color-theme font-12">No. HP (Alternatif)</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-house font-14"></i>
                        <textarea name="address_ktp" id="address_ktp" class="form-control rounded-s" rows="2" placeholder="Alamat lengkap sesuai KTP">{{ old('address_ktp', optional(auth()->user()->employee)->address_ktp) }}</textarea>
                        <label for="address_ktp" class="color-theme font-12">Alamat KTP</label>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-geo-alt font-14"></i>
                        <textarea name="address_domisili" id="address_domisili" class="form-control rounded-s" rows="2" placeholder="Kosongkan jika sama dengan KTP">{{ old('address_domisili', optional(auth()->user()->employee)->address_domisili) }}</textarea>
                        <label for="address_domisili" class="color-theme font-12">Alamat Domisili</label>
                    </div>

                    <div class="row mt-4">
                        <div class="col-6"><button type="button" class="btn btn-full border-blue-dark color-blue-dark rounded-s font-700 btn-prev"><i class="bi bi-arrow-left me-1"></i> Kembali</button></div>
                        <div class="col-6"><button type="button" class="btn btn-full bg-blue-dark rounded-s font-700 btn-next">Lanjut <i class="bi bi-arrow-right ms-1"></i></button></div>
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
                            <select class="form-control rounded-s" id="department_id" name="department_id" required>
                                <option value="" disabled selected>Pilih Departemen</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ (string) $currentDepartment === (string) $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        <label for="department_id" class="color-theme font-12">Departemen *</label>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-briefcase font-14"></i>
                        @if ($positions->isEmpty())
                            <select class="form-control rounded-s" id="position_id" name="position_id" disabled>
                                <option value="">(Belum ada posisi tersedia)</option>
                            </select>
                        @else
                            <select class="form-control rounded-s" id="position_id" name="position_id" required>
                                <option value="" disabled selected>Pilih Posisi</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}" {{ (string) $currentPosition === (string) $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        <label for="position_id" class="color-theme font-12">Posisi/Jabatan *</label>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-calendar-check font-14"></i>
                        @if ($workSchedules->isEmpty())
                            <select class="form-control rounded-s" id="work_schedule_id" name="work_schedule_id" disabled>
                                <option value="">(Belum ada jadwal kerja)</option>
                            </select>
                        @else
                            <select class="form-control rounded-s" id="work_schedule_id" name="work_schedule_id">
                                <option value="" selected>Sesuai Standar (Opsional)</option>
                                @foreach ($workSchedules as $ws)
                                    <option value="{{ $ws->id }}" {{ (string) old('work_schedule_id', optional(auth()->user()->employee)->work_schedule_id) === (string) $ws->id ? 'selected' : '' }}>{{ $ws->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <label for="work_schedule_id" class="color-theme font-12">Jadwal Kerja</label>
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
                        <input type="date" class="form-control rounded-s" id="hire_date" name="hire_date" value="{{ $employeeHireDate }}" required />
                        <label for="hire_date" class="color-theme font-12">Tanggal Bergabung *</label>
                    </div>
                    
                    <hr class="mt-4 mb-4" style="opacity: 0.1">
                    
                    <div class="row mb-0">
                        <div class="col-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-arrow-up-right-square font-14"></i>
                                <input type="number" name="height_cm" id="height_cm" class="form-control rounded-s" placeholder="Tinggi (cm)" value="{{ old('height_cm', optional(auth()->user()->employee)->height_cm) }}" />
                                <label for="height_cm" class="color-theme font-12">Tinggi (cm)</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-custom form-label form-icon">
                                <i class="bi bi-arrow-down-right-square font-14"></i>
                                <input type="number" name="weight_kg" id="weight_kg" class="form-control rounded-s" placeholder="Berat (kg)" value="{{ old('weight_kg', optional(auth()->user()->employee)->weight_kg) }}" />
                                <label for="weight_kg" class="color-theme font-12">Berat (kg)</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-custom form-label form-icon">
                        <i class="bi bi-bandaid font-14"></i>
                        <input type="text" name="degenerative_diseases" id="degenerative_diseases" class="form-control rounded-s" placeholder="Misal: Asma, Hipertensi (kosongkan jika tidak ada)" value="{{ old('degenerative_diseases', optional(auth()->user()->employee)->degenerative_diseases) }}" />
                        <label for="degenerative_diseases" class="color-theme font-12">Penyakit Bawaan</label>
                    </div>

                    <div class="row mt-4">
                        <div class="col-6"><button type="button" class="btn btn-full border-blue-dark color-blue-dark rounded-s font-700 btn-prev"><i class="bi bi-arrow-left me-1"></i> Kembali</button></div>
                        <div class="col-6"><button type="button" class="btn btn-full bg-blue-dark rounded-s font-700 btn-next">Lanjut <i class="bi bi-arrow-right ms-1"></i></button></div>
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
                                $emRows = optional(auth()->user()->employee)->emergencyContacts ? optional(auth()->user()->employee)->emergencyContacts->toArray() : [];
                            }
                        @endphp
                        @if(!empty($emRows))
                            @foreach($emRows as $i => $row)
                                <div class="em-row repeater-card" data-index="{{ $i }}">
                                    <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-em"><i class="bi bi-x-lg font-12"></i></button>
                                    <div class="row mb-0">
                                        <div class="col-12 col-md-6 mb-2"><input type="text" name="emergency[{{ $i }}][name]" class="form-control form-control-sm" placeholder="Nama Lengkap" value="{{ $row['name'] ?? $row->name ?? '' }}"></div>
                                        <div class="col-6 col-md-6 mb-2"><input type="text" name="emergency[{{ $i }}][relation]" class="form-control form-control-sm" placeholder="Hubungan (Ayah/Istri/Dll)" value="{{ $row['relation'] ?? $row->relation ?? '' }}"></div>
                                        <div class="col-6 col-md-12 mb-2"><input type="tel" name="emergency[{{ $i }}][phone]" class="form-control form-control-sm" placeholder="Nomor Telepon" value="{{ $row['phone'] ?? $row->phone ?? '' }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-emergency" class="btn btn-s border-highlight color-highlight rounded-s mb-4"><i class="bi bi-plus-circle pe-2"></i>Tambah Kontak Darurat</button>

                    <h6 class="font-14 font-700 mb-2 mt-2 color-highlight">Susunan Keluarga</h6>
                    <div id="family-list">
                        @php
                            $familyRows = old('family', null);
                            if (is_null($familyRows)) {
                                $familyRows = optional(auth()->user()->employee)->familyMembers ? optional(auth()->user()->employee)->familyMembers->toArray() : [];
                            }
                        @endphp
                        @if(!empty($familyRows))
                            @foreach($familyRows as $i => $row)
                                <div class="family-row repeater-card" data-index="{{ $i }}">
                                    <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-family"><i class="bi bi-x-lg font-12"></i></button>
                                    <div class="row mb-0">
                                        <div class="col-12 col-md-6 mb-2"><input type="text" name="family[{{ $i }}][name]" class="form-control form-control-sm" placeholder="Nama Anggota Keluarga" value="{{ $row['name'] ?? $row->name ?? '' }}"></div>
                                        <div class="col-12 col-md-6 mb-2"><input type="text" name="family[{{ $i }}][relation]" class="form-control form-control-sm" placeholder="Hubungan (Anak/Suami/Dll)" value="{{ $row['relation'] ?? $row->relation ?? '' }}"></div>
                                        <div class="col-6 col-md-6 mb-2">
                                            <select name="family[{{ $i }}][gender]" class="form-control form-control-sm">
                                                <option value="" selected disabled>Gender</option>
                                                <option value="M" {{ (isset($row['gender']) && $row['gender']=='M') || (isset($row->gender) && $row->gender=='M') ? 'selected' : '' }}>Laki-laki</option>
                                                <option value="F" {{ (isset($row['gender']) && $row['gender']=='F') || (isset($row->gender) && $row->gender=='F') ? 'selected' : '' }}>Perempuan</option>
                                            </select>
                                        </div>
                                        <div class="col-6 col-md-6 mb-2"><input type="number" name="family[{{ $i }}][age]" class="form-control form-control-sm" placeholder="Umur (Tahun)" value="{{ $row['age'] ?? $row->age ?? '' }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-family" class="btn btn-s border-highlight color-highlight rounded-s"><i class="bi bi-plus-circle pe-2"></i>Tambah Keluarga</button>

                    <div class="row mt-4">
                        <div class="col-6"><button type="button" class="btn btn-full border-blue-dark color-blue-dark rounded-s font-700 btn-prev"><i class="bi bi-arrow-left me-1"></i> Kembali</button></div>
                        <div class="col-6"><button type="button" class="btn btn-full bg-blue-dark rounded-s font-700 btn-next">Lanjut <i class="bi bi-arrow-right ms-1"></i></button></div>
                    </div>
                </div>

                <!-- STEP 5: Pendidikan & Pelatihan -->
                <div class="step-content" id="step5">
                    <h6 class="font-14 font-700 mb-2 mt-2 color-highlight">Riwayat Pendidikan Terakhir</h6>
                    <div id="education-list">
                        @php
                            $eduRows = old('education', null);
                            if (is_null($eduRows)) {
                                $eduRows = optional(auth()->user()->employee)->educationRecords ? optional(auth()->user()->employee)->educationRecords->toArray() : [];
                            }
                        @endphp
                        @if(!empty($eduRows))
                            @foreach($eduRows as $i => $row)
                                <div class="edu-row repeater-card" data-index="{{ $i }}">
                                    <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-edu"><i class="bi bi-x-lg font-12"></i></button>
                                    <div class="row mb-0">
                                        <div class="col-12 col-md-6 mb-2"><input type="text" name="education[{{ $i }}][school_name]" class="form-control form-control-sm" placeholder="Nama Institusi / Universitas" value="{{ $row['school_name'] ?? $row->school_name ?? '' }}"></div>
                                        <div class="col-12 col-md-6 mb-2"><input type="text" name="education[{{ $i }}][major]" class="form-control form-control-sm" placeholder="Jurusan / Program Studi" value="{{ $row['major'] ?? $row->major ?? '' }}"></div>
                                        <div class="col-8 col-md-6 mb-2"><input type="text" name="education[{{ $i }}][city]" class="form-control form-control-sm" placeholder="Kota" value="{{ $row['city'] ?? $row->city ?? '' }}"></div>
                                        <div class="col-4 col-md-6 mb-2"><input type="number" name="education[{{ $i }}][start_year]" class="form-control form-control-sm" placeholder="Tahun" value="{{ $row['start_year'] ?? $row->start_year ?? '' }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-education" class="btn btn-s border-highlight color-highlight rounded-s mb-4"><i class="bi bi-plus-circle pe-2"></i>Tambah Pendidikan</button>

                    <h6 class="font-14 font-700 mb-2 mt-2 color-highlight">Riwayat Pelatihan / Sertifikasi</h6>
                    <div id="training-list">
                        @php
                            $trRows = old('training', null);
                            if (is_null($trRows)) {
                                $trRows = optional(auth()->user()->employee)->trainingRecords ? optional(auth()->user()->employee)->trainingRecords->toArray() : [];
                            }
                        @endphp
                        @if(!empty($trRows))
                            @foreach($trRows as $i => $row)
                                <div class="tr-row repeater-card" data-index="{{ $i }}">
                                    <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-tr"><i class="bi bi-x-lg font-12"></i></button>
                                    <div class="row mb-0">
                                        <div class="col-12 col-md-6 mb-2"><input type="text" name="training[{{ $i }}][course_name]" class="form-control form-control-sm" placeholder="Nama Pelatihan / Sertifikasi" value="{{ $row['course_name'] ?? $row->course_name ?? '' }}"></div>
                                        <div class="col-12 col-md-6 mb-2"><input type="text" name="training[{{ $i }}][organizer]" class="form-control form-control-sm" placeholder="Penyelenggara" value="{{ $row['organizer'] ?? $row->organizer ?? '' }}"></div>
                                        <div class="col-8 col-md-6 mb-2"><input type="text" name="training[{{ $i }}][city]" class="form-control form-control-sm" placeholder="Kota" value="{{ $row['city'] ?? $row->city ?? '' }}"></div>
                                        <div class="col-4 col-md-6 mb-2"><input type="text" name="training[{{ $i }}][duration]" class="form-control form-control-sm" placeholder="Durasi (Jam/Hari)" value="{{ $row['duration'] ?? $row->duration ?? '' }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-training" class="btn btn-s border-highlight color-highlight rounded-s"><i class="bi bi-plus-circle pe-2"></i>Tambah Pelatihan</button>

                    <div class="row mt-4 mb-3">
                        <div class="col-5"><button type="button" class="btn btn-full border-blue-dark color-blue-dark rounded-s font-700 btn-prev"><i class="bi bi-arrow-left me-1"></i> Kembali</button></div>
                        <div class="col-7">
                            <button type="submit" class="btn btn-full gradient-green rounded-s font-700 shadow-bg shadow-bg-s" @if ($departments->isEmpty() || $positions->isEmpty()) disabled @endif>
                                <i class="bi bi-check-circle pe-1"></i> Simpan Profil
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Photo preview
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

        // Stepper Logic
        const steps = document.querySelectorAll('.step-content');
        const navItems = document.querySelectorAll('.stepper-item');
        const stepText = document.getElementById('currentStepText');
        let currentStep = 1;

        function showStep(stepIndex) {
            // Validasi client-side dimatikan sementara sesuai permintaan
            /*
            if (stepIndex > currentStep) {
                const currentFormSection = steps[currentStep - 1];
                const requiredInputs = currentFormSection.querySelectorAll('input[required], select[required]');
                let isValid = true;
                
                requiredInputs.forEach(input => {
                    if (!input.value) {
                        isValid = false;
                        input.classList.add('border-red-dark');
                    } else {
                        input.classList.remove('border-red-dark');
                    }
                });

                if (!isValid) {
                    // Trigger browser native validation popup
                    const invalidInput = currentFormSection.querySelector('input[required]:invalid, select[required]:invalid');
                    if(invalidInput) invalidInput.reportValidity();
                    return;
                }
            }
            */

            // Hide all
            steps.forEach(el => el.classList.remove('active'));
            navItems.forEach((el, index) => {
                el.classList.remove('active');
                if (index < stepIndex - 1) {
                    el.classList.add('completed');
                } else {
                    el.classList.remove('completed');
                }
            });

            // Show target
            currentStep = stepIndex;
            document.getElementById('step' + currentStep).classList.add('active');
            navItems[currentStep - 1].classList.add('active');
            stepText.textContent = currentStep;
            
            // Scroll nav into view
            navItems[currentStep - 1].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            window.scrollTo(0, 0);
        }

        document.querySelectorAll('.btn-next').forEach(btn => {
            btn.addEventListener('click', () => {
                if (currentStep < 5) showStep(currentStep + 1);
            });
        });

        document.querySelectorAll('.btn-prev').forEach(btn => {
            btn.addEventListener('click', () => {
                if (currentStep > 1) showStep(currentStep - 1);
            });
        });
        
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                const step = parseInt(this.getAttribute('data-step'));
                // Only allow clicking to previous or completed steps
                if (step < currentStep || this.classList.contains('completed')) {
                    showStep(step);
                }
            });
        });

        // Repeater Logic
        function makeIndex(container) {
            const rows = container.querySelectorAll(':scope > div');
            rows.forEach((row, idx) => {
                row.setAttribute('data-index', idx);
                const inputs = row.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (!name) return;
                    const newName = name.replace(/\[\d+\]/, '['+idx+']');
                    input.setAttribute('name', newName);
                });
            });
        }

        function addRow(listId, templateHtml) {
            const list = document.getElementById(listId);
            const idx = list.children.length;
            const wrapper = document.createElement('div');
            wrapper.innerHTML = templateHtml.replace(/__INDEX__/g, idx);
            list.appendChild(wrapper.firstElementChild);
            makeIndex(list);
        }

        // Education Template
        const eduTemplate = `
        <div class="edu-row repeater-card" data-index="__INDEX__">
            <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-edu"><i class="bi bi-x-lg font-12"></i></button>
            <div class="row mb-0">
                <div class="col-12 col-md-6 mb-2"><input type="text" name="education[__INDEX__][school_name]" class="form-control form-control-sm" placeholder="Nama Institusi / Universitas"></div>
                <div class="col-12 col-md-6 mb-2"><input type="text" name="education[__INDEX__][major]" class="form-control form-control-sm" placeholder="Jurusan / Program Studi"></div>
                <div class="col-8 col-md-6 mb-2"><input type="text" name="education[__INDEX__][city]" class="form-control form-control-sm" placeholder="Kota"></div>
                <div class="col-4 col-md-6 mb-2"><input type="number" name="education[__INDEX__][start_year]" class="form-control form-control-sm" placeholder="Tahun"></div>
            </div>
        </div>`;

        // Training Template
        const trTemplate = `
        <div class="tr-row repeater-card" data-index="__INDEX__">
            <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-tr"><i class="bi bi-x-lg font-12"></i></button>
            <div class="row mb-0">
                <div class="col-12 col-md-6 mb-2"><input type="text" name="training[__INDEX__][course_name]" class="form-control form-control-sm" placeholder="Nama Pelatihan / Sertifikasi"></div>
                <div class="col-12 col-md-6 mb-2"><input type="text" name="training[__INDEX__][organizer]" class="form-control form-control-sm" placeholder="Penyelenggara"></div>
                <div class="col-8 col-md-6 mb-2"><input type="text" name="training[__INDEX__][city]" class="form-control form-control-sm" placeholder="Kota"></div>
                <div class="col-4 col-md-6 mb-2"><input type="text" name="training[__INDEX__][duration]" class="form-control form-control-sm" placeholder="Durasi"></div>
            </div>
        </div>`;

        // Family Template
        const famTemplate = `
        <div class="family-row repeater-card" data-index="__INDEX__">
            <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-family"><i class="bi bi-x-lg font-12"></i></button>
            <div class="row mb-0">
                <div class="col-12 col-md-6 mb-2"><input type="text" name="family[__INDEX__][name]" class="form-control form-control-sm" placeholder="Nama Anggota Keluarga"></div>
                <div class="col-12 col-md-6 mb-2"><input type="text" name="family[__INDEX__][relation]" class="form-control form-control-sm" placeholder="Hubungan (Anak/Suami/Dll)"></div>
                <div class="col-6 col-md-6 mb-2">
                    <select name="family[__INDEX__][gender]" class="form-control form-control-sm">
                        <option value="" selected disabled>Gender</option>
                        <option value="M">Laki-laki</option>
                        <option value="F">Perempuan</option>
                    </select>
                </div>
                <div class="col-6 col-md-6 mb-2"><input type="number" name="family[__INDEX__][age]" class="form-control form-control-sm" placeholder="Umur"></div>
            </div>
        </div>`;

        // Emergency Template
        const emTemplate = `
        <div class="em-row repeater-card" data-index="__INDEX__">
            <button type="button" class="btn btn-xxs bg-red-dark repeater-remove remove-em"><i class="bi bi-x-lg font-12"></i></button>
            <div class="row mb-0">
                <div class="col-12 col-md-6 mb-2"><input type="text" name="emergency[__INDEX__][name]" class="form-control form-control-sm" placeholder="Nama Lengkap"></div>
                <div class="col-6 col-md-6 mb-2"><input type="text" name="emergency[__INDEX__][relation]" class="form-control form-control-sm" placeholder="Hubungan"></div>
                <div class="col-6 col-md-12 mb-2"><input type="tel" name="emergency[__INDEX__][phone]" class="form-control form-control-sm" placeholder="Nomor Telepon"></div>
            </div>
        </div>`;

        document.getElementById('add-education').addEventListener('click', () => addRow('education-list', eduTemplate));
        document.getElementById('add-training').addEventListener('click', () => addRow('training-list', trTemplate));
        document.getElementById('add-family').addEventListener('click', () => addRow('family-list', famTemplate));
        document.getElementById('add-emergency').addEventListener('click', () => addRow('emergency-list', emTemplate));

        // Event delegation for removal
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.repeater-remove');
            if (!btn) return;
            
            if (btn.classList.contains('remove-edu')) {
                const row = btn.closest('.edu-row'); if (row) { row.remove(); makeIndex(document.getElementById('education-list')); }
            } else if (btn.classList.contains('remove-tr')) {
                const row = btn.closest('.tr-row'); if (row) { row.remove(); makeIndex(document.getElementById('training-list')); }
            } else if (btn.classList.contains('remove-family')) {
                const row = btn.closest('.family-row'); if (row) { row.remove(); makeIndex(document.getElementById('family-list')); }
            } else if (btn.classList.contains('remove-em')) {
                const row = btn.closest('.em-row'); if (row) { row.remove(); makeIndex(document.getElementById('emergency-list')); }
            }
        });
    });
</script>
@endpush
