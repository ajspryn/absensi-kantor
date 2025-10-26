<!-- Footer utama karyawan -->
<div id="footer-bar" class="footer-bar footer-bar-detached">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? ' active-nav' : '' }}">
        <i class="bi bi-house-fill"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('employee.attendance.history') }}" class="{{ request()->routeIs('employee.attendance.history') ? ' active-nav' : '' }}">
        <i class="bi bi-clock-history"></i>
        <span>Riwayat</span>
    </a>
    <a href="{{ route('employee.attendance.index') }}" class="{{ request()->routeIs('employee.attendance.index') ? ' active-nav' : '' }}">
        <i class="bi bi-camera-fill"></i>
        <span>Absen</span>
    </a>
    <a href="{{ route('employee.profile.index') }}" class="{{ request()->routeIs('employee.profile.index') ? ' active-nav' : '' }}">
        <i class="bi bi-person-circle"></i>
        <span>Profil</span>
    </a>
    <a href="#" class="footer-link" data-bs-toggle="offcanvas" data-bs-target="#menu-main">
        <i class="bi bi-list"></i>
        <span>Menu</span>
    </a>
</div>
