@extends('layouts.admin')

@section('title', 'Tambah Lokasi Kantor - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Tambah Lokasi',
        'backUrl' => route('admin.office-locations.index'),
    ])
@endsection

@section('content')
    @include('admin.partials.alerts')

    <div class="card card-style shadow-m mb-4">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3 shadow-s" style="width: 45px; height: 45px;">
                    <i class="bi bi-geo-alt-fill color-white font-18"></i>
                </div>
                <div>
                    <h3 class="font-700 mb-0 color-dark-dark">Tambah Lokasi Kantor</h3>
                    <p class="mb-0 font-12 opacity-70">Lengkapi informasi lokasi untuk absensi</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.office-locations.store') }}">
                @csrf

                <!-- Location Name -->
                <div class="form-custom form-label mb-3">
                    <div class="mb-2">
                        <label for="name" class="color-theme font-12 font-600 mb-1 d-block">Nama Lokasi</label>
                        <p class="font-11 opacity-70 mb-2">Nama atau identitas lokasi kantor</p>
                    </div>
                    <input type="text" class="form-control rounded-s border-2" id="name" name="name" value="{{ old('name') }}" placeholder="Kantor Pusat, Cabang Jakarta, dll" required style="min-height: 45px;" />
                </div>

                <!-- Address -->
                <div class="form-custom form-label mb-3">
                    <div class="mb-2">
                        <label for="address" class="color-theme font-12 font-600 mb-1 d-block">Alamat Lengkap</label>
                        <p class="font-11 opacity-70 mb-2">Alamat detail lokasi kantor</p>
                    </div>
                    <textarea class="form-control rounded-s border-2" id="address" name="address" rows="3" placeholder="Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta" required style="min-height: 80px;">{{ old('address') }}</textarea>
                </div>

                <!-- Coordinates -->
                <div class="row g-2">
                    <div class="col-6">
                        <div class="form-custom form-label mb-3">
                            <div class="mb-2">
                                <label for="latitude" class="color-theme font-12 font-600 mb-1 d-block">Latitude</label>
                                <p class="font-11 opacity-70 mb-2">Koordinat lintang</p>
                            </div>
                            <input type="number" class="form-control rounded-s border-2" id="latitude" name="latitude" value="{{ old('latitude') }}" step="any" placeholder="-6.200000" required style="min-height: 45px;" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-custom form-label mb-3">
                            <div class="mb-2">
                                <label for="longitude" class="color-theme font-12 font-600 mb-1 d-block">Longitude</label>
                                <p class="font-11 opacity-70 mb-2">Koordinat bujur</p>
                            </div>
                            <input type="number" class="form-control rounded-s border-2" id="longitude" name="longitude" value="{{ old('longitude') }}" step="any" placeholder="106.816666" required style="min-height: 45px;" />
                        </div>
                    </div>
                </div>

                <!-- Get Current Location Button -->
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-sm bg-blue-dark color-white rounded-s" onclick="getCurrentLocation()" style="min-height: 40px;">
                        <i class="bi bi-crosshair pe-2"></i>Gunakan Lokasi Saat Ini
                    </button>
                </div>

                <!-- Radius -->
                <div class="form-custom form-label mb-3">
                    <div class="mb-2">
                        <label for="radius" class="color-theme font-12 font-600 mb-1 d-block">Radius Absensi (Meter)</label>
                        <p class="font-11 opacity-70 mb-2">Jarak maksimal untuk dapat absen dari lokasi ini</p>
                    </div>
                    <input type="number" class="form-control rounded-s border-2" id="radius" name="radius" value="{{ old('radius', 100) }}" min="10" max="1000" placeholder="100 meter" required style="min-height: 45px;" />
                </div>

                <!-- Description -->
                <div class="form-custom form-label mb-3">
                    <div class="mb-2">
                        <label for="description" class="color-theme font-12 font-600 mb-1 d-block">Deskripsi (Opsional)</label>
                        <p class="font-11 opacity-70 mb-2">Keterangan tambahan tentang lokasi</p>
                    </div>
                    <textarea class="form-control rounded-s border-2" id="description" name="description" rows="2" placeholder="Gedung bertingkat 10, lantai 5, dll" style="min-height: 60px;">{{ old('description') }}</textarea>
                </div>

                <!-- Status -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                    <label class="form-check-label font-12" for="is_active">
                        <strong>Lokasi Aktif</strong>
                        <span class="d-block font-11 color-theme mt-1">Centang jika lokasi dapat digunakan untuk absensi</span>
                    </label>
                </div>

                <div class="alert bg-info-dark rounded-s mb-3" role="alert">
                    <div class="d-flex">
                        <div class="align-self-center">
                            <i class="bi bi-info-circle pe-2 font-14"></i>
                        </div>
                        <div class="align-self-center">
                            <strong class="font-12">Tips:</strong><br>
                            <small class="font-10">Gunakan Google Maps untuk mendapatkan koordinat yang akurat. Klik kanan pada lokasi di Google Maps dan salin koordinat yang muncul.</small>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('admin.office-locations.index') }}" class="btn btn-full rounded-s btn-danger font-600 text-uppercase w-100" style="min-height: 45px;">
                            <i class="bi bi-x-circle pe-2"></i>Batal
                        </a>
                    </div>
                    <div class="col-6">
                        <button type="submit" class='btn btn-full rounded-s bg-highlight shadow-bg shadow-bg-s font-600 text-uppercase w-100' style="min-height: 45px;">
                            <i class="bi bi-check-circle pe-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function getCurrentLocation() {
            if (navigator.geolocation) {
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="bi bi-hourglass pe-2"></i>Mengambil lokasi...';
                button.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('latitude').value = position.coords.latitude.toFixed(8);
                        document.getElementById('longitude').value = position.coords.longitude.toFixed(8);

                        button.innerHTML = '<i class="bi bi-check-circle pe-2"></i>Lokasi berhasil didapat!';
                        setTimeout(() => {
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }, 2000);
                    },
                    function(error) {
                        alert('Error: ' + error.message);
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                alert('Geolocation tidak didukung oleh browser ini.');
            }
        }
    </script>
@endpush
