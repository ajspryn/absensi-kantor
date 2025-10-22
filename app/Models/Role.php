<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'permissions',
        'is_active',
        'is_default',
        'priority'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'priority' => 'integer'
    ];

    // Default permissions available in the system
    public static function getAvailablePermissions()
    {
        return [
            'dashboard' => [
                'admin.dashboard' => 'Akses Dashboard Admin',
                'employee.dashboard' => 'Akses Dashboard Karyawan'
            ],
            'employees' => [
                'employees.view' => 'Lihat Data Karyawan',
                'employees.create' => 'Tambah Karyawan',
                'employees.edit' => 'Edit Karyawan',
                'employees.delete' => 'Hapus Karyawan',
                'employees.manage' => 'Kelola Semua Karyawan'
            ],
            'departments' => [
                'departments.view' => 'Lihat Departemen',
                'departments.create' => 'Tambah Departemen',
                'departments.edit' => 'Edit Departemen',
                'departments.delete' => 'Hapus Departemen',
                'departments.manage' => 'Kelola Departemen'
            ],
            'positions' => [
                'positions.view' => 'Lihat Posisi',
                'positions.create' => 'Tambah Posisi',
                'positions.edit' => 'Edit Posisi',
                'positions.delete' => 'Hapus Posisi',
                'positions.manage' => 'Kelola Posisi'
            ],
            'attendance' => [
                'attendance.view' => 'Lihat Data Absensi',
                'attendance.manage' => 'Kelola Data Absensi',
                'attendance.checkin' => 'Check-in Absensi',
                'attendance.checkout' => 'Check-out Absensi',
                'attendance.reports' => 'Laporan Absensi',
                'attendance.corrections.request' => 'Ajukan Koreksi Absensi',
                'attendance.corrections.approve' => 'Setujui Koreksi Absensi'
            ],
            'schedules' => [
                'schedules.view' => 'Lihat Jadwal Kerja',
                'schedules.create' => 'Tambah Jadwal Kerja',
                'schedules.edit' => 'Edit Jadwal Kerja',
                'schedules.delete' => 'Hapus Jadwal Kerja',
                'schedules.assign' => 'Assign Jadwal Kerja'
            ],
            'locations' => [
                'locations.view' => 'Lihat Lokasi Kantor',
                'locations.create' => 'Tambah Lokasi Kantor',
                'locations.edit' => 'Edit Lokasi Kantor',
                'locations.delete' => 'Hapus Lokasi Kantor'
            ],
            'roles' => [
                'roles.view' => 'Lihat Role',
                'roles.create' => 'Tambah Role',
                'roles.edit' => 'Edit Role',
                'roles.delete' => 'Hapus Role',
                'roles.assign' => 'Assign Role ke User'
            ],
            'settings' => [
                'settings.view' => 'Lihat Pengaturan',
                'settings.edit' => 'Edit Pengaturan',
                'settings.system' => 'Pengaturan Sistem'
            ],
            'reports' => [
                'reports.view' => 'Lihat Laporan',
                'reports.export' => 'Export Laporan',
                'reports.analytics' => 'Analytics & Statistics'
            ]
        ];
    }

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    // Check if role has specific permission
    public function hasPermission($permission)
    {
        if (!$this->is_active) {
            return false;
        }

        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    // Check if role has any permission from array
    public function hasAnyPermission(array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    // Check if role has all permissions from array
    public function hasAllPermissions(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    // Grant permission to role
    public function grantPermission($permission)
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
            $this->save();
        }
        return $this;
    }

    // Revoke permission from role
    public function revokePermission($permission)
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_filter($permissions, function ($p) use ($permission) {
            return $p !== $permission;
        });
        $this->permissions = array_values($permissions);
        $this->save();
        return $this;
    }

    // Sync permissions (replace all)
    public function syncPermissions(array $permissions)
    {
        $this->permissions = $permissions;
        $this->save();
        return $this;
    }

    // Get default role for new users
    public static function getDefaultRole()
    {
        return static::where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    // Mutators
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst(strtolower($value));
    }

    // Accessors
    public function getPermissionCountAttribute()
    {
        return count($this->permissions ?? []);
    }

    public function getUserCountAttribute()
    {
        return $this->users()->count();
    }

    public function getIsSystemRoleAttribute()
    {
        return in_array(strtolower($this->name), ['admin', 'super admin', 'system']);
    }

    // Helper methods
    public function canBeDeleted()
    {
        return !$this->is_system_role && $this->users()->count() === 0;
    }

    public function getPermissionsByCategory()
    {
        $allPermissions = static::getAvailablePermissions();
        $rolePermissions = $this->permissions ?? [];
        $categorized = [];

        foreach ($allPermissions as $category => $permissions) {
            $categorized[$category] = [];
            foreach ($permissions as $key => $label) {
                $categorized[$category][$key] = [
                    'label' => $label,
                    'granted' => in_array($key, $rolePermissions)
                ];
            }
        }

        return $categorized;
    }
}
