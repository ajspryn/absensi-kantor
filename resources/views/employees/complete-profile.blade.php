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
                <div class="file-data text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <img id="image-data" src="{{ asset('template/images/avatars/5s.png') }}" class="img-fluid rounded-circle border-4 border-theme" style="width: 100px; height: 100px; object-fit: cover;" alt="Profile Photo">
                        <div class="position-absolute bottom-0 end-0 bg-highlight rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-camera-fill color-white font-14"></i>
                        </div>
                    </div>
                    <span class="upload-file-name d-block text-center mt-2 font-12" data-text-before="<i class='bi bi-check-circle-fill color-green-dark pe-2'></i> Foto:" data-text-after=" siap diupload.">
                    </span>
                    <div class="mt-3">
                        <input type="file" name="photo" class="upload-file" accept="image/*">
                        <p class="btn btn-l text-uppercase font-700 rounded-s upload-file-text bg-highlight shadow-bg shadow-bg-s">
                            <i class="bi bi-camera-fill pe-2"></i>Upload Foto Profil
                        </p>
                    </div>
                </div>

                <!-- Employee ID -->
                <div class="form-custom form-label form-icon mb-3">
                    <i class="bi bi-person-badge font-14"></i>
                    <input type="text" class="form-control rounded-s" id="employee_id" name="employee_id" placeholder="EMP001" value="{{ old('employee_id', optional(auth()->user()->employee)->employee_id) }}" required />
                    <label for="employee_id" class="color-theme font-12">ID Karyawan</label>
                    <span class="font-10">(required)</span>
                </div>

                <!-- Full Name -->
                <div class="form-custom form-label form-icon mb-3">
                    <i class="bi bi-person-circle font-14"></i>
                    <input type="text" class="form-control rounded-s" id="full_name" name="full_name" placeholder="Nama Lengkap" value="{{ old('full_name', optional(auth()->user()->employee)->full_name ?? auth()->user()->name) }}" required />
                    <label for="full_name" class="color-theme font-12">Nama Lengkap</label>
                    <span class="font-10">(required)</span>
                </div>

                <!-- Department -->
                <div class="form-custom form-label form-icon mb-3">
                    <i class="bi bi-building font-14"></i>
                    @php
                        $currentDepartment = old('department_id', optional(auth()->user()->employee)->department_id);
                    @endphp
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
                    <span class="font-10">(required)</span>
                </div>

                <!-- Position -->
                <div class="form-custom form-label form-icon mb-3">
                    <i class="bi bi-briefcase font-14"></i>
                    @php
                        $currentPosition = old('position_id', optional(auth()->user()->employee)->position_id);
                        // prefer controller-provided id if available
                        if (isset($currentPositionId) && $currentPositionId) {
                            $currentPosition = $currentPositionId;
                        }
                    @endphp
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
                    <span class="font-10">(required)</span>
                </div>

                <!-- Phone -->
                <div class="form-custom form-label form-icon mb-3">
                    <i class="bi bi-phone font-14"></i>
                    <input type="tel" class="form-control rounded-s" id="phone" name="phone" placeholder="08123456789" value="{{ old('phone', optional(auth()->user()->employee)->phone) }}" />
                    <label for="phone" class="color-theme font-12">No. Telepon</label>
                    <span class="font-10">(opsional)</span>
                </div>

                <!-- Hire Date -->
                <div class="form-custom form-label form-icon mb-4">
                    <i class="bi bi-calendar-date font-14"></i>
                    @php
                        $employeeHireDate = old('hire_date', optional(auth()->user()->employee)->hire_date);
                        if ($employeeHireDate) {
                            try {
                                $employeeHireDate = date('Y-m-d', strtotime($employeeHireDate));
                            } catch (\Exception $e) {
                                $employeeHireDate = date('Y-m-d');
                            }
                        } else {
                            $employeeHireDate = date('Y-m-d');
                        }
                    @endphp
                    <input type="date" class="form-control rounded-s" id="hire_date" name="hire_date" value="{{ $employeeHireDate }}" required />
                    <label for="hire_date" class="color-theme font-12">Tanggal Bergabung</label>
                    <span class="font-10">(required)</span>
                </div>

                <button type="submit" class='btn rounded-s btn-l gradient-green text-uppercase font-700 mt-4 mb-3 btn-full shadow-bg shadow-bg-s' @if ($departments->isEmpty() || $positions->isEmpty()) disabled @endif>
                    <i class="bi bi-check-circle pe-2"></i>Simpan Profil
                </button>
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
    </script>
@endpush
