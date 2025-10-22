<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::withCount('users');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->has('active') && $request->get('active') !== '') {
            $query->where('is_active', $request->boolean('active'));
        }

        $roles = $query
            ->orderBy('priority')
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        $totalRoles = Role::count();
        $activeRoles = Role::where('is_active', true)->count();
        $totalUsers = User::whereNotNull('role_id')->count();
        $usersWithoutRole = User::whereNull('role_id')->count();

        return view('admin.roles.index', compact(
            'roles',
            'totalRoles',
            'activeRoles',
            'totalUsers',
            'usersWithoutRole'
        ));
    }

    public function create()
    {
        $availablePermissions = Role::getAvailablePermissions();

        return view('admin.roles.create', compact('availablePermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'priority' => 'required|integer|between:0,999',
        ]);

        // Ensure only one default role exists
        if ($validated['is_default'] ?? false) {
            Role::where('is_default', true)->update(['is_default' => false]);
        }

        // Clean permissions array
        $validated['permissions'] = array_filter($validated['permissions'] ?? []);

        $role = Role::create($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil dibuat!');
    }

    public function show(Role $role)
    {
        $role->load(['users.employee']);
        $availablePermissions = Role::getAvailablePermissions();
        $rolePermissions = $role->getPermissionsByCategory();

        return view('admin.roles.show', compact('role', 'availablePermissions', 'rolePermissions'));
    }

    public function edit(Role $role)
    {
        $availablePermissions = Role::getAvailablePermissions();
        $rolePermissions = $role->getPermissionsByCategory();

        return view('admin.roles.edit', compact('role', 'availablePermissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'priority' => 'required|integer|between:0,999',
        ]);

        // Ensure only one default role exists
        if ($validated['is_default'] ?? false) {
            Role::where('is_default', true)
                ->where('id', '!=', $role->id)
                ->update(['is_default' => false]);
        }

        // Clean permissions array
        $validated['permissions'] = array_filter($validated['permissions'] ?? []);

        $role->update($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil diperbarui!');
    }

    public function destroy(Role $role)
    {
        // Check if role can be deleted
        if (!$role->canBeDeleted()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus role system atau role yang masih digunakan!');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil dihapus!');
    }

    public function toggleStatus(Role $role)
    {
        // Prevent deactivating system roles
        if ($role->is_system_role && $role->is_active) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menonaktifkan role system!');
        }

        $role->update([
            'is_active' => !$role->is_active
        ]);

        $status = $role->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Role berhasil {$status}!");
    }

    public function setDefault(Role $role)
    {
        if (!$role->is_active) {
            return redirect()->back()
                ->with('error', 'Hanya role aktif yang dapat dijadikan default!');
        }

        // Remove default from all other roles
        Role::where('is_default', true)->update(['is_default' => false]);

        // Set this role as default
        $role->update(['is_default' => true]);

        return redirect()->back()
            ->with('success', "Role {$role->name} berhasil dijadikan default!");
    }

    public function assignUsers(Request $request, Role $role)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        User::whereIn('id', $validated['user_ids'])
            ->update(['role_id' => $role->id]);

        $count = count($validated['user_ids']);

        return redirect()->back()
            ->with('success', "{$count} user berhasil di-assign ke role {$role->name}!");
    }

    public function removeUser(Request $request, Role $role)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Check if user currently has this role
        if ($user->role_id !== $role->id) {
            return redirect()->back()
                ->with('error', 'User tidak memiliki role ini!');
        }

        // Assign default role or remove role
        $defaultRole = Role::getDefaultRole();
        $user->update([
            'role_id' => $defaultRole ? $defaultRole->id : null
        ]);

        return redirect()->back()
            ->with('success', "User berhasil dihapus dari role {$role->name}!");
    }

    public function permissions(Role $role)
    {
        $availablePermissions = Role::getAvailablePermissions();
        $rolePermissions = $role->getPermissionsByCategory();

        return view('admin.roles.permissions', compact('role', 'availablePermissions', 'rolePermissions'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string'
        ]);

        $permissions = array_filter($validated['permissions'] ?? []);
        $role->syncPermissions($permissions);

        return redirect()->back()
            ->with('success', 'Permissions berhasil diperbarui!');
    }
}
