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
            <p class="color-white font-12 opacity-70 mb-n1">{{ $employee->department->name ?? 'Karyawan' }}</p>
        </div>
        <div class="card-overlay bg-gradient-fade rounded-0"></div>
    </div>

    <style>
        .menu-list a i:first-child {
            width: 30px;
            text-align: center;
            margin-right: 10px;
            font-size: 16px;
            display: inline-block;
        }
        .menu-list a span {
            font-weight: 500;
        }
        .menu-list a i:last-child {
            margin-left: auto;
            opacity: 0.3;
            font-size: 10px;
        }
    </style>
    <div class="menu-list">
        <div class="card card-style rounded-m p-2 py-2 mb-0">
            <a href="{{ route('dashboard') }}" id="nav-homes" class="d-flex align-items-center {{ request()->routeIs('dashboard') ? 'active-item' : '' }}">
                <i class="gradient-blue shadow-bg shadow-bg-xs bi bi-house-fill color-white"></i>
                <span>Dashboard</span>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('employee.attendance.index') }}" id="nav-attendance" class="d-flex align-items-center {{ request()->routeIs('employee.attendance.*') ? 'active-item' : '' }}">
                <i class="gradient-green shadow-bg shadow-bg-xs bi bi-card-checklist color-white"></i>
                <span>Absensi</span>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('employee.attendance.history') }}" id="nav-history" class="d-flex align-items-center {{ request()->routeIs('employee.attendance.history') ? 'active-item' : '' }}">
                <i class="gradient-orange shadow-bg shadow-bg-xs bi bi-clock-history color-white"></i>
                <span>Riwayat Absensi</span>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('employee.schedule.index') }}" id="nav-schedule" class="d-flex align-items-center {{ request()->routeIs('employee.schedule.*') ? 'active-item' : '' }}">
                <i class="gradient-magenta shadow-bg shadow-bg-xs bi bi-calendar-check color-white"></i>
                <span>Jadwal Kerja</span>
                <i class="bi bi-chevron-right"></i>
            </a>

            @if (auth()->user() && auth()->user()->hasPermission('leave.request'))
                <a href="{{ route('employee.leave.requests.index') }}" class="d-flex align-items-center {{ request()->routeIs('employee.leave.requests.*') ? 'active-item' : '' }}">
                    <i class="gradient-red shadow-bg shadow-bg-xs bi bi-calendar-plus color-white"></i>
                    <span>Pengajuan Izin</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif

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
                    <a href="{{ route($correctionsRoute) }}" id="nav-corrections" class="d-flex align-items-center {{ request()->routeIs('employee.attendance.corrections.*') ? 'active-item' : '' }}">
                        <i class="gradient-blue shadow-bg shadow-bg-xs bi bi-pencil-square color-white"></i>
                        <span>Koreksi Absensi</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @endif
            @endif

            @if (auth()->user() && (auth()->user()->hasPermission('daily_activities.create') || auth()->user()->hasPermission('daily_activities.view_own')))
                <a href="{{ route('employee.daily-activities.index') }}" id="nav-daily" class="d-flex align-items-center {{ request()->routeIs('employee.daily-activities.*') ? 'active-item' : '' }}">
                    <i class="gradient-teal shadow-bg shadow-bg-xs bi bi-clipboard-data color-white"></i>
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
            <div class="card card-style rounded-m p-2 py-2 mb-0">
                @if (auth()->user() && (auth()->user()->hasPermission('leave.approve') || auth()->user()->hasPermission('leave.verify')))
                    @php
                        $leaveRoute = null;
                        if (\Illuminate\Support\Facades\Route::has('admin.leave-requests.index')) {
                            $leaveRoute = 'admin.leave-requests.index';
                        }
                    @endphp

                    @if ($leaveRoute)
                        <a href="{{ route($leaveRoute) }}" class="d-flex align-items-center {{ request()->routeIs('admin.leave-requests.*') ? 'active-item' : '' }}">
                            <i class="gradient-red shadow-bg shadow-bg-xs bi bi-person-check color-white"></i>
                            <span>Persetujuan Izin</span>
                            @if (isset($pendingLeaveCount) && $pendingLeaveCount > 0)
                                <em class="badge badge-s bg-red-dark ms-auto me-2">{{ $pendingLeaveCount }}</em>
                            @endif
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @endif
                @endif

                @if (auth()->user() && ((auth()->user()->hasPermission('attendance.corrections.approve') && auth()->user()->isManager()) || auth()->user()->hasPermission('attendance.corrections.verify')))
                    <a href="{{ route('admin.attendance-corrections.index') }}" class="d-flex align-items-center {{ request()->routeIs('admin.attendance-corrections.*') ? 'active-item' : '' }}">
                        <i class="gradient-green shadow-bg shadow-bg-xs bi bi-check2-square color-white"></i>
                        <span>Persetujuan Koreksi</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @endif

                @if (auth()->user() && auth()->user()->hasPermission('daily_activities.view_department'))
                    <a href="{{ route('admin.daily-activities.index') }}" class="d-flex align-items-center {{ request()->routeIs('admin.daily-activities.*') ? 'active-item' : '' }}">
                        <i class="gradient-blue shadow-bg shadow-bg-xs bi bi-bar-chart-line color-white"></i>
                        <span>Laporan Activity</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @endif
            </div>
        </div>
    @endif

    <span class="menu-divider mt-4">AKUN</span>
    <div class="menu-list">
        <div class="card card-style rounded-m p-2 py-2 mb-0">
            <a href="{{ route('employee.profile.index') }}" class="d-flex align-items-center {{ request()->routeIs('employee.profile.index') ? 'active-item' : '' }}">
                <i class="gradient-mint shadow-bg shadow-bg-xs bi bi-person-circle color-white"></i>
                <span>Profil Saya</span>
                <i class="bi bi-chevron-right"></i>
            </a>
            @php
                // If any required employee profile field is missing, show quick link to complete profile
                $needsComplete = false;
                $empCheck = $employee ?? optional(auth()->user())->employee;
                if (!$empCheck) {
                    $needsComplete = true;
                } else {
                    // Require all important profile fields to be filled
                    $required = [
                        'employee_id','full_name','department_id','position_id','work_schedule_id',
                        'email','phone','mobile','address','address_ktp','address_domisili','nik_ktp',
                        'gender','birth_place','birth_date','marital_status','residence_status',
                        'hire_date','photo','education_history','training_history','family_structure','emergency_contact'
                    ];

                    foreach ($required as $f) {
                        $val = $empCheck->{$f} ?? null;
                        if (is_array($val) || $val instanceof \Illuminate\Contracts\Support\Arrayable) {
                            if (empty($val)) { $needsComplete = true; break; }
                        } else {
                            if (empty($val) && !is_numeric($val)) { $needsComplete = true; break; }
                        }
                    }
                }
            @endphp
            @if ($needsComplete)
                <style>
                    .complete-profile-card {
                        background: linear-gradient(135deg, #fff9e6 0%, #fff2cc 100%) !important;
                        border: 1px solid #ffe599 !important;
                        transition: all 0.2s ease;
                    }
                    .complete-profile-card:hover {
                        background: linear-gradient(135deg, #fff2cc 0%, #ffeb99 100%) !important;
                    }
                </style>
                <a href="{{ route('employee.profile.complete') }}" class="complete-profile-card mt-3 rounded-s p-2 d-flex align-items-center border-0">
                    <i class="gradient-yellow shadow-bg shadow-bg-xs bi bi-person-check color-white"></i>
                    <span class="font-13 color-yellow-dark font-600">Lengkapi Profil</span>
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
