<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::with(['department', 'employees'])
            ->withCount('employees');

        // Filters
        // Only filter by department if the positions table actually has the column
        if (Schema::hasColumn('positions', 'department_id') && $request->filled('department_id')) {
            $query->where('department_id', $request->get('department_id'));
        }
        if ($request->has('active') && $request->get('active') !== '') {
            $query->where('is_active', $request->boolean('active'));
        }

        // Order by department_id only when column exists to avoid sqlite missing-column errors
        if (Schema::hasColumn('positions', 'department_id')) {
            $query->orderBy('department_id');
        }

        // Order by level only if the column exists in the schema
        if (Schema::hasColumn('positions', 'level')) {
            $query->orderBy('level');
        }
        $query->orderBy('name');

        $positions = $query->paginate(10)->appends($request->query());

        $totalPositions = Position::count();
        $activePositions = Position::where('is_active', true)->count();
        $totalEmployees = Position::withCount('employees')->get()->sum('employees_count');
        $positionsWithEmployees = Position::whereHas('employees')->count();

        // For filter select options
        $departmentsList = Department::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.positions.index', compact(
            'positions',
            'totalPositions',
            'activePositions',
            'totalEmployees',
            'positionsWithEmployees',
            'departmentsList'
        ));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('admin.positions.create', compact('departments'));
    }

    public function store(Request $request)
    {
        // Build validation rules conditionally based on schema
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // level is optional now (some deployments don't want to set it from UI)
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'is_active' => 'sometimes|boolean',
        ];

        // department_id is optional in the UI now; validate only if column exists
        if (Schema::hasColumn('positions', 'department_id')) {
            $rules['department_id'] = 'nullable|exists:departments,id';
        }

        // level may or may not exist; make it optional when present
        if (Schema::hasColumn('positions', 'level')) {
            $rules['level'] = 'nullable|integer';
        }

        $validated = $request->validate($rules);

        // Check unique name within department
        // Unique name within department if department column exists, otherwise unique name globally
        $existingQuery = Position::where('name', $validated['name']);
        if (Schema::hasColumn('positions', 'department_id') && isset($validated['department_id'])) {
            $existingQuery->where('department_id', $validated['department_id']);
        }
        $existingPosition = $existingQuery->first();

        if ($existingPosition) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Posisi dengan nama tersebut sudah ada di departemen ini!');
        }

        // Filter validated data to only columns that exist in the positions table to avoid
        // inserting keys for missing columns (some test/sqlite schemas may omit department_id)
        $columns = Schema::getColumnListing('positions');
        $payload = array_intersect_key($validated, array_flip($columns));

        Position::create($payload);

        return redirect()->route('admin.positions.index')
            ->with('success', 'Posisi berhasil dibuat!');
    }

    public function show(Position $position)
    {
        $position->load(['department', 'employees.user']);

        return view('admin.positions.show', compact('position'));
    }

    public function edit(Position $position)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('admin.positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, Position $position)
    {
        // Build validation rules conditionally based on schema for update
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // level optional during update as well
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'is_active' => 'sometimes|boolean',
        ];
        if (Schema::hasColumn('positions', 'department_id')) {
            $rules['department_id'] = 'nullable|exists:departments,id';
        }
        if (Schema::hasColumn('positions', 'level')) {
            $rules['level'] = 'nullable|integer';
        }

        $validated = $request->validate($rules);

        // Check unique name within department (excluding current position)
        $existingQuery = Position::where('name', $validated['name']);
        if (Schema::hasColumn('positions', 'department_id') && isset($validated['department_id'])) {
            $existingQuery->where('department_id', $validated['department_id']);
        }
        $existingQuery->where('id', '!=', $position->id);
        $existingPosition = $existingQuery->first();

        if ($existingPosition) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Posisi dengan nama tersebut sudah ada di departemen ini!');
        }

        // Filter validated data to only existing columns before updating
        $columns = Schema::getColumnListing('positions');
        $payload = array_intersect_key($validated, array_flip($columns));

        $position->update($payload);

        return redirect()->route('admin.positions.index')
            ->with('success', 'Posisi berhasil diperbarui!');
    }

    public function destroy(Position $position)
    {
        // If there are employees, unassign them (set position_id = null) before deleting.
        // Use a transaction to ensure both operations succeed or fail together.
        DB::transaction(function () use ($position) {
            if ($position->employees()->count() > 0) {
                // Set position_id to null for all employees holding this position
                $position->employees()->update(['position_id' => null]);
            }

            $position->delete();
        });

        return redirect()->route('admin.positions.index')
            ->with('success', 'Posisi berhasil dihapus dan karyawan yang terkait telah dilepaskan.');
    }

    public function toggleStatus(Position $position)
    {
        $position->update([
            'is_active' => !$position->is_active
        ]);

        $status = $position->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Posisi berhasil {$status}!");
    }

    public function getPositionsByDepartment(Request $request)
    {
        $departmentId = $request->get('department_id');

        $query = Position::where('is_active', true);

        // Order by level only when column exists
        if (Schema::hasColumn('positions', 'level')) {
            $query->orderBy('level')->orderBy('name');
        } else {
            $query->orderBy('name');
        }

        if (Schema::hasColumn('positions', 'department_id')) {
            $query->where('department_id', $departmentId);
        }

        $select = ['id', 'name'];
        if (Schema::hasColumn('positions', 'level')) {
            $select[] = 'level';
        }

        $positions = $query->get($select);

        return response()->json($positions);
    }
}
