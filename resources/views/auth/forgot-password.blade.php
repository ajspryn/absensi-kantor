@extends('layouts.app')

@section('title', 'Lupa Password - Aplikasi Absensi')

@section('page-class', 'mb-0 pb-0')

@section('content')
    <div class="card card-style mb-0 bg-transparent shadow-0 bg-3 mx-0 rounded-0" data-card-height="cover">
        <div class="card-center">
            <div class="card card-style mx-3">
                <div class="content">
                    <div class="text-center mb-4">
                        <div class="bg-orange-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 75px; height: 75px;">
                            <i class="bi bi-key color-white font-32"></i>
                        </div>
                        <h1 class="font-800 font-24 mb-2 color-theme">Lupa Password</h1>
                        <p class="font-12 opacity-70 mb-0">Ajukan permintaan reset password ke admin</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert bg-danger-dark alert-dismissible text-white rounded-s fade show pe-2 mb-3" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle pe-2"></i>
                                <strong>{{ $errors->first() }}</strong>
                            </div>
                            <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.request') }}">
                        @csrf
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-envelope-fill font-16 color-blue-dark"></i>
                            <input type="email" class="form-control rounded-s" id="email" name="email" placeholder="Masukkan email Anda" value="{{ old('email') }}" required style="min-height: 50px; font-size: 16px;" />
                            <label for="email" class="color-theme font-12">Alamat Email</label>
                            <span class="font-10 color-green-dark">(email yang terdaftar)</span>
                        </div>

                        <div class="form-custom form-label mb-3">
                            <textarea class="form-control rounded-s" id="reason" name="reason" placeholder="Jelaskan alasan Anda lupa password" required style="min-height: 80px; font-size: 16px;">{{ old('reason') }}</textarea>
                            <label for="reason" class="color-theme font-12">Alasan Lupa Password</label>
                            <span class="font-10 color-green-dark">(wajib diisi)</span>
                        </div>

                        <div class="text-center">
                            <button type="submit" class='btn rounded-s btn-l gradient-orange text-uppercase font-600 mb-3 shadow-bg shadow-bg-s' style="min-height: 50px; font-size: 14px; width: 280px;">
                                <i class="bi bi-send pe-2 font-16"></i>Kirim Permintaan
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="color-highlight font-14 text-decoration-none">
                            <i class="bi bi-arrow-left pe-1"></i>Kembali ke Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
