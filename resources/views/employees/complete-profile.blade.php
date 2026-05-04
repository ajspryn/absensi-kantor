@extends('layouts.app')

@section('title', 'Lengkapi Profil - Aplikasi Absensi')

@section('page-class', 'mb-0 pb-0')

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
        <div class="content">
            <h1 class="text-center font-800 font-26 mb-2">Lengkapi Profil Anda</h1>
            <p class="text-center font-13 mt-n2 mb-4 color-theme opacity-70">
                Silakan lengkapi data profil Anda untuk mulai menggunakan aplikasi absensi
            </p>

            @if ($errors->any())
                <div class="alert bg-red-dark alert-dismissible color-white rounded-s fade show pe-2 mb-3" role="alert">
                    <strong>Error:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('employee.profile.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Photo Upload -->
                <div class="file-data text-center mb-3">
                    <div class="position-relative d-inline-block">
                        <img id="image-data" src="{{ optional(auth()->user()->employee)->photo ? asset('storage/' . optional(auth()->user()->employee)->photo) : asset('template/images/avatars/5s.png') }}" class="img-fluid rounded-circle border-4 border-theme" style="width: 100px; height: 100px; object-fit: cover;" alt="Profile Photo">
                        <div class="position-absolute bottom-0 end-0 bg-highlight rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-camera-fill color-white font-14"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <input type="file" name="photo" class="upload-file" accept="image/*">
                    </div>
                </div>

                <!-- Sections -->
                <div class="mb-3">
                    <h5 class="font-14 font-700 mb-2">1. Informasi Pribadi</h5>
                    <div class="card card-style p-3 mb-2">
                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-person-badge font-14"></i>
                            <input type="text" class="form-control rounded-s" id="employee_id" name="employee_id" placeholder="EMP001" value="{{ old('employee_id', optional(auth()->user()->employee)->employee_id) }}" required />
                            <label for="employee_id" class="color-theme font-12">ID Karyawan</label>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-person-circle font-14"></i>
                            <input type="text" class="form-control rounded-s" id="full_name" name="full_name" placeholder="Nama Lengkap" value="{{ old('full_name', optional(auth()->user()->employee)->full_name ?? auth()->user()->name) }}" required />
                            <label for="full_name" class="color-theme font-12">Nama Lengkap</label>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-custom form-label form-icon mb-2">
                                    <i class="bi bi-gender-ambiguous font-14"></i>
                                    <select name="gender" id="gender" class="form-control rounded-s">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="M" {{ old('gender', optional(auth()->user()->employee)->gender) === 'M' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="F" {{ old('gender', optional(auth()->user()->employee)->gender) === 'F' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    <label for="gender" class="color-theme font-12">Jenis Kelamin</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-custom form-label form-icon mb-2">
                                    <i class="bi bi-geo-alt font-14"></i>
                                    <input type="text" name="birth_place" id="birth_place" class="form-control rounded-s" placeholder="Tempat Lahir" value="{{ old('birth_place', optional(auth()->user()->employee)->birth_place) }}" />
                                    <label for="birth_place" class="color-theme font-12">Tempat Lahir</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-custom form-label form-icon mb-2">
                                    <i class="bi bi-calendar-date font-14"></i>
                                    <input type="date" name="birth_date" id="birth_date" class="form-control rounded-s" value="{{ old('birth_date', optional(auth()->user()->employee)->birth_date ? date('Y-m-d', strtotime(optional(auth()->user()->employee)->birth_date)) : '') }}" />
                                    <label for="birth_date" class="color-theme font-12">Tanggal Lahir</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-custom form-label form-icon mb-2">
                                    <i class="bi bi-card-text font-14"></i>
                                    <input type="text" name="nik_ktp" id="nik_ktp" class="form-control rounded-s" placeholder="1234567890123456" value="{{ old('nik_ktp', optional(auth()->user()->employee)->nik_ktp) }}" />
                                    <label for="nik_ktp" class="color-theme font-12">NIK / No. KTP</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-custom form-label form-icon mb-2">
                                    <i class="bi bi-people-fill font-14"></i>
                                    <select name="marital_status" id="marital_status" class="form-control rounded-s">
                                        <option value="">Pilih Status Perkawinan</option>
                                        <option value="belum" {{ old('marital_status', optional(auth()->user()->employee)->marital_status) == 'belum' ? 'selected' : '' }}>Belum Menikah</option>
                                        <option value="menikah" {{ old('marital_status', optional(auth()->user()->employee)->marital_status) == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                        <option value="cerai_hidup" {{ old('marital_status', optional(auth()->user()->employee)->marital_status) == 'cerai_hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                        <option value="cerai_mati" {{ old('marital_status', optional(auth()->user()->employee)->marital_status) == 'cerai_mati' ? 'selected' : '' }}>Cerai Mati</option>
                                    </select>
                                    <label for="marital_status" class="color-theme font-12">Status Perkawinan</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-custom form-label form-icon mb-2">
                                    <i class="bi bi-house-door-fill font-14"></i>
                                    <select name="residence_status" id="residence_status" class="form-control rounded-s">
                                        <option value="">Status Tempat Tinggal</option>
                                        <option value="milik_pribadi" {{ old('residence_status', optional(auth()->user()->employee)->residence_status) == 'milik_pribadi' ? 'selected' : '' }}>Milik Pribadi</option>
                                        <option value="milik_orangtua" {{ old('residence_status', optional(auth()->user()->employee)->residence_status) == 'milik_orangtua' ? 'selected' : '' }}>Milik Orangtua</option>
                                        <option value="sewa_kontrak" {{ old('residence_status', optional(auth()->user()->employee)->residence_status) == 'sewa_kontrak' ? 'selected' : '' }}>Sewa / Kontrak</option>
                                        <option value="lainnya" {{ old('residence_status', optional(auth()->user()->employee)->residence_status) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    <label for="residence_status" class="color-theme font-12">Status Tempat Tinggal</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-heart-pulse font-14"></i>
                            <input type="text" name="health_condition" id="health_condition" class="form-control rounded-s" placeholder="Kondisi Kesehatan (singkat)" value="{{ old('health_condition', optional(auth()->user()->employee)->health_condition) }}" />
                            <label for="health_condition" class="color-theme font-12">Kondisi Kesehatan</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h5 class="font-14 font-700 mb-2">2. Kontak & Alamat</h5>
                    <div class="card card-style p-3 mb-2">
                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-envelope font-14"></i>
                            <input type="email" class="form-control rounded-s" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" readonly />
                            <label for="email" class="color-theme font-12">Alamat Email</label>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-phone font-14"></i>
                            <input type="tel" class="form-control rounded-s" id="phone" name="phone" placeholder="021-... / 0812..." value="{{ old('phone', optional(auth()->user()->employee)->phone) }}" />
                            <label for="phone" class="color-theme font-12">Telepon</label>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-phone-vibrate font-14"></i>
                            <input type="tel" class="form-control rounded-s" id="mobile" name="mobile" placeholder="0812..." value="{{ old('mobile', optional(auth()->user()->employee)->mobile) }}" />
                            <label for="mobile" class="color-theme font-12">No. HP</label>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-geo-alt font-14"></i>
                            <textarea name="address" id="address" class="form-control rounded-s" rows="2" placeholder="Alamat lengkap">{{ old('address', optional(auth()->user()->employee)->address) }}</textarea>
                            <label for="address" class="color-theme font-12">Alamat Domisili</label>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-house font-14"></i>
                            <textarea name="address_ktp" id="address_ktp" class="form-control rounded-s" rows="2" placeholder="Alamat sesuai KTP">{{ old('address_ktp', optional(auth()->user()->employee)->address_ktp) }}</textarea>
                            <label for="address_ktp" class="color-theme font-12">Alamat KTP</label>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-building font-14"></i>
                            <textarea name="address_domisili" id="address_domisili" class="form-control rounded-s" rows="2" placeholder="Alamat domisili jika berbeda">{{ old('address_domisili', optional(auth()->user()->employee)->address_domisili) }}</textarea>
                            <label for="address_domisili" class="color-theme font-12">Alamat Domisili</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h5 class="font-14 font-700 mb-2">3. Data Kepegawaian</h5>
                    <div class="card card-style p-3 mb-2">
                        @php
                            $currentDepartment = old('department_id', optional(auth()->user()->employee)->department_id);
                            $currentPosition = old('position_id', optional(auth()->user()->employee)->position_id);
                            if (isset($currentPositionId) && $currentPositionId) { $currentPosition = $currentPositionId; }
                            $workSchedules = $workSchedules ?? \App\Models\WorkSchedule::where('is_active', true)->get();
                        @endphp

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-building font-14"></i>
                            @if ($departments->isEmpty())
                                <select class="form-control rounded-s" id="department_id" name="department_id" disabled>
                                    <option value="">(Belum ada departemen tersedia)</option>
                                </select>
                            @else
                                <select class="form-control rounded-s" id="department_id" name="department_id" required>
                                    <option value="">Pilih Departemen</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ (string) $currentDepartment === (string) $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            <label for="department_id" class="color-theme font-12">Departemen</label>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-briefcase font-14"></i>
                            @if ($positions->isEmpty())
                                <select class="form-control rounded-s" id="position_id" name="position_id" disabled>
                                    <option value="">(Belum ada posisi tersedia)</option>
                                </select>
                            @else
                                <select class="form-control rounded-s" id="position_id" name="position_id" required>
                                    <option value="">Pilih Posisi</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}" {{ (string) $currentPosition === (string) $position->id ? 'selected' : '' }}>
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            <label for="position_id" class="color-theme font-12">Posisi/Jabatan</label>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-calendar-check font-14"></i>
                            @if ($workSchedules->isEmpty())
                                <select class="form-control rounded-s" id="work_schedule_id" name="work_schedule_id" disabled>
                                    <option value="">(Belum ada jadwal kerja)</option>
                                </select>
                            @else
                                <select class="form-control rounded-s" id="work_schedule_id" name="work_schedule_id">
                                    <option value="">Pilih Jadwal Kerja (opsional)</option>
                                    @foreach ($workSchedules as $ws)
                                        <option value="{{ $ws->id }}" {{ (string) old('work_schedule_id', optional(auth()->user()->employee)->work_schedule_id) === (string) $ws->id ? 'selected' : '' }}>{{ $ws->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <label for="work_schedule_id" class="color-theme font-12">Jadwal Kerja</label>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-calendar-date font-14"></i>
                            @php
                                $employeeHireDate = old('hire_date', optional(auth()->user()->employee)->hire_date);
                                if ($employeeHireDate) {
                                    try { $employeeHireDate = date('Y-m-d', strtotime($employeeHireDate)); } catch (\Exception $e) { $employeeHireDate = date('Y-m-d'); }
                                } else { $employeeHireDate = date('Y-m-d'); }
                            @endphp
                            <input type="date" class="form-control rounded-s" id="hire_date" name="hire_date" value="{{ $employeeHireDate }}" required />
                            <label for="hire_date" class="color-theme font-12">Tanggal Bergabung</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h5 class="font-14 font-700 mb-2">4. Keluarga & Kontak Darurat</h5>
                    <div class="card card-style p-3 mb-2">
                        <div class="mb-2">
                            <label class="color-theme font-12">Struktur Keluarga</label>
                            <div id="family-list">
                                @php
                                    $familyRows = old('family', null);
                                    if (is_null($familyRows)) {
                                        $familyRows = optional(auth()->user()->employee)->familyMembers ? optional(auth()->user()->employee)->familyMembers->toArray() : [];
                                    }
                                @endphp
                                @if(!empty($familyRows))
                                    @foreach($familyRows as $i => $row)
                                        <div class="family-row mb-2 p-2 rounded-s bg-light" data-index="{{ $i }}">
                                            <div class="row g-2">
                                                <div class="col-4">
                                                    <input type="text" name="family[{{ $i }}][relation]" class="form-control" placeholder="Hubungan" value="{{ $row['relation'] ?? $row->relation ?? '' }}">
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" name="family[{{ $i }}][name]" class="form-control" placeholder="Nama" value="{{ $row['name'] ?? $row->name ?? '' }}">
                                                </div>
                                                <div class="col-2">
                                                    <select name="family[{{ $i }}][gender]" class="form-control">
                                                        <option value="">Jenis Kelamin</option>
                                                        <option value="M" {{ (isset($row['gender']) && $row['gender']=='M') || (isset($row->gender) && $row->gender=='M') ? 'selected' : '' }}>Pria</option>
                                                        <option value="F" {{ (isset($row['gender']) && $row['gender']=='F') || (isset($row->gender) && $row->gender=='F') ? 'selected' : '' }}>Wanita</option>
                                                    </select>
                                                </div>
                                                <div class="col-1">
                                                    <input type="number" name="family[{{ $i }}][age]" class="form-control" placeholder="Umur" value="{{ $row['age'] ?? $row->age ?? '' }}">
                                                </div>
                                                <div class="col-1 d-flex align-items-center">
                                                    <button type="button" class="btn btn-sm btn-danger remove-family">-</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="mt-2">
                                <button type="button" id="add-family" class="btn btn-sm btn-primary">Tambah Keluarga</button>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="color-theme font-12">Kontak Darurat (Wajib 2 orang)</label>
                            <div id="emergency-list">
                                @php
                                    $emRows = old('emergency', null);
                                    if (is_null($emRows)) {
                                        $emRows = optional(auth()->user()->employee)->emergencyContacts ? optional(auth()->user()->employee)->emergencyContacts->toArray() : [];
                                    }
                                @endphp
                                @if(!empty($emRows))
                                    @foreach($emRows as $i => $row)
                                        <div class="em-row mb-2 p-2 rounded-s bg-light" data-index="{{ $i }}">
                                            <div class="row g-2">
                                                <div class="col-4"><input type="text" name="emergency[{{ $i }}][name]" class="form-control" placeholder="Nama" value="{{ $row['name'] ?? $row->name ?? '' }}"></div>
                                                <div class="col-4"><input type="text" name="emergency[{{ $i }}][relation]" class="form-control" placeholder="Hubungan" value="{{ $row['relation'] ?? $row->relation ?? '' }}"></div>
                                                <div class="col-3"><input type="text" name="emergency[{{ $i }}][phone]" class="form-control" placeholder="Telepon" value="{{ $row['phone'] ?? $row->phone ?? '' }}"></div>
                                                <div class="col-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-danger remove-em">-</button></div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="mt-2"><button type="button" id="add-emergency" class="btn btn-sm btn-primary">Tambah Kontak Darurat</button></div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h5 class="font-14 font-700 mb-2">5. Pendidikan & Pelatihan</h5>
                    <div class="card card-style p-3 mb-2">
                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-journal-bookmark font-14"></i>
                            <label class="color-theme font-12">Riwayat Pendidikan</label>
                            <div id="education-list" class="mb-2">
                                @php
                                    $eduRows = old('education', null);
                                    if (is_null($eduRows)) {
                                        $eduRows = optional(auth()->user()->employee)->educationRecords ? optional(auth()->user()->employee)->educationRecords->toArray() : [];
                                    }
                                @endphp
                                @if(!empty($eduRows))
                                    @foreach($eduRows as $i => $row)
                                        <div class="edu-row mb-2 p-2 rounded-s bg-light" data-index="{{ $i }}">
                                            <div class="row g-2">
                                                <div class="col-4"><input type="text" name="education[{{ $i }}][school_name]" class="form-control" placeholder="Nama Sekolah" value="{{ $row['school_name'] ?? $row->school_name ?? '' }}"></div>
                                                <div class="col-3"><input type="text" name="education[{{ $i }}][city]" class="form-control" placeholder="Kota" value="{{ $row['city'] ?? $row->city ?? '' }}"></div>
                                                <div class="col-3"><input type="text" name="education[{{ $i }}][major]" class="form-control" placeholder="Jurusan" value="{{ $row['major'] ?? $row->major ?? '' }}"></div>
                                                <div class="col-1"><input type="number" name="education[{{ $i }}][start_year]" class="form-control" placeholder="Mulai" value="{{ $row['start_year'] ?? $row->start_year ?? '' }}"></div>
                                                <div class="col-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-danger remove-edu">-</button></div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="mt-2"><button type="button" id="add-education" class="btn btn-sm btn-primary">Tambah Pendidikan</button></div>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-book font-14"></i>
                            <label class="color-theme font-12">Riwayat Pelatihan</label>
                            <div id="training-list" class="mb-2">
                                @php
                                    $trRows = old('training', null);
                                    if (is_null($trRows)) {
                                        $trRows = optional(auth()->user()->employee)->trainingRecords ? optional(auth()->user()->employee)->trainingRecords->toArray() : [];
                                    }
                                @endphp
                                @if(!empty($trRows))
                                    @foreach($trRows as $i => $row)
                                        <div class="tr-row mb-2 p-2 rounded-s bg-light" data-index="{{ $i }}">
                                            <div class="row g-2">
                                                <div class="col-4"><input type="text" name="training[{{ $i }}][course_name]" class="form-control" placeholder="Bidang/Jenis" value="{{ $row['course_name'] ?? $row->course_name ?? '' }}"></div>
                                                <div class="col-3"><input type="text" name="training[{{ $i }}][organizer]" class="form-control" placeholder="Penyelenggara" value="{{ $row['organizer'] ?? $row->organizer ?? '' }}"></div>
                                                <div class="col-2"><input type="text" name="training[{{ $i }}][city]" class="form-control" placeholder="Kota" value="{{ $row['city'] ?? $row->city ?? '' }}"></div>
                                                <div class="col-2"><input type="text" name="training[{{ $i }}][duration]" class="form-control" placeholder="Lama" value="{{ $row['duration'] ?? $row->duration ?? '' }}"></div>
                                                <div class="col-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-danger remove-tr">-</button></div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="mt-2"><button type="button" id="add-training" class="btn btn-sm btn-primary">Tambah Pelatihan</button></div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h5 class="font-14 font-700 mb-2">6. Lain-lain</h5>
                    <div class="card card-style p-3 mb-2">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-custom form-label form-icon mb-2">
                                    <i class="bi bi-arrow-up-right-square font-14"></i>
                                    <input type="number" name="height_cm" id="height_cm" class="form-control rounded-s" placeholder="Tinggi (cm)" value="{{ old('height_cm', optional(auth()->user()->employee)->height_cm) }}" />
                                    <label for="height_cm" class="color-theme font-12">Tinggi (cm)</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-custom form-label form-icon mb-2">
                                    <i class="bi bi-arrow-down-right-square font-14"></i>
                                    <input type="number" name="weight_kg" id="weight_kg" class="form-control rounded-s" placeholder="Berat (kg)" value="{{ old('weight_kg', optional(auth()->user()->employee)->weight_kg) }}" />
                                    <label for="weight_kg" class="color-theme font-12">Berat (kg)</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-emoji-smile font-14"></i>
                            <input type="text" name="hobby" id="hobby" class="form-control rounded-s" placeholder="Hobi / minat" value="{{ old('hobby', optional(auth()->user()->employee)->hobby) }}" />
                            <label for="hobby" class="color-theme font-12">Hobi</label>
                        </div>

                        <div class="form-custom form-label form-icon mb-2">
                            <i class="bi bi-bandaid font-14"></i>
                            <input type="text" name="degenerative_diseases" id="degenerative_diseases" class="form-control rounded-s" placeholder="Penyakit degeneratif (jika ada)" value="{{ old('degenerative_diseases', optional(auth()->user()->employee)->degenerative_diseases) }}" />
                            <label for="degenerative_diseases" class="color-theme font-12">Penyakit Degeneratif</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class='btn rounded-s btn-l gradient-green text-uppercase font-700 mt-2 mb-3 btn-full shadow-bg shadow-bg-s' @if ($departments->isEmpty() || $positions->isEmpty()) disabled @endif>
                        <i class="bi bi-check-circle pe-2"></i>Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Information Card -->
    <div class="card card-style">
        <div class="content">
            <div class="alert bg-blue-dark color-white rounded-s" role="alert">
                <i class="bi bi-info-circle-fill pe-2"></i>
                <strong>Informasi:</strong><br>
                Data yang Anda masukkan akan digunakan untuk proses absensi dan administrasi karyawan.
                Pastikan semua data yang dimasukkan benar dan sesuai.
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Preview uploaded image (guard selector)
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

        // Simple repeater helpers
        function makeIndex(container) {
            // reset names to sequential indexes
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

        document.addEventListener('click', function(e) {
            if (e.target.matches('#add-education')) {
                const template = '<div class="edu-row mb-2 p-2 rounded-s bg-light" data-index="__INDEX__">\n  <div class="row g-2">\n    <div class="col-4"><input type="text" name="education[__INDEX__][school_name]" class="form-control" placeholder="Nama Sekolah"></div>\n    <div class="col-3"><input type="text" name="education[__INDEX__][city]" class="form-control" placeholder="Kota"></div>\n    <div class="col-3"><input type="text" name="education[__INDEX__][major]" class="form-control" placeholder="Jurusan"></div>\n    <div class="col-1"><input type="number" name="education[__INDEX__][start_year]" class="form-control" placeholder="Mulai"></div>\n    <div class="col-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-danger remove-edu">-</button></div>\n  </div>\n</div>';
                addRow('education-list', template);
            }

            if (e.target.matches('#add-training')) {
                const template = '<div class="tr-row mb-2 p-2 rounded-s bg-light" data-index="__INDEX__">\n  <div class="row g-2">\n    <div class="col-4"><input type="text" name="training[__INDEX__][course_name]" class="form-control" placeholder="Bidang/Jenis"></div>\n    <div class="col-3"><input type="text" name="training[__INDEX__][organizer]" class="form-control" placeholder="Penyelenggara"></div>\n    <div class="col-2"><input type="text" name="training[__INDEX__][city]" class="form-control" placeholder="Kota"></div>\n    <div class="col-2"><input type="text" name="training[__INDEX__][duration]" class="form-control" placeholder="Lama"></div>\n    <div class="col-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-danger remove-tr">-</button></div>\n  </div>\n</div>';
                addRow('training-list', template);
            }

            if (e.target.matches('#add-family')) {
                const template = '<div class="family-row mb-2 p-2 rounded-s bg-light" data-index="__INDEX__">\n  <div class="row g-2">\n    <div class="col-4"><input type="text" name="family[__INDEX__][relation]" class="form-control" placeholder="Hubungan"></div>\n    <div class="col-4"><input type="text" name="family[__INDEX__][name]" class="form-control" placeholder="Nama"></div>\n    <div class="col-2">\n      <select name="family[__INDEX__][gender]" class="form-control">\n        <option value="">Jenis Kelamin</option>\n        <option value="M">Pria</option>\n        <option value="F">Wanita</option>\n      </select>\n    </div>\n    <div class="col-1"><input type="number" name="family[__INDEX__][age]" class="form-control" placeholder="Umur"></div>\n    <div class="col-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-danger remove-family">-</button></div>\n  </div>\n</div>';
                addRow('family-list', template);
            }

            if (e.target.matches('#add-emergency')) {
                const template = '<div class="em-row mb-2 p-2 rounded-s bg-light" data-index="__INDEX__">\n  <div class="row g-2">\n    <div class="col-4"><input type="text" name="emergency[__INDEX__][name]" class="form-control" placeholder="Nama"></div>\n    <div class="col-4"><input type="text" name="emergency[__INDEX__][relation]" class="form-control" placeholder="Hubungan"></div>\n    <div class="col-3"><input type="text" name="emergency[__INDEX__][phone]" class="form-control" placeholder="Telepon"></div>\n    <div class="col-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-danger remove-em">-</button></div>\n  </div>\n</div>';
                addRow('emergency-list', template);
            }

            // remove handlers
            if (e.target.matches('.remove-edu')) {
                const row = e.target.closest('.edu-row'); if (row) { row.remove(); makeIndex(document.getElementById('education-list')); }
            }
            if (e.target.matches('.remove-tr')) {
                const row = e.target.closest('.tr-row'); if (row) { row.remove(); makeIndex(document.getElementById('training-list')); }
            }
            if (e.target.matches('.remove-family')) {
                const row = e.target.closest('.family-row'); if (row) { row.remove(); makeIndex(document.getElementById('family-list')); }
            }
            if (e.target.matches('.remove-em')) {
                const row = e.target.closest('.em-row'); if (row) { row.remove(); makeIndex(document.getElementById('emergency-list')); }
            }
        });
    </script>
@endpush
