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

        <a href="{{ route('dashboard') }}" class="d-flex py-1">
            <div class="align-self-center">
                <i class="bi bi-house-door color-blue-dark font-16"></i>
            </div>
            <div class="align-self-center ps-3">
                <h5 class="pt-1 mb-0">Dashboard</h5>
            </div>
        </a>

        <a href="{{ route('employee.attendance.index') }}" class="d-flex py-1">
            <div class="align-self-center">
                <i class="bi bi-camera color-green-dark font-16"></i>
            </div>
            <div class="align-self-center ps-3">
                <h5 class="pt-1 mb-0">Absensi</h5>
            </div>
        </a>

        <a href="{{ route('employee.schedule.index') }}" class="d-flex py-1">
            <div class="align-self-center">
                <i class="bi bi-calendar-week color-purple-dark font-16"></i>
            </div>
            <div class="align-self-center ps-3">
                <h5 class="pt-1 mb-0">Jadwal Kerja</h5>
            </div>
        </a>

        <a href="{{ route('employee.attendance.history') }}" class="d-flex py-1">
            <div class="align-self-center">
                <i class="bi bi-clock-history color-orange-dark font-16"></i>
            </div>
            <div class="align-self-center ps-3">
                <h5 class="pt-1 mb-0">Riwayat Absensi</h5>
            </div>
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
                <a href="{{ route($correctionsRoute) }}" class="d-flex py-1">
                    <div class="align-self-center position-relative">
                        <i class="bi bi-pencil-square color-blue-dark font-16"></i>
                    </div>
                    <div class="align-self-center ps-3">
                        <h5 class="pt-1 mb-0">Koreksi Absensi Saya</h5>
                    </div>
                </a>
            @endif
        @endif

        @if (auth()->user() && ((auth()->user()->hasPermission('attendance.corrections.approve') && auth()->user()->isManager()) || auth()->user()->hasPermission('attendance.corrections.verify')))
            <a href="{{ route('admin.attendance-corrections.index') }}" class="d-flex py-1">
                <div class="align-self-center position-relative">
                    <i class="bi bi-check2-square color-green-dark font-16"></i>
                </div>
                <div class="align-self-center ps-3">
                    <h5 class="pt-1 mb-0">Persetujuan Koreksi Absensi</h5>
                </div>
            </a>
        @endif

        <a href="{{ route('employee.profile.index') }}" class="d-flex py-1">
            <div class="align-self-center">
                <i class="bi bi-person-circle color-purple-dark font-16"></i>
            </div>
            <div class="align-self-center ps-3">
                <h5 class="pt-1 mb-0">Profil</h5>
            </div>
        </a>

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
