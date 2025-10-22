@extends('layouts.app')

@section('title', 'Absensi - Aplikasi Absensi')

@section('header')
    <!-- Header -->
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-15">Ambil Absensi</a>
    </div>
@endsection

@section('footer')
    @include('employee.footer')
@endsection

@section('sidebar')
    @include('employee.sidebar')
@endsection

@section('content')
    <!-- Employee Info Card -->
    <div class="card card-style">
        <div class="content">
            <div class="d-flex">
                <div class="align-self-center">
                    <img src="{{ $employee->photo ? asset('storage/' . $employee->photo) : asset('template/images/avatars/5s.png') }}" width="55" height="55" class="rounded-circle me-3">
                </div>
                <div class="align-self-center flex-grow-1">
                    <h5 class="mb-1 font-16">{{ $employee->full_name }}</h5>
                    <p class="mb-0 font-11 opacity-70">{{ $employee->employee_id }} • {{ $employee->position_name ?? '-' }}</p>
                    <p class="mb-0 font-11 opacity-70">{{ optional($employee->department)->name ?? '-' }}</p>
                </div>
                <div class="align-self-center">
                    <div class="text-center">
                        <i class="bi bi-calendar-date color-theme font-18 d-block"></i>
                        <p class="mb-0 font-10 opacity-70">{{ now()->format('d M') }}</p>
                    </div>
                </div>
            </div>

            <div class="divider my-1"></div>

            <!-- Current Time Section -->
            <div class="text-center">
                <div class="bg-theme rounded-circle d-inline-flex align-items-center justify-content-center mb-1" style="width: 40px; height: 40px;">
                    <i class="bi bi-clock color-white font-12"></i>
                </div>
                <h1 id="current-time" class="font-700 font-22 mb-0 color-theme">--:--:--</h1>
                @if ($todayAttendance && $todayAttendance->location_name)
                    <p class="mb-0 font-9 opacity-70">
                        <i class="bi bi-geo-alt pe-1"></i>{{ $todayAttendance->location_name }} <span id="timezone-info" class="font-8"></span>
                    </p>
                @else
                    <p class="mb-0 font-9 opacity-70">
                        <i class="bi bi-geo-alt pe-1"></i>Kantor Pusat - Jakarta <span id="timezone-info" class="font-8"></span>
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Attendance Status & Interactive Cards -->
    <div class="card card-style">
        <div class="content">
            @php
                $activeSchedule = $employee->workSchedule;
            @endphp
            @if (!$activeSchedule || !$activeSchedule->is_active)
                <div class="alert bg-red-dark color-white rounded-s mb-2 border-start border-2 border-red-dark py-2" role="alert">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-exclamation-triangle color-red-dark pe-2 font-14"></i>
                        <div>
                            <strong class="font-12">Anda belum memiliki jadwal kerja aktif. Silakan hubungi admin untuk penugasan jadwal.</strong>
                        </div>
                    </div>
                </div>
            @else
                <div class="row g-2 mb-1">
                    @if (!$todayAttendance || !$todayAttendance->check_in)
                        <!-- Check In Card (Clickable) -->
                        <div class="col-6">
                            <div class="bg-green-dark rounded-s py-2 px-2 text-center border border-green-dark clickable-card" onclick="startCheckIn()" style="cursor: pointer; transition: all 0.3s ease; position: relative;">
                                <div style="position: absolute; top: 5px; right: 8px;">
                                    <i class="bi bi-cursor font-8 text-white-50"></i>
                                </div>
                                <i class="bi bi-box-arrow-in-right text-white font-14 d-block mb-1"></i>
                                <small class="d-block font-9 text-white-50 mb-1">Masuk</small>
                                <strong class="font-11 text-white d-block mb-1">--:--</strong>
                                <small class="font-8 text-white d-block">Klik untuk Absen Masuk</small>
                            </div>
                        </div>
                        <!-- Placeholder for Check Out -->
                        <div class="col-6">
                            <div class="bg-gray-dark rounded-s py-2 px-2 text-center border border-gray-dark">
                                <i class="bi bi-box-arrow-right text-white font-14 d-block mb-1"></i>
                                <small class="d-block font-9 text-white-50 mb-1">Keluar</small>
                                <strong class="font-11 text-white">--:--</strong>
                            </div>
                        </div>
                    @elseif(!$todayAttendance->check_out)
                        <!-- Completed Check In -->
                        <div class="col-6">
                            <div class="bg-green-dark rounded-s py-2 px-2 text-center border border-green-dark">
                                <i class="bi bi-box-arrow-in-right text-white font-14 d-block mb-1"></i>
                                <small class="d-block font-9 text-white-50 mb-1">Masuk</small>
                                <strong class="font-11 text-white">{{ $todayAttendance->check_in->setTimezone('Asia/Jakarta')->format('H:i') }}</strong>
                            </div>
                        </div>
                        <!-- Check Out Card (Clickable) -->
                        <div class="col-6">
                            <div class="bg-blue-dark rounded-s py-2 px-2 text-center border border-blue-dark clickable-card" onclick="startCheckOut()" style="cursor: pointer; transition: all 0.3s ease; position: relative;">
                                <div style="position: absolute; top: 5px; right: 8px;">
                                    <i class="bi bi-cursor font-8 text-white-50"></i>
                                </div>
                                <i class="bi bi-box-arrow-right text-white font-14 d-block mb-1"></i>
                                <small class="d-block font-9 text-white-50 mb-1">Keluar</small>
                                <strong class="font-11 text-white d-block mb-1">--:--</strong>
                                <small class="font-8 text-white d-block">Klik untuk Absen Keluar</small>
                            </div>
                        </div>
                    @else
                        <!-- Both completed -->
                        <div class="col-6">
                            <div class="bg-green-dark rounded-s py-2 px-2 text-center border border-green-dark">
                                <i class="bi bi-box-arrow-in-right text-white font-14 d-block mb-1"></i>
                                <small class="d-block font-9 text-white-50 mb-1">Masuk</small>
                                <strong class="font-11 text-white">{{ $todayAttendance->check_in->setTimezone('Asia/Jakarta')->format('H:i') }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-blue-dark rounded-s py-2 px-2 text-center border border-blue-dark">
                                <i class="bi bi-box-arrow-right text-white font-14 d-block mb-1"></i>
                                <small class="d-block font-9 text-white-50 mb-1">Keluar</small>
                                <strong class="font-11 text-white">{{ $todayAttendance->check_out->setTimezone('Asia/Jakarta')->format('H:i') }}</strong>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @if ($todayAttendance && $todayAttendance->check_in && $todayAttendance->check_out)
                <div class="alert bg-green-dark color-white rounded-s mb-0 border-start border-2 border-green-dark py-1" role="alert">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-hourglass-split color-green-dark pe-2 font-12"></i>
                        <div>
                            <strong class="font-11">Total: {{ $todayAttendance->getWorkingHoursFormatted() }}</strong>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Schedule Status Alert -->
            @if ($todayAttendance && ($todayAttendance->check_in || $todayAttendance->check_out))
                @php
                    $statusClass = match ($todayAttendance->schedule_status ?? 'on_time') {
                        'late' => 'bg-orange-dark color-white border-orange-dark',
                        'early_leave' => 'bg-blue-dark color-white border-blue-dark',
                        'late_early_leave' => 'bg-red-dark color-white border-red-dark',
                        default => 'bg-green-dark color-white border-green-dark',
                    };

                    $statusIcon = match ($todayAttendance->schedule_status ?? 'on_time') {
                        'late' => 'bi-clock',
                        'early_leave' => 'bi-speedometer2',
                        'late_early_leave' => 'bi-exclamation-triangle',
                        default => 'bi-check-circle',
                    };
                @endphp

                <div class="alert {{ $statusClass }} rounded-s mt-2 mb-0 border-start border-2 py-1" role="alert">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="{{ $statusIcon }} pe-2 font-12"></i>
                        <div>
                            <strong class="font-11">{{ $todayAttendance->getScheduleStatusText() }}</strong>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons (Alternative option) -->
    @if (!$todayAttendance || ($todayAttendance && !$todayAttendance->check_out))
        <div class="card card-style">
            <div class="content">
                @if (!$todayAttendance || !$todayAttendance->check_in)
                    <!-- Check In Button -->
                    <div class="text-center">
                        <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-camera-fill text-white font-20"></i>
                        </div>
                        <h5 class="font-700 mb-2 font-15">Mulai Absensi Masuk</h5>
                        <p class="mb-3 opacity-70 font-12">Pastikan Anda berada di lokasi kantor dan siap mengambil foto selfie</p>

                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-m bg-green-dark text-white text-uppercase font-600 rounded-s shadow-bg shadow-bg-s mb-2" onclick="startCheckIn()" style="min-height: 50px; min-width: 200px;">
                                <i class="bi bi-camera-fill pe-2 font-16"></i>Check In Sekarang
                            </button>
                        </div>

                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-info-circle pe-2 color-green-dark font-12"></i>
                            <p class="font-11 opacity-70 mb-0">Atau klik langsung pada card jam di atas</p>
                        </div>
                    </div>
                @elseif($todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out)
                    <!-- Check Out Button -->
                    <div class="text-center">
                        <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-camera text-white font-20"></i>
                        </div>
                        <h5 class="font-700 mb-2 font-15">Waktunya Absen Keluar</h5>
                        <p class="mb-3 opacity-70 font-12">Selesaikan absensi Anda hari ini dengan foto selfie</p>

                        <div class="alert bg-green-dark color-white rounded-s mb-3 py-2" role="alert">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock pe-2 font-12"></i>
                                <div>
                                    <small class="font-11">Check in: <strong>{{ $todayAttendance->check_in->setTimezone('Asia/Jakarta')->format('H:i:s') }}</strong></small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-m bg-blue-dark text-white text-uppercase font-600 rounded-s shadow-bg shadow-bg-s mb-2" onclick="startCheckOut()" style="min-height: 50px; min-width: 200px;">
                                <i class="bi bi-camera-fill pe-2 font-16"></i>Check Out Sekarang
                            </button>
                        </div>

                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-info-circle pe-2 color-blue-dark font-12"></i>
                            <p class="font-11 opacity-70 mb-0">Atau klik langsung pada card jam di atas</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Working Hours Summary (when both check-in and check-out are done) -->
    @if ($todayAttendance && $todayAttendance->check_in && $todayAttendance->check_out)
        <div class="card card-style">
            <div class="content text-center">
                <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 70px; height: 70px;">
                    <i class="bi bi-check-circle-fill text-white font-26"></i>
                </div>
                <h4 class="font-700 mb-2 font-16">Absensi Hari Ini Selesai</h4>
                <p class="mb-2 opacity-70 font-12">Terima kasih atas kerja keras Anda hari ini!</p>

                <div class="bg-green-dark rounded-s p-2 mb-3">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="d-block font-10 color-white opacity-80">Masuk</small>
                            <strong class="font-14 color-white">{{ $todayAttendance->check_in->setTimezone('Asia/Jakarta')->format('H:i') }}</strong>
                        </div>
                        <div class="col-4">
                            @if ($todayAttendance && $todayAttendance->location_name)
                                <p class="mb-0 font-9 opacity-70">
                                    <i class="bi bi-geo-alt pe-1"></i>{{ $todayAttendance->location_name }} <span id="timezone-info" class="font-8"></span>
                                </p>
                            @else
                                <p class="mb-0 font-9 opacity-70">
                                    <i class="bi bi-geo-alt pe-1"></i>Kantor Pusat - Jakarta <span id="timezone-info" class="font-8"></span>
                                </p>
                            @endif
                        </div>
                        <div class="col-4">
                            <small class="d-block font-10 opacity-70">Total</small>
                            <strong class="font-14 color-orange-dark">{{ $todayAttendance->getWorkingHoursFormatted() }}</strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-calendar-check pe-2 color-green-dark font-12"></i>
                    <p class="font-11 opacity-70 mb-0">Sampai jumpa besok!</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Info -->
    <div class="card card-style">
        <div class="content">
            <h5 class="font-700 mb-2">Informasi Penting</h5>
            <div class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-geo-alt-fill color-red-dark font-15 me-3"></i>
                </div>
                <div class="align-self-center">
                    <h6 class="mb-0 font-14">Lokasi akan terdeteksi otomatis</h6>
                    <p class="mb-0 font-10 opacity-70">Pastikan GPS Anda aktif</p>
                </div>
            </div>
            <div class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-camera-fill color-blue-dark font-15 me-3"></i>
                </div>
                <div class="align-self-center">
                    <h6 class="mb-0 font-14">Foto selfie diperlukan</h6>
                    <p class="mb-0 font-10 opacity-70">Untuk verifikasi kehadiran</p>
                </div>
            </div>
            <div class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-shield-check color-green-dark font-15 me-3"></i>
                </div>
                <div class="align-self-center">
                    <h6 class="mb-0 font-14">Data tersimpan aman</h6>
                    <p class="mb-0 font-10 opacity-70">Privasi Anda terjaga</p>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('modals')
    <div class="modal fade" id="attendanceModal" tabindex="1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-s">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-700" id="attendanceModalLabel">Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-3">
                    <div id="step1" class="attendance-step">
                        <div class="text-center mb-3">
                            <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-geo-alt text-white font-24"></i>
                            </div>
                            <h6 class="font-600 mb-2">Langkah 1: Deteksi Lokasi</h6>
                        </div>
                        <div class="text-center py-3">
                            <div id="location-status">
                                <p class="mb-3 font-13">Mendeteksi lokasi Anda...</p>
                                <div class="spinner-border color-highlight" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="step2" class="attendance-step d-none">
                        <div class="text-center mb-3">
                            <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-camera-fill text-white font-24"></i>
                            </div>
                            <h6 class="font-600 mb-2">Langkah 2: Ambil Foto Selfie</h6>
                        </div>

                        <!-- Camera Error Display -->
                        <div id="camera-error" class="d-none text-center py-4">
                            <i class="bi bi-camera-video-off color-red-dark font-40 d-block mb-3"></i>
                            <h6 id="error-message" class="color-red-dark"></h6>
                            <button type="button" class="btn btn-s bg-blue-dark text-white rounded-s mt-3" onclick="retryCameraAccess()" style="min-height: 45px;">
                                <i class="bi bi-arrow-clockwise pe-2 font-14"></i>Coba Lagi
                            </button>
                        </div>

                        <!-- Camera Interface -->
                        <div id="camera-interface" class="text-center py-3">
                            <div class="position-relative d-inline-block w-100" style="max-width: 350px;">
                                <video id="camera" class="w-100 rounded-s" style="height: 250px; object-fit: cover;" autoplay playsinline></video>
                                <canvas id="canvas" class="d-none"></canvas>
                                <img id="captured-photo" class="d-none w-100 rounded-s" style="height: 250px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" id="capture-btn" class="btn btn-s bg-blue-dark text-white rounded-s flex-fill" onclick="capturePhoto()" style="min-height: 45px;">
                                    <i class="bi bi-camera-fill pe-2 font-14"></i>Ambil Foto
                                </button>
                                <button type="button" class="btn btn-s bg-orange-dark text-white rounded-s flex-fill d-none" id="retake-btn" onclick="retakePhoto()" style="min-height: 45px;">
                                    <i class="bi bi-arrow-clockwise pe-2 font-14"></i>Ulangi Foto
                                </button>
                                <button type="button" class="btn btn-s bg-green-dark text-white rounded-s flex-fill d-none" id="proceed-btn" onclick="proceedToSubmit()" style="min-height: 45px;">
                                    <i class="bi bi-check-circle pe-2 font-14"></i>Lanjutkan
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="step3" class="attendance-step d-none">
                        <div class="text-center mb-3">
                            <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-check-circle text-white font-24"></i>
                            </div>
                            <h6 class="font-600 mb-2">Konfirmasi Absensi</h6>
                            <p class="font-12 opacity-70 mb-3">Tambahkan catatan jika diperlukan, atau langsung submit absensi</p>
                        </div>
                        <textarea class="form-control rounded-s mb-3" id="attendance-notes" rows="2" placeholder="Catatan (opsional)..."></textarea>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-s bg-gray-dark text-white rounded-s flex-fill" onclick="goBackToPhoto()" style="min-height: 45px;">
                                <i class="bi bi-arrow-left pe-2 font-14"></i>Kembali
                            </button>
                            <button type="button" class="btn btn-s bg-green-dark text-white rounded-s flex-fill" onclick="submitAttendance()" style="min-height: 45px;">
                                <i class="bi bi-check-circle pe-2 font-14"></i>Submit Absensi
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-none">
                    <!-- Footer buttons moved to step 3 for better UX -->
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        let currentAttendanceType = '';
        let currentLocation = null;
        let currentPhoto = null;
        let stream = null;

        // Prevent zoom on double tap for iOS
        document.addEventListener('touchstart', function(event) {
            if (event.touches.length > 1) {
                event.preventDefault();
            }
        }, {
            passive: false
        });

        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);

        // Update current time
        function updateTime() {
            const now = new Date();

            // Format time for Jakarta timezone (WIB/UTC+7)
            const timeString = now.toLocaleTimeString('id-ID', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: 'Asia/Jakarta'
            });

            // Get timezone info for debugging
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

            document.getElementById('current-time').textContent = timeString;

            // Show timezone info (can be removed after debugging)
            const timezoneElement = document.getElementById('timezone-info');
            if (timezoneElement) {
                timezoneElement.textContent = `(${timezone})`;
            }
        }

        // Start time updates
        setInterval(updateTime, 1000);
        updateTime();

        function startCheckIn() {
            currentAttendanceType = 'checkin';
            document.getElementById('attendanceModalLabel').textContent = 'Check In';
            resetModal();
            new bootstrap.Modal(document.getElementById('attendanceModal')).show();
            getLocation();
        }

        function startCheckOut() {
            currentAttendanceType = 'checkout';
            document.getElementById('attendanceModalLabel').textContent = 'Check Out';
            resetModal();
            new bootstrap.Modal(document.getElementById('attendanceModal')).show();
            getLocation();
        }

        function resetModal() {
            document.querySelectorAll('.attendance-step').forEach(step => step.classList.add('d-none'));
            document.getElementById('step1').classList.remove('d-none');

            // Reset camera interface
            document.getElementById('camera-error').classList.add('d-none');
            document.getElementById('camera-interface').classList.remove('d-none');

            // Reset all buttons
            document.getElementById('retake-btn').classList.add('d-none');
            document.getElementById('proceed-btn').classList.add('d-none');
            document.getElementById('capture-btn').classList.remove('d-none');

            // Reset camera
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            // Reset photo display
            document.getElementById('camera').classList.remove('d-none');
            document.getElementById('captured-photo').classList.add('d-none');

            currentLocation = null;
            currentPhoto = null;
        }

        function getLocation() {
            const statusDiv = document.getElementById('location-status');

            if (!navigator.geolocation) {
                statusDiv.innerHTML = '<i class="bi bi-x-circle color-red-dark font-30 d-block mb-2"></i><p class="text-danger">Geolocation tidak didukung browser Anda</p>';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    currentLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };

                    statusDiv.innerHTML = `
                    <i class="bi bi-hourglass color-blue-dark font-30 d-block mb-2"></i>
                    <p class="text-primary">Memvalidasi lokasi...</p>
                    <small class="opacity-70">Lat: ${currentLocation.latitude.toFixed(6)}, Lng: ${currentLocation.longitude.toFixed(6)}</small>
                `;

                    // Validate location before proceeding
                    validateLocationBeforePhoto();
                },
                function(error) {
                    let errorMessage = 'Gagal mendapatkan lokasi';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Akses lokasi ditolak. Mohon izinkan akses lokasi.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Informasi lokasi tidak tersedia';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Request lokasi timeout';
                            break;
                    }

                    statusDiv.innerHTML = `
                    <i class="bi bi-x-circle color-red-dark font-30 d-block mb-2"></i>
                    <p class="text-danger">${errorMessage}</p>
                    <button type="button" class="btn btn-s bg-blue-dark text-white rounded-s" onclick="getLocation()" style="min-height: 40px;">
                        <i class="bi bi-arrow-clockwise pe-1 font-12"></i>Coba Lagi
                    </button>
                `;
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                }
            );
        }

        function validateLocationBeforePhoto() {
            const statusDiv = document.getElementById('location-status');

            const formData = new FormData();
            formData.append('latitude', currentLocation.latitude);
            formData.append('longitude', currentLocation.longitude);
            formData.append('_token', '{{ csrf_token() }}');

            // Use different endpoint based on attendance type
            const validateUrl = currentAttendanceType === 'checkout' ?
                '{{ route('employee.attendance.validate-location-checkout') }}' :
                '{{ route('employee.attendance.validate-location') }}';

            fetch(validateUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusDiv.innerHTML = `
                            <i class="bi bi-check-circle color-green-dark font-30 d-block mb-2"></i>
                            <p class="text-success">Lokasi valid!</p>
                            <small class="opacity-70">${data.location.name}</small>
                        `;

                        setTimeout(() => {
                            showStep2();
                        }, 1500);
                    } else {
                        statusDiv.innerHTML = `
                            <i class="bi bi-x-circle color-red-dark font-30 d-block mb-2"></i>
                            <p class="text-danger">${data.error}</p>
                            <button type="button" class="btn btn-s bg-blue-dark text-white rounded-s" onclick="getLocation()" style="min-height: 40px;">
                                <i class="bi bi-arrow-clockwise pe-1 font-12"></i>Coba Lagi
                            </button>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    statusDiv.innerHTML = `
                        <i class="bi bi-x-circle color-red-dark font-30 d-block mb-2"></i>
                        <p class="text-danger">Gagal memvalidasi lokasi. Silakan coba lagi.</p>
                        <button type="button" class="btn btn-s bg-blue-dark text-white rounded-s" onclick="getLocation()" style="min-height: 40px;">
                            <i class="bi bi-arrow-clockwise pe-1 font-12"></i>Coba Lagi
                        </button>
                    `;
                });
        }

        function showStep2() {
            document.getElementById('step1').classList.add('d-none');
            document.getElementById('step2').classList.remove('d-none');

            // Reset camera interface
            document.getElementById('camera-error').classList.add('d-none');
            document.getElementById('camera-interface').classList.remove('d-none');

            startCamera();
        }

        function retryCameraAccess() {
            // Hide error and show camera interface
            document.getElementById('camera-error').classList.add('d-none');
            document.getElementById('camera-interface').classList.remove('d-none');

            startCamera();
        }

        function startCamera() {
            const constraints = {
                video: {
                    facingMode: 'user',
                    width: {
                        ideal: 640,
                        max: 1280
                    },
                    height: {
                        ideal: 480,
                        max: 720
                    }
                }
            };

            navigator.mediaDevices.getUserMedia(constraints)
                .then(function(mediaStream) {
                    stream = mediaStream;
                    const video = document.getElementById('camera');
                    video.srcObject = stream;
                    video.setAttribute('playsinline', true); // Important for iOS
                    video.play();
                })
                .catch(function(error) {
                    console.error('Error accessing camera:', error);
                    let errorMessage = 'Gagal mengakses kamera';

                    if (error.name === 'NotAllowedError') {
                        errorMessage = 'Akses kamera ditolak. Mohon izinkan akses kamera.';
                    } else if (error.name === 'NotFoundError') {
                        errorMessage = 'Kamera tidak ditemukan di perangkat Anda.';
                    }

                    // Hide camera interface and show error
                    document.getElementById('camera-interface').classList.add('d-none');
                    document.getElementById('camera-error').classList.remove('d-none');
                    document.getElementById('error-message').textContent = errorMessage;
                });
        }

        function capturePhoto() {
            const video = document.getElementById('camera');
            const canvas = document.getElementById('canvas');
            const capturedPhoto = document.getElementById('captured-photo');

            // Set canvas size to match video
            const videoRect = video.getBoundingClientRect();
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert to blob with good quality for mobile
            canvas.toBlob(function(blob) {
                currentPhoto = blob;
                const url = URL.createObjectURL(blob);
                capturedPhoto.src = url;
                capturedPhoto.classList.remove('d-none');
                video.classList.add('d-none');

                // Stop camera stream to save battery
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }

                // Show retake and proceed buttons, hide capture button
                document.getElementById('retake-btn').classList.remove('d-none');
                document.getElementById('proceed-btn').classList.remove('d-none');
                document.getElementById('capture-btn').classList.add('d-none');
            }, 'image/jpeg', 0.9); // Higher quality for better recognition
        }

        function retakePhoto() {
            const video = document.getElementById('camera');
            const capturedPhoto = document.getElementById('captured-photo');

            // Stop existing stream if any
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            // Reset camera interface visibility
            document.getElementById('camera-error').classList.add('d-none');
            document.getElementById('camera-interface').classList.remove('d-none');

            // Reset UI elements
            video.classList.remove('d-none');
            capturedPhoto.classList.add('d-none');
            document.getElementById('retake-btn').classList.add('d-none');
            document.getElementById('proceed-btn').classList.add('d-none');
            document.getElementById('capture-btn').classList.remove('d-none');

            // Reset data
            currentPhoto = null;
            document.getElementById('step3').classList.add('d-none');

            // Restart camera stream
            startCamera();
        }

        function proceedToSubmit() {
            // Hide step 2 and show step 3
            document.getElementById('step2').classList.add('d-none');
            document.getElementById('step3').classList.remove('d-none');
        }

        function goBackToPhoto() {
            // Show step 2 and hide step 3
            document.getElementById('step2').classList.remove('d-none');
            document.getElementById('step3').classList.add('d-none');
        }

        function showStep3() {
            // This function is now handled by proceedToSubmit()
            proceedToSubmit();
        }

        function submitAttendance() {
            if (!currentLocation || !currentPhoto) {
                alert('Data lokasi atau foto tidak lengkap');
                return;
            }

            const formData = new FormData();
            formData.append('latitude', currentLocation.latitude);
            formData.append('longitude', currentLocation.longitude);
            formData.append('photo', currentPhoto, 'attendance_photo.jpg');
            formData.append('notes', document.getElementById('attendance-notes').value);
            formData.append('_token', '{{ csrf_token() }}');

            const url = currentAttendanceType === 'checkin' ?
                '{{ route('employee.attendance.checkin') }}' :
                '{{ route('employee.attendance.checkout') }}';

            // Show loading - find submit button in step 3
            const submitBtn = document.querySelector('#step3 .btn.bg-green-dark');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass pe-2"></i>Memproses...';

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let message = data.message;
                        if (data.schedule_status && data.schedule_status !== 'Tepat waktu') {
                            message += '\n\nStatus Jadwal: ' + data.schedule_status;
                        }
                        alert(message);
                        // Ask service worker to clear cache so subsequent navigation fetches fresh HTML
                        if (navigator.serviceWorker && navigator.serviceWorker.controller) {
                            navigator.serviceWorker.controller.postMessage({
                                type: 'CLEAR_CACHE'
                            });
                        }

                        // Give SW a moment to clear/update cache then reload from network
                        setTimeout(() => {
                            // Force reload from network
                            window.location.reload(true);
                        }, 300);
                    } else {
                        alert(data.error || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memproses absensi');
                })
                .finally(() => {
                    // Reset submit button
                    const submitBtn = document.querySelector('#step3 .btn.bg-green-dark');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-check-circle pe-2"></i>Submit Absensi';
                    }
                });
        }

        // Close modal handler
        document.getElementById('attendanceModal').addEventListener('hidden.bs.modal', function() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        });
    </script>
@endpush

@push('scripts')
    <style>
        .clickable-card:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }
    </style>
@endpush
