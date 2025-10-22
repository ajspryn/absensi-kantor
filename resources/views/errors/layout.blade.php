@extends('layouts.app')

@section('title', 'Terjadi Kesalahan - ' . ($exception->getStatusCode() ?? 'Error'))

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
    @php
        $statusCode = $exception->getStatusCode() ?? 500;
        $statusText = '';
        $statusColor = 'red';
        $statusIcon = 'exclamation-circle';

        switch ($statusCode) {
            case 401:
                $statusText = 'Unauthorized - Authentication Required';
                $statusColor = 'orange';
                $statusIcon = 'lock';
                break;
            case 419:
                $statusText = 'Page Expired - Session Timeout';
                $statusColor = 'purple';
                $statusIcon = 'clock';
                break;
            case 429:
                $statusText = 'Too Many Requests - Rate Limit Exceeded';
                $statusColor = 'blue';
                $statusIcon = 'speedometer';
                break;
            case 503:
                $statusText = 'Service Unavailable - Maintenance Mode';
                $statusColor = 'gray';
                $statusIcon = 'tools';
                break;
            default:
                $statusText = 'Something Went Wrong';
                $statusColor = 'red';
                $statusIcon = 'exclamation-triangle';
        }
    @endphp

    <div class="card card-style shadow-m">
        <div class="content text-center">
            <!-- Error Icon -->
            <div class="bg-{{ $statusColor }}-dark rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-4 shadow-s" style="width: 80px; height: 80px;">
                <i class="bi bi-{{ $statusIcon }} color-white font-28"></i>
            </div>

            <!-- Error Title -->
            <h2 class="font-700 mb-3 color-{{ $statusColor }}-dark">{{ $statusCode }} - {{ $statusText }}</h2>

            <!-- Error Message -->
            <p class="font-11 opacity-70 mb-4">
                @switch($statusCode)
                    @case(401)
                        Anda perlu login untuk mengakses halaman ini. Silakan login terlebih dahulu.
                    @break

                    @case(419)
                        Sesi Anda telah berakhir. Silakan refresh halaman dan coba lagi.
                    @break

                    @case(429)
                        Terlalu banyak permintaan. Silakan tunggu beberapa saat sebelum mencoba lagi.
                    @break

                    @case(503)
                        Aplikasi sedang dalam mode maintenance. Silakan coba beberapa saat lagi.
                    @break

                    @default
                        Terjadi kesalahan yang tidak terduga. Silakan coba lagi atau hubungi administrator.
                @endswitch
            </p>

            <!-- Error Details -->
            @if (isset($exception) && $exception->getMessage())
                <div class="alert alert-style bg-{{ $statusColor }}-light mb-4">
                    <div class="alert-icon">
                        <i class="bi bi-info-circle-fill color-{{ $statusColor }}-dark"></i>
                    </div>
                    <div class="alert-content">
                        <h6 class="alert-title color-{{ $statusColor }}-dark font-600">Detail Error</h6>
                        <p class="alert-text font-11 mb-0">{{ $exception->getMessage() }}</p>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="row g-2 mb-4">
                @if ($statusCode == 401)
                    <div class="col-6">
                        <a href="{{ route('login') }}" class="btn btn-full rounded-s gradient-blue text-uppercase font-600 shadow-bg shadow-bg-s">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('dashboard') }}" class="btn btn-full rounded-s gradient-gray text-uppercase font-600 shadow-bg shadow-bg-s">
                            <i class="bi bi-house-fill me-2"></i>Home
                        </a>
                    </div>
                @elseif($statusCode == 419)
                    <div class="col-6">
                        <a href="javascript:location.reload()" class="btn btn-full rounded-s gradient-green text-uppercase font-600 shadow-bg shadow-bg-s">
                            <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('dashboard') }}" class="btn btn-full rounded-s gradient-blue text-uppercase font-600 shadow-bg shadow-bg-s">
                            <i class="bi bi-house-fill me-2"></i>Dashboard
                        </a>
                    </div>
                @else
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
                @endif
            </div>

            <!-- Status Code Info -->
            <div class="row g-0">
                <div class="col-12">
                    <div class="bg-theme rounded-s p-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <span class="color-theme font-12 font-700">{{ $statusCode }}</span>
                            </div>
                            <div class="text-center">
                                <h6 class="font-600 mb-0 color-white">HTTP Error Code</h6>
                                <p class="font-10 mb-0 color-white opacity-70">{{ now()->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Help -->
    <div class="card card-style shadow-m mt-4">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-question-circle color-white font-18"></i>
                </div>
                <div>
                    <h5 class="font-600 mb-0 color-dark-dark">Bantuan</h5>
                    <p class="mb-0 font-11 opacity-70">Langkah yang dapat dilakukan</p>
                </div>
            </div>

            <div class="list-group list-custom-small">
                @switch($statusCode)
                    @case(401)
                        <div class="list-group-item">
                            <div class="d-flex">
                                <div class="align-self-center">
                                    <i class="bi bi-check-circle color-blue-dark font-14 me-3"></i>
                                </div>
                                <div class="align-self-center">
                                    <p class="mb-0 font-12">Login dengan username dan password yang benar</p>
                                </div>
                            </div>
                        </div>
                    @break

                    @case(419)
                        <div class="list-group-item">
                            <div class="d-flex">
                                <div class="align-self-center">
                                    <i class="bi bi-check-circle color-blue-dark font-14 me-3"></i>
                                </div>
                                <div class="align-self-center">
                                    <p class="mb-0 font-12">Refresh halaman untuk mendapatkan sesi baru</p>
                                </div>
                            </div>
                        </div>
                    @break

                    @default
                        <div class="list-group-item">
                            <div class="d-flex">
                                <div class="align-self-center">
                                    <i class="bi bi-check-circle color-blue-dark font-14 me-3"></i>
                                </div>
                                <div class="align-self-center">
                                    <p class="mb-0 font-12">Coba refresh halaman atau tunggu beberapa saat</p>
                                </div>
                            </div>
                        </div>
                @endswitch

                <div class="list-group-item">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-check-circle color-blue-dark font-14 me-3"></i>
                        </div>
                        <div class="align-self-center">
                            <p class="mb-0 font-12">Hubungi administrator jika masalah berlanjut</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
