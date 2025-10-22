@extends('layouts.admin')

@section('title', 'Tambah Departemen - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Tambah Departemen',
        'backUrl' => route('admin.departments.index'),
        'rightHtml' => '<a href="' . route('admin.departments.index') . '"><i class="bi bi-list font-13 color-highlight"></i></a>',
    ])
@endsection

@section('content')
    @include('admin.partials.alerts')

    <!-- Form Header -->
    <div class="card card-style shadow-m mb-4">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-building-add color-white font-18"></i>
                </div>
                <div>
                    <h4 class="font-700 mb-0 color-dark-dark">Tambah Departemen Baru</h4>
                    <p class="mb-0 font-12 opacity-70">Buat departemen untuk mengorganisir karyawan</p>
                </div>
            </div>
        </div>
    </div>


    <form method="POST" action="{{ route('admin.departments.store') }}">
        @csrf
        <div class="card card-style shadow-m mb-4">
            <div class="content">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center me-2 shadow-s" style="width: 35px; height: 35px;">
                        <i class="bi bi-info-circle color-white font-14"></i>
                    </div>
                    <h4 class="font-700 mb-0 color-dark-dark">Informasi Departemen</h4>
                </div>

                <!-- Department Name -->
                <div class="form-custom form-label form-icon mb-3">
                    <i class="bi bi-building font-14"></i>
                    <input type="text" class="form-control rounded-s" name="name" value="{{ old('name') }}" required style="min-height: 45px;" placeholder="Nama Departemen" />
                    <label class="color-theme font-11">Nama Departemen <span class="color-red-dark">*</span></label>
                    <span class="font-10 color-white-50">Contoh: IT, HR, Finance, Marketing</span>
                    @error('name')
                        <div class="color-red-dark font-10 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-custom form-label form-icon mb-3">
                    <i class="bi bi-text-paragraph font-14"></i>
                    <textarea class="form-control rounded-s" name="description" rows="4" style="min-height: 80px;" placeholder="Deskripsi Departemen">{{ old('description') }}</textarea>
                    <label class="color-theme font-11">Deskripsi (Opsional)</label>
                    <span class="font-10 color-white-50">Jelaskan tugas dan tanggung jawab departemen</span>
                    @error('description')
                        <div class="color-red-dark font-10 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Manager -->
                <div class="form-custom form-label form-icon mb-3">
                    <i class="bi bi-person-badge font-14"></i>
                    <select class="form-control rounded-s" name="manager_id" style="min-height: 45px;">
                        <option value="">Pilih Manager (Opsional)</option>
                        @foreach ($availableManagers as $employee)
                            <option value="{{ $employee->id }}" {{ old('manager_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }} - {{ $employee->employee_id }}
                            </option>
                        @endforeach
                    </select>
                    <label class="color-theme font-11">Manager Departemen</label>
                    <span class="font-10 color-white-50">Manager dapat dipilih nanti setelah departemen dibuat</span>
                    @error('manager_id')
                        <div class="color-red-dark font-10 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="form-custom form-label form-icon mb-3">
                    <i class="bi bi-toggle-on font-14"></i>
                    <select class="form-control rounded-s" name="is_active" style="min-height: 45px;">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    <label class="color-theme font-11">Status</label>
                    <span class="font-10 color-white-50">Departemen aktif dapat digunakan untuk penugasan karyawan</span>
                    @error('is_active')
                        <div class="color-red-dark font-10 mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="card card-style shadow-m mb-4">
            <div class="content">
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-full rounded-s btn-danger font-600 text-uppercase w-100">
                            <i class="bi bi-x-circle pe-2"></i>Batal
                        </a>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-full rounded-s bg-highlight shadow-bg shadow-bg-s font-600 text-uppercase w-100">
                            <i class="bi bi-check-circle pe-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </div>
    </form>
@endsection
