@extends('layouts.admin')

@section('title', 'Detail Jadwal Kerja')

@section('content')
    @include('admin.partials.alerts')
    <div class="card card-style mb-2" style="margin-top:8px;">
        <div class="card-body py-3 px-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h4 class="mb-1"><i class="fa fa-calendar-alt me-2"></i>{{ $workSchedule->name }}</h4>
                    <span class="badge {{ $workSchedule->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $workSchedule->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <div>
                    <a href="{{ route('admin.work-schedules.edit', $workSchedule) }}" class="btn btn-sm btn-primary rounded-s"><i class="fa fa-edit me-1"></i>Edit</a>
                    <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-sm btn-dark rounded-s"><i class="fa fa-arrow-left me-1"></i>Kembali</a>
                </div>
            </div>
            <div class="divider my-2"></div>
            <div class="mb-2">
                <i class="fa fa-info-circle me-1 text-primary"></i>
                <strong>Deskripsi:</strong> {{ $workSchedule->description }}
            </div>
            <div class="row mb-2">
                <div class="col-md-6 mb-2">
                    <i class="fa fa-clock me-1 text-info"></i>
                    <strong>Jam Kerja:</strong> {{ $workSchedule->getWorkingHoursRange() }}
                </div>
                <div class="col-md-6 mb-2">
                    <i class="fa fa-coffee me-1 text-warning"></i>
                    <strong>Istirahat:</strong> {{ $workSchedule->break_start_time }} - {{ $workSchedule->break_end_time }}
                </div>
            </div>
            <div class="mb-2">
                <i class="fa fa-calendar-week me-1 text-success"></i>
                <strong>Hari Kerja:</strong>
                @php
                    $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                @endphp
                @foreach ($workSchedule->work_days as $day)
                    <span class="badge bg-theme me-1">{{ $days[$day] }}</span>
                @endforeach
            </div>
            <div class="mb-2">
                <i class="fa fa-calendar-check me-1 text-info"></i>
                <strong>Tanggal Berlaku:</strong> @php echo \Carbon\Carbon::parse($workSchedule->effective_date)->format('d M Y'); @endphp
            </div>
            @if ($workSchedule->end_date)
                <div class="mb-2">
                    <i class="fa fa-calendar-times me-1 text-danger"></i>
                    <strong>Tanggal Berakhir:</strong> @php echo \Carbon\Carbon::parse($workSchedule->end_date)->format('d M Y'); @endphp
                </div>
            @endif
            <div class="mb-2">
                <i class="fa fa-clock me-1 text-primary"></i>
                <strong>Total Jam Kerja:</strong> {{ $workSchedule->total_hours }} jam
            </div>
            <div class="mb-2">
                <i class="fa fa-clock me-1 text-warning"></i>
                <strong>Batas Lembur:</strong> {{ $workSchedule->overtime_threshold }} jam
            </div>
            <div class="mb-2">
                <i class="fa fa-exchange-alt me-1 text-info"></i>
                <strong>Fleksibel:</strong> {{ $workSchedule->is_flexible ? 'Ya' : 'Tidak' }}
            </div>
            <div class="mb-2">
                <i class="fa fa-map-marker-alt me-1 text-success"></i>
                <strong>Lokasi Wajib:</strong> {{ $workSchedule->location_required ? 'Ya' : 'Tidak' }}
            </div>
        </div>
    </div>
@endsection
