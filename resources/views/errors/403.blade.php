@extends('layouts.app')

@section('title', 'Akses Ditolak - 403')

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-caret-left-fill font-11 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-13">Akses Ditolak</a>
        <div class="d-flex">
            <a href="{{ route('dashboard') }}"><i class="bi bi-house-fill font-13 color-highlight"></i></a>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-style shadow-m">
        <div class="content text-center">
            <!-- Error Icon -->
            <div class="bg-red-dark rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-4 shadow-s" style="width: 80px; height: 80px;">
                <i class="bi bi-shield-exclamation color-white font-28"></i>
            </div>

            <!-- Error Title -->
            <h2 class="font-700 mb-3 color-red-dark">403 - Akses Ditolak</h2>

            <!-- Error Message -->
            <p class="font-11 opacity-70 mb-4">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
                Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
            </p>

            <!-- Permission Info -->
            @if (isset($exception) && strpos($exception->getMessage(), 'Required permissions:') !== false)
                <div class="alert alert-style bg-red-light mb-4">
                    <div class="alert-icon">
                        <i class="bi bi-key-fill color-red-dark"></i>
                    </div>
                    <div class="alert-content">
                        <h6 class="alert-title color-red-dark font-600">Permission Diperlukan</h6>
                        <p class="alert-text font-11 mb-0">{{ $exception->getMessage() }}</p>
                    </div>
                </div>
            @endif

            <!-- User Info -->
            @auth
                <div class="row g-0 mb-4">
                    <div class="col-12">
                        <div class="bg-red-light rounded-s p-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-yellow-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill color-theme font-16"></i>
                                </div>
                                <div class="flex-fill text-start">
                                    <h6 class="font-600 text-white mb-0">{{ auth()->user()->name }}</h6>
                                    <p class="font-10 mb-0 text-white opacity-70">{{ auth()->user()->role->name ?? 'No Role' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth

            <!-- Action Buttons -->
            <div class="row g-2">
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
        </div>
    </div>

    <!-- Help Card -->
    <div class="card card-style shadow-m mt-4">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-question-circle color-white font-18"></i>
                </div>
                <div>
                    <h5 class="font-600 mb-0 color-dark-dark">Butuh Bantuan?</h5>
                    <p class="mb-0 font-11 opacity-70">Cara mengatasi masalah akses</p>
                </div>
            </div>

            <div class="list-group list-custom-small">
                <div class="list-group-item">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-1-circle color-blue-dark font-14 me-3"></i>
                        </div>
                        <div class="align-self-center">
                            <p class="mb-0 font-12">Pastikan Anda login dengan akun yang benar</p>
                        </div>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-2-circle color-blue-dark font-14 me-3"></i>
                        </div>
                        <div class="align-self-center">
                            <p class="mb-0 font-12">Hubungi administrator untuk menambah permission</p>
                        </div>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-3-circle color-blue-dark font-14 me-3"></i>
                        </div>
                        <div class="align-self-center">
                            <p class="mb-0 font-12">Coba logout dan login kembali</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
