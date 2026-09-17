@extends('layouts.admin')

@section('title', 'Akses Aplikasi SSO - Admin')

@section('header')
    @include('admin.header', ['title' => 'Akses Aplikasi SSO', 'backUrl' => route('admin.employees.index')])
@endsection

@section('content')
    @include('admin.partials.alerts')

    <div class="card card-style">
        <div class="content">
            <h3 class="font-700 mb-1">Pengaturan akses SSO</h3>
            <p class="font-12 opacity-70 mb-1">User: {{ $user->name }}</p>
            <p class="font-11 opacity-70">Pilih role aplikasi untuk user ini. Jika tidak diberi akses, user tidak bisa login ke aplikasi client tersebut.</p>

            <form method="POST" action="{{ route('admin.sso-applications.users.access.update', $user) }}">
                @csrf
                @method('PUT')
                @forelse ($applications as $application)
                    @php($access = $currentAccess->get($application->id))
                    <div class="border rounded-s p-3 mb-3">
                        <label for="application-{{ $application->id }}" class="font-600 font-13 mb-2 d-block">{{ $application->name }}</label>
                        <select id="application-{{ $application->id }}" name="access[{{ $application->id }}]" class="form-control rounded-s">
                            <option value="">Tidak ada akses</option>
                            @foreach ($application->applicationRoles as $role)
                                <option value="{{ $role->id }}" {{ $access && $access->sso_application_role_id === $role->id ? 'selected' : '' }}>
                                    {{ $role->name }} ({{ $role->code }})
                                </option>
                            @endforeach
                        </select>
                        @if ($application->applicationRoles->isEmpty())
                            <small class="font-11 opacity-70">Role belum didaftarkan. Tambahkan role pada <a href="{{ route('admin.sso-applications.index') }}">menu Aplikasi SSO</a>, lalu buka ulang halaman ini.</small>
                        @endif
                    </div>
                @empty
                    <p class="font-12 opacity-70">Belum ada aplikasi SSO aktif.</p>
                @endforelse
                <button type="submit" class="btn bg-highlight rounded-s"><i class="bi bi-check-circle me-2"></i>Simpan akses</button>
            </form>
        </div>
    </div>
@endsection
