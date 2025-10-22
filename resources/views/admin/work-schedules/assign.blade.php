@extends('layouts.admin')

@section('title', 'Tugaskan Jadwal Kerja - Admin')

@section('header')
    @include('admin.header', [
        'title' => 'Tugaskan Jadwal Kerja',
        'backUrl' => route('admin.work-schedules.index'),
    ])
@endsection

@section('content')
    @include('admin.partials.alerts')
    <style>
        .card-uniform,
        .card.card-style {
            width: 100%;
            max-width: none;
            margin: 0 0 1.5rem 0;
        }

        .section-header {
            background: linear-gradient(135deg, #e3f2fd, #f8faff);
            border: 1px solid rgba(33, 150, 243, 0.2);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        }

        .employee-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: white;
        }

        .employee-card:hover {
            border-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.15);
        }

        .employee-card.selected {
            border-color: #28a745;
            background: linear-gradient(135deg, #f8fff9, #e8f5e9);
            transform: translateY(-2px);
        }

        .employee-card.selected.has-schedule {
            border-color: #ffc107;
            background: linear-gradient(135deg, #fff8e1, #fffdf7);
        }

        .employee-card.selected.has-schedule::before {
            content: "⚠️ Jadwal akan diganti";
            position: absolute;
            top: -10px;
            right: 10px;
            background: #ffc107;
            color: #000;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            z-index: 1;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-assigned {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .status-unassigned {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .current-schedule {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border: 1px solid rgba(33, 150, 243, 0.2);
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
        }

        .employee-checkbox {
            transform: scale(1.2);
        }
    </style>
@section('header')
    <div class="header-bar header-fixed header-app header-bar-detached">
        <a data-back-button href="{{ route('admin.work-schedules.index') }}"><i class="bi bi-caret-left-fill font-11 color-theme ps-2"></i></a>
        <a href="#" class="header-title color-theme font-13">Tugaskan Jadwal Kerja</a>
        <a href="#" class="header-icon header-icon-4" data-menu="menu-main"><i class="bi bi-three-dots color-theme"></i></a>
    </div>
@endsection

<div class="page-content px-2 px-md-4 py-2 py-md-4">
    <!-- Info Section -->
    <div class="card card-style mb-4">
        <div class="content">
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="width: 35px; height: 35px; background: linear-gradient(135deg, #2196f3, #1976d2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-check text-white font-14"></i>
                    </div>
                    <div>
                        <h5 class="font-700 mb-0">Tugaskan Jadwal Kerja</h5>
                        <p class="mb-0 font-11 opacity-70">Pilih karyawan dan jadwal yang akan ditugaskan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Assignment Form -->
    <div class="card card-style mb-4">
        <div class="content">
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="width: 35px; height: 35px; background: linear-gradient(135deg, #6f42c1, #5a32a3); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-funnel text-white font-14"></i>
                    </div>
                    <div>
                        <h5 class="font-700 mb-0">Penugasan Massal</h5>
                        <p class="mb-0 font-11 opacity-70">Tugaskan jadwal ke banyak karyawan sekaligus</p>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 col-md-6 mb-3">
                    <label class="form-label font-600">Pilih Jadwal</label>
                    <select class="form-select" id="bulk-schedule">
                        <option value="">-- Pilih Jadwal --</option>
                        @foreach ($schedules as $schedule)
                            <option value="{{ $schedule->id }}">
                                {{ $schedule->name }} ({{ $schedule->getWorkingHoursRange() }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label class="form-label font-600">Tanggal Efektif</label>
                    <input type="date" class="form-control" id="bulk-effective-date" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 col-md-4 mb-2">
                    <label class="form-label font-600">Filter Departemen</label>
                    <select class="form-select" id="filter-department">
                        <option value="">Semua Departemen</option>
                        @foreach ($employees->unique('employee.department.name')->pluck('employee.department') as $dept)
                            @if ($dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 mb-2">
                    <label class="form-label font-600">Filter Status</label>
                    <select class="form-select" id="filter-schedule-status">
                        <option value="">Semua Status</option>
                        <option value="assigned">Sudah Ada Jadwal</option>
                        <option value="unassigned">Belum Ada Jadwal</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 mb-2">
                    <label class="form-label font-600">Aksi Massal</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-m gradient-blue flex-fill" id="select-all-btn">
                            <i class="bi bi-check-all me-1"></i>Pilih Semua
                        </button>
                        <button type="button" class="btn btn-m gradient-red flex-fill" id="clear-all-btn">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Assignment -->
    <form method="POST" action="{{ route('admin.work-schedules.store-assignment') }}" id="assignment-form">
        @csrf

        <!-- Employee List -->
        @if ($employees->count() > 0)
            <div id="employee-list">
                @foreach ($employees as $employee)
                    <div class="card card-style employee-card mb-3" data-department="{{ $employee->employee?->department_id ?? '' }}" data-schedule-status="{{ $employee->workSchedules->where('is_active', true)->count() > 0 ? 'assigned' : 'unassigned' }}">
                        <div class="content">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3" style="width: 35px; height: 35px; background: linear-gradient(135deg, #007bff, #0056b3); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-person text-white font-14"></i>
                                        </div>
                                        <div>
                                            <h5 class="font-700 mb-1">{{ $employee->name }}</h5>
                                            @if ($employee->workSchedules->where('is_active', true)->count() > 0)
                                                <span class="status-badge status-assigned">Ada Jadwal</span>
                                            @else
                                                <span class="status-badge status-unassigned">Belum Ada</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bi bi-envelope font-11 me-2 color-theme"></i>
                                            <span class="font-11 color-theme">{{ $employee->email }}</span>
                                        </div>

                                        @if ($employee->employee?->department)
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="bi bi-building font-11 me-2 color-theme"></i>
                                                <span class="font-11 color-theme">{{ optional($employee->employee->department)->name ?? '-' }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($employee->workSchedules->where('is_active', true)->count() > 0)
                                        <div class="current-schedule">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="bi bi-clock font-11 me-2 color-blue-dark"></i>
                                                <strong class="font-11 color-blue-dark">Jadwal Saat Ini:</strong>
                                            </div>
                                            @foreach ($employee->workSchedules->where('is_active', true) as $currentSchedule)
                                                <div class="font-10 color-theme">
                                                    {{ $currentSchedule->name }}
                                                    ({{ $currentSchedule->getWorkingHoursRange() }})
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input employee-checkbox" type="checkbox" id="employee{{ $employee->id }}" data-employee-id="{{ $employee->id }}" value="{{ $employee->id }}">
                                    <label class="form-check-label font-11 font-600" for="employee{{ $employee->id }}">
                                        Pilih
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Assignment Summary -->
            <div class="card card-style mb-4" id="assignment-summary" style="display: none;">
                <div class="content">
                    <div class="section-header">
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="width: 35px; height: 35px; background: linear-gradient(135deg, #28a745, #20c997); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-check-circle text-white font-14"></i>
                            </div>
                            <div>
                                <h5 class="font-700 mb-0">Ringkasan Penugasan</h5>
                                <p class="mb-0 font-11 opacity-70" id="summary-text">Tidak ada karyawan yang dipilih</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-m gradient-red btn-full font-600">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-m gradient-green btn-full font-600" id="submit-button" disabled>
                                <i class="bi bi-check-circle me-2"></i>Tugaskan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="card card-style">
                <div class="content text-center">
                    <div class="mx-auto mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #e9ecef, #f8f9fa); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-people-fill font-24 color-gray-dark"></i>
                    </div>
                    <h4 class="font-700 mb-2">Tidak ada karyawan</h4>
                    <p class="color-theme mb-4">Belum ada karyawan aktif yang dapat ditugaskan jadwal</p>
                    <a href="{{ route('admin.employees.create') }}" class="btn btn-m gradient-blue btn-full">
                        <i class="bi bi-person-plus me-2"></i>Tambah Karyawan
                    </a>
                </div>
            </div>
        @endif
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('assignment-form');
        const submitButton = document.getElementById('submit-button');
        const assignmentSummary = document.getElementById('assignment-summary');
        const summaryText = document.getElementById('summary-text');
        const employeeCheckboxes = document.querySelectorAll('.employee-checkbox');
        const employeeCards = document.querySelectorAll('.employee-card');

        // Filter elements
        const filterDepartment = document.getElementById('filter-department');
        const filterScheduleStatus = document.getElementById('filter-schedule-status');
        const bulkSchedule = document.getElementById('bulk-schedule');
        const bulkEffectiveDate = document.getElementById('bulk-effective-date');
        const selectAllBtn = document.getElementById('select-all-btn');
        const clearAllBtn = document.getElementById('clear-all-btn');

        // Update submit button and summary
        function updateSubmitButton() {
            const selectedEmployees = document.querySelectorAll('.employee-checkbox:checked');
            const count = selectedEmployees.length;
            const scheduleSelected = bulkSchedule && bulkSchedule.value !== '';

            if (count > 0 && scheduleSelected) {
                submitButton.disabled = false;
                assignmentSummary.style.display = 'block';
                const scheduleName = bulkSchedule.options[bulkSchedule.selectedIndex]?.text || '';

                // Count employees with existing schedules
                let existingScheduleCount = 0;
                selectedEmployees.forEach(checkbox => {
                    const card = checkbox.closest('.employee-card');
                    const hasSchedule = card.dataset.scheduleStatus === 'assigned';
                    if (hasSchedule) {
                        existingScheduleCount++;
                    }
                });

                let summaryMessage = `${count} karyawan akan ditugaskan jadwal "${scheduleName}"`;
                if (existingScheduleCount > 0) {
                    summaryMessage += ` (${existingScheduleCount} sudah memiliki jadwal dan akan diganti)`;
                }

                summaryText.textContent = summaryMessage;
                updateFormData();
            } else {
                submitButton.disabled = true;
                if (count === 0) {
                    assignmentSummary.style.display = 'none';
                } else {
                    assignmentSummary.style.display = 'block';
                    summaryText.textContent = 'Pilih jadwal untuk melanjutkan';
                }
            }
        }

        // Update form data for submission
        function updateFormData() {
            // Clear existing hidden inputs
            const existingInputs = form.querySelectorAll('input[name^="assignments["]');
            existingInputs.forEach(input => input.remove());

            // Add new hidden inputs for selected employees
            const selectedEmployees = document.querySelectorAll('.employee-checkbox:checked');
            selectedEmployees.forEach(checkbox => {
                const employeeId = checkbox.dataset.employeeId;

                if (bulkSchedule && bulkSchedule.value && bulkEffectiveDate && bulkEffectiveDate.value) {
                    // Create hidden inputs for assignment
                    const scheduleInput = document.createElement('input');
                    scheduleInput.type = 'hidden';
                    scheduleInput.name = 'assignments[' + employeeId + '][schedule_id]';
                    scheduleInput.value = bulkSchedule.value;
                    form.appendChild(scheduleInput);

                    const userInput = document.createElement('input');
                    userInput.type = 'hidden';
                    userInput.name = 'assignments[' + employeeId + '][user_id]';
                    userInput.value = employeeId;
                    form.appendChild(userInput);

                    const effectiveDateInput = document.createElement('input');
                    effectiveDateInput.type = 'hidden';
                    effectiveDateInput.name = 'assignments[' + employeeId + '][effective_date]';
                    effectiveDateInput.value = bulkEffectiveDate.value;
                    form.appendChild(effectiveDateInput);
                }
            });
        }

        // Filter employees
        function filterEmployees() {
            const departmentFilter = filterDepartment ? filterDepartment.value : '';
            const statusFilter = filterScheduleStatus ? filterScheduleStatus.value : '';

            employeeCards.forEach(card => {
                const department = card.dataset.department;
                const status = card.dataset.scheduleStatus;

                let showCard = true;

                if (departmentFilter && department !== departmentFilter) {
                    showCard = false;
                }

                if (statusFilter && status !== statusFilter) {
                    showCard = false;
                }

                card.style.display = showCard ? 'block' : 'none';
            });
        }

        // Event listeners
        employeeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const card = this.closest('.employee-card');
                const hasSchedule = card.dataset.scheduleStatus === 'assigned';

                if (this.checked) {
                    card.classList.add('selected');
                    if (hasSchedule) {
                        card.classList.add('has-schedule');
                        card.style.position = 'relative'; // For ::before positioning
                    }
                } else {
                    card.classList.remove('selected', 'has-schedule');
                    card.style.position = ''; // Reset position
                }
                updateSubmitButton();
            });
        });
        if (bulkSchedule) {
            bulkSchedule.addEventListener('change', updateSubmitButton);
        }

        if (bulkEffectiveDate) {
            bulkEffectiveDate.addEventListener('change', updateSubmitButton);
        }

        if (filterDepartment) {
            filterDepartment.addEventListener('change', filterEmployees);
        }

        if (filterScheduleStatus) {
            filterScheduleStatus.addEventListener('change', filterEmployees);
        }

        // Select all visible employees
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                const visibleCheckboxes = Array.from(employeeCheckboxes).filter(checkbox => {
                    const card = checkbox.closest('.employee-card');
                    return card.style.display !== 'none';
                });

                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                    const card = checkbox.closest('.employee-card');
                    const hasSchedule = card.dataset.scheduleStatus === 'assigned';

                    card.classList.add('selected');
                    if (hasSchedule) {
                        card.classList.add('has-schedule');
                        card.style.position = 'relative';
                    }
                });
                updateSubmitButton();
            });
        }

        // Clear all selections
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                employeeCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    const card = checkbox.closest('.employee-card');
                    card.classList.remove('selected', 'has-schedule');
                    card.style.position = '';
                });
                updateSubmitButton();
            });
        } // Form submission
        if (form) {
            form.addEventListener('submit', function(e) {
                const selectedEmployees = document.querySelectorAll('.employee-checkbox:checked');
                const selectedCount = selectedEmployees.length;

                if (selectedCount === 0) {
                    e.preventDefault();
                    alert('Pilih minimal 1 karyawan untuk ditugaskan jadwal');
                    return;
                }

                if (!bulkSchedule || !bulkSchedule.value) {
                    e.preventDefault();
                    alert('Pilih jadwal yang akan ditugaskan');
                    return;
                }

                // Count employees with existing schedules
                let existingScheduleCount = 0;
                selectedEmployees.forEach(checkbox => {
                    const card = checkbox.closest('.employee-card');
                    const hasSchedule = card.dataset.scheduleStatus === 'assigned';
                    if (hasSchedule) {
                        existingScheduleCount++;
                    }
                });

                // Create confirmation message
                let confirmMessage = `Apakah Anda yakin ingin menugaskan jadwal kepada ${selectedCount} karyawan?`;
                if (existingScheduleCount > 0) {
                    confirmMessage += `\n\nPerhatian: ${existingScheduleCount} karyawan sudah memiliki jadwal aktif dan akan diganti dengan jadwal baru.`;
                }

                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                    return;
                }

                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
            });
        }

        // Initialize
        updateSubmitButton();
    });
</script>
@endpush
