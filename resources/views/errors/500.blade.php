@extends('layouts.app')

@section('title', 'Terjadi Kesalahan - 500')

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-caret-left-fill font-11 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-13">Terjadi Kesalahan</a>
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
                <i class="bi bi-exclamation-octagon color-white font-28"></i>
            </div>

            <!-- Error Title -->
            <h2 class="font-700 mb-3 color-red-dark">500 - Internal Server Error</h2>

            <!-- Error Message -->
            <p class="font-11 opacity-70 mb-4">
                Maaf, terjadi kesalahan pada server. Tim teknis kami akan segera memperbaiki masalah ini.
                Silakan coba beberapa saat lagi.
            </p>

            <!-- Error Details (only in debug mode) -->
            @if (config('app.debug') && isset($exception))
                <div class="alert alert-style bg-red-light mb-4">
                    <div class="alert-icon">
                        <i class="bi bi-bug-fill color-red-dark"></i>
                    </div>
                    <div class="alert-content text-start">
                        <h6 class="alert-title color-red-dark font-600">Debug Information</h6>
                        <p class="alert-text font-10 mb-2 text-break">
                            <strong>File:</strong> {{ $exception->getFile() ?? 'Unknown' }}<br>
                            <strong>Line:</strong> {{ $exception->getLine() ?? 'Unknown' }}<br>
                            <strong>Message:</strong> {{ $exception->getMessage() ?? 'No message' }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Status Info -->
            <div class="row g-0 mb-4">
                <div class="col-12">
                    <div class="bg-theme rounded-s p-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-server color-theme font-16"></i>
                            </div>
                            <div class="text-center">
                                <h6 class="font-600 mb-0 color-white">Server Mengalami Masalah</h6>
                                <p class="font-10 mb-0 color-white opacity-70">Waktu: {{ now()->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row g-2 mb-4">
                <div class="col-6">
                    <a href="{{ route('dashboard') }}" class="btn btn-full rounded-s gradient-blue text-uppercase font-600 shadow-bg shadow-bg-s">
                        <i class="bi bi-house-fill me-2"></i>Dashboard
                    </a>
                </div>
                <div class="col-6">
                    <a href="javascript:location.reload()" class="btn btn-full rounded-s gradient-green text-uppercase font-600 shadow-bg shadow-bg-s">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </a>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="alert alert-style bg-blue-light">
                <div class="alert-icon">
                    <i class="bi bi-headset color-blue-dark"></i>
                </div>
                <div class="alert-content">
                    <h6 class="alert-title color-blue-dark font-600">Masih Bermasalah?</h6>
                    <p class="alert-text font-11 mb-0">
                        Hubungi administrator atau tim teknis jika masalah berlanjut.
                        Sertakan informasi waktu dan aktivitas yang sedang dilakukan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Troubleshooting Card -->
    <div class="card card-style shadow-m mt-4">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-orange-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-tools color-white font-18"></i>
                </div>
                <div>
                    <h5 class="font-600 mb-0 color-dark-dark">Troubleshooting</h5>
                    <p class="mb-0 font-11 opacity-70">Langkah yang bisa dicoba</p>
                </div>
            </div>

            <div class="list-group list-custom-small">
                <div class="list-group-item">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-1-circle color-orange-dark font-14 me-3"></i>
                        </div>
                        <div class="align-self-center">
                            <p class="mb-0 font-12">Refresh halaman (tekan F5 atau tombol refresh)</p>
                        </div>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-2-circle color-orange-dark font-14 me-3"></i>
                        </div>
                        <div class="align-self-center">
                            <p class="mb-0 font-12">Tunggu beberapa menit dan coba lagi</p>
                        </div>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-3-circle color-orange-dark font-14 me-3"></i>
                        </div>
                        <div class="align-self-center">
                            <p class="mb-0 font-12">Bersihkan cache browser</p>
                        </div>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-4-circle color-orange-dark font-14 me-3"></i>
                        </div>
                        <div class="align-self-center">
                            <p class="mb-0 font-12">Logout dan login kembali</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
