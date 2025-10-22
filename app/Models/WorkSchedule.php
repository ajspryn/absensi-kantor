<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'start_time',
        'end_time',
        'break_start_time',
        'break_end_time',
        'work_days', // JSON array of work days (0=Sunday, 1=Monday, etc.)
        'is_flexible',
        'location_required',
        'is_active',
        'effective_date',
        'end_date',
        'total_hours',
        'overtime_threshold',
        'late_tolerance'
    ];

    protected $casts = [
        'work_days' => 'array',
        'is_flexible' => 'boolean',
        'location_required' => 'boolean',
        'is_active' => 'boolean',
        'effective_date' => 'date',
        'end_date' => 'date',
        'total_hours' => 'decimal:2',
        'overtime_threshold' => 'decimal:2',
        'late_tolerance' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Helper methods
    public function getWorkDaysFormatted()
    {
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu'
        ];

        if (empty($this->work_days)) {
            return 'Tidak ada hari kerja';
        }

        return implode(', ', array_map(function ($day) use ($days) {
            return $days[$day] ?? $day;
        }, $this->work_days));
    }

    public function getTotalHoursFormatted()
    {
        // If total_hours missing but start/end exist, attempt to recalculate
        if (is_null($this->total_hours) && $this->start_time && $this->end_time) {
            $this->calculateTotalHours();
        }

        if (!$this->total_hours) {
            return '0 jam';
        }

        $hours = (int) floor((float) $this->total_hours);
        $minutes = (int) round(((float) $this->total_hours - $hours) * 60);

        if ($minutes > 0) {
            return "{$hours} jam {$minutes} menit";
        }

        return "{$hours} jam";
    }

    public function calculateTotalHours()
    {
        if ($this->start_time && $this->end_time) {
            $start = Carbon::parse($this->start_time);
            $end = Carbon::parse($this->end_time);

            $totalMinutes = $end->diffInMinutes($start);

            // Subtract break time if exists
            if ($this->break_start_time && $this->break_end_time) {
                $breakStart = Carbon::parse($this->break_start_time);
                $breakEnd = Carbon::parse($this->break_end_time);
                $breakMinutes = $breakEnd->diffInMinutes($breakStart);
                $totalMinutes -= $breakMinutes;
            }

            $this->total_hours = $totalMinutes / 60;
            $this->save();
        }
    }

    public function getWorkingHoursRange()
    {
        if (!$this->start_time || !$this->end_time) {
            return '-';
        }
        // Ensure HH:MM format
        try {
            $start = Carbon::parse($this->start_time)->format('H:i');
            $end = Carbon::parse($this->end_time)->format('H:i');
            return $start . ' - ' . $end;
        } catch (\Throwable $e) {
            return $this->start_time . ' - ' . $this->end_time;
        }
    }

    public function isActiveToday()
    {
        $today = Carbon::now();
        $dayOfWeek = $today->dayOfWeek;

        // Check if today is a work day
        if (!in_array($dayOfWeek, $this->work_days ?? [])) {
            return false;
        }

        // Check if schedule is active
        if (!$this->is_active) {
            return false;
        }

        // Check effective date
        if ($this->effective_date && $today->lt($this->effective_date)) {
            return false;
        }

        // Check end date
        if ($this->end_date && $today->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    public function isWorkDay($date = null)
    {
        $checkDate = $date ? Carbon::parse($date) : Carbon::now();
        $dayOfWeek = $checkDate->dayOfWeek;

        return in_array($dayOfWeek, $this->work_days ?? []);
    }

    public function getScheduleType()
    {
        if ($this->is_flexible) {
            return 'Fleksibel';
        }

        if (count($this->work_days ?? []) == 7) {
            return 'Full Time (7 Hari)';
        }

        if (count($this->work_days ?? []) >= 5) {
            return 'Full Time';
        }

        return 'Part Time';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeEffectiveOn($query, $date = null)
    {
        $checkDate = $date ?? Carbon::now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($checkDate) {
                $q->whereNull('effective_date')
                    ->orWhere('effective_date', '<=', $checkDate);
            })
            ->where(function ($q) use ($checkDate) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $checkDate);
            });
    }
}
