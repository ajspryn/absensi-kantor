@extends('layouts.app')

@section('title', 'Offline - Aplikasi Absensi')

@section('content')
    <div class="card card-style shadow-m">
        <div class="content text-center">
            <div class="mb-4">
                <i class="bi bi-wifi-off font-50 color-red-dark"></i>
            </div>
            <h3 class="font-700 color-dark-dark mb-3">Tidak Ada Koneksi</h3>
            <p class="color-dark-light mb-4">
                Anda sedang offline. Beberapa fitur mungkin tidak tersedia.
            </p>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="bg-light-dark rounded-m p-3">
                        <h5 class="font-600 mb-2">📱 Fitur Offline:</h5>
                        <ul class="list-unstyled text-start">
                            <li class="mb-2">✅ Lihat data yang sudah di-cache</li>
                            <li class="mb-2">✅ Akses halaman utama</li>
                            <li class="mb-2">✅ Lihat profil karyawan</li>
                            <li class="mb-2">❌ Input absensi (perlu koneksi)</li>
                            <li class="mb-2">❌ Import data (perlu koneksi)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <button onclick="checkConnection()" class="btn btn-l btn-primary rounded-s font-700 mb-3">
                <i class="bi bi-arrow-clockwise me-2"></i>Coba Lagi
            </button>

            <div class="mb-3">
                <small class="color-dark-light">
                    Status: <span id="connection-status" class="color-red-dark font-600">Offline</span>
                </small>
            </div>
        </div>
    </div>

    <script>
        function checkConnection() {
            if (navigator.onLine) {
                window.location.reload();
            } else {
                alert('Masih tidak ada koneksi internet. Silakan coba lagi nanti.');
            }
        }

        // Monitor connection status
        function updateConnectionStatus() {
            const status = document.getElementById('connection-status');
            if (navigator.onLine) {
                status.textContent = 'Online';
                status.className = 'color-green-dark font-600';
                // Auto reload when connection is restored
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                status.textContent = 'Offline';
                status.className = 'color-red-dark font-600';
            }
        }

        window.addEventListener('online', updateConnectionStatus);
        window.addEventListener('offline', updateConnectionStatus);

        // Check initial status
        updateConnectionStatus();
    </script>
@endsection
