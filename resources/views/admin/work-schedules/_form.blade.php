@push('styles')
    <style>
        .section-header {
            background: linear-gradient(135deg, #e3f2fd, #f8faff);
            border: 1px solid rgba(33, 150, 243, 0.2);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .feature-card {
            background: linear-gradient(135deg, #fff3cd, #fffbf0);
            border: 1px solid #ffeaa7;
            border-radius: 12px;
            padding: 15px;
            transition: all 0.3s ease;
        }
    </style>
@endpush

@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $days = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        0 => 'Minggu',
    ];
    $workDaysOld = old('work_days', $workSchedule->work_days ?? [1, 2, 3, 4, 5]);
@endphp

@if ($errors->any())
    <div class="alert bg-danger-dark alert-dismissible text-white rounded-s fade show pe-2 mb-3" role="alert">
        <strong>Error:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close opacity-20 font-11 pt-3 mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Informasi Dasar -->
<div class="card card-style mb-4">
    <div class="content">
        <div class="section-header">
            <div class="d-flex align-items-center">
                <div class="me-3" style="width: 35px; height: 35px; background: linear-gradient(135deg, #28a745, #1e7e34); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-info-circle text-white font-14"></i>
                </div>
                <div>
                    <h5 class="font-700 mb-0">Informasi Dasar</h5>
                    <p class="mb-0 font-11 opacity-70">Data dasar jadwal kerja</p>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label font-600">Nama Jadwal <span class="color-red-dark">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $workSchedule->name ?? '') }}" placeholder="Contoh: Shift Pagi, Shift Malam, WFH Fleksibel" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label font-600">Deskripsi</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Penjelasan tentang jadwal kerja ini">{{ old('description', $workSchedule->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-0">
            <label for="user_id" class="form-label font-600">Karyawan</label>
            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                <option value="">-- Template Jadwal (Untuk Semua) --</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" {{ old('user_id', $workSchedule->user_id ?? '') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }} - {{ $emp->email }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Pilih karyawan atau biarkan kosong untuk template</small>
            @error('user_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<!-- Waktu Kerja -->
<div class="card card-style mb-4">
    <div class="content">
        <div class="section-header">
            <div class="d-flex align-items-center">
                <div class="me-3" style="width: 35px; height: 35px; background: linear-gradient(135deg, #fd7e14, #e55a00); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-clock text-white font-14"></i>
                </div>
                <div>
                    <h5 class="font-700 mb-0">Waktu Kerja</h5>
                    <p class="mb-0 font-11 opacity-70">Jam mulai dan selesai kerja</p>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-6">
                <div class="mb-3">
                    <label for="start_time" class="form-label font-600">Jam Mulai <span class="color-red-dark">*</span></label>
                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $workSchedule->start_time ?? '08:00') }}" required>
                    @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-6">
                <div class="mb-3">
                    <label for="end_time" class="form-label font-600">Jam Selesai <span class="color-red-dark">*</span></label>
                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', $workSchedule->end_time ?? '17:00') }}" required>
                    @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-6">
                <div class="mb-0">
                    <label for="break_start_time" class="form-label font-600">Jam Mulai Istirahat</label>
                    <input type="time" class="form-control @error('break_start_time') is-invalid @enderror" id="break_start_time" name="break_start_time" value="{{ old('break_start_time', $workSchedule->break_start_time ?? '') }}">
                    @error('break_start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-6">
                <div class="mb-0">
                    <label for="break_end_time" class="form-label font-600">Jam Selesai Istirahat</label>
                    <input type="time" class="form-control @error('break_end_time') is-invalid @enderror" id="break_end_time" name="break_end_time" value="{{ old('break_end_time', $workSchedule->break_end_time ?? '') }}">
                    @error('break_end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div id="total-hours-display" class="feature-card mt-3 text-center d-none">
            <h6 class="font-600 mb-0">Total Jam Kerja: <span id="total-hours-text">0 jam</span></h6>
        </div>
    </div>
</div>

<!-- Hari Kerja -->
<div class="card card-style mb-4">
    <div class="content">
        <div class="section-header">
            <div class="d-flex align-items-center">
                <div class="me-3" style="width: 35px; height: 35px; background: linear-gradient(135deg, #6f42c1, #5a32a3); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-calendar-week text-white font-14"></i>
                </div>
                <div>
                    <h5 class="font-700 mb-0">Hari Kerja</h5>
                    <p class="mb-0 font-11 opacity-70">Pilih hari-hari kerja dalam seminggu</p>
                </div>
            </div>
        </div>

        <div class="row g-3">
            @foreach ($days as $dayNumber => $dayName)
                <div class="col-6 col-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input day-checkbox" type="checkbox" name="work_days[]" value="{{ $dayNumber }}" id="day{{ $dayNumber }}" {{ in_array($dayNumber, $workDaysOld) ? 'checked' : '' }}>
                        <label class="form-check-label font-600" for="day{{ $dayNumber }}">{{ $dayName }}</label>
                    </div>
                </div>
            @endforeach
        </div>
        @error('work_days')
            <div class="text-danger mt-2 font-12">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Pengaturan Tambahan -->
<div class="card card-style mb-4">
    <div class="content">
        <div class="section-header">
            <div class="d-flex align-items-center">
                <div class="me-3" style="width: 35px; height: 35px; background: linear-gradient(135deg, #17a2b8, #138496); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-gear text-white font-14"></i>
                </div>
                <div>
                    <h5 class="font-700 mb-0">Pengaturan Tambahan</h5>
                    <p class="mb-0 font-11 opacity-70">Konfigurasi lanjutan jadwal kerja</p>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_flexible" id="is_flexible" value="1" {{ old('is_flexible', $workSchedule->is_flexible ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label font-600" for="is_flexible">Jadwal Fleksibel</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="location_required" id="location_required" value="1" {{ old('location_required', $workSchedule->location_required ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label font-600" for="location_required">Wajib Lokasi Kantor</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $workSchedule->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label font-600" for="is_active">Aktif</label>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <label for="overtime_threshold" class="form-label font-600">Batas Lembur (jam)</label>
                <input type="number" class="form-control @error('overtime_threshold') is-invalid @enderror" id="overtime_threshold" name="overtime_threshold" value="{{ old('overtime_threshold', $workSchedule->overtime_threshold ?? '') }}" step="0.5" min="0" placeholder="8.0">
                @error('overtime_threshold')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label for="late_tolerance" class="form-label font-600">Toleransi Terlambat (menit)</label>
                <input type="number" class="form-control @error('late_tolerance') is-invalid @enderror" id="late_tolerance" name="late_tolerance" value="{{ old('late_tolerance', $workSchedule->late_tolerance ?? 15) }}" min="0" max="60" placeholder="15">
                @error('late_tolerance')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label for="effective_date" class="form-label font-600">Tanggal Berlaku</label>
                <input type="date" class="form-control @error('effective_date') is-invalid @enderror" id="effective_date" name="effective_date" value="{{ old('effective_date', isset($workSchedule->effective_date) ? $workSchedule->effective_date?->format('Y-m-d') : '') }}">
                @error('effective_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label font-600">Tanggal Berakhir</label>
                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', isset($workSchedule->end_date) ? $workSchedule->end_date?->format('Y-m-d') : '') }}">
                @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<!-- Submit Button -->
<div class="card card-style mb-4">
    <div class="content">
        <div class="d-flex flex-column flex-md-row gap-3 align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="me-3" style="width: 35px; height: 35px; background: linear-gradient(135deg, #28a745, #20c997); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-check-circle text-white font-14"></i>
                </div>
                <div>
                    <h6 class="font-600 mb-0">{{ $isEdit ? 'Perbarui Jadwal' : 'Simpan Jadwal' }}</h6>
                    <p class="mb-0 font-11 opacity-70">{{ $isEdit ? 'Simpan perubahan pada jadwal kerja' : 'Buat jadwal kerja baru' }}</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-2"></i>{{ $isEdit ? 'Perbarui Jadwal' : 'Simpan Jadwal' }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const breakStartInput = document.getElementById('break_start_time');
            const breakEndInput = document.getElementById('break_end_time');
            const workDaysCheckboxes = document.querySelectorAll('.day-checkbox');
            const totalHoursDisplay = document.getElementById('total-hours-display');
            const totalHoursText = document.getElementById('total-hours-text');

            function calculateWorkHours() {
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;
                const breakStart = breakStartInput.value;
                const breakEnd = breakEndInput.value;

                if (!startTime || !endTime) {
                    totalHoursDisplay?.classList.add('d-none');
                    return;
                }

                const start = new Date('2000-01-01T' + startTime + ':00');
                const end = new Date('2000-01-01T' + endTime + ':00');
                let workHours = (end - start) / (1000 * 60 * 60);

                if (breakStart && breakEnd) {
                    const bs = new Date('2000-01-01T' + breakStart + ':00');
                    const be = new Date('2000-01-01T' + breakEnd + ':00');
                    const bd = (be - bs) / (1000 * 60 * 60);
                    if (bd > 0) workHours -= bd;
                }

                const selectedDays = document.querySelectorAll('.day-checkbox:checked').length;
                const totalWeeklyHours = workHours * selectedDays;

                if (workHours > 0 && totalHoursDisplay && totalHoursText) {
                    totalHoursText.textContent = workHours.toFixed(1) + ' jam/hari (' + totalWeeklyHours.toFixed(1) + ' jam/minggu)';
                    totalHoursDisplay.classList.remove('d-none');
                }
            }

            startTimeInput?.addEventListener('change', calculateWorkHours);
            endTimeInput?.addEventListener('change', calculateWorkHours);
            breakStartInput?.addEventListener('change', calculateWorkHours);
            breakEndInput?.addEventListener('change', calculateWorkHours);
            workDaysCheckboxes?.forEach(cb => cb.addEventListener('change', calculateWorkHours));

            // Initial calculation
            calculateWorkHours();
        });
    </script>
@endpush
