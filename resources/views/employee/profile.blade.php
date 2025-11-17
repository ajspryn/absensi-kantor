@extends('layouts.app')

@section('title', 'Profil - Aplikasi Absensi')

@section('header')
    <!-- Header -->
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-15">Profil Saya</a>
        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#menu-settings"><i class="bi bi-gear font-16 color-theme"></i></a>
    </div>
@endsection

@section('sidebar')
    @include('employee.sidebar')
    <!-- Settings Sidebar -->
    <div id="menu-settings" class="offcanvas offcanvas-end offcanvas-detached rounded-m" style="width:280px;">
        <div class="content">
            <div class="d-flex pb-2">
                <div class="align-self-center">
                    <h1 class="mb-0 font-16">Pengaturan</h1>
                </div>
                <div class="align-self-center ms-auto">
                    <a href="#" class="ps-4" data-bs-dismiss="offcanvas">
                        <i class="bi bi-x color-red-dark font-24 line-height-xl"></i>
                    </a>
                </div>
            </div>
            <div class="divider mb-2"></div>
            <a href="#" class="d-flex py-1" onclick="showEditProfile()">
                <div class="align-self-center">
                    <i class="bi bi-pencil-square color-blue-dark font-14"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h6 class="pt-1 mb-0 font-13">Edit Profil</h6>
                </div>
            </a>
            <a href="#" class="d-flex py-1" onclick="showChangePassword()">
                <div class="align-self-center">
                    <i class="bi bi-shield-lock color-green-dark font-14"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h6 class="pt-1 mb-0 font-13">Ganti Password</h6>
                </div>
            </a>
            <div class="divider my-2"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="d-flex py-1 w-100 border-0 bg-transparent">
                    <div class="align-self-center">
                        <i class="bi bi-box-arrow-right color-red-dark font-14"></i>
                    </div>
                    <div class="align-self-center ps-3">
                        <h6 class="pt-1 mb-0 font-13">Logout</h6>
                    </div>
                </button>
            </form>
        </div>
    </div>
@endsection

@section('footer')
    @include('employee.footer')
@endsection

@section('content')
    <!-- Profile Header Card -->
    <div class="card card-style">
        <div class="content py-3">
            <div class="text-center">
                <div class="position-relative d-inline-block mb-2">
                    <img src="{{ $employee->photo ? asset('storage/' . $employee->photo) : asset('template/images/avatars/5s.png') }}" width="75" height="75" class="rounded-circle border-3 border-{{ $employee->is_active ? 'green' : 'gray' }}-dark">
                    <span class="position-absolute bottom-0 end-0 bg-{{ $employee->is_active ? 'green' : 'red' }}-dark border-3 border-white rounded-circle" style="width: 20px; height: 20px;"></span>
                </div>

                <h3 class="font-700 mb-1 font-15">{{ $employee->full_name }}</h3>
                <p class="mb-1 font-11 opacity-70">{{ $employee->position->name ?? '-' }}</p>
                <p class="mb-0 font-10 opacity-70">{{ optional($employee->department)->name ?? '-' }}</p>

                <div class="d-flex justify-content-center mt-2">
                    <span class="badge bg-{{ $employee->is_active ? 'green' : 'red' }}-dark rounded-xl font-9 px-2 py-1">
                        {{ $employee->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Information -->
    <div class="card card-style">
        <div class="content py-3">
            <h5 class="font-700 mb-2 font-14">Informasi Personal</h5>

            <div class="d-flex py-1 border-bottom">
                <div class="align-self-center">
                    <i class="bi bi-person-badge color-blue-dark font-13 me-3"></i>
                </div>
                <div class="align-self-center flex-grow-1">
                    <h6 class="mb-0 font-11">ID Karyawan</h6>
                    <p class="mb-0 font-10 opacity-70">{{ $employee->employee_id }}</p>
                </div>
            </div>

            <div class="d-flex py-1 border-bottom">
                <div class="align-self-center">
                    <i class="bi bi-envelope color-green-dark font-13 me-3"></i>
                </div>
                <div class="align-self-center flex-grow-1">
                    <h6 class="mb-0 font-11">Email</h6>
                    <p class="mb-0 font-10 opacity-70">{{ $employee->email ?? 'Belum diatur' }}</p>
                </div>
            </div>

            <div class="d-flex py-1 border-bottom">
                <div class="align-self-center">
                    <i class="bi bi-telephone color-orange-dark font-13 me-3"></i>
                </div>
                <div class="align-self-center flex-grow-1">
                    <h6 class="mb-0 font-11">Nomor Telepon</h6>
                    <p class="mb-0 font-10 opacity-70">{{ $employee->phone ?? 'Belum diatur' }}</p>
                </div>
            </div>

            <div class="d-flex py-1 border-bottom">
                <div class="align-self-center">
                    <i class="bi bi-geo-alt color-red-dark font-13 me-3"></i>
                </div>
                <div class="align-self-center flex-grow-1">
                    <h6 class="mb-0 font-11">Alamat</h6>
                    <p class="mb-0 font-10 opacity-70">{{ $employee->address ?? 'Belum diatur' }}</p>
                </div>
            </div>

            <div class="d-flex py-1">
                <div class="align-self-center">
                    <i class="bi bi-calendar3 color-purple-dark font-13 me-3"></i>
                </div>
                <div class="align-self-center flex-grow-1">
                    <h6 class="mb-0 font-11">Bergabung Sejak</h6>
                    <p class="mb-0 font-10 opacity-70">{{ $employee->created_at->locale('id')->translatedFormat('d F Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="card card-style">
        <div class="content py-3">
            <h5 class="font-700 mb-2 font-14">Statistik Bulan Ini</h5>

            <div class="row g-2">
                <div class="col-4 text-center">
                    <div class="bg-green-dark rounded-s py-2 px-1">
                        <i class="bi bi-check-circle text-white font-16 d-block mb-1"></i>
                        <h6 class="font-600 text-white mb-0 font-12">{{ $monthlyStats['present'] ?? 0 }}</h6>
                        <small class="font-9 text-white-50">Hadir</small>
                    </div>
                </div>
                <div class="col-4 text-center">
                    <div class="bg-red-dark rounded-s py-2 px-1">
                        <i class="bi bi-x-circle text-white font-16 d-block mb-1"></i>
                        <h6 class="font-600 text-white mb-0 font-12">{{ $monthlyStats['absent'] ?? 0 }}</h6>
                        <small class="font-9 text-white-50">Alpha</small>
                    </div>
                </div>
                <div class="col-4 text-center">
                    <div class="bg-blue-dark rounded-s py-2 px-1">
                        <i class="bi bi-calendar-x text-white font-16 d-block mb-1"></i>
                        <h6 class="font-600 text-white mb-0 font-12">{{ $monthlyStats['leave'] ?? 0 }}</h6>
                        <small class="font-9 text-white-50">Cuti</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card card-style">
        <div class="content py-3">
            <h5 class="font-700 mb-2 font-14">Aksi Cepat</h5>

            <div class="row g-2">
                <div class="col-6">
                    <a href="{{ route('employee.attendance.index') }}" class="btn btn-full btn-s bg-green-dark text-white text-uppercase font-600 rounded-s" style="min-height: 45px;">
                        <i class="bi bi-camera pe-2 font-14"></i>Absensi
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('employee.attendance.history') }}" class="btn btn-full btn-s bg-blue-dark text-white text-uppercase font-600 rounded-s" style="min-height: 45px;">
                        <i class="bi bi-clock-history pe-2 font-14"></i>Riwayat
                    </a>
                </div>
            </div>

            <div class="row g-2 mt-1">
                <div class="col-6">
                    <button id="pwaInstallButton" type="button" class="btn btn-full btn-s bg-orange-dark text-white text-uppercase font-600 rounded-s" style="min-height: 45px;" onclick="installPWA()">
                        <i class="bi bi-download pe-2 font-14"></i>Install App
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-full btn-s bg-orange-dark text-white text-uppercase font-600 rounded-s" onclick="showEditProfile()" style="min-height: 45px;">
                        <i class="bi bi-pencil-square pe-2 font-14"></i>Edit Profil
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->


@endsection


@push('modals')
    <!-- Edit Profile Modal: moved to global modals stack to ensure top-level z-index -->
    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-s">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-700">Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-3">
                    <form id="editProfileForm">
                        @csrf

                        <div class="mb-2">
                            <label class="form-label font-600 font-11">Nama Lengkap</label>
                            <input type="text" class="form-control rounded-s" name="full_name" value="{{ $employee->full_name }}" style="min-height: 45px;">
                        </div>

                        <div class="mb-2">
                            <label class="form-label font-600 font-11">Email</label>
                            <input type="email" class="form-control rounded-s" name="email" value="{{ $employee->email }}" style="min-height: 45px;">
                        </div>

                        <div class="mb-2">
                            <label class="form-label font-600 font-11">Nomor Telepon</label>
                            <input type="text" class="form-control rounded-s" name="phone" value="{{ $employee->phone }}" placeholder="Contoh: +62812345678" style="min-height: 45px;">
                        </div>

                        <div class="mb-2">
                            <label class="form-label font-600 font-11">Alamat</label>
                            <textarea class="form-control rounded-s" name="address" rows="2" placeholder="Alamat lengkap">{{ $employee->address }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-600 font-11">Foto Profil</label>
                            <input type="file" class="form-control rounded-s" name="photo" accept="image/*" style="min-height: 45px;">
                            <small class="text-muted font-9">Format: JPG, PNG. Maksimal 2MB</small>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="button" class="btn btn-s bg-gray-dark text-white rounded-s flex-fill" data-bs-dismiss="modal" style="min-height: 45px;">
                                <i class="bi bi-x pe-2 font-14"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-s bg-green-dark text-white rounded-s flex-fill" style="min-height: 45px;">
                                <i class="bi bi-check pe-2 font-14"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal: moved to global modals stack to ensure top-level z-index -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-s">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-700">Ganti Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-3">
                    <form id="changePasswordForm">
                        @csrf

                        <div class="mb-2">
                            <label class="form-label font-600 font-11">Password Saat Ini</label>
                            <input type="password" class="form-control rounded-s" name="current_password" required style="min-height: 45px;">
                        </div>

                        <div class="mb-2">
                            <label class="form-label font-600 font-11">Password Baru</label>
                            <input type="password" class="form-control rounded-s" name="new_password" required style="min-height: 45px;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-600 font-11">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control rounded-s" name="new_password_confirmation" required style="min-height: 45px;">
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="button" class="btn btn-s bg-gray-dark text-white rounded-s flex-fill" data-bs-dismiss="modal" style="min-height: 45px;">
                                <i class="bi bi-x pe-2 font-14"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-s bg-green-dark text-white rounded-s flex-fill" style="min-height: 45px;">
                                <i class="bi bi-shield-check pe-2 font-14"></i>Ganti Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        // Ensure offcanvas is hidden before showing modal to avoid z-index conflicts
        function openModalAfterOffcanvas(modalId) {
            const offcanvasEl = document.getElementById('menu-settings');
            const showModal = () => {
                const modalEl = document.getElementById(modalId);
                const modal = new bootstrap.Modal(modalEl, {
                    backdrop: true,
                    focus: true
                });
                modal.show();
            };

            if (offcanvasEl && offcanvasEl.classList.contains('show')) {
                // --- PWA Install Button Logic ---
                // Show the install button only when install prompt is available and app not yet installed
                (function() {
                    const installBtn = document.getElementById('pwaInstallButton');

                    // Hide button if already installed
                    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
                    if (isStandalone) {
                        if (installBtn) installBtn.style.display = 'none';
                        return;
                    }

                    // Store the prompt globally
                    window.deferredPrompt = window.deferredPrompt || null;

                    window.addEventListener('beforeinstallprompt', (e) => {
                        // Prevent mini-infobar
                        e.preventDefault();
                        window.deferredPrompt = e;
                        if (installBtn) installBtn.style.display = 'block';
                    });

                    // If the layout already captured the prompt earlier
                    if (window.deferredPrompt) {
                        if (installBtn) installBtn.style.display = 'block';
                    }

                    // Fallback: show button after a short delay for browsers that support PWA
                    setTimeout(() => {
                        if ('serviceWorker' in navigator && !isStandalone && installBtn && !installBtn.style.display) {
                            installBtn.style.display = 'block';
                        }
                    }, 1500);
                })();

                function installPWA() {
                    if (window.deferredPrompt) {
                        window.deferredPrompt.prompt();
                        window.deferredPrompt.userChoice.then((choiceResult) => {
                            if (choiceResult.outcome === 'accepted') {
                                console.log('PWA install accepted');
                            } else {
                                console.log('PWA install dismissed');
                            }
                            window.deferredPrompt = null;
                        });
                    } else {
                        // Fallback instructions for iOS/Safari or when prompt not available
                        alert('Untuk menginstal aplikasi: buka menu browser lalu pilih "Tambah ke Layar Utama".');
                    }
                }

                const instance = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                const handler = () => {
                    offcanvasEl.removeEventListener('hidden.bs.offcanvas', handler);
                    showModal();
                };
                offcanvasEl.addEventListener('hidden.bs.offcanvas', handler);
                instance.hide();
            } else {
                showModal();
            }
        }

        function showEditProfile() {
            openModalAfterOffcanvas('editProfileModal');
        }

        function showChangePassword() {
            openModalAfterOffcanvas('changePasswordModal');
        }

        // Handle Edit Profile Form
        document.getElementById('editProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');

            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass pe-2 font-14"></i>Menyimpan...';

            fetch('{{ route('employee.profile.update') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Profil berhasil diperbarui!');
                        location.reload();
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan profil');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-check pe-2 font-14"></i>Simpan';
                });
        });

        // Handle Change Password Form
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');

            // Validate password confirmation
            const newPassword = formData.get('new_password');
            const confirmPassword = formData.get('new_password_confirmation');

            if (newPassword !== confirmPassword) {
                alert('Konfirmasi password tidak cocok');
                return;
            }

            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass pe-2 font-14"></i>Mengubah...';

            fetch('{{ route('employee.profile.change-password') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Password berhasil diubah!');
                        bootstrap.Modal.getInstance(document.getElementById('changePasswordModal')).hide();
                        this.reset();
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengubah password');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-shield-check pe-2 font-14"></i>Ganti Password';
                });
        });
    </script>
@endpush
