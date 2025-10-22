@extends('layouts.app')

@section('title', 'Jadwal Kerja Saya')

@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('dashboard') }}"><i class="bi bi-arrow-left font-16 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-15">Jadwal Kerja Saya</a>
        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#menu-main"><i class="bi bi-list font-16 color-theme"></i></a>
    </div>
@endsection

@section('sidebar')
    @include('employee.sidebar')
@endsection

@section('footer')
    @include('employee.footer')
@endsection

@push('styles')
    <style>
        .schedule-card {
            background: linear-gradient(135deg, #e8f5e8 0%, #f8fff8 100%);
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(40, 167, 69, 0.15);
            transition: all 0.3s ease;
        }

        .schedule-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(40, 167, 69, 0.25);
        }

        .schedule-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 12px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
        }

        .schedule-detail-card {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border: 1px solid rgba(40, 167, 69, 0.1);
            border-radius: 12px;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .schedule-detail-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .time-display {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            font-weight: 600;
        }

        .hours-display {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: white;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            font-weight: 600;
        }

        .info-badge {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            margin: 4px;
        }

        .feature-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            margin: 4px;
        }

        .feature-badge.flexible {
            background: linear-gradient(135deg, #9c27b0, #7b1fa2);
            color: white;
        }

        .feature-badge.location {
            background: linear-gradient(135deg, #ff5722, #d84315);
            color: white;
        }

        .no-schedule-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px dashed #dee2e6;
            border-radius: 16px;
            padding: 40px 20px;
            text-align: center;
        }

        .schedule-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .workdays-display {
            background: linear-gradient(135deg, #e3f2fd, #f8faff);
            border: 1px solid rgba(33, 150, 243, 0.2);
            border-radius: 12px;
            padding: 15px;
            margin: 15px 0;
        }

        .break-time-display {
            background: linear-gradient(135deg, #fff3e0, #fffaf6);
            border: 1px solid rgba(255, 152, 0, 0.2);
            border-radius: 12px;
            padding: 15px;
            margin: 15px 0;
        }
    </style>
@endpush

@section('content')

    <!-- Current Schedule Card -->
    @if ($currentSchedule)
        <div class="card card-style schedule-card">
            <div class="content">
                <div class="schedule-header">
                    <div class="d-flex align-items-center mb-3">
                        <div class="schedule-icon me-3" style="width: 50px; height: 50px; font-size: 20px;">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <h5 class="font-700 mb-0 font-17">Jadwal Kerja Aktif</h5>
                            <p class="mb-0 font-12 opacity-80">Jadwal kerja yang sedang berlaku saat ini</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="font-700 mb-0 font-18">{{ $currentSchedule->name }}</h4>
                        <span class="info-badge">{{ $currentSchedule->getScheduleType() }}</span>
                    </div>

                    @if ($currentSchedule->description)
                        <p class="mb-0 font-13 opacity-90 mt-2">{{ $currentSchedule->description }}</p>
                    @endif
                </div>

                <!-- Working Hours -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="time-display">
                            <i class="bi bi-clock font-16 d-block mb-2"></i>
                            <p class="mb-1 font-12 font-600">{{ $currentSchedule->getWorkingHoursRange() }}</p>
                            <p class="mb-0 font-10 opacity-80">Jam Kerja</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hours-display">
                            <i class="bi bi-hourglass font-16 d-block mb-2"></i>
                            <p class="mb-1 font-12 font-600">{{ $currentSchedule->getTotalHoursFormatted() }}</p>
                            <p class="mb-0 font-10 opacity-80">Total Jam</p>
                        </div>
                    </div>
                </div>

                <!-- Work Days -->
                <div class="workdays-display">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-calendar-week color-primary font-14 me-2"></i>
                        <h6 class="font-600 mb-0 color-primary">Hari Kerja</h6>
                    </div>
                    <p class="mb-0 font-13 color-primary font-600">{{ $currentSchedule->getWorkDaysFormatted() }}</p>
                </div>

                @if ($currentSchedule->break_start_time && $currentSchedule->break_end_time)
                    <div class="break-time-display">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-cup color-warning font-14 me-2"></i>
                            <h6 class="font-600 mb-0 color-warning">Jam Istirahat</h6>
                        </div>
                        <p class="mb-0 font-13 color-warning font-600">{{ $currentSchedule->break_start_time }} - {{ $currentSchedule->break_end_time }}</p>
                    </div>
                @endif

                <!-- Additional Features -->
                @if ($currentSchedule->is_flexible || $currentSchedule->location_required || $currentSchedule->late_tolerance > 0)
                    <div class="mt-4">
                        <h6 class="font-600 mb-3 color-theme">Fitur Tambahan</h6>
                        <div class="d-flex flex-wrap">
                            @if ($currentSchedule->is_flexible)
                                <span class="feature-badge flexible">
                                    <i class="bi bi-clock-history me-1"></i>Jadwal Fleksibel
                                </span>
                            @endif
                            @if ($currentSchedule->location_required)
                                <span class="feature-badge location">
                                    <i class="bi bi-geo-alt me-1"></i>Wajib di Kantor
                                </span>
                            @endif
                            @if ($currentSchedule->late_tolerance > 0)
                                <span class="feature-badge" style="background: linear-gradient(135deg, #795548, #5d4037); color: white;">
                                    <i class="bi bi-clock me-1"></i>Toleransi {{ $currentSchedule->late_tolerance }}m
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Schedule Details -->
                @if ($currentSchedule->overtime_threshold || $currentSchedule->effective_date)
                    <div class="row g-2 mt-4">
                        @if ($currentSchedule->overtime_threshold)
                            <div class="col-6">
                                <div class="schedule-detail-card text-center">
                                    <i class="bi bi-clock-fill color-info font-14 d-block mb-1"></i>
                                    <p class="mb-0 font-11 color-info font-600">{{ $currentSchedule->overtime_threshold }}h</p>
                                    <p class="mb-0 font-10 color-info">Batas Lembur</p>
                                </div>
                            </div>
                        @endif
                        @if ($currentSchedule->effective_date)
                            <div class="col-6">
                                <div class="schedule-detail-card text-center">
                                    <i class="bi bi-calendar-event color-success font-14 d-block mb-1"></i>
                                    <p class="mb-0 font-11 color-success font-600">{{ $currentSchedule->effective_date->format('d/m/Y') }}</p>
                                    <p class="mb-0 font-10 color-success">Mulai Berlaku</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- No Current Schedule -->
        <div class="card card-style">
            <div class="content no-schedule-card">
                <div class="schedule-icon" style="background: linear-gradient(135deg, #dee2e6, #adb5bd); color: #6c757d;">
                    <i class="bi bi-calendar-x"></i>
                </div>
                <h4 class="font-700 mb-2">Belum Ada Jadwal Aktif</h4>
                <p class="color-gray-dark mb-3">Anda belum memiliki jadwal kerja yang aktif saat ini. Hubungi admin untuk mendapatkan jadwal kerja.</p>
                <div class="info-badge" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
                    <i class="bi bi-info-circle me-1"></i>Hubungi Admin
                </div>
            </div>
        </div>
    @endif

    <!-- Today's Schedule Status -->
    @if ($currentSchedule && $currentSchedule->isActiveToday())
        <div class="card card-style schedule-detail-card">
            <div class="content">
                <div class="d-flex align-items-center mb-3">
                    <div class="schedule-icon me-3" style="width: 45px; height: 45px; font-size: 18px; background: linear-gradient(135deg, #007bff, #0056b3);">
                        <i class="bi bi-calendar-day"></i>
                    </div>
                    <div>
                        <h5 class="font-700 mb-0 font-16">Status Hari Ini</h5>
                        <p class="mb-0 font-12 opacity-70">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>

                <div class="p-3 text-center" style="background: linear-gradient(135deg, #e3f2fd, #f8faff); border-radius: 12px;">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="schedule-detail-card">
                                <i class="bi bi-check-circle color-success font-18 d-block mb-1"></i>
                                <p class="mb-0 font-11 color-success font-600">Hari Kerja</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="schedule-detail-card">
                                <i class="bi bi-clock color-primary font-18 d-block mb-1"></i>
                                <p class="mb-0 font-11 color-primary font-600">{{ $currentSchedule->start_time }}</p>
                                <p class="mb-0 font-10 color-primary">Mulai</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="schedule-detail-card">
                                <i class="bi bi-clock-history color-info font-18 d-block mb-1"></i>
                                <p class="mb-0 font-11 color-info font-600">{{ $currentSchedule->end_time }}</p>
                                <p class="mb-0 font-10 color-info">Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($currentSchedule)
        <div class="card card-style schedule-detail-card">
            <div class="content">
                <div class="p-4 text-center" style="background: linear-gradient(135deg, #fff3e0, #fffaf6); border-radius: 12px;">
                    <div class="schedule-icon mx-auto mb-3" style="background: linear-gradient(135deg, #ff9800, #f57c00); color: white;">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <h6 class="font-600 mb-2 color-warning">Bukan Hari Kerja</h6>
                    <p class="mb-0 font-12 color-warning">Hari ini bukan hari kerja sesuai jadwal Anda</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Upcoming Schedules -->
    @if ($upcomingSchedules->count() > 0)
        <div class="card card-style schedule-detail-card">
            <div class="content">
                <div class="d-flex align-items-center mb-3">
                    <div class="schedule-icon me-3" style="width: 45px; height: 45px; font-size: 18px; background: linear-gradient(135deg, #9c27b0, #7b1fa2);">
                        <i class="bi bi-calendar-plus"></i>
                    </div>
                    <div>
                        <h5 class="font-700 mb-0 font-16">Jadwal Mendatang</h5>
                        <p class="mb-0 font-12 opacity-70">Jadwal yang akan berlaku di masa depan</p>
                    </div>
                </div>

                @foreach ($upcomingSchedules as $schedule)
                    <div class="p-3 mb-3 schedule-detail-card" style="background: linear-gradient(135deg, #f3e5f5, #faf4fc); border: 1px solid rgba(156,39,176,0.2);">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="font-600 mb-0 font-14" style="color: #7b1fa2;">{{ $schedule->name }}</h6>
                            <span class="info-badge" style="background: linear-gradient(135deg, #9c27b0, #7b1fa2);">{{ $schedule->getScheduleType() }}</span>
                        </div>

                        <div class="row g-2">
                            <div class="col-12 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-event me-2" style="color: #7b1fa2;"></i>
                                    <span class="font-12 font-600" style="color: #7b1fa2;">
                                        Berlaku mulai: {{ $schedule->effective_date?->translatedFormat('d F Y') ?? 'Segera' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock me-2" style="color: #7b1fa2;"></i>
                                    <span class="font-12" style="color: #7b1fa2;">{{ $schedule->start_time }} - {{ $schedule->end_time }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-week me-2" style="color: #7b1fa2;"></i>
                                    <span class="font-12" style="color: #7b1fa2;">{{ $schedule->getWorkDaysFormatted() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- All Schedules History -->
    @if ($allSchedules->count() > 0)
        <div class="card card-style schedule-detail-card">
            <div class="content">
                <div class="d-flex align-items-center mb-3">
                    <div class="schedule-icon me-3" style="width: 45px; height: 45px; font-size: 18px; background: linear-gradient(135deg, #6c757d, #545b62);">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h5 class="font-700 mb-0 font-16">Riwayat Jadwal</h5>
                        <p class="mb-0 font-12 opacity-70">Semua jadwal kerja yang pernah ditetapkan</p>
                    </div>
                </div>

                @foreach ($allSchedules as $schedule)
                    <div class="p-3 mb-2 schedule-detail-card {{ !$loop->last ? 'mb-3' : '' }}" style="background: linear-gradient(135deg, #f8f9fa, #ffffff); border: 1px solid rgba(108,117,125,0.2);">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="font-600 mb-0 font-13 me-2" style="color: #495057;">{{ $schedule->name }}</h6>
                                    @if ($schedule->is_active)
                                        <span class="info-badge" style="background: linear-gradient(135deg, #28a745, #1e7e34);">Aktif</span>
                                    @else
                                        <span class="info-badge" style="background: linear-gradient(135deg, #6c757d, #545b62);">Nonaktif</span>
                                    @endif
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clock me-2 font-11" style="color: #6c757d;"></i>
                                            <span class="font-11" style="color: #6c757d;">{{ $schedule->start_time }} - {{ $schedule->end_time }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar-week me-2 font-11" style="color: #6c757d;"></i>
                                            <span class="font-11" style="color: #6c757d;">{{ $schedule->getWorkDaysFormatted() }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if ($schedule->effective_date)
                                    <p class="mb-0 font-10 mt-2" style="color: #6c757d; opacity: 0.8;">
                                        Berlaku: {{ $schedule->effective_date->translatedFormat('d M Y') }}
                                        @if ($schedule->end_date)
                                            - {{ $schedule->end_date->translatedFormat('d M Y') }}
                                        @endif
                                    </p>
                                @endif
                            </div>

                            <div class="text-end">
                                <span class="info-badge" style="background: linear-gradient(135deg, #17a2b8, #138496);">{{ $schedule->getScheduleType() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="card card-style schedule-detail-card">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <div class="schedule-icon me-3" style="width: 45px; height: 45px; font-size: 18px; background: linear-gradient(135deg, #fd7e14, #e55a00);">
                    <i class="bi bi-lightning"></i>
                </div>
                <div>
                    <h5 class="font-700 mb-0 font-16">Aksi Cepat</h5>
                    <p class="mb-0 font-12 opacity-70">Navigasi cepat ke fitur lain</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <a href="{{ route('employee.attendance.index') }}" class="btn btn-m bg-green-dark text-white text-uppercase font-600 rounded-s w-100">
                        <i class="bi bi-calendar-check font-16 d-block mb-1"></i>
                        <span class="font-11 font-600">Absen Hari Ini</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('employee.attendance.history') }}" class="btn btn-m bg-blue-dark text-white text-uppercase font-600 rounded-s w-100">
                        <i class="bi bi-clock-history font-16 d-block mb-1"></i>
                        <span class="font-11 font-600">Riwayat Absen</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to schedule cards
            const scheduleCards = document.querySelectorAll('.schedule-card, .schedule-detail-card');
            scheduleCards.forEach(function(card) {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                    this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
                });
            });

            // Add click animation to action buttons
            const actionButtons = document.querySelectorAll('.btn');
            actionButtons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });

            // Auto-refresh time display
            function updateCurrentTime() {
                const now = new Date();
                const timeElements = document.querySelectorAll('.current-time');
                timeElements.forEach(function(element) {
                    element.textContent = now.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                });
            }

            // Update time every second
            setInterval(updateCurrentTime, 1000);
            updateCurrentTime(); // Initial call

            // Add animation on page load
            const cards = document.querySelectorAll('.card');
            cards.forEach(function(card, index) {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(function() {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Add pulse effect to active schedule
            const activeScheduleCards = document.querySelectorAll('.schedule-card');
            activeScheduleCards.forEach(function(card) {
                setInterval(function() {
                    card.style.boxShadow = '0 4px 20px rgba(40,167,69,0.3)';
                    setTimeout(function() {
                        card.style.boxShadow = '0 4px 20px rgba(40,167,69,0.15)';
                    }, 1000);
                }, 3000);
            });
        });
    </script>
@endpush
