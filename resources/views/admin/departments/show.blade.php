@section('header')
    @include('admin.header', [
        'title' => 'Detail Departemen',
        'backUrl' => route('admin.departments.index'),
        'rightHtml' => '<a href="' . route('admin.departments.edit', $department) . '"><i class="bi bi-pencil font-13 color-highlight"></i></a>',
    ])
@endsection

@section('footer')
@endsection
@extends('layouts.admin')

@section('title', 'Detail Departemen')

@push('styles')
    <style>
        .stat-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .employee-card {
            transition: all 0.2s ease-in-out;
            border-left: 4px solid #e9ecef;
        }

        .employee-card:hover {
            background-color: #f8f9fa;
            border-left-color: #0d6efd;
        }

        .employee-card.manager-card {
            border-left-color: #198754;
            background-color: #f8fff9;
        }

        .page-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }

        .info-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    @include('admin.partials.alerts')
    <div class="card card-style shadow-m mb-4">
        <div class="content d-flex align-items-center justify-content-between">
            <div>
                <h3 class="font-700 mb-1 color-dark-dark"><i class="bi bi-building me-2 color-blue-dark"></i>{{ $department->name }}</h3>
                <p class="mb-0 font-12 opacity-70">Detail informasi departemen dan karyawan</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-sm bg-blue-dark text-white rounded-s"><i class="bi bi-pencil me-1"></i>Edit</a>
                <a href="{{ route('admin.departments.index') }}" class="btn btn-sm bg-theme text-dark rounded-s"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
            </div>
        </div>
    </div>

    <!-- Department Info -->
    <div class="card card-style shadow-m mb-4">
        <div class="content">
            <h5 class="font-700 mb-3 color-blue-dark"><i class="bi bi-info-circle me-2"></i>Informasi Departemen</h5>
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-1">{{ $department->name }}</h4>
                            <span class="badge {{ $department->is_active ? 'bg-green-dark text-white' : 'bg-secondary text-dark' }} font-12 px-3 py-2">
                                <i class="bi bi-circle-fill me-1"></i>{{ $department->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Dibuat</small>
                            <strong>{{ $department->created_at->format('d M Y') }}</strong>
                        </div>
                    </div>

                    @if ($department->description)
                        <div class="alert bg-light border-0 mb-4">
                            <i class="bi bi-quote text-muted me-2"></i>
                            <em>{{ $department->description }}</em>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card card-style bg-blue-light h-100 p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                        <i class="bi bi-person-badge color-white font-14"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Manager Departemen</small>
                                        @if ($department->manager)
                                            <strong class="color-blue-dark">{{ $department->manager->user->name }}</strong>
                                            <br><small class="text-muted">{{ $department->manager->employee_id }}</small>
                                        @else
                                            <em class="color-orange-dark">Belum ada manager</em>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-style bg-green-light h-100 p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                        <i class="bi bi-people-fill color-white font-14"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Total Karyawan</small>
                                        <strong class="color-green-dark h5 mb-0">{{ $department->employees->count() }} orang</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-style stat-card h-100">
                <div class="card-body text-center p-3">
                    <div class="bg-blue-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 45px; height: 45px;">
                        <i class="bi bi-people-fill color-white font-18"></i>
                    </div>
                    <h4 class="mb-1 color-blue-dark">{{ $department->employees->count() }}</h4>
                    <p class="mb-0 font-11 opacity-70">Total Karyawan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-style stat-card h-100">
                <div class="card-body text-center p-3">
                    <div class="bg-green-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 45px; height: 45px;">
                        <i class="bi bi-briefcase color-white font-18"></i>
                    </div>
                    <h4 class="mb-1 color-green-dark">{{ $department->employees->whereNotNull('position_id')->count() }}</h4>
                    <p class="mb-0 font-11 opacity-70">Ada Posisi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-style stat-card h-100">
                <div class="card-body text-center p-3">
                    <div class="bg-orange-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 45px; height: 45px;">
                        <i class="bi bi-exclamation-triangle color-white font-18"></i>
                    </div>
                    <h4 class="mb-1 color-orange-dark">{{ $department->employees->whereNull('position_id')->count() }}</h4>
                    <p class="mb-0 font-11 opacity-70">Tanpa Posisi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-style stat-card h-100">
                <div class="card-body text-center p-3">
                    <div class="bg-teal-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 45px; height: 45px;">
                        <i class="bi bi-diagram-3 color-white font-18"></i>
                    </div>
                    <h4 class="mb-1 color-teal-dark">{{ $department->positions->count() }}</h4>
                    <p class="mb-0 font-11 opacity-70">Total Posisi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees List -->
    <div class="card card-style shadow-sm">
        <div class="card-header bg-secondary-dark text-white">
            <h5 class="mb-0"><i class="fa fa-users me-2"></i>Daftar Karyawan ({{ $department->employees->count() }})</h5>
        </div>
        <div class="card-body p-0">
            @forelse($department->employees as $employee)
                <div class="employee-card {{ $employee->id == $department->manager_id ? 'manager-card' : '' }} p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <h6 class="mb-0 me-2">{{ $employee->user->name }}</h6>
                                @if ($employee->id == $department->manager_id)
                                    <span class="badge bg-success-dark text-white">
                                        <i class="fa fa-crown me-1"></i>Manager
                                    </span>
                                @endif
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">ID Karyawan</small>
                                    <strong>{{ $employee->employee_id }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Email</small>
                                    <span class="text-primary">{{ $employee->user->email }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Posisi</small>
                                    @if ($employee->getPositionName())
                                        <span class="text-success">
                                            <i class="fa fa-briefcase me-1"></i>{{ $employee->getPositionName() }}
                                            @if (is_object($employee->position) && isset($employee->position->level))
                                                <small class="text-muted">(Level {{ $employee->position->level }})</small>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-warning">
                                            <i class="fa fa-exclamation-triangle me-1"></i>Belum ada posisi
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-secondary text-white btn-sm" type="button" data-bs-toggle="dropdown">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.employees.show', $employee) }}">
                                        <i class="fa fa-eye me-2 text-primary"></i>Detail Karyawan
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.employees.edit', $employee) }}">
                                        <i class="fa fa-edit me-2 text-warning"></i>Edit Karyawan
                                    </a>
                                </li>
                                @if ($employee->id != $department->manager_id)
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.departments.set-manager', $department) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="manager_id" value="{{ $employee->id }}">
                                            <button type="submit" class="dropdown-item" onclick="return confirm('Jadikan {{ $employee->user->name }} sebagai manager?')">
                                                <i class="fa fa-user-tie me-2 text-success"></i>Jadikan Manager
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fa fa-users font-30 text-muted"></i>
                    </div>
                    <h6 class="text-muted mb-3">Belum ada karyawan di departemen ini</h6>
                    <p class="text-muted mb-3">Mulai dengan menambahkan karyawan pertama untuk departemen {{ $department->name }}</p>
                    <a href="{{ route('admin.employees.create') }}?department_id={{ $department->id }}" class="btn btn-primary text-white">
                        <i class="fa fa-plus me-2"></i>Tambah Karyawan
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Positions in Department -->
    @if ($department->positions->count() > 0)
        <div class="card card-style mt-4 shadow-sm">
            <div class="card-header bg-info-dark text-white">
                <h5 class="mb-0"><i class="fa fa-sitemap me-2"></i>Posisi di Departemen ({{ $department->positions->count() }})</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @foreach ($department->positions as $position)
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">{{ $position->name }}</h6>
                                        <span class="badge {{ $position->is_active ? 'bg-success-dark text-white' : 'bg-secondary-dark text-white' }}">
                                            {{ $position->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>

                                    <div class="row g-2 small text-muted">
                                        <div class="col-6">
                                            <i class="fa fa-layer-group me-1"></i>Level {{ $position->level }}
                                        </div>
                                        <div class="col-6">
                                            <i class="fa fa-users me-1"></i>{{ $position->employees->count() }} karyawan
                                        </div>
                                        @if ($position->min_salary || $position->max_salary)
                                            <div class="col-12">
                                                <i class="fa fa-money-bill me-1"></i>
                                                Rp {{ number_format($position->min_salary ?? 0) }} - Rp {{ number_format($position->max_salary ?? 0) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.positions.create') }}?department_id={{ $department->id }}" class="btn btn-primary text-white">
                            <i class="fa fa-plus me-2"></i>Tambah Posisi
                        </a>
                        <a href="{{ route('admin.positions.index') }}?department={{ $department->id }}" class="btn btn-secondary text-white">
                            <i class="fa fa-list me-2"></i>Lihat Semua Posisi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card card-style mt-4 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fa fa-sitemap font-30 text-muted"></i>
                </div>
                <h6 class="text-muted mb-3">Belum ada posisi di departemen ini</h6>
                <p class="text-muted mb-3">Buat struktur organisasi yang jelas dengan menambahkan posisi-posisi yang dibutuhkan</p>
                <a href="{{ route('admin.positions.create') }}?department_id={{ $department->id }}" class="btn btn-primary text-white">
                    <i class="fa fa-plus me-2"></i>Tambah Posisi Pertama
                </a>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll for internal links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Hover effects for employee cards
            document.querySelectorAll('.employee-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });

            // Enhanced dropdowns
            document.querySelectorAll('.dropdown-toggle').forEach(dropdown => {
                dropdown.addEventListener('click', function() {
                    // Add ripple effect
                    const ripple = document.createElement('span');
                    ripple.className = 'ripple';
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 300);
                });
            });

            // Confirmation dialogs with better UX
            document.querySelectorAll('form[action*="set-manager"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const employeeName = this.querySelector('button').textContent.match(/Jadikan (.+) sebagai/)[1];

                    if (confirm(`Konfirmasi perubahan manager\n\nApakah Anda yakin ingin menjadikan ${employeeName} sebagai manager departemen?\n\nManager sebelumnya akan kehilangan status manager.`)) {
                        // Show loading state
                        const button = this.querySelector('button');
                        const originalText = button.innerHTML;
                        button.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Mengubah...';
                        button.disabled = true;

                        this.submit();
                    }
                });
            });

            // Auto-refresh with visibility check (optional)
            let refreshInterval;

            function startAutoRefresh() {
                refreshInterval = setInterval(function() {
                    if (document.visibilityState === 'visible') {
                        // Optional: Add a subtle indicator that data is being refreshed
                        const indicator = document.createElement('div');
                        indicator.className = 'position-fixed top-0 end-0 m-3 alert alert-info alert-sm';
                        indicator.innerHTML = '<i class="fa fa-sync fa-spin me-2"></i>Memperbarui data...';
                        indicator.style.zIndex = '9999';
                        document.body.appendChild(indicator);

                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                }, 300000); // 5 minutes
            }

            // Only start auto-refresh if user wants it (you can make this optional)
            // startAutoRefresh();

            // Stop auto-refresh when page is not visible
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'hidden' && refreshInterval) {
                    clearInterval(refreshInterval);
                }
            });

            // Search functionality for employees (if many employees)
            @if ($department->employees->count() > 5)
                const employeeCards = document.querySelectorAll('.employee-card');
                if (employeeCards.length > 5) {
                    // Add search box
                    const employeeSection = document.querySelector('.card-header h5').parentElement;
                    const searchHTML = `
                        <div class="input-group input-group-sm mt-2">
                            <input type="text" class="form-control" id="employee-search" placeholder="Cari karyawan...">
                            <span class="input-group-text">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                    `;
                    employeeSection.insertAdjacentHTML('beforeend', searchHTML);

                    // Search functionality
                    document.getElementById('employee-search').addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        employeeCards.forEach(card => {
                            const name = card.querySelector('h6').textContent.toLowerCase();
                            const email = card.querySelector('.text-primary').textContent.toLowerCase();
                            const empId = card.querySelector('strong').textContent.toLowerCase();

                            if (name.includes(searchTerm) || email.includes(searchTerm) || empId.includes(searchTerm)) {
                                card.style.display = '';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    });
                }
            @endif

            // Tooltips for better UX
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
