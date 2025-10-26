<!-- Main Sidebar-->
<div id="menu-main" class="offcanvas offcanvas-start offcanvas-detached rounded-m" style="width:280px;">
    <div class="content">
        <div class="d-flex pb-2">
            <div class="align-self-center">
                <h1 class="mb-0">Menu</h1>
            </div>
            <div class="align-self-center ms-auto">
                <a href="#" class="ps-4" data-bs-dismiss="offcanvas">
                    <i class="bi bi-x color-red-dark font-26 line-height-xl"></i>
                </a>
            </div>
        </div>
        <div class="divider mb-2"></div>

        <div class="mb-2">
            <a href="{{ route('dashboard') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door color-blue-dark font-18"></i>
                <div class="ps-3">Dashboard</div>
            </a>
        </div>

        <h6 class="font-600 font-12 opacity-70 mb-2">Master Data</h6>

        <a href="{{ route('admin.employees.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
            <i class="bi bi-people color-green-dark font-18"></i>
            <div class="ps-3">Kelola Karyawan</div>
        </a>

        <a href="{{ route('admin.departments.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
            <i class="bi bi-building color-brown-dark font-18"></i>
            <div class="ps-3">Departemen</div>
        </a>

        <a href="{{ route('admin.positions.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.positions.*') ? 'active' : '' }}">
            <i class="bi bi-briefcase color-teal-dark font-18"></i>
            <div class="ps-3">Posisi</div>
        </a>

        <a href="{{ route('admin.roles.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <i class="bi bi-shield-check text-purple font-18"></i>
            <div class="ps-3">Role & Permissions</div>
        </a>

        <a href="{{ route('admin.office-locations.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.office-locations.*') ? 'active' : '' }}">
            <i class="bi bi-geo-alt text-purple font-18"></i>
            <div class="ps-3">Lokasi Kantor</div>
        </a>

        <a href="{{ route('admin.work-schedules.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.work-schedules.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check color-blue-dark font-18"></i>
            <div class="ps-3">Jadwal Kerja</div>
        </a>

        <a href="{{ route('admin.password-reset.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.password-reset.*') ? 'active' : '' }}">
            <i class="bi bi-key color-orange-dark font-18"></i>
            <div class="ps-3">Reset Password</div>
            @if (isset($pendingResetRequests) && $pendingResetRequests->count() > 0)
                <div class="ms-auto">
                    <span class="badge bg-red-dark font-11">{{ $pendingResetRequests->count() }}</span>
                </div>
            @endif
        </a>

        <div class="divider my-2"></div>
        <h6 class="font-600 font-12 opacity-70 mb-2">Pengaturan</h6>

        <a href="{{ route('admin.settings.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear color-blue-dark font-18"></i>
            <div class="ps-3">Pengaturan Umum</div>
        </a>

        <div class="divider my-2"></div>
        <h6 class="font-600 font-12 opacity-70 mb-2">Absensi & Laporan</h6>

        @if (auth()->user() && (auth()->user()->hasPermission('attendance.corrections.approve') || auth()->user()->hasPermission('attendance.corrections.verify')))
            <a href="{{ route('admin.attendance-corrections.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.attendance-corrections.*') ? 'active' : '' }}">
                <i class="bi bi-activity color-red-dark font-18"></i>
                <div class="ps-3">Koreksi Absensi</div>
            </a>
        @endif

        <a href="{{ route('admin.attendance.reports.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.attendance.reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line color-blue-dark font-18"></i>
            <div class="ps-3">Laporan Absensi</div>
            <div class="ms-auto">
                <span class="badge bg-blue-dark rounded-xl">Analytics</span>
            </div>
        </a>

        @if (auth()->user() && (auth()->user()->hasPermission('leave.approve') || auth()->user()->hasPermission('leave.verify')))
            <a href="{{ route('admin.leave-requests.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.leave-requests.*') ? 'active' : '' }}">
                <i class="bi bi-person-check color-red-dark font-18"></i>
                <div class="ps-3">Pengajuan Izin</div>
                @if (isset($pendingLeaveCount) && $pendingLeaveCount > 0)
                    <div class="ms-auto">
                        <span class="badge bg-danger">{{ $pendingLeaveCount }}</span>
                    </div>
                @endif
            </a>
        @endif

        <div class="divider mb-2"></div>
        <div class="text-center pb-2">
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger w-100">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </div>
    </div>
</div>
