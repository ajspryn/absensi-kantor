@extends('layouts.app')

@section('title', 'Login - Aplikasi Absensi')

@section('page-class', 'mb-0 pb-0')

@section('content')
    <div class="card card-style mb-0 bg-transparent shadow-0 bg-3 mx-0 rounded-0" data-card-height="cover">
        <div class="card-center">
            <div class="card card-style mx-3">
                <div class="content">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <img src="{{ App\Models\AppSetting::getLogoUrl() }}" alt="Logo Aplikasi" class="rounded-s" style="max-width: 120px; max-height: 80px;">
                        </div>
                        <h1 class="font-800 font-24 mb-2 color-theme">Selamat Datang</h1>
                        <p class="font-12 opacity-70 mb-0">Masukkan kredensial Anda untuk melanjutkan</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert bg-red-dark alert-dismissible text-white rounded-s fade show pe-2 mb-3" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle pe-2"></i>
                                <strong>{{ $errors->first() }}</strong>
                            </div>
                            <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert bg-success alert-dismissible text-white rounded-s fade show pe-2 mb-3" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle pe-2"></i>
                                <strong>{{ session('success') }}</strong>
                            </div>
                            <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-envelope-fill font-16 color-blue-dark"></i>
                            <input type="email" class="form-control rounded-s" id="email" name="email" placeholder="Masukkan email Anda" value="{{ old('email') }}" required style="min-height: 50px; font-size: 16px;" />
                            <label for="email" class="color-theme font-12">Alamat Email</label>
                            <span class="font-10 color-green-dark">(wajib diisi)</span>
                        </div>

                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-lock-fill font-16 color-blue-dark"></i>
                            <input type="password" class="form-control rounded-s" id="password" name="password" placeholder="Masukkan password Anda" required style="min-height: 50px; font-size: 16px;" />
                            <label for="password" class="color-theme font-12">Password</label>
                            <span class="font-10 color-green-dark">(wajib diisi)</span>
                        </div>

                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class='btn rounded-s btn-l gradient-green text-uppercase font-600 mb-3 shadow-bg shadow-bg-s' style="min-height: 50px; font-size: 14px; width: 280px;">
                                <i class="bi bi-box-arrow-in-right pe-2 font-16"></i>Masuk ke Aplikasi
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('password.request.form') }}" class="color-highlight font-12 text-decoration-none">
                            <i class="bi bi-key pe-1 font-11"></i>Lupa Password?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
