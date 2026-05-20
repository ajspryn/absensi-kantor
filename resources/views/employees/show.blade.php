@extends('layouts.app')

@section('title', 'Detail Karyawan - Admin')

@section('header')
    <!-- Header -->
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('admin.employees.index') }}"><i class="bi bi-caret-left-fill font-11 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-13">Detail Karyawan</a>
        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasPermission('employees.edit'))<a href="{{ route('admin.employees.edit', $employee) }}" class=""><i class="bi bi-pencil-fill font-13 color-highlight"></i></a>@endif
    </div>
@endsection

@section('content')
    <!-- Employee Profile Card -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center">
                <div class="align-self-center">
                    <div class="position-relative">
                        <img src="{{ $employee->photo ? asset('storage/' . $employee->photo) : asset('template/images/avatars/5s.png') }}" width="80" height="80" class="rounded-circle me-3 border-4 border-{{ $employee->is_active ? 'green' : 'red' }}-dark">
                        <span class="position-absolute bottom-0 end-0 bg-{{ $employee->is_active ? 'green' : 'red' }}-dark border-2 border-white rounded-circle me-3" style="width: 24px; height: 24px;"></span>
                    </div>
                </div>
                <div class="align-self-center flex-grow-1">
                    <h1 class="font-700 font-20 mb-1">{{ $employee->full_name }}</h1>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-theme rounded-xl font-11 me-2" style="color:#fff !important;">{{ $employee->employee_id }}</span>
                        <span class="badge bg-highlight rounded-xl font-11" style="color:#fff !important;">{{ $employee->position_name ?? '-' }}</span>
                    </div>
                    <p class="mb-0 font-13 color-theme">
                        <i class="bi bi-building pe-1"></i>{{ optional($employee->department)->name ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Information -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-blue-light rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-person-badge color-blue-dark font-18"></i>
                </div>
                <h4 class="font-700 mb-0">Informasi Pekerjaan</h4>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="bg-gray-light rounded-s p-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-envelope color-blue-dark font-16 me-3"></i>
                            <div>
                                <h6 class="mb-0 font-13 color-theme">Email</h6>
                                <p class="mb-0 font-14 font-600 color-theme">{{ $employee->user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <div class="bg-gray-light rounded-s p-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-date color-orange-dark font-16 me-3"></i>
                            <div>
                                <h6 class="mb-0 font-13 color-theme">Tanggal Bergabung</h6>
                                <p class="mb-0 font-14 font-600 color-theme">{{ optional($employee->hire_date)->format('d F Y') ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="bg-gray-light rounded-s p-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-{{ $employee->is_active ? 'check-circle' : 'x-circle' }} color-{{ $employee->is_active ? 'green' : 'red' }}-dark font-16 me-3"></i>
                            <div>
                                <h6 class="mb-0 font-13 color-theme">Status</h6>
                                <p class="mb-0 font-14 font-600 color-{{ $employee->is_active ? 'green' : 'red' }}-dark">
                                    {{ $employee->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Info Card -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-violet-light rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-person-vcard color-violet-dark font-18"></i>
                </div>
                <h4 class="font-700 mb-0">Data Pribadi</h4>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="bg-gray-light rounded-s p-3">
                        <h6 class="mb-0 font-13 color-theme opacity-70">NIK KTP</h6>
                        <p class="mb-0 font-14 font-600 color-theme">{{ $employee->nik_ktp ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="bg-gray-light rounded-s p-3">
                        <h6 class="mb-0 font-13 color-theme opacity-70">Tempat, Tanggal Lahir</h6>
                        <p class="mb-0 font-14 font-600 color-theme">
                            {{ $employee->birth_place ?? '-' }}, 
                            {{ $employee->birth_date ? CarbonCarbon::parse($employee->birth_date)->format('d F Y') : '-' }}
                        </p>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="bg-gray-light rounded-s p-3">
                        <h6 class="mb-0 font-13 color-theme opacity-70">Jenis Kelamin & Status</h6>
                        <p class="mb-0 font-14 font-600 color-theme">
                            {{ $employee->gender == 'M' ? 'Laki-Laki' : ($employee->gender == 'F' ? 'Perempuan' : '-') }} / 
                            {{ $employee->marital_status ?? '-' }}
                        </p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="bg-gray-light rounded-s p-3">
                        <h6 class="mb-0 font-13 color-theme opacity-70">Fisik & Hobi</h6>
                        <p class="mb-0 font-14 font-600 color-theme">
                            Tinggi: {{ $employee->height_cm ?? '-' }} cm | Berat: {{ $employee->weight_kg ?? '-' }} kg <br>
                            Hobi: {{ $employee->hobby ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kontak dan Alamat Card -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-green-light rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-geo-alt color-green-dark font-18"></i>
                </div>
                <h4 class="font-700 mb-0">Kontak & Alamat</h4>
            </div>

            <div class="row">
                <div class="col-6 mb-3">
                    <div class="bg-gray-light rounded-s p-3 h-100">
                        <h6 class="mb-0 font-13 color-theme opacity-70">Telepon</h6>
                        <p class="mb-0 font-14 font-600 color-theme">{{ $employee->phone ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="bg-gray-light rounded-s p-3 h-100">
                        <h6 class="mb-0 font-13 color-theme opacity-70">No. HP</h6>
                        <p class="mb-0 font-14 font-600 color-theme">{{ $employee->mobile ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="bg-gray-light rounded-s p-3">
                        <h6 class="mb-0 font-13 color-theme opacity-70">Alamat KTP</h6>
                        <p class="mb-0 font-14 font-600 color-theme">{{ $employee->address_ktp ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="bg-gray-light rounded-s p-3">
                        <h6 class="mb-0 font-13 color-theme opacity-70">Alamat Domisili</h6>
                        <p class="mb-0 font-14 font-600 color-theme">{{ $employee->address_domisili ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="bg-gray-light rounded-s p-3">
                        <h6 class="mb-0 font-13 color-theme opacity-70">Status Tempat Tinggal / Pajak / dll</h6>
                        <p class="mb-0 font-14 font-600 color-theme">{{ $employee->residence_status ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Kesehatan -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-red-light rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-heart-pulse color-red-dark font-18"></i>
                </div>
                <h4 class="font-700 mb-0">Informasi Kesehatan</h4>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="bg-gray-light rounded-s p-3">
                        <h6 class="mb-0 font-13 color-theme opacity-70">Kondisi Kesehatan</h6>
                        <p class="mb-0 font-14 font-600 color-theme">{{ $employee->health_condition ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="bg-gray-light rounded-s p-3">
                        <h6 class="mb-0 font-13 color-theme opacity-70">Penyakit Bawaan / Medis</h6>
                        <p class="mb-0 font-14 font-600 color-theme">
                            {{ $employee->degenerative_diseases ?? '-' }} 
                            @if($employee->has_medical_history) <span class="badge bg-red-dark ms-2">Ada Riwayat Khusus</span> @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Keluarga & Darurat -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-yellow-light rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-people color-yellow-dark font-18"></i>
                </div>
                <h4 class="font-700 mb-0">Keluarga & Darurat</h4>
            </div>

            <h5 class="font-600 font-14 mb-2">Kontak Darurat</h5>
            @php $emergencies = is_string($employee->emergency_contact) ? json_decode($employee->emergency_contact, true) : $employee->emergency_contact; @endphp
            @if(!empty($emergencies) && is_array($emergencies))
                @foreach($emergencies as $em)
                <div class="bg-gray-light rounded-s p-2 mb-2 d-flex justify-content-between">
                    <div><span class="font-600">{{ $em['name'] ?? '-' }}</span> <span class="font-11 opacity-60">({{ $em['relation'] ?? '-' }})</span></div>
                    <div><a href="tel:{{ $em['phone'] ?? '' }}" class="color-blue-dark">{{ $em['phone'] ?? '-' }}</a></div>
                </div>
                @endforeach
            @else
                <p class="font-13 opacity-70 mb-3">Tidak ada data.</p>
            @endif

            <h5 class="font-600 font-14 mb-2 mt-4">Susunan Keluarga</h5>
            @php $families = is_string($employee->family_structure) ? json_decode($employee->family_structure, true) : $employee->family_structure; @endphp
            @if(!empty($families) && is_array($families))
                @foreach($families as $fam)
                <div class="bg-gray-light rounded-s p-2 mb-2">
                    <div class="d-flex justify-content-between">
                        <span class="font-600">{{ $fam['name'] ?? '-' }}</span>
                        <span class="font-12">{{ $fam['relation'] ?? '-' }}</span>
                    </div>
                    <div class="font-12 opacity-70">{{ $fam['education'] ?? '-' }} - {{ $fam['occupation'] ?? '-' }}</div>
                </div>
                @endforeach
            @else
                <p class="font-13 opacity-70">Tidak ada data.</p>
            @endif
        </div>
    </div>

    <!-- Riwayat Edukasi & Pelatihan -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-brown-light rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-mortarboard color-brown-dark font-18"></i>
                </div>
                <h4 class="font-700 mb-0">Edukasi & Pelatihan</h4>
            </div>

            <h5 class="font-600 font-14 mb-2">Riwayat Pendidikan</h5>
            @php $educations = is_string($employee->education_history) ? json_decode($employee->education_history, true) : $employee->education_history; @endphp
            @if(!empty($educations) && is_array($educations))
                @foreach($educations as $edu)
                <div class="bg-gray-light rounded-s p-2 mb-2">
                    <h6 class="font-13 font-600 mb-0">{{ $edu['institution'] ?? '-' }} <span class="fw-normal">({{ $edu['degree'] ?? '-' }})</span></h6>
                    <span class="font-11 opacity-70">{{ $edu['year_start'] ?? '-' }} - {{ $edu['year_end'] ?? '-' }} | IPK: {{ $edu['gpa'] ?? '-' }}</span>
                </div>
                @endforeach
            @else
                <p class="font-13 opacity-70 mb-3">Tidak ada data.</p>
            @endif

            <h5 class="font-600 font-14 mb-2 mt-4">Riwayat Pelatihan</h5>
            @php $trainings = is_string($employee->training_history) ? json_decode($employee->training_history, true) : $employee->training_history; @endphp
            @if(!empty($trainings) && is_array($trainings))
                @foreach($trainings as $tr)
                <div class="bg-gray-light rounded-s p-2 mb-2">
                    <h6 class="font-13 font-600 mb-0">{{ $tr['course_name'] ?? '-' }}</h6>
                    <span class="font-11 opacity-70">{{ $tr['organizer'] ?? '-' }} | {{ $tr['year'] ?? '-' }}</span>
                </div>
                @endforeach
            @else
                <p class="font-13 opacity-70">Tidak ada data.</p>
            @endif
        </div>
    </div>

    <!-- Dokumen Pendukung -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-dark-light rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-file-earmark-text color-black font-18"></i>
                </div>
                <h4 class="font-700 mb-0">Dokumen Pendukung</h4>
            </div>

            <div class="row">
                <div class="col-4 text-center">
                    <div class="bg-gray-light rounded-s p-2">
                        <i class="bi bi-file-pdf font-24 {{ $employee->ktp_path ? 'color-blue-dark' : 'color-gray-dark opacity-30' }}"></i>
                        <h6 class="font-11 mt-1 mb-1">KTP</h6>
                        @if($employee->ktp_path)
                            <a href="{{ asset('storage/'.$employee->ktp_path) }}" target="_blank" class="font-10 color-highlight">Lihat</a>
                        @else
                            <span class="font-10 opacity-50">-</span>
                        @endif
                    </div>
                </div>
                <div class="col-4 text-center">
                    <div class="bg-gray-light rounded-s p-2">
                        <i class="bi bi-file-pdf font-24 {{ $employee->kk_path ? 'color-blue-dark' : 'color-gray-dark opacity-30' }}"></i>
                        <h6 class="font-11 mt-1 mb-1">KK</h6>
                        @if($employee->kk_path)
                            <a href="{{ asset('storage/'.$employee->kk_path) }}" target="_blank" class="font-10 color-highlight">Lihat</a>
                        @else
                            <span class="font-10 opacity-50">-</span>
                        @endif
                    </div>
                </div>
                <div class="col-4 text-center">
                    <div class="bg-gray-light rounded-s p-2">
                        <i class="bi bi-file-pdf font-24 {{ $employee->marriage_certificate_path ? 'color-blue-dark' : 'color-gray-dark opacity-30' }}"></i>
                        <h6 class="font-11 mt-1 mb-1">S. Nikah</h6>
                        @if($employee->marriage_certificate_path)
                            <a href="{{ asset('storage/'.$employee->marriage_certificate_path) }}" target="_blank" class="font-10 color-highlight">Lihat</a>
                        @else
                            <span class="font-10 opacity-50">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Recent Attendance (if needed in future) -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-green-light rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-clock-history color-green-dark font-18"></i>
                </div>
                <h4 class="font-700 mb-0">Statistik Absensi</h4>
            </div>

            <div class="row text-center">
                <div class="col-4">
                    <div class="bg-blue-light p-3 rounded-s">
                        <i class="bi bi-calendar-check color-blue-dark font-20 d-block mb-2"></i>
                        <h6 class="mb-1 color-blue-dark font-600">Bulan Ini</h6>
                        <p class="mb-0 font-12 opacity-70">Coming Soon</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-green-light p-3 rounded-s">
                        <i class="bi bi-check-circle color-green-dark font-20 d-block mb-2"></i>
                        <h6 class="mb-1 color-green-dark font-600">Hadir</h6>
                        <p class="mb-0 font-12 opacity-70">Coming Soon</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-red-light p-3 rounded-s">
                        <i class="bi bi-x-circle color-red-dark font-20 d-block mb-2"></i>
                        <h6 class="mb-1 color-red-dark font-600">Tidak Hadir</h6>
                        <p class="mb-0 font-12 opacity-70">Coming Soon</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card card-style">
        <div class="content">
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasPermission('employees.edit'))
@if(auth()->user()->hasRole('Admin') || auth()->user()->hasPermission('employees.edit'))
                    <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-full rounded-s bg-highlight shadow-bg shadow-bg-s font-700 text-uppercase mb-2 w-100" style="background-color: #ff9800 !important; color: #fff !important; border: none !important;">
                        <i class="bi bi-pencil pe-2"></i>Edit
                    </a>
                </div>
                <div class="col-12 col-md-6">
                    <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')" class="d-inline w-100">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-full rounded-s bg-red-dark font-700 text-uppercase mb-2 w-100" style="background-color: #d32f2f !important; color: #fff !important; border: none !important;">
                            <i class="bi bi-trash pe-2"></i>Hapus
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
