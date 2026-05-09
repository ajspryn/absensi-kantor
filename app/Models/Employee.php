<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'user_id',
        'department_id',
        'position_id',
        'work_schedule_id',
        'full_name',
        'phone',
        'email',
        'address',
        'hire_date',
        'salary',
        'is_active',
        'allow_remote_attendance',
        'photo',
        'nik_ktp',
        'jabatan',
        'address_ktp',
        'address_domisili',
        'mobile',
        'gender',
        'height_cm',
        'weight_kg',
        'hobby',
        'birth_place',
        'birth_date',
        'marital_status',
        'residence_status',
        'health_condition',
        'degenerative_diseases',
        'has_medical_history',
        'education_history',
        'training_history',
        'family_structure',
        'emergency_contact',
        'commitment_notes',
        'financing_notes',
        'has_credit_issue',
        'credit_institution',
        'credit_plafond',
        'credit_monthly_installment',
        'financing_history',
        'ktp_path',
        'kk_path',
        'marriage_certificate_path',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
        'allow_remote_attendance' => 'boolean',
        'birth_date' => 'date',
        'has_medical_history' => 'boolean',
        'has_credit_issue' => 'boolean',
        'education_history' => 'array',
        'training_history' => 'array',
        'family_structure' => 'array',
        'emergency_contact' => 'array',
        'credit_plafond' => 'decimal:2',
        'credit_monthly_installment' => 'decimal:2',
        'financing_history' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function educationRecords()
    {
        return $this->hasMany(EducationRecord::class);
    }

    public function trainingRecords()
    {
        return $this->hasMany(TrainingRecord::class);
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class, 'work_schedule_id');
    }

    public function role()
    {
        return $this->hasOneThrough(Role::class, User::class, 'id', 'id', 'user_id', 'role_id');
    }

    public function getTodayAttendance()
    {
        return $this->attendances()->whereDate('date', today())->first();
    }

    public function hasCheckedInToday()
    {
        $attendance = $this->getTodayAttendance();
        return $attendance && $attendance->check_in;
    }

    public function hasCheckedOutToday()
    {
        $attendance = $this->getTodayAttendance();
        return $attendance && $attendance->check_out;
    }

    public function hasRole($roleName)
    {
        return $this->user && $this->user->role && $this->user->role->name === $roleName;
    }

    public function hasPermission($permission)
    {
        return $this->user && $this->user->role && $this->user->role->hasPermission($permission);
    }

    public function getRoleName()
    {
        return $this->user && $this->user->role ? $this->user->role->name : 'No Role';
    }

    public function getDepartmentName()
    {
        return $this->department && isset($this->department->name) ? $this->department->name : null;
    }

    public function getPositionName()
    {
        if ($this->position && isset($this->position->name)) {
            return $this->position->name;
        } elseif (is_string($this->position) && ! empty($this->position)) {
            return $this->position;
        }
        return null;
    }

    public function getPositionNameAttribute()
    {
        if (is_object($this->position) && property_exists($this->position, 'name')) {
            return $this->position->name;
        }
        if (is_string($this->position) && ! empty($this->position)) {
            return $this->position;
        }
        return null;
    }

    public function getFullPositionName()
    {
        $dept = $this->getDepartmentName();
        $pos = $this->getPositionName();
        if ($pos && $dept) return "{$pos} - {$dept}";
        if ($pos) return $pos;
        if ($dept) return $dept;
        return '-';
    }

    public function isActive()
    {
        return $this->user && $this->user->is_active;
    }

    public function getStatusBadge()
    {
        if ($this->isActive()) {
            return '<span class="badge bg-success">Active</span>';
        } else {
            return '<span class="badge bg-danger">Inactive</span>';
        }
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('employee_id', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('department', function ($deptQuery) use ($search) {
                    $deptQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('position', function ($posQuery) use ($search) {
                    $posQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    public function scopeByRole($query, $roleId)
    {
        return $query->whereHas('user', function ($userQuery) use ($roleId) {
            $userQuery->where('role_id', $roleId);
        });
    }

    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($userQuery) {
            $userQuery->where('is_active', true);
        });
    }

    public function getAttendanceWithMissing($startDate, $endDate)
    {
        if (! $this->workSchedule) {
            $attendances = $this->attendances()
                ->whereBetween('date', [\Carbon\Carbon::parse($startDate), \Carbon\Carbon::parse($endDate)])
                ->orderBy('date', 'desc')
                ->get();
            // Optional: You could fetch leaves here too, but normally employees have workSchedule.
            return $attendances;
        }

        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        $existingAttendances = $this->attendances()
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

        // Get leaves for this requested period
        $leaves = \App\Models\LeaveRequest::where('employee_id', $this->id)
            ->whereIn('status', ['approved', 'verified'])
            ->where(function ($q) use ($start, $end) {
                // Ensure overlapping bounds
                $startStr = $start->format('Y-m-d 00:00:00');
                $endStr = $end->format('Y-m-d 23:59:59');
                $q->whereBetween('start_date', [$startStr, $endStr])
                    ->orWhereBetween('end_date', [$startStr, $endStr])
                    ->orWhere(function ($q2) use ($startStr, $endStr) {
                        $q2->where('start_date', '<=', $startStr)
                            ->where('end_date', '>=', $endStr);
                    });
            })->get();

        $result = collect();
        $current = \Carbon\Carbon::parse($startDate);

        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');

            // Cek apakah ada cuti/izin
            $empLeave = $leaves->filter(function ($l) use ($dateStr) {
                return \Carbon\Carbon::parse($l->start_date)->format('Y-m-d') <= $dateStr &&
                    \Carbon\Carbon::parse($l->end_date)->format('Y-m-d') >= $dateStr;
            })->first();

            if ($existingAttendances->has($dateStr)) {
                $att = $existingAttendances->get($dateStr);
                if ($empLeave && empty($att->check_in)) {
                    $att->is_leave = true;
                    $att->leave_record = $empLeave;
                }
                $result->push($att);
            } elseif ($this->workSchedule->isWorkDay($current) || $empLeave) {
                $statusStr = $empLeave ? 'leave' : 'absent';
                $result->push((object) [
                    'id' => null,
                    'employee_id' => $this->id,
                    'date' => \Carbon\Carbon::parse($dateStr), // ensure it's a carbon instance for the view
                    'check_in' => null,
                    'check_out' => null,
                    'check_in_photo' => null,
                    'check_out_photo' => null,
                    'check_in_location' => null,
                    'check_out_location' => null,
                    'check_in_address' => null,
                    'check_out_address' => null,
                    'notes' => $empLeave ? ('[' . strtoupper($empLeave->type) . '] ' . $empLeave->reason) : null,
                    'schedule_status' => $statusStr,
                    'late_minutes' => null,
                    'early_leave_minutes' => null,
                    'created_at' => null,
                    'updated_at' => null,
                    'employee' => $this,
                    'is_missing' => $empLeave ? false : true,
                    'is_leave' => $empLeave ? true : false,
                    'leave_record' => $empLeave,
                    'schedule_status_text' => $empLeave ? strtoupper($empLeave->type) : 'Tidak Hadir'
                ]);
            }
            $current->addDay();
        }

        return $result->sortByDesc('date')->values();
    }

    public function getMissingProfileFields(array $requiredFields = []): array
    {
        $missing = [];
        foreach ($requiredFields as $f) {
            $value = $this->{$f} ?? null;
            if (in_array($f, ['employee_id', 'department_id', 'position_id'], true)) {
                if ($value === null || $value === '') $missing[] = $f;
                continue;
            }
            if (is_string($value)) {
                if (trim($value) === '') $missing[] = $f;
                continue;
            }
            if ($value === null || $value === '') $missing[] = $f;
        }
        return $missing;
    }

    public function isProfileComplete(array $requiredFields = []): bool
    {
        return empty($this->getMissingProfileFields($requiredFields));
    }
}
