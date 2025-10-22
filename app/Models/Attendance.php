<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'latitude_in',
        'longitude_in',
        'latitude_out',
        'longitude_out',
        'photo_in',
        'photo_out',
        'notes',
        'status',
        'schedule_status',
        'late_minutes',
        'early_leave_minutes',
        'working_hours',
        'office_location_id',
        'location_name',
        'work_schedule_id',
        'is_remote'
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in' => 'datetime',
            'check_out' => 'datetime',
            'latitude_in' => 'decimal:8',
            'longitude_in' => 'decimal:8',
            'latitude_out' => 'decimal:8',
            'longitude_out' => 'decimal:8',
            'is_remote' => 'boolean',
        ];
    }

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function officeLocation()
    {
        return $this->belongsTo(OfficeLocation::class);
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    // Helper methods
    public function getWorkingHoursFormatted()
    {
        $minutes = max(0, (int) $this->working_hours);
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return "{$hours} jam {$minutes} menit";
    }

    public function calculateWorkingHours()
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = $this->check_in instanceof \Carbon\Carbon ? $this->check_in : \Carbon\Carbon::parse($this->check_in);
            $checkOut = $this->check_out instanceof \Carbon\Carbon ? $this->check_out : \Carbon\Carbon::parse($this->check_out);
            if ($checkOut > $checkIn) {
                $diff = $checkIn->diffInMinutes($checkOut);
                $this->working_hours = $diff;
            } else {
                $this->working_hours = 0;
            }
            $this->save();
        }
    }

    public function calculateScheduleStatus()
    {
        $employee = $this->employee;
        if (!$employee) {
            return;
        }

        // Ensure workSchedule relation is loaded
        $employee->load('workSchedule');
        $schedule = $employee->workSchedule;

        if (!$schedule) {
            return;
        }

        $isLate = false;
        $isEarlyLeave = false;
        $lateMinutes = 0;
        $earlyLeaveMinutes = 0;

        // Check if today is a work day
        $dayOfWeek = $this->date->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
        if (!in_array($dayOfWeek, $schedule->work_days ?? [])) {
            return; // Not a work day
        }

        // Check for late arrival - ensure timezone consistency
        if ($this->check_in && $schedule->start_time) {
            $checkInJakarta = $this->check_in->setTimezone('Asia/Jakarta');
            $scheduledStart = $this->date->copy()->setTimezone('Asia/Jakarta')->setTimeFromTimeString($schedule->start_time);
            if ($checkInJakarta->gt($scheduledStart)) {
                $isLate = true;
                $lateMinutes = $checkInJakarta->diffInMinutes($scheduledStart);
            }
        }

        // Check for early leave - ensure timezone consistency
        if ($this->check_out && $schedule->end_time) {
            $checkOutJakarta = $this->check_out->setTimezone('Asia/Jakarta');
            $scheduledEnd = $this->date->copy()->setTimezone('Asia/Jakarta')->setTimeFromTimeString($schedule->end_time);
            if ($checkOutJakarta->lt($scheduledEnd)) {
                $isEarlyLeave = true;
                $earlyLeaveMinutes = $scheduledEnd->diffInMinutes($checkOutJakarta);
            }
        }

        // Determine schedule status
        if ($isLate && $isEarlyLeave) {
            $this->schedule_status = 'late_early_leave';
        } elseif ($isLate) {
            $this->schedule_status = 'late';
        } elseif ($isEarlyLeave) {
            $this->schedule_status = 'early_leave';
        } else {
            $this->schedule_status = 'on_time';
        }

        $this->late_minutes = $lateMinutes;
        $this->early_leave_minutes = $earlyLeaveMinutes;
        $this->save();
    }

    public function getScheduleStatusText()
    {
        switch ($this->schedule_status) {
            case 'late':
                return "Terlambat {$this->late_minutes} menit";
            case 'early_leave':
                return "Pulang cepat {$this->early_leave_minutes} menit";
            case 'late_early_leave':
                return "Terlambat {$this->late_minutes} menit & Pulang cepat {$this->early_leave_minutes} menit";
            case 'absent':
                return 'Tidak Hadir';
            case 'on_time':
            default:
                return 'Tepat waktu';
        }
    }

    public function getScheduleStatusBadge()
    {
        switch ($this->schedule_status) {
            case 'late':
                return '<span class="badge bg-warning text-dark">Terlambat</span>';
            case 'early_leave':
                return '<span class="badge bg-info text-dark">Pulang Cepat</span>';
            case 'late_early_leave':
                return '<span class="badge bg-danger">Terlambat & Pulang Cepat</span>';
            case 'absent':
                return '<span class="badge bg-secondary">Tidak Hadir</span>';
            case 'on_time':
            default:
                return '<span class="badge bg-success">Tepat Waktu</span>';
        }
    }
}
