@php
    $employee = $employee ?? optional(auth()->user())->employee;
@endphp
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

        <!-- Profile Section -->
        <div class="d-flex py-2">
            <div class="align-self-center">
                <img src="{{ $employee && $employee->photo ? asset('storage/' . $employee->photo) : asset('template/images/avatars/5s.png') }}" width="40" height="40" class="rounded-circle" alt="Avatar">
            </div>
            <div class="align-self-center ps-3">
                <h5 class="mb-0">{{ $employee->full_name ?? auth()->user()->name }}</h5>
                <p class="mb-0 font-11 opacity-70">{{ $employee->position_name ?? 'Karyawan' }}</p>
            </div>
        </div>

        <div class="divider my-2"></div>

        {{-- Primary section: Utama --}}
        <div class="mb-2">
            <h6 class="mb-1 text-uppercase font-10 opacity-60">Utama</h6>
            <a href="{{ route('dashboard') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="{{ request()->routeIs('dashboard') ? 'page' : '' }}">
                <i class="bi bi-house-door color-blue-dark font-18"></i>
                <div class="ps-3">
                    <div class="mb-0">Dashboard</div>
                </div>
            </a>
        </div>

        {{-- Absensi section --}}
        <div class="mb-2">
            <h6 class="mb-1 text-uppercase font-10 opacity-60">Absensi</h6>
            <a href="{{ route('employee.attendance.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('employee.attendance.*') ? 'active' : '' }}">
                <i class="bi bi-card-checklist color-green-dark font-18"></i>
                <div class="ps-3">Absensi</div>
            </a>

            <a href="{{ route('employee.attendance.history') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('employee.attendance.history') ? 'active' : '' }}">
                <i class="bi bi-clock-history color-orange-dark font-18"></i>
                <div class="ps-3">Riwayat Absensi</div>
            </a>

            <a href="{{ route('employee.schedule.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('employee.schedule.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check color-purple-dark font-18"></i>
                <div class="ps-3">Jadwal Kerja</div>
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
                    <a href="{{ route($correctionsRoute) }}" class="d-flex py-1 align-items-center {{ request()->routeIs('employee.attendance.corrections.*') ? 'active' : '' }}">
                        <i class="bi bi-pencil-square color-blue-dark font-18"></i>
                        <div class="ps-3">Koreksi Absensi Saya</div>
                    </a>
                @endif
            @endif

            @if (auth()->user() && (auth()->user()->hasPermission('daily_activities.create') || auth()->user()->hasPermission('daily_activities.view_own')))
                <a href="{{ route('employee.daily-activities.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('employee.daily-activities.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-data color-teal-dark font-18"></i>
                    <div class="ps-3">Daily Activity</div>
                </a>
            @endif
        </div>

        {{-- Shortcuts / Admin (if user has admin-ish permissions) --}}
        @php
            $hasAdminShortcuts = auth()->user() && (auth()->user()->hasPermission('leave.request') || auth()->user()->hasPermission('leave.approve') || auth()->user()->hasPermission('leave.verify') || auth()->user()->hasPermission('attendance.corrections.approve') || auth()->user()->hasPermission('attendance.corrections.verify') || auth()->user()->hasPermission('daily_activities.view_department'));
        @endphp

        @if ($hasAdminShortcuts)
            <div class="mb-2">
                <h6 class="mb-1 text-uppercase font-10 opacity-60">Shortcuts</h6>

                @if (auth()->user() && auth()->user()->hasPermission('leave.request'))
                    <a href="{{ route('employee.leave.requests.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('employee.leave.requests.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-plus color-red-dark font-18"></i>
                        <div class="ps-3">Pengajuan Izin</div>
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
                        <a href="{{ route($leaveRoute) }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.leave-requests.*') ? 'active' : '' }}">
                            <i class="bi bi-person-check color-red-dark font-18"></i>
                            <div class="ps-3">Persetujuan Izin</div>
                            @if (isset($pendingLeaveCount) && $pendingLeaveCount > 0)
                                <span class="badge bg-danger ms-auto">{{ $pendingLeaveCount }}</span>
                            @endif
                        </a>
                    @endif
                @endif

                @if (auth()->user() && ((auth()->user()->hasPermission('attendance.corrections.approve') && auth()->user()->isManager()) || auth()->user()->hasPermission('attendance.corrections.verify')))
                    <a href="{{ route('admin.attendance-corrections.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.attendance-corrections.*') ? 'active' : '' }}">
                        <i class="bi bi-check2-square color-green-dark font-18"></i>
                        <div class="ps-3">Persetujuan Koreksi</div>
                    </a>
                @endif

                @if (auth()->user() && auth()->user()->hasPermission('daily_activities.view_department'))
                    <a href="{{ route('admin.daily-activities.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('admin.daily-activities.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-line color-blue-dark font-18"></i>
                        <div class="ps-3">Laporan Daily Activity</div>
                    </a>
                @endif
            </div>
        @endif

        {{-- Profile / Logout --}}
        <div class="mb-2">
            <h6 class="mb-1 text-uppercase font-10 opacity-60">Akun</h6>
            <a href="{{ route('employee.profile.index') }}" class="d-flex py-1 align-items-center {{ request()->routeIs('employee.profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-circle color-purple-dark font-18"></i>
                <div class="ps-3">Profil</div>
            </a>
        </div>

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
