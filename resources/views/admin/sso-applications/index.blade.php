@extends('layouts.admin')

@section('title', 'Aplikasi SSO - Admin')

@section('header')
    @include('admin.header', ['title' => 'Aplikasi SSO', 'backUrl' => route('dashboard')])
@endsection

@section('content')
    @include('admin.partials.alerts')

    @if (session('sso_credentials'))
        <div class="card card-style border border-warning">
            <div class="content">
                <h4 class="font-700 color-orange-dark"><i class="bi bi-key me-2"></i>Simpan kredensial aplikasi</h4>
                <p class="font-12">Secret hanya ditampilkan sekarang. Simpan di secret manager aplikasi client dan jangan masukkan ke source control.</p>
                <div class="bg-gray-light rounded-s p-3 font-monospace font-12">
                    <div><strong>client_id:</strong> {{ session('sso_credentials.client_id') }}</div>
                    <div><strong>client_secret:</strong> {{ session('sso_credentials.client_secret') }}</div>
                </div>
            </div>
        </div>
    @endif

    <div class="card card-style">
        <div class="content">
            <h3 class="font-700 mb-1">Aplikasi yang terhubung</h3>
            <p class="font-12 opacity-70">Daftar aplikasi client yang dapat login menggunakan Absensi sebagai Identity Provider.</p>

            @forelse ($applications as $application)
                <div class="border rounded-s p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <h5 class="font-700 mb-1">{{ $application->name }}</h5>
                            <p class="font-11 opacity-70 mb-2">Client ID: {{ $application->client_id }}</p>
                        </div>
                        <span class="badge {{ $application->is_active ? 'bg-green-dark' : 'bg-gray-dark' }}">
                            {{ $application->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="font-11 mb-2"><i class="bi bi-link-45deg me-1"></i>{{ implode(', ', $application->redirect_uris ?? []) }}</p>
                    <div class="d-flex flex-wrap gap-1">
                        @forelse ($application->applicationRoles as $role)
                            <span class="badge bg-blue-dark">{{ $role->name }} <small>({{ $role->code }})</small></span>
                        @empty
                            <span class="font-11 opacity-70">Belum ada role. Aplikasi client dapat mendaftarkan role melalui API.</span>
                        @endforelse
                    </div>
                    <p class="font-11 opacity-70 mt-2 mb-0">{{ $application->user_access_count }} user memiliki akses</p>
                </div>
            @empty
                <p class="font-12 opacity-70">Belum ada aplikasi terhubung.</p>
            @endforelse
        </div>
    </div>

    <div class="card card-style">
        <div class="content">
            <h4 class="font-700 mb-1">Daftarkan aplikasi client</h4>
            <p class="font-12 opacity-70">Masukkan URL callback aplikasi client. Satu baris untuk satu redirect URI. Nilai ini harus sama persis dengan callback yang dipasang di aplikasi lain.</p>
            <form method="POST" action="{{ route('admin.sso-applications.store') }}">
                @csrf
                <div class="form-custom form-label mb-3">
                    <label for="name">Nama aplikasi</label>
                    <input id="name" name="name" type="text" class="form-control rounded-s" value="{{ old('name') }}" required>
                </div>
                <div class="form-custom form-label mb-3">
                    <label for="redirect_uris">Redirect URI</label>
                    <textarea id="redirect_uris" name="redirect_uris" class="form-control rounded-s" rows="3" required>{{ old('redirect_uris') }}</textarea>
                </div>
                <button type="submit" class="btn bg-highlight rounded-s"><i class="bi bi-plus-circle me-2"></i>Daftarkan aplikasi</button>
            </form>
        </div>
    </div>
@endsection
