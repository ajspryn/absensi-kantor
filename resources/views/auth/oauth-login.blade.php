@extends('layouts.app')

@section('title', 'Login SSO - Absensi')
@section('page-class', 'mb-0 pb-0')

@section('content')
    <div class="card card-style mb-0 bg-transparent shadow-0 bg-3 mx-0 rounded-0" data-card-height="cover">
        <div class="card-center">
            <div class="card card-style mx-3">
                <div class="content">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <img src="{{ App\Models\AppSetting::getLogoUrl() }}" alt="Logo Absensi" class="rounded-s" style="max-width: 120px; max-height: 80px;">
                        </div>
                        <h1 class="font-800 font-24 mb-2 color-theme">Masuk dengan SSO</h1>
                        <p class="font-12 opacity-70 mb-0">Gunakan akun Absensi Anda. Setelah berhasil, Anda akan kembali ke aplikasi tujuan.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert bg-red-dark text-white rounded-s mb-3" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert bg-blue-dark text-white rounded-s mb-3" role="alert">
                            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-envelope-fill font-16 color-blue-dark"></i>
                            <input type="email" class="form-control rounded-s" id="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                            <label for="email" class="color-theme font-12">Email</label>
                        </div>
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-lock-fill font-16 color-blue-dark"></i>
                            <input type="password" class="form-control rounded-s" id="password" name="password" placeholder="Password" required>
                            <label for="password" class="color-theme font-12">Password</label>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn rounded-s btn-l gradient-green text-uppercase font-600 shadow-bg shadow-bg-s" style="min-height: 50px; font-size: 14px; width: 280px;">
                                <i class="bi bi-box-arrow-in-right pe-2 font-16"></i>Lanjutkan ke aplikasi
                            </button>
                        </div>
                    </form>

                    <p class="text-center font-11 opacity-70 mt-3 mb-0">Jika login gagal, Anda akan dikembalikan ke aplikasi tujuan dengan informasi kegagalan.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
