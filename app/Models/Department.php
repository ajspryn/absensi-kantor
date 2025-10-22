<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function manager()
    {
        // Manager is stored as a User reference (manager_id -> users.id)
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Helper methods
    public function getActiveEmployeesCount()
    {
        return $this->employees()->whereHas('user', function ($query) {
            $query->where('is_active', true);
        })->count();
    }

    public function getActivePositionsCount()
    {
        return $this->positions()->where('is_active', true)->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
