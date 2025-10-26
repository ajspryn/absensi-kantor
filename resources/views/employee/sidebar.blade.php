@php
    $employee = $employee ?? optional(auth()->user())->employee;
@endphp
<div id="menu-main" data-menu-active="nav-homes" class="offcanvas offcanvas-start offcanvas-detached rounded-m" style="width:280px;">
    <!-- Top card (styling similar to template) -->
    <div class="card card-style bg-23 mb-3 rounded-m mt-3" data-card-height="120" style="background-image: url('{{ $employee && $employee->photo ? asset('storage/' . $employee->photo) : asset('template/images/avatars/5s.png') }}'); background-size: cover; background-position: center;">
        <div class="card-top m-3">
            <a href="#" data-bs-dismiss="offcanvas" class="icon icon-xs bg-theme rounded-s color-theme float-end"><i class="bi bi-caret-left-fill"></i></a>
        </div>
        <div class="card-bottom p-3">
            <h1 class="color-white font-16 font-700 mb-n2">{{ $employee->full_name ?? auth()->user()->name }}</h1>
            <p class="color-white font-12 opacity-70 mb-n1">{{ $employee->position_name ?? 'Karyawan' }}</p>
        </div>
        <div class="card-overlay bg-gradient-fade rounded-0"></div>
    </div>

    <span class="menu-divider">NAVIGATION</span>
    <div class="menu-list">
        <div class="card card-style rounded-m p-3 py-2 mb-0">
            <a href="{{ route('dashboard') }}" id="nav-homes" class="{{ request()->routeIs('dashboard') ? 'active-item' : '' }}">
                <i class="gradient-blue shadow-bg shadow-bg-xs bi bi-house-fill"></i>
                <span>Dashboard</span>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('employee.attendance.index') }}" id="nav-attendance" class="{{ request()->routeIs('employee.attendance.*') ? 'active-item' : '' }}">
                <i class="gradient-green shadow-bg shadow-bg-xs bi bi-card-checklist"></i>
                <span>Absensi</span>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('employee.attendance.history') }}" id="nav-history" class="{{ request()->routeIs('employee.attendance.history') ? 'active-item' : '' }}">
                <i class="gradient-orange shadow-bg shadow-bg-xs bi bi-clock-history"></i>
                <span>Riwayat Absensi</span>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('employee.schedule.index') }}" id="nav-schedule" class="{{ request()->routeIs('employee.schedule.*') ? 'active-item' : '' }}">
                <i class="gradient-magenta shadow-bg shadow-bg-xs bi bi-calendar-check"></i>
                <span>Jadwal Kerja</span>
                <i class="bi bi-chevron-right"></i>
            </a>

            @if (auth()->user() && auth()->user()->hasPermission('attendance.corrections.request'))
                @php
                    $correctionsRoute = null;
                    if (\Illuminate\Support\Facades\Route::has('employee.attendance.corrections.index')) {
                        $correctionsRoute = 'employee.attendance.corrections.index';
                    } elseif (\Illuminate\Support\Facades\Route::has('attendance.corrections.index')) {
                        $correctionsRoute = 'attendance.corrections.index';
                    }
                @endphp

                @if ($correctionsRoute)
                    <a href="{{ route($correctionsRoute) }}" id="nav-corrections" class="{{ request()->routeIs('employee.attendance.corrections.*') ? 'active-item' : '' }}">
                        <i class="gradient-blue shadow-bg shadow-bg-xs bi bi-pencil-square"></i>
                        <span>Koreksi Absensi Saya</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @endif
            @endif

            @if (auth()->user() && (auth()->user()->hasPermission('daily_activities.create') || auth()->user()->hasPermission('daily_activities.view_own')))
                <a href="{{ route('employee.daily-activities.index') }}" id="nav-daily" class="{{ request()->routeIs('employee.daily-activities.*') ? 'active-item' : '' }}">
                    <i class="gradient-teal shadow-bg shadow-bg-xs bi bi-clipboard-data"></i>
                    <span>Daily Activity</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif
        </div>
    </div>

    @php
        $hasAdminShortcuts = auth()->user() && (auth()->user()->hasPermission('leave.request') || auth()->user()->hasPermission('leave.approve') || auth()->user()->hasPermission('leave.verify') || auth()->user()->hasPermission('attendance.corrections.approve') || auth()->user()->hasPermission('attendance.corrections.verify') || auth()->user()->hasPermission('daily_activities.view_department'));
    @endphp

    @if ($hasAdminShortcuts)
        <span class="menu-divider mt-4">SHORTCUTS</span>
        <div class="menu-list">
            <div class="card card-style rounded-m p-3 py-2 mb-0">
                @if (auth()->user() && auth()->user()->hasPermission('leave.request'))
                    <a href="{{ route('employee.leave.requests.index') }}" class="{{ request()->routeIs('employee.leave.requests.*') ? 'active-item' : '' }}">
                        <i class="gradient-red shadow-bg shadow-bg-xs bi bi-calendar-plus"></i>
                        <span>Pengajuan Izin</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @endif

                @if (auth()->user() && (auth()->user()->hasPermission('leave.approve') || auth()->user()->hasPermission('leave.verify')))
                    @php
                        $leaveRoute = null;
                        if (\Illuminate\Support\Facades\Route::has('admin.leave-requests.index')) {
                            $leaveRoute = 'admin.leave-requests.index';
                        }
                    @endphp

                    @if ($leaveRoute)
                        <a href="{{ route($leaveRoute) }}" class="{{ request()->routeIs('admin.leave-requests.*') ? 'active-item' : '' }}">
                            <i class="gradient-red shadow-bg shadow-bg-xs bi bi-person-check"></i>
                            <span>Persetujuan Izin</span>
                            @if (isset($pendingLeaveCount) && $pendingLeaveCount > 0)
                                <em class="badge badge-s bg-red-dark ms-auto">{{ $pendingLeaveCount }}</em>
                            @endif
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @endif
                @endif

                @if (auth()->user() && ((auth()->user()->hasPermission('attendance.corrections.approve') && auth()->user()->isManager()) || auth()->user()->hasPermission('attendance.corrections.verify')))
                    <a href="{{ route('admin.attendance-corrections.index') }}" class="{{ request()->routeIs('admin.attendance-corrections.*') ? 'active-item' : '' }}">
                        <i class="gradient-green shadow-bg shadow-bg-xs bi bi-check2-square"></i>
                        <span>Persetujuan Koreksi</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @endif

                @if (auth()->user() && auth()->user()->hasPermission('daily_activities.view_department'))
                    <a href="{{ route('admin.daily-activities.index') }}" class="{{ request()->routeIs('admin.daily-activities.*') ? 'active-item' : '' }}">
                        <i class="gradient-blue shadow-bg shadow-bg-xs bi bi-bar-chart-line"></i>
                        <span>Laporan Daily Activity</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @endif
            </div>
        </div>
    @endif

    <span class="menu-divider mt-4">AKUN</span>
    <div class="menu-list">
        <div class="card card-style rounded-m p-3 py-2 mb-0">
            <a href="{{ route('employee.profile.index') }}" class="{{ request()->routeIs('employee.profile.*') ? 'active-item' : '' }}">
                <i class="gradient-mint shadow-bg shadow-bg-xs bi bi-person-circle"></i>
                <span>Profil</span>
                <i class="bi bi-chevron-right"></i>
            </a>
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
