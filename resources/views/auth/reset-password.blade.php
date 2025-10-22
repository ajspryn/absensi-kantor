@extends('layouts.app')

@section('title', 'Reset Password - Aplikasi Absensi')

@section('page-class', 'mb-0 pb-0')

@section('content')
    <div class="card card-style mb-0 bg-transparent shadow-0 bg-3 mx-0 rounded-0" data-card-height="cover">
        <div class="card-center">
            <div class="card card-style mx-3">
                <div class="content">
                    <div class="text-center mb-4">
                        <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-shield-check color-white font-36"></i>
                        </div>
                        <h1 class="font-800 font-26 mb-2 color-theme">Reset Password</h1>
                        <p class="font-13 opacity-70 mb-0">Masukkan password baru untuk akun <strong>{{ $resetRequest->email }}</strong></p>
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

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $resetRequest->token }}">

                        <div class="form-custom form-label form-icon mb-4">
                            <i class="bi bi-lock-fill font-18 color-blue-dark"></i>
                            <input type="password" class="form-control rounded-s" id="password" name="password" placeholder="Masukkan password baru" required style="min-height: 55px; font-size: 16px;" />
                            <label for="password" class="color-theme font-13">Password Baru</label>
                            <span class="font-11 color-green-dark">(minimal 8 karakter)</span>
                        </div>

                        <div class="form-custom form-label form-icon mb-4">
                            <i class="bi bi-lock-fill font-18 color-blue-dark"></i>
                            <input type="password" class="form-control rounded-s" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password baru" required style="min-height: 55px; font-size: 16px;" />
                            <label for="password_confirmation" class="color-theme font-13">Konfirmasi Password</label>
                            <span class="font-11 color-green-dark">(ulangi password)</span>
                        </div>

                        <div class="text-center">
                            <button type="submit" class='btn rounded-s btn-l gradient-green text-uppercase font-700 mb-4 shadow-bg shadow-bg-s' style="min-height: 60px; font-size: 15px; width: 280px;">
                                <i class="bi bi-check-circle pe-2 font-18"></i>Update Password
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
