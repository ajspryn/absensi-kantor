@extends('layouts.app')@extends('layouts.app')



@section('title', 'Tambah Karyawan - Admin')@section('title', 'Tambah Karyawan - Admin')



@section('header')@section('header')

<!-- Header --> <!-- Header -->

<div class="header-bar header-fixed header-app header-bar-detached">
    <div class="header-bar header-fixed header-app header-bar-detached">

        <a data-back-button href="{{ route('admin.employees.index') }}"><i class="bi bi-caret-left-fill font-11 color-theme ps-2"></i></a> <a data-back-button href="{{ route('admin.employees.index') }}"><i class="bi bi-caret-left-fill font-11 color-theme ps-2"></i></a>

        <a href="#" class="header-title color-theme font-13">Tambah Karyawan</a> <a href="#" class="header-title color-theme font-13">Tambah Karyawan</a>

        <a href="{{ route('admin.employees.index') }}" class=""><i class="bi bi-list font-13 color-highlight"></i></a> <a href="#" class="show-on-theme-light" data-toggle-theme><i class="bi bi-moon-fill font-13"></i></a>

    </div>
</div>

@endsection@endsection



@section('content')@section('content')

@if ($errors->any())
    <div class="alert bg-red-dark alert-dismissible color-white rounded-s fade show pe-2 mb-3" role="alert">
        <strong>Error:</strong>
        <ul class="mb-0 mt-2">

            @foreach ($errors->all() as $error)
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    <li>{{ $error }}</li>
                @endforeach
            @endforeach

        </ul>
        </ul>

        <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button> <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button>

    </div>
    </div>

@endif
@endif



<!-- Form Header -->
<div class="card card-style">

    <div class="card card-style">
        <div class="content">

            <div class="content">
                <div class="d-flex align-items-center mb-3">

                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">

                            <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;"> <i class="bi bi-person-plus color-white font-18"></i>

                                <i class="bi bi-person-plus color-white font-20"></i>
                            </div>

                        </div>
                        <div>

                            <div>
                                <h1 class="font-700 font-16 mb-0">Tambah Karyawan Baru</h1>

                                <h4 class="font-700 mb-0">Tambah Karyawan Baru</h4>
                                <p class="mb-0 font-11 color-white-50">Lengkapi form untuk menambah karyawan</p>

                                <p class="mb-0 font-11 color-white-50">Isi semua informasi karyawan dengan lengkap</p>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
            <form method="POST" action="{{ route('admin.employees.store') }}" enctype="multipart/form-data">

        </div> @csrf



        <form method="POST" action="{{ route('admin.employees.store') }}" enctype="multipart/form-data"> <!-- Photo Upload -->

            @csrf <div class="file-data text-center mb-3">

                <div class="position-relative d-inline-block">

                    <!-- Account Information --> <img id="image-data" src="{{ asset('template/images/avatars/5s.png') }}" class="img-fluid rounded-circle border-3 border-theme" style="width: 80px; height: 80px; object-fit: cover;" alt="Profile Photo">

                    <div class="card card-style">
                        <div class="position-absolute bottom-0 end-0 bg-highlight rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">

                            <div class="content"> <i class="bi bi-camera-fill color-white font-12"></i>

                                <div class="d-flex align-items-center mb-3"> </div>

                                <i class="bi bi-person-circle color-blue-dark font-18 me-2"></i>
                            </div>

                            <h5 class="font-700 mb-0">Informasi Akun</h5> <span class="upload-file-name d-block text-center mt-2 font-11" data-text-before="<i class='bi bi-check-circle-fill color-green-dark pe-2'></i> Foto:" data-text-after=" siap diupload.">

                        </div> </span>

                        <div class="mt-2">

                            <!-- Photo Upload --> <input type="file" name="photo" class="upload-file" accept="image/*">

                            <div class="form-custom form-label form-icon mb-3">
                                <p class="btn btn-l text-uppercase font-600 rounded-s upload-file-text bg-highlight shadow-bg shadow-bg-s" style="min-height: 45px;">

                                    <i class="bi bi-camera font-14"></i> <i class="bi bi-camera-fill pe-2"></i>Upload Foto

                                    <input type="file" class="form-control rounded-s" name="photo" accept="image/*" style="min-height: 45px;" />
                                </p>

                                <label class="color-theme font-11">Foto Profil (Opsional)</label>
                            </div>

                            <span class="font-10 color-white-50">Format: JPG, PNG (Max: 2MB)</span>
                        </div>

                    </div>

                    <!-- Employee ID -->

                    <!-- Full Name -->
                    <div class="form-custom form-label form-icon mb-3">

                        <div class="form-custom form-label form-icon mb-3"> <i class="bi bi-person-badge font-14"></i>

                            <i class="bi bi-person font-14"></i> <input type="text" class="form-control rounded-s" id="employee_id" name="employee_id" placeholder="EMP001" value="{{ old('employee_id') }}" required style="min-height: 45px;" />

                            <input type="text" class="form-control rounded-s" name="full_name" value="{{ old('full_name') }}" required style="min-height: 45px;" /> <label for="employee_id" class="color-theme font-11">ID Karyawan</label>

                            <label class="color-theme font-11">Nama Lengkap <span class="color-red-dark">*</span></label> <span class="font-10">(required)</span>

                        </div>
                    </div>



                    <!-- Email --> <!-- Email -->

                    <div class="form-custom form-label form-icon mb-3">
                        <div class="form-custom form-label form-icon mb-3">

                            <i class="bi bi-envelope font-14"></i> <i class="bi bi-envelope font-14"></i>

                            <input type="email" class="form-control rounded-s" name="email" value="{{ old('email') }}" required style="min-height: 45px;" /> <input type="email" class="form-control rounded-s" id="email" name="email" placeholder="email@example.com" value="{{ old('email') }}" required style="min-height: 45px;" />

                            <label class="color-theme font-11">Email <span class="color-red-dark">*</span></label> <label for="email" class="color-theme font-11">Email</label>

                        </div> <span class="font-10">(required)</span>

                    </div>

                    <!-- Password -->

                    <div class="form-custom form-label form-icon mb-3"> <!-- Password -->

                        <i class="bi bi-lock font-14"></i>
                        <div class="form-custom form-label form-icon mb-3">

                            <input type="password" class="form-control rounded-s" name="password" required style="min-height: 45px;" /> <i class="bi bi-lock font-14"></i>

                            <label class="color-theme font-11">Password <span class="color-red-dark">*</span></label> <input type="password" class="form-control rounded-s" id="password" name="password" placeholder="Password" required style="min-height: 45px;" />

                            <span class="font-10 color-white-50">Minimal 8 karakter</span> <label for="password" class="color-theme font-11">Password</label>

                        </div> <span class="font-10">(required)</span>

                    </div>

                    <!-- Confirm Password -->

                    <div class="form-custom form-label form-icon mb-3"> <!-- Full Name -->

                        <i class="bi bi-lock-fill font-14"></i>
                        <div class="form-custom form-label form-icon mb-3">

                            <input type="password" class="form-control rounded-s" name="password_confirmation" required style="min-height: 45px;" /> <i class="bi bi-person-circle font-14"></i>

                            <label class="color-theme font-11">Konfirmasi Password <span class="color-red-dark">*</span></label> <input type="text" class="form-control rounded-s" id="full_name" name="full_name" placeholder="Nama Lengkap" value="{{ old('full_name') }}" required style="min-height: 45px;" />

                        </div> <label for="full_name" class="color-theme font-11">Nama Lengkap</label>

                    </div> <span class="font-10">(required)</span>

                </div>
            </div>



            <!-- Employee Information --> <!-- Department -->

            <div class="card card-style">
                <div class="form-custom form-label form-icon mb-3">

                    <div class="content"> <i class="bi bi-building font-14"></i>

                        <div class="d-flex align-items-center mb-3"> <select class="form-control rounded-s" id="department_id" name="department_id" required style="min-height: 45px;">

                                <i class="bi bi-briefcase color-green-dark font-18 me-2"></i>
                                <option value="">Pilih Departemen</option>

                                <h5 class="font-700 mb-0">Informasi Karyawan</h5>
                                @foreach ($departments as $department)
                        </div>
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>

                            {{ $department->name }}

                            <!-- Employee ID -->
                        </option>

                        <div class="form-custom form-label form-icon mb-3">
                            @endforeach

                            <i class="bi bi-person-badge font-14"></i> </select>

                            <input type="text" class="form-control rounded-s" name="employee_id" value="{{ old('employee_id') }}" required style="min-height: 45px;" /> <label for="department_id" class="color-theme font-11">Departemen</label>

                            <label class="color-theme font-11">ID Karyawan <span class="color-red-dark">*</span></label> <span class="font-10">(required)</span>

                            <span class="font-10 color-white-50">Contoh: EMP001, KRY001</span>
                        </div>

                    </div>

                    <!-- Position -->

                    <!-- Department -->
                    <div class="form-custom form-label form-icon mb-3">

                        <div class="form-custom form-label form-icon mb-3"> <i class="bi bi-briefcase font-14"></i>

                            <i class="bi bi-building font-14"></i> <select class="form-control rounded-s" id="position_id" name="position_id" required style="min-height: 45px;">

                                <select class="form-control rounded-s" name="department_id" required style="min-height: 45px;">
                                    <option value="">Pilih Posisi</option>

                                    <option value="">Pilih Departemen</option>
                                    @foreach ($positions as $position)
                                        @foreach ($departments as $department)
                                            <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>

                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}> {{ $position->name }}

                                                {{ $department->name }} </option>

                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>

                            </select> <label for="position_id" class="color-theme font-11">Posisi/Jabatan</label>

                            <label class="color-theme font-11">Departemen <span class="color-red-dark">*</span></label> <span class="font-10">(required)</span>

                        </div>
                    </div>



                    <!-- Position --> <!-- Role -->

                    <div class="form-custom form-label form-icon mb-3">
                        <div class="form-custom form-label form-icon mb-3">

                            <i class="bi bi-award font-14"></i> <i class="bi bi-shield-check font-14"></i>

                            <select class="form-control rounded-s" name="position_id" required style="min-height: 45px;"> <select class="form-control rounded-s" id="role_id" name="role_id" required style="min-height: 45px;">

                                    <option value="">Pilih Posisi</option>
                                    <option value="">Pilih Role</option>

                                    @foreach ($positions as $position)
                                        @foreach ($roles as $role)
                                            <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>

                                                {{ $position->name }} {{ $role->name }}

                                            </option>
                                            </option>
                                        @endforeach
                                    @endforeach

                                </select> </select>

                            <label class="color-theme font-11">Posisi <span class="color-red-dark">*</span></label> <label for="role_id" class="color-theme font-11">Role Pengguna</label>

                        </div> <span class="font-10">(required)</span>

                    </div>

                    <!-- Role -->

                    <div class="form-custom form-label form-icon mb-3"> <!-- Phone -->

                        <i class="bi bi-shield-check font-14"></i>
                        <div class="form-custom form-label form-icon mb-3">

                            <select class="form-control rounded-s" name="role_id" required style="min-height: 45px;"> <i class="bi bi-phone font-14"></i>

                                <option value="">Pilih Role</option> <input type="tel" class="form-control rounded-s" id="phone" name="phone" placeholder="08123456789" value="{{ old('phone') }}" style="min-height: 45px;" />

                                @foreach ($roles as $role)
                                    <label for="phone" class="color-theme font-11">No. Telepon</label>

                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}> <span class="font-10">(opsional)</span>

                                        {{ $role->name }} - {{ $role->description }}
                        </div>

                        </option>
                    </div>
                    @endforeach

                    </select> <!-- Hire Date -->

                    <label class="color-theme font-11">Role <span class="color-red-dark">*</span></label>
                    <div class="form-custom form-label form-icon mb-3">

                    </div> <i class="bi bi-calendar-date font-14"></i>

                    <input type="date" class="form-control rounded-s" id="hire_date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required style="min-height: 45px;" />

                    <!-- Phone --> <label for="hire_date" class="color-theme font-11">Tanggal Bergabung</label>

                    <div class="form-custom form-label form-icon mb-3"> <span class="font-10">(required)</span>

                        <i class="bi bi-telephone font-14"></i>
                    </div>

                    <input type="text" class="form-control rounded-s" name="phone" value="{{ old('phone') }}" style="min-height: 45px;" />

                    <label class="color-theme font-11">No. Telepon (Opsional)</label> <!-- Salary -->

                    <span class="font-10 color-white-50">Contoh: 08123456789</span>
                    <div class="form-custom form-label form-icon mb-3">

                    </div> <i class="bi bi-currency-dollar font-14"></i>

                    <input type="number" class="form-control rounded-s" id="salary" name="salary" placeholder="5000000" value="{{ old('salary') }}" style="min-height: 45px;" />

                    <!-- Hire Date --> <label for="salary" class="color-theme font-11">Gaji (Rp)</label>

                    <div class="form-custom form-label form-icon mb-3"> <span class="font-10">(opsional)</span>

                        <i class="bi bi-calendar-date font-14"></i>
                    </div>

                    <input type="date" class="form-control rounded-s" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required style="min-height: 45px;" />

                    <label class="color-theme font-11">Tanggal Bergabung <span class="color-red-dark">*</span></label> <!-- Status -->

                </div>
                <div class="form-check form-check-custom mb-3">

                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>

                    <!-- Salary --> <label class="form-check-label font-12" for="is_active">

                        <div class="form-custom form-label form-icon mb-3"> <strong>Karyawan Aktif</strong>

                            <i class="bi bi-currency-dollar font-14"></i> <span class="d-block font-10 color-white-50">Centang jika karyawan dalam status aktif</span>

                            <input type="number" class="form-control rounded-s" name="salary" value="{{ old('salary') }}" min="0" style="min-height: 45px;" />
                    </label>

                    <label class="color-theme font-11">Gaji (Opsional)</label>
                </div>

                <span class="font-10 color-white-50">Dalam Rupiah (tanpa titik/koma)</span>

            </div>
            <div class="row g-2">

                <div class="col-6">

                    <!-- Status --> <a href="{{ route('admin.employees.index') }}" class="btn btn-l bg-gray-dark color-white rounded-s text-uppercase font-600 btn-full" style="min-height: 45px;">

                        <div class="form-custom form-label form-icon mb-3"> <i class="bi bi-x-circle pe-2"></i>Batal

                            <i class="bi bi-toggle-on font-14"></i>
                    </a>

                    <select class="form-control rounded-s" name="is_active" style="min-height: 45px;">
                </div>

                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                <div class="col-6">

                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Non-Aktif</option> <button type="submit" class='btn btn-l gradient-green text-uppercase font-600 rounded-s btn-full shadow-bg shadow-bg-s' style="min-height: 45px;">

                        </select> <i class="bi bi-check-circle pe-2"></i>Simpan

                        <label class="color-theme font-11">Status</label> </button>

                </div>
            </div>

    </div>
</div>

</div>
</form>

</div>

<!-- Submit Buttons --> </div>

<div class="card card-style">@endsection

<div class="content">
    <div class="row">
        <div class="col-6 pe-2">
            <button type="submit" class="btn btn-full rounded-s bg-highlight shadow-bg shadow-bg-s font-600 text-uppercase">
                <i class="bi bi-check-circle pe-2"></i>Simpan
            </button>
        </div>
        <div class="col-6 ps-2">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-full rounded-s bg-theme shadow-bg shadow-bg-s font-600 text-uppercase">
                <i class="bi bi-x-circle pe-2"></i>Batal
            </a>
        </div>
    </div>
</div>
</div>
</form>
@endsection
