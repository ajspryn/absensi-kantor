@extends('layouts.admin')

@section('title', 'Manajemen Jadwal Kerja')

@section('header')
    @include('admin.header', [
        'title' => 'Manajemen Jadwal Kerja',
        'backUrl' => route('admin.settings.index'),
        'rightHtml' => '<a href="' . route('admin.work-schedules.create') . '" class=""><i class="bi bi-plus font-13 color-highlight"></i></a>',
    ])
@endsection

@section('content')
    @include('admin.partials.alerts')
    <div class="px-2 px-md-4 py-2 py-md-4">
        <style>
            .card-uniform,
            .card.card-style {
                width: 100%;
                max-width: none;
                margin: 0 0 1.5rem 0;
            }

            .schedule-card {
                border-left: 4px solid #007bff;
                transition: all 0.3s ease;
            }

            .schedule-card.active {
                border-left-color: #28a745;
                background: linear-gradient(135deg, #e8f5e8 0%, #f8fff8 100%);
            }

            .schedule-card.inactive {
                border-left-color: #6c757d;
                background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            }

            .schedule-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }

            .schedule-time {
                background: linear-gradient(135deg, #007bff, #0056b3);
                color: white;
                border-radius: 8px;
                padding: 8px 12px;
                font-weight: 600;
                display: inline-block;
                margin-right: 8px;
            }

            .schedule-type-badge {
                background: linear-gradient(135deg, #17a2b8, #138496);
                color: white;
                border-radius: 12px;
                padding: 4px 12px;
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
            }

            .stats-card {
                background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
                border: none;
                border-radius: 12px;
                transition: all 0.3s ease;
            }

            .stats-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                margin-bottom: 10px;
            }

            .quick-action-btn {
                background: linear-gradient(135deg, #007bff, #0056b3);
                border: none;
                border-radius: 10px;
                color: white;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            }

            .quick-action-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
                color: white;
            }

            .quick-action-btn.btn-orange {
                background: linear-gradient(135deg, #fd7e14, #e55a00);
                box-shadow: 0 4px 15px rgba(253, 126, 20, 0.3);
            }

            .quick-action-btn.btn-orange:hover {
                box-shadow: 0 6px 20px rgba(253, 126, 20, 0.4);
            }
        </style>



        <!-- Statistics Cards -->
        <div class="card card-style card-uniform stats-card">
            <div class="content">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #007bff, #0056b3); color: white;">
                        <i class="bi bi-bar-chart"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="font-700 mb-0 font-16">Statistik Jadwal Kerja</h5>
                        <p class="mb-0 font-11 opacity-70">Ringkasan data jadwal kerja sistem</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center p-3" style="background: linear-gradient(135deg, #e3f2fd, #f8faff); border-radius: 12px;">
                            <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #2196f3, #1976d2); color: white;">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <h3 class="font-700 font-20 mb-1" style="color: #1976d2;">{{ $totalSchedules }}</h3>
                            <p class="mb-0 font-11 font-600" style="color: #1976d2;">Total Jadwal</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3" style="background: linear-gradient(135deg, #e8f5e9, #f1f8f2); border-radius: 12px;">
                            <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #4caf50, #388e3c); color: white;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h3 class="font-700 font-20 mb-1" style="color: #388e3c;">{{ $activeSchedules }}</h3>
                            <p class="mb-0 font-11 font-600" style="color: #388e3c;">Jadwal Aktif</p>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-6">
                        <div class="text-center p-3" style="background: linear-gradient(135deg, #fff3e0, #fffaf6); border-radius: 12px;">
                            <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #ff9800, #f57c00); color: white;">
                                <i class="bi bi-people"></i>
                            </div>
                            <h3 class="font-700 font-20 mb-1" style="color: #f57c00;">{{ $employeesWithSchedule }}</h3>
                            <p class="mb-0 font-11 font-600" style="color: #f57c00;">Karyawan Terjadwal</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3" style="background: linear-gradient(135deg, #f3e5f5, #faf4fc); border-radius: 12px;">
                            <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #9c27b0, #7b1fa2); color: white;">
                                <i class="bi bi-clock"></i>
                            </div>
                            <h3 class="font-700 font-20 mb-1" style="color: #7b1fa2;">{{ $flexibleSchedules }}</h3>
                            <p class="mb-0 font-11 font-600" style="color: #7b1fa2;">Jadwal Fleksibel</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card card-style card-uniform stats-card">
            <div class="content">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #28a745, #1e7e34); color: white;">
                        <i class="bi bi-lightning"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="font-700 mb-0 font-16">Aksi Cepat</h5>
                        <p class="mb-0 font-11 opacity-70">Kelola jadwal kerja dengan mudah</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('admin.work-schedules.create') }}" class="btn btn-s rounded-s quick-action-btn w-100 py-3">
                            <i class="bi bi-plus-circle font-16 d-block mb-1"></i>
                            <span class="font-11 font-600">Buat Jadwal</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.work-schedules.assign') }}" class="btn btn-s rounded-s quick-action-btn btn-orange w-100 py-3">
                            <i class="bi bi-person-check font-16 d-block mb-1"></i>
                            <span class="font-11 font-600">Tugaskan</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedules List -->
        @if ($schedules->count() > 0)
            <div class="d-flex align-items-center mb-3">
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #6f42c1, #5a32a3); color: white;">
                    <i class="bi bi-list-ul"></i>
                </div>
                <div>
                    <h5 class="font-700 mb-0 font-16">Daftar Jadwal Kerja</h5>
                    <p class="mb-0 font-11 opacity-70">{{ $schedules->count() }} jadwal tersedia</p>
                </div>
            </div>

            @foreach ($schedules as $schedule)
                <div class="card card-style schedule-card {{ $schedule->is_active ? 'active' : 'inactive' }}">
                    <div class="content">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="font-700 mb-0 font-16 me-2">{{ $schedule->name }}</h5>
                                    @if ($schedule->is_active)
                                        <span class="badge bg-success text-white badge-sm">
                                            <i class="bi bi-check-circle me-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-white badge-sm">
                                            <i class="bi bi-x-circle me-1"></i>Nonaktif
                                        </span>
                                    @endif
                                </div>

                                @if ($schedule->user)
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                            <i class="bi bi-person color-white font-11"></i>
                                        </div>
                                        <p class="mb-0 font-12 color-theme font-600">{{ $schedule->user->name }}</p>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                            <i class="bi bi-people color-white font-11"></i>
                                        </div>
                                        <p class="mb-0 font-12 color-info font-600">Template Jadwal</p>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <span class="schedule-time">
                                        <i class="bi bi-clock me-1"></i>{{ $schedule->getWorkingHoursRange() }}
                                    </span>
                                    <span class="schedule-type-badge">{{ $schedule->getScheduleType() }}</span>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center p-2 rounded" style="background: rgba(0,123,255,0.1);">
                                            <i class="bi bi-calendar-week color-primary me-2"></i>
                                            <span class="font-12 color-primary font-600">{{ $schedule->getWorkDaysFormatted() }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center p-2 rounded" style="background: rgba(40,167,69,0.1);">
                                            <i class="bi bi-hourglass color-success me-2"></i>
                                            <span class="font-11 color-success font-600">{{ $schedule->getTotalHoursFormatted() }}</span>
                                        </div>
                                    </div>
                                    @if ($schedule->break_start_time && $schedule->break_end_time)
                                        <div class="col-6">
                                            <div class="d-flex align-items-center p-2 rounded" style="background: rgba(255,193,7,0.1);">
                                                <i class="bi bi-cup color-warning me-2"></i>
                                                <span class="font-11 color-warning font-600">{{ $schedule->break_start_time }} - {{ $schedule->break_end_time }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    @if ($schedule->is_flexible)
                                        <span class="badge" style="background: linear-gradient(135deg, #9c27b0, #7b1fa2); color: white;">
                                            <i class="bi bi-clock-history me-1"></i>Fleksibel
                                        </span>
                                    @endif
                                    @if ($schedule->location_required)
                                        <span class="badge" style="background: linear-gradient(135deg, #ff5722, #d84315); color: white;">
                                            <i class="bi bi-geo-alt me-1"></i>Wajib Lokasi
                                        </span>
                                    @endif
                                    @if ($schedule->late_tolerance > 0)
                                        <span class="badge" style="background: linear-gradient(135deg, #795548, #5d4037); color: white;">
                                            <i class="bi bi-clock me-1"></i>Toleransi {{ $schedule->late_tolerance }}m
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" style="background: rgba(0,0,0,0.05); border-radius: 8px;">
                                    <i class="bi bi-three-dots-vertical color-theme"></i>
                                </button>
                                <ul class="dropdown-menu shadow-lg" style="border-radius: 12px;">
                                    <li><a class="dropdown-item" href="{{ route('admin.work-schedules.show', $schedule) }}">
                                            <i class="bi bi-eye me-2 color-primary"></i>Lihat Detail
                                        </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.work-schedules.edit', $schedule) }}">
                                            <i class="bi bi-pencil me-2 color-warning"></i>Edit
                                        </a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.work-schedules.toggle-status', $schedule) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item">
                                                @if ($schedule->is_active)
                                                    <i class="bi bi-toggle-off me-2 color-warning"></i>Nonaktifkan
                                                @else
                                                    <i class="bi bi-toggle-on me-2 color-success"></i>Aktifkan
                                                @endif
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.work-schedules.destroy', $schedule) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i>Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        @if ($schedule->description)
                            <div class="p-3 rounded mt-3" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-left: 3px solid #6c757d;">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-info-circle color-secondary me-2 mt-1"></i>
                                    <p class="mb-0 font-12 color-secondary">{{ $schedule->description }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            @if ($schedules->hasPages())
                <div class="card card-style stats-card">
                    <div class="content">
                        {{ $schedules->links() }}
                    </div>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="card card-style">
                <div class="content text-center py-5">
                    <div class="stat-icon mx-auto mb-3" style="background: linear-gradient(135deg, #e9ecef, #f8f9fa); color: #6c757d;">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <h4 class="font-700 mb-2">Belum ada jadwal kerja</h4>
                    <p class="color-gray-dark mb-4">Mulai buat jadwal kerja untuk mengatur waktu kerja karyawan</p>
                    <a href="{{ route('admin.work-schedules.create') }}" class="btn btn-m rounded-s quick-action-btn font-13 font-600">
                        <i class="bi bi-plus-circle me-2"></i>Buat Jadwal Pertama
                    </a>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide success/error messages after 3 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }, 3000);
            });

            // Add hover effects to schedule cards
            const scheduleCards = document.querySelectorAll('.schedule-card');
            scheduleCards.forEach(function(card) {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
                });
            });

            // Add click animation to action buttons
            const actionButtons = document.querySelectorAll('.quick-action-btn');
            actionButtons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });
        });
    </script>
@endpush
