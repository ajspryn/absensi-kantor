@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan - 404')

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-caret-left-fill font-11 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-13">Halaman Tidak Ditemukan</a>
        <div class="d-flex">
            <a href="{{ route('dashboard') }}"><i class="bi bi-house-fill font-13 color-highlight"></i></a>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-style shadow-m">
        <div class="content text-center">
            <!-- Error Icon -->
            <div class="bg-orange-dark rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-4 shadow-s" style="width: 80px; height: 80px;">
                <i class="bi bi-exclamation-triangle color-white font-28"></i>
            </div>

            <!-- Error Title -->
            <h2 class="font-700 mb-3 color-orange-dark">404 - Halaman Tidak Ditemukan</h2>

            <!-- Error Message -->
            <p class="font-11 opacity-70 mb-4">
                Maaf, halaman yang Anda cari tidak dapat ditemukan.
                Mungkin halaman telah dipindahkan, dihapus, atau URL yang Anda masukkan salah.
            </p>

            <!-- Search or URL Info -->
            <div class="alert alert-style bg-orange-light mb-4">
                <div class="alert-icon">
                    <i class="bi bi-info-circle-fill color-orange-dark"></i>
                </div>
                <div class="alert-content">
                    <h6 class="alert-title color-orange-dark font-600">URL yang Diminta</h6>
                    <p class="alert-text font-11 mb-0 text-break">{{ request()->fullUrl() }}</p>
                </div>
            </div>

            <!-- Quick Navigation -->
            <div class="row g-2 mb-4">
                <div class="col-12">
                    <h6 class="font-600 mb-3 color-dark-dark">Navigasi Cepat</h6>
                </div>
                <div class="col-6">
                    <a href="{{ route('dashboard') }}" class="btn btn-full rounded-s gradient-blue text-uppercase font-600 shadow-bg shadow-bg-s">
                        <i class="bi bi-house-fill me-2"></i>Dashboard
                    </a>
                </div>
                <div class="col-6">
                    <a href="javascript:history.back()" class="btn btn-full rounded-s gradient-gray text-uppercase font-600 shadow-bg shadow-bg-s">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>

            @auth
                <!-- Quick Menu for Authenticated Users -->
                <div class="row g-2">
                    @canDo('employees.view')
                    <div class="col-6">
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-s rounded-s bg-green-light color-green-dark">
                            <i class="bi bi-people-fill me-1"></i>Karyawan
                        </a>
                    </div>
                    @endCanDo

                    @canDo('attendance.checkin')
                    <div class="col-6">
                        <a href="{{ route('employee.attendance.index') }}" class="btn btn-s rounded-s bg-blue-light color-blue-dark">
                            <i class="bi bi-clock-fill me-1"></i>Absensi
                        </a>
                    </div>
                    @endCanDo

                    @canDo('schedules.view')
                    <div class="col-6">
                        <a href="{{ route('employee.schedule.index') }}" class="btn btn-s rounded-s bg-purple-light color-purple-dark">
                            <i class="bi bi-calendar-fill me-1"></i>Jadwal
                        </a>
                    </div>
                    @endCanDo

                    @canDo('roles.view')
                    <div class="col-6">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-s rounded-s bg-red-light color-red-dark">
                            <i class="bi bi-shield-fill me-1"></i>Roles
                        </a>
                    </div>
                    @endCanDo
                </div>
            @endauth
        </div>
    </div>

    <!-- Suggestions Card -->
    <div class="card card-style shadow-m mt-4">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-lightbulb color-white font-18"></i>
                </div>
                <div>
                    <h5 class="font-600 mb-0 color-dark-dark">Saran</h5>
                    <p class="mb-0 font-11 opacity-70">Hal yang bisa Anda coba</p>
                </div>
            </div>

            <div class="list-group list-custom-small">
                <div class="list-group-item">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-check-circle color-green-dark font-14 me-3"></i>
                        </div>
                        <div class="align-self-center">
                            <p class="mb-0 font-12">Periksa kembali URL yang Anda ketik</p>
                        </div>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-check-circle color-green-dark font-14 me-3"></i>
                        </div>
                        <div class="align-self-center">
                            <p class="mb-0 font-12">Gunakan menu navigasi untuk menemukan halaman</p>
                        </div>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-check-circle color-green-dark font-14 me-3"></i>
                        </div>
                        <div class="align-self-center">
                            <p class="mb-0 font-12">Refresh halaman atau coba beberapa saat lagi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
