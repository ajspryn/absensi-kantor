<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // Use Laravel's native hashed cast so passwords are hashed when set
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function activeWorkSchedule()
    {
        return $this->hasOne(WorkSchedule::class)
            ->where('is_active', true)
            ->effectiveOn();
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role && strtolower($this->role->name) === 'admin';
    }

    public function isEmployee()
    {
        return $this->role && strtolower($this->role->name) === 'employee';
    }

    public function isManager()
    {
        return $this->role && strtolower($this->role->name) === 'manager';
    }

    // Permission methods
    public function hasPermission($permission)
    {
        if (!$this->role || !$this->role->is_active) {
            return false;
        }

        return $this->role->hasPermission($permission);
    }

    public function hasAnyPermission(array $permissions)
    {
        if (!$this->role || !$this->role->is_active) {
            return false;
        }

        return $this->role->hasAnyPermission($permissions);
    }

    public function hasAllPermissions(array $permissions)
    {
        if (!$this->role || !$this->role->is_active) {
            return false;
        }

        return $this->role->hasAllPermissions($permissions);
    }

    public function canDo($permission)
    {
        return $this->hasPermission($permission);
    }

    public function cannotDo($permission)
    {
        return !$this->hasPermission($permission);
    }

    // Role assignment
    public function assignRole($roleId)
    {
        $this->role_id = $roleId;
        $this->save();
        return $this;
    }

    public function removeRole()
    {
        $this->role_id = null;
        $this->save();
        return $this;
    }

    // Get user permissions
    public function getPermissions()
    {
        return $this->role ? $this->role->permissions : [];
    }

    public function getPermissionsByCategory()
    {
        return $this->role ? $this->role->getPermissionsByCategory() : [];
    }

    // Check if user has role
    public function hasRole($roleName)
    {
        return $this->role && strtolower($this->role->name) === strtolower($roleName);
    }

    public function hasAnyRole(array $roleNames)
    {
        if (!$this->role) {
            return false;
        }

        foreach ($roleNames as $roleName) {
            if (strtolower($this->role->name) === strtolower($roleName)) {
                return true;
            }
        }

        return false;
    }

    // Scopes
    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    public function scopeWithPermission($query, $permission)
    {
        return $query->whereHas('role', function ($q) use ($permission) {
            $q->where('is_active', true)
                ->whereJsonContains('permissions', $permission);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereHas('role', function ($q) {
                $q->where('is_active', true);
            });
    }

    // Accessors
    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->name : 'No Role';
    }

    public function getIsActiveUserAttribute()
    {
        return $this->is_active && $this->role && $this->role->is_active;
    }
}
