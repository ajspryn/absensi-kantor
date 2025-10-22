<!-- Footer utama karyawan -->
<div id="footer-bar" class="footer-bar footer-bar-detached">
    <a href="{{ route('dashboard') }}" class="d-flex flex-column align-items-center justify-content-center{{ request()->routeIs('dashboard') ? ' active-nav' : '' }}">
        <i class="bi bi-house-fill font-16"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('employee.attendance.history') }}" class="d-flex flex-column align-items-center justify-content-center{{ request()->routeIs('employee.attendance.history') ? ' active-nav' : '' }}">
        <i class="bi bi-clock-history font-16"></i>
        <span>Riwayat</span>
    </a>
    <a href="{{ route('employee.attendance.index') }}" class="d-flex flex-column align-items-center justify-content-center{{ request()->routeIs('employee.attendance.index') ? ' active-nav' : '' }}">
        <i class="bi bi-camera-fill font-16"></i>
        <span>Absen</span>
    </a>
    <a href="{{ route('employee.profile.index') }}" class="d-flex flex-column align-items-center justify-content-center{{ request()->routeIs('employee.profile.index') ? ' active-nav' : '' }}">
        <i class="bi bi-person-circle font-16"></i>
        <span>Profil</span>
    </a>
    <a href="#" class="d-flex flex-column align-items-center justify-content-center footer-link" data-bs-toggle="offcanvas" data-bs-target="#menu-main">
        <i class="bi bi-list"></i>
        <span>Menu</span>
    </a>
</div>
