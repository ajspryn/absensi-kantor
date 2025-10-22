<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceCorrection extends Model
{
     use HasFactory;

     protected $fillable = [
          'user_id',
          'employee_id',
          'attendance_id',
          'date',
          'original_check_in',
          'original_check_out',
          'corrected_check_in',
          'corrected_check_out',
          'reason',
          'attachment_path',
          'status',
          'manager_approver_id',
          'manager_approved_at',
          'hr_approver_id',
          'hr_approved_at',
          'rejected_by_id',
          'rejected_reason',
          'rejected_at',
     ];

     protected function casts(): array
     {
          return [
               'date' => 'date',
               'original_check_in' => 'datetime',
               'original_check_out' => 'datetime',
               'corrected_check_in' => 'datetime',
               'corrected_check_out' => 'datetime',
               'manager_approved_at' => 'datetime',
               'hr_approved_at' => 'datetime',
               'rejected_at' => 'datetime',
          ];
     }

     // Relations
     public function user()
     {
          return $this->belongsTo(User::class);
     }

     public function employee()
     {
          return $this->belongsTo(Employee::class);
     }

     public function attendance()
     {
          return $this->belongsTo(Attendance::class);
     }

     public function managerApprover()
     {
          return $this->belongsTo(User::class, 'manager_approver_id');
     }

     public function hrApprover()
     {
          return $this->belongsTo(User::class, 'hr_approver_id');
     }

     // Status helpers
     const STATUS_PENDING = 'pending';
     const STATUS_MANAGER_APPROVED = 'manager_approved';
     const STATUS_HR_APPROVED = 'hr_approved';
     const STATUS_APPROVED = 'approved';
     const STATUS_REJECTED = 'rejected';

     public function isPending()
     {
          return $this->status === static::STATUS_PENDING;
     }

     public function isFullyApproved()
     {
          return $this->status === static::STATUS_APPROVED;
     }

     // Scopes
     public function scopePending($q)
     {
          return $q->where('status', static::STATUS_PENDING);
     }

     public function scopeForEmployee($q, $employeeId)
     {
          return $q->where('employee_id', $employeeId);
     }
}
