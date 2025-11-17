<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'attachment_path',
        'status',
        'approver_id',
        'approved_at',
        'verifier_id',
        'verified_at',
        'rejected_by_id',
        'rejected_reason',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'approved_at' => 'datetime',
            'verified_at' => 'datetime',
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    // Status helpers
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';

    public function isPending()
    {
        return $this->status === static::STATUS_PENDING;
    }

    public function isFullyApproved()
    {
        return $this->status === static::STATUS_VERIFIED;
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
