<!-- Admin sidebar (styled like employee sidebar/template) -->
<div id="menu-main" data-menu-active="nav-homes" class="offcanvas offcanvas-start offcanvas-detached rounded-m" style="width: min(280px, 92vw);">
    <div class="card card-style bg-23 mb-3 rounded-m mt-3" data-card-height="120">
        <div class="card-top m-3">
            <a href="#" data-bs-dismiss="offcanvas" class="icon icon-xs bg-theme rounded-s color-theme float-end"><i class="bi bi-caret-left-fill"></i></a>
        </div>
        <div class="card-bottom p-3">
            <h1 class="color-white font-16 font-700 mb-n2">Admin</h1>
            <p class="color-white font-12 opacity-70 mb-n1">Panel Administrasi</p>
        </div>
        <div class="card-overlay bg-gradient-fade rounded-0"></div>
    </div>

    <span class="menu-divider">NAVIGATION</span>
    <div class="menu-list">
        <div class="card card-style rounded-m p-3 py-2 mb-0">
            <a href="{{ route('dashboard') }}" id="nav-homes" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="gradient-blue shadow-bg shadow-bg-xs bi bi-house-fill"></i>
                <span>Dashboard</span>
                <i class="bi bi-chevron-right"></i>
            </a>

            <h6 class="font-600 font-12 opacity-70 mb-2 mt-2">Master Data</h6>

            @if (auth()->user() &&
                    auth()->user()->hasAnyPermission(['employees.view', 'employees.manage']))
                <a href="{{ route('admin.employees.index') }}" class="{{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                    <i class="gradient-green shadow-bg shadow-bg-xs bi bi-people"></i>
                    <span>Kelola Karyawan</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif

            @if (auth()->user() && auth()->user()->hasPermission('departments.view'))
                <a href="{{ route('admin.departments.index') }}" class="{{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                    <i class="gradient-brown shadow-bg shadow-bg-xs bi bi-building"></i>
                    <span>Departemen</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif

            @if (auth()->user() && auth()->user()->hasPermission('positions.view'))
                <a href="{{ route('admin.positions.index') }}" class="{{ request()->routeIs('admin.positions.*') ? 'active' : '' }}">
                    <i class="gradient-teal shadow-bg shadow-bg-xs bi bi-briefcase"></i>
                    <span>Posisi</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif

            @if (auth()->user() && auth()->user()->hasPermission('roles.view'))
                <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="gradient-purple shadow-bg shadow-bg-xs bi bi-shield-check"></i>
                    <span>Role & Permissions</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif

            @if (auth()->user() && auth()->user()->hasPermission('locations.view'))
                <a href="{{ route('admin.office-locations.index') }}" class="{{ request()->routeIs('admin.office-locations.*') ? 'active' : '' }}">
                    <i class="gradient-purple shadow-bg shadow-bg-xs bi bi-geo-alt"></i>
                    <span>Lokasi Kantor</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif

            @if (auth()->user() && auth()->user()->hasPermission('schedules.view'))
                <a href="{{ route('admin.work-schedules.index') }}" class="{{ request()->routeIs('admin.work-schedules.*') ? 'active' : '' }}">
                    <i class="gradient-blue shadow-bg shadow-bg-xs bi bi-calendar-check"></i>
                    <span>Jadwal Kerja</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif

            @if (auth()->user() &&
                    auth()->user()->hasAnyPermission(['employees.edit', 'employees.manage']))
                <a href="{{ route('admin.password-reset.index') }}" class="{{ request()->routeIs('admin.password-reset.*') ? 'active' : '' }}">
                    <i class="gradient-orange shadow-bg shadow-bg-xs bi bi-key"></i>
                    <span>Reset Password</span>
                    @if (isset($pendingResetRequests) && $pendingResetRequests->count() > 0)
                        <em class="badge badge-s bg-red-dark ms-auto">{{ $pendingResetRequests->count() }}</em>
                    @endif
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif
        </div>
    </div>

    <span class="menu-divider mt-4">PENGATURAN</span>
    <div class="menu-list">
        <div class="card card-style rounded-m p-3 py-2 mb-0">
            @if (auth()->user() && auth()->user()->hasPermission('settings.view'))
                <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="gradient-blue shadow-bg shadow-bg-xs bi bi-gear"></i>
                    <span>Pengaturan Umum</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif
        </div>
    </div>

    <span class="menu-divider mt-4">ABSENSI & LAPORAN</span>
    <div class="menu-list">
        <div class="card card-style rounded-m p-3 py-2 mb-0">
            @if (auth()->user() && (auth()->user()->hasPermission('attendance.corrections.approve') || auth()->user()->hasPermission('attendance.corrections.verify')))
                <a href="{{ route('admin.attendance-corrections.index') }}" class="{{ request()->routeIs('admin.attendance-corrections.*') ? 'active' : '' }}">
                    <i class="gradient-red shadow-bg shadow-bg-xs bi bi-activity"></i>
                    <span>Koreksi Absensi</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif

            @if (auth()->user() && auth()->user()->hasPermission('attendance.reports'))
                <a href="{{ route('admin.attendance.reports.index') }}" class="{{ request()->routeIs('admin.attendance.reports.*') ? 'active' : '' }}">
                    <i class="gradient-blue shadow-bg shadow-bg-xs bi bi-bar-chart-line"></i>
                    <span>Laporan Absensi</span>
                    <div class="ms-auto">
                        <span class="badge bg-blue-dark rounded-xl">Analytics</span>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif

            @if (auth()->user() && (auth()->user()->hasPermission('leave.approve') || auth()->user()->hasPermission('leave.verify')))
                <a href="{{ route('admin.leave-requests.index') }}" class="{{ request()->routeIs('admin.leave-requests.*') ? 'active' : '' }}">
                    <i class="gradient-red shadow-bg shadow-bg-xs bi bi-person-check"></i>
                    <span>Pengajuan Izin</span>
                    @if (isset($pendingLeaveCount) && $pendingLeaveCount > 0)
                        <em class="badge badge-s bg-red-dark ms-auto">{{ $pendingLeaveCount }}</em>
                    @endif
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif

            @if (auth()->user() &&
                    auth()->user()->hasAnyPermission(['daily_activities.view_department', 'daily_activities.view_all']))
                <a href="{{ route('admin.daily-activities.index') }}" class="{{ request()->routeIs('admin.daily-activities.*') ? 'active' : '' }}">
                    <i class="gradient-blue shadow-bg shadow-bg-xs bi bi-journal-check"></i>
                    <span>Laporan Daily Activity</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif
        </div>
    </div>

    <div class="menu-divider mb-6 mt-4"></div>
    <div class="text-center p-3 py-2 mb-0">
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger w-100">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
        </form>
    </div>
</div>
