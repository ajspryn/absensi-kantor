@extends('layouts.admin')

@section('title', 'Pengaturan Aplikasi - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Pengaturan Aplikasi',
        'backUrl' => route('dashboard'),
    ])
@endsection

@section('content')
    @include('admin.partials.alerts')

    @include('admin.partials.section-header', [
        'title' => 'Pengaturan Aplikasi',
        'subtitle' => 'Konfigurasi sistem dan preferensi aplikasi',
        'icon' => 'bi bi-gear-fill',
    ])

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf

        <!-- General Settings -->
        <div class="card card-style shadow-m">
            <div class="content">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 40px; height: 40px;">
                            <i class="bi bi-building color-white font-16"></i>
                        </div>
                        <div>
                            <h4 class="font-700 mb-0 color-dark-dark">Pengaturan Umum</h4>
                            <p class="mb-0 font-12 opacity-70">Informasi dasar aplikasi dan perusahaan</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm bg-gray-dark rounded-s" onclick="resetGroup('general')" style="min-height: 35px;">
                        <i class="bi bi-arrow-clockwise color-white font-12"></i>
                    </button>
                </div>

                @if (isset($settingsGroups['general']))
                    @foreach ($settingsGroups['general'] as $setting)
                        <div class="form-custom form-label mb-4">
                            @if ($setting->type === 'boolean')
                                <div class="form-check form-check-custom">
                                    <input class="form-check-input" type="checkbox" name="settings[{{ $setting->key }}]" id="{{ $setting->key }}" value="1" {{ $setting->getValue() ? 'checked' : '' }}>
                                    <label class="form-check-label font-12" for="{{ $setting->key }}">
                                        <strong>{{ $setting->label }}</strong>
                                        @if ($setting->description)
                                            <span class="d-block font-11 color-theme mt-1">{{ $setting->description }}</span>
                                        @endif
                                    </label>
                                </div>
                            @elseif ($setting->type === 'file')
                                <div class="mb-2">
                                    <label for="{{ $setting->key }}" class="color-theme font-12 font-600 mb-1 d-block">{{ $setting->label }}</label>
                                    @if ($setting->description)
                                        <p class="font-11 opacity-70 mb-2">{{ $setting->description }}</p>
                                    @endif
                                </div>

                                @if ($setting->value && file_exists(public_path('storage/' . $setting->value)))
                                    <div class="bg-gray-light rounded-s p-3 mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="align-self-center me-3">
                                                @if (str_contains($setting->key, 'logo'))
                                                    <img src="{{ asset('storage/' . $setting->value) }}" alt="Current Logo" class="rounded-s" style="max-width: 80px; max-height: 50px;">
                                                @else
                                                    <img src="{{ asset('storage/' . $setting->value) }}" alt="Current Favicon" class="rounded-s" style="max-width: 32px; max-height: 32px;">
                                                @endif
                                            </div>
                                            <div class="align-self-center flex-grow-1">
                                                <p class="mb-0 font-12 font-600">File saat ini</p>
                                                <p class="mb-0 font-10 opacity-70">{{ basename($setting->value) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <input type="file" class="form-control rounded-s border-2" id="{{ $setting->key }}" name="files[{{ $setting->key }}]" accept="{{ str_contains($setting->key, 'favicon') ? '.ico,.png' : '.jpg,.jpeg,.png' }}" style="min-height: 45px;" />
                            @else
                                <div class="mb-2">
                                    <label for="{{ $setting->key }}" class="color-theme font-12 font-600 mb-1 d-block">{{ $setting->label }}</label>
                                    @if ($setting->description)
                                        <p class="font-11 opacity-70 mb-2">{{ $setting->description }}</p>
                                    @endif
                                </div>
                                <input type="{{ $setting->type === 'number' ? 'number' : 'text' }}" class="form-control rounded-s border-2" id="{{ $setting->key }}" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" placeholder="Masukkan {{ strtolower($setting->label) }}" style="min-height: 45px;" />
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Attendance Settings -->
        <!-- Pengaturan Absensi dipindahkan ke halaman Jadwal Kerja -->

        <!-- Notification Settings -->
        <div class="card card-style">
            <div class="content">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-purple-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-bell color-white font-16"></i>
                        </div>
                        <div>
                            <h5 class="font-700 mb-0 font-15">Pengaturan Notifikasi</h5>
                            <p class="mb-0 font-11 opacity-70">Konfigurasi email dan notifikasi sistem</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm bg-gray-dark rounded-s" onclick="resetGroup('notifications')" style="min-height: 35px;">
                        <i class="bi bi-arrow-clockwise color-white font-12"></i>
                    </button>
                </div>

                @if (isset($settingsGroups['notifications']))
                    @foreach ($settingsGroups['notifications'] as $setting)
                        <div class="form-custom form-label mb-4">
                            @if ($setting->type === 'boolean')
                                <div class="form-check form-check-custom">
                                    <input class="form-check-input" type="checkbox" name="settings[{{ $setting->key }}]" id="{{ $setting->key }}" value="1" {{ $setting->getValue() ? 'checked' : '' }}>
                                    <label class="form-check-label font-12" for="{{ $setting->key }}">
                                        <strong>{{ $setting->label }}</strong>
                                        @if ($setting->description)
                                            <span class="d-block font-11 color-theme mt-1">{{ $setting->description }}</span>
                                        @endif
                                    </label>
                                </div>
                            @else
                                <div class="mb-2">
                                    <label for="{{ $setting->key }}" class="color-theme font-12 font-600 mb-1 d-block">{{ $setting->label }}</label>
                                    @if ($setting->description)
                                        <p class="font-11 opacity-70 mb-2">{{ $setting->description }}</p>
                                    @endif
                                </div>
                                <input type="{{ $setting->type === 'number' ? 'number' : ($setting->key === 'admin_email' ? 'email' : 'text') }}" class="form-control rounded-s border-2" id="{{ $setting->key }}" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" placeholder="{{ $setting->key === 'admin_email' ? 'admin@perusahaan.com' : 'Masukkan ' . strtolower($setting->label) }}" style="min-height: 45px;" />
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Security Settings -->
        <div class="card card-style">
            <div class="content">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-red-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-shield-check color-white font-16"></i>
                        </div>
                        <div>
                            <h5 class="font-700 mb-0 font-15">Pengaturan Keamanan</h5>
                            <p class="mb-0 font-11 opacity-70">Konfigurasi password dan session aplikasi</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm bg-gray-dark rounded-s" onclick="resetGroup('security')" style="min-height: 35px;">
                        <i class="bi bi-arrow-clockwise color-white font-12"></i>
                    </button>
                </div>

                @if (isset($settingsGroups['security']))
                    @foreach ($settingsGroups['security'] as $setting)
                        <div class="form-custom form-label mb-4">
                            @if ($setting->type === 'boolean')
                                <div class="form-check form-check-custom">
                                    <input class="form-check-input" type="checkbox" name="settings[{{ $setting->key }}]" id="{{ $setting->key }}" value="1" {{ $setting->getValue() ? 'checked' : '' }}>
                                    <label class="form-check-label font-12" for="{{ $setting->key }}">
                                        <strong>{{ $setting->label }}</strong>
                                        @if ($setting->description)
                                            <span class="d-block font-11 color-theme mt-1">{{ $setting->description }}</span>
                                        @endif
                                    </label>
                                </div>
                            @else
                                <div class="mb-2">
                                    <label for="{{ $setting->key }}" class="color-theme font-12 font-600 mb-1 d-block">{{ $setting->label }}</label>
                                    @if ($setting->description)
                                        <p class="font-11 opacity-70 mb-2">{{ $setting->description }}</p>
                                    @endif
                                </div>
                                <input type="number" class="form-control rounded-s border-2" id="{{ $setting->key }}" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" placeholder="{{ $setting->key === 'password_min_length' ? 'Minimal 6 karakter' : 'Dalam menit (contoh: 120)' }}" min="1" style="min-height: 45px;" />
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Save Button -->
        <div class="card card-style">
            <div class="content">
                <button type="submit" class="btn btn-full btn-l bg-green-dark text-uppercase font-600 rounded-s shadow-bg shadow-bg-s" style="min-height: 50px;">
                    <i class="bi bi-check-circle pe-2"></i>Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        function resetGroup(group) {
            if (confirm('Apakah Anda yakin ingin mereset pengaturan ' + group + ' ke default?')) {
                // Create a form to submit reset request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.settings.reset') }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const groupInput = document.createElement('input');
                groupInput.type = 'hidden';
                groupInput.name = 'group';
                groupInput.value = group;

                form.appendChild(csrfToken);
                form.appendChild(groupInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endpush
