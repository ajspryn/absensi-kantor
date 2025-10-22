<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        // Use withCount for related counts, but guard for older/simpler schemas where
        // `positions.department_id` may not exist (sqlite tests or earlier migrations).
        $with = ['employees'];

        if (Schema::hasColumn('positions', 'department_id')) {
            $with[] = 'positions';
        }

        // Also eager-load manager and its user relationship to avoid null access in views
        // manager() returns a User model. eager-load only 'manager' (no 'user' on User)
        $query = Department::with('manager')->withCount($with);

        if ($request->has('active') && $request->get('active') !== '') {
            $query->where('is_active', $request->boolean('active'));
        }

        $departments = $query
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        $totalDepartments = Department::count();
        $activeDepartments = Department::where('is_active', true)->count();
        $totalEmployees = Department::withCount('employees')->get()->sum('employees_count');
        $departmentsWithManager = Department::whereNotNull('manager_id')->count();

        return view('admin.departments.index', compact(
            'departments',
            'totalDepartments',
            'activeDepartments',
            'totalEmployees',
            'departmentsWithManager'
        ));
    }

    public function create()
    {
        $availableManagers = Employee::whereHas('user', function ($query) {
            $query->where('is_active', true);
        })->orderBy('full_name')->get();

        return view('admin.departments.create', compact('availableManagers'));
    }

    public function store(Request $request)
    {
        // manager_id should reference users.id (Department.manager() belongsTo User)
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            // Accept a user id here (users.id)
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Departemen berhasil dibuat!');
    }

    public function show(Department $department)
    {
        $department->load(['employees.user', 'positions', 'manager']);

        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $department->load(['employees.user', 'employees.position', 'manager']);

        $availableManagers = Employee::whereHas('user', function ($query) {
            $query->where('is_active', true);
        })->orderBy('full_name')->get();

        return view('admin.departments.edit', compact('department', 'availableManagers'));
    }

    public function update(Request $request, Department $department)
    {
        // manager_id should reference users.id (Department.manager() belongsTo User)
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            // Accept a user id here (users.id)
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Departemen berhasil diperbarui!');
    }

    public function destroy(Department $department)
    {
        // Check if department has employees
        if ($department->employees()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus departemen yang masih memiliki karyawan!');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Departemen berhasil dihapus!');
    }

    public function toggleStatus(Department $department)
    {
        $department->update([
            'is_active' => !$department->is_active
        ]);

        $status = $department->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Departemen berhasil {$status}!");
    }

    public function setManager(Request $request, Department $department)
    {

        // Expect manager_id as a users.id. Find the Employee owning that user.
        $validated = $request->validate([
            'manager_id' => 'required|exists:users,id'
        ]);

        // Find the employee record for this user
        $employee = Employee::where('user_id', $validated['manager_id'])->first();

        if (!$employee) {
            return redirect()->back()
                ->with('error', 'Pengguna yang dipilih bukan karyawan yang valid.');
        }

        if ($employee->department_id && $employee->department_id != $department->id) {
            return redirect()->back()
                ->with('error', 'Karyawan harus berada di departemen yang sama!');
        }

        // Store the user id as manager_id to match Department.manager() relation
        $department->update([
            'manager_id' => $validated['manager_id']
        ]);

        return redirect()->back()
            ->with('success', "Manager departemen berhasil diperbarui!");
    }
}
