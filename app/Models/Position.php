<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'department_id',
        'level',
        'min_salary',
        'max_salary',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'min_salary' => 'decimal:2',
            'max_salary' => 'decimal:2',
        ];
    }

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // Helper methods
    public function getActiveEmployeesCount()
    {
        return $this->employees()->whereHas('user', function ($query) {
            $query->where('is_active', true);
        })->count();
    }

    public function getSalaryRangeFormatted()
    {
        if (!$this->min_salary && !$this->max_salary) {
            return 'Tidak ditentukan';
        }

        if ($this->min_salary && $this->max_salary) {
            return 'Rp ' . number_format($this->min_salary, 0, ',', '.') . ' - Rp ' . number_format($this->max_salary, 0, ',', '.');
        }

        if ($this->min_salary) {
            return 'Minimal Rp ' . number_format($this->min_salary, 0, ',', '.');
        }

        return 'Maksimal Rp ' . number_format($this->max_salary, 0, ',', '.');
    }

    public function getLevelName()
    {
        $levels = [
            1 => 'Entry Level',
            2 => 'Junior',
            3 => 'Senior',
            4 => 'Lead',
            5 => 'Manager',
            6 => 'Senior Manager',
            7 => 'Director',
            8 => 'VP',
            9 => 'C-Level'
        ];

        return $levels[$this->level] ?? 'Level ' . $this->level;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }
}
