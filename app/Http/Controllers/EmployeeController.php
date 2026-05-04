<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Imports\EmployeesImport;
use App\Exports\EmployeeTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'user.role', 'department', 'position']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('department_id')) {
            $query->byDepartment($request->department_id);
        }

        if ($request->filled('position_id')) {
            $query->byPosition($request->position_id);
        }

        if ($request->filled('role_id')) {
            $query->byRole($request->role_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->where('is_active', false);
                });
            }
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(10);

        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $roles = Role::all();

        $stats = [
            'total' => Employee::count(),
            'active' => Employee::active()->count(),
            'inactive' => Employee::whereHas('user', function ($q) {
                $q->where('is_active', false);
            })->count(),
            'with_position' => Employee::whereNotNull('position_id')->count(),
            'new_this_month' => Employee::whereMonth('created_at', now()->month)->count(),
        ];

        return view('employees.index', compact('employees', 'departments', 'positions', 'roles', 'stats'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $workSchedules = \App\Models\WorkSchedule::where('is_active', true)->get();
        return view('admin.employees.create', compact('departments', 'positions', 'workSchedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'required|string|unique:employees',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'work_schedule_id' => 'required|exists:work_schedules,id',
            // optional profile fields
            'nik_ktp' => 'nullable|string|max:50',
            'address_ktp' => 'nullable|string',
            'address_domisili' => 'nullable|string',
            'mobile' => 'nullable|string|max:30',
            'gender' => 'nullable|in:M,F',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'health_condition' => 'nullable|string',
            'education_history' => 'nullable|string',
        ]);

        // If emergency contacts are submitted, require at least 2 entries
        if ($request->has('emergency') && is_array($request->input('emergency'))) {
            if (count(array_filter($request->input('emergency'), function ($r) {
                return !empty($r['name'] ?? null);
            })) < 2) {
                return back()->withInput()->withErrors(['emergency' => 'Harap masukkan minimal 2 kontak darurat.']);
            }
        }

        DB::beginTransaction();
        try {
            // Ambil role employee default (role_id = 2 untuk employee)
            $employeeRole = \App\Models\Role::where('name', 'employee')->first();
            if (!$employeeRole) {
                $employeeRole = \App\Models\Role::find(2); // fallback ke ID 2
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $employeeRole ? $employeeRole->id : 2,
                'is_active' => true,
            ]);

            // Handle photo upload if provided
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('employee_photos', 'public');
            }

            // Prepare JSON fields if any
            $education = $this->parseJsonField($request->input('education_history'));
            $training = $this->parseJsonField($request->input('training_history'));
            $family = $this->parseJsonField($request->input('family_structure'));
            $emergency = $this->parseJsonField($request->input('emergency_contact'));

            $position = \App\Models\Position::find($request->position_id);
            Employee::create([
                'employee_id' => $request->employee_id,
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'work_schedule_id' => $request->work_schedule_id,
                'hire_date' => now(),
                'is_active' => true,
                'full_name' => $request->name,
                'email' => $request->email,
                'photo' => $photoPath,
                // optional profile fields
                'nik_ktp' => $request->input('nik_ktp'),
                'address_ktp' => $request->input('address_ktp'),
                'address_domisili' => $request->input('address_domisili'),
                'mobile' => $request->input('mobile'),
                'gender' => $request->input('gender'),
                'birth_place' => $request->input('birth_place'),
                'birth_date' => $request->input('birth_date'),
                'health_condition' => $request->input('health_condition'),
                'education_history' => $education,
                'training_history' => $training,
                'family_structure' => $family,
                'emergency_contact' => $emergency,
            ]);

            DB::commit();
            return redirect()->route('admin.employees.index')
                ->with('success', 'Karyawan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal menambahkan karyawan: ' . $e->getMessage());
        }
    }

    public function show(Employee $employee)
    {
        $employee->load(['user', 'user.role', 'department', 'position', 'attendances' => function ($query) {
            $query->orderBy('date', 'desc')->limit(10);
        }]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();

        // Load active roles and ensure the employee role is included so it can be selected
        $roles = Role::where('is_active', true)->orderBy('priority', 'asc')->get();
        $employee->load(['user', 'department', 'position']);

        return view('employees.edit', compact('employee', 'departments', 'positions', 'roles'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'employee_id' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
            'allow_remote_attendance' => 'boolean',
            // Extended profile fields
            'nik_ktp' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:255',
            'address_ktp' => 'nullable|string',
            'address_domisili' => 'nullable|string',
            'mobile' => 'nullable|string|max:30',
            'gender' => 'nullable|in:M,F',
            'height_cm' => 'nullable|integer|min:0',
            'weight_kg' => 'nullable|integer|min:0',
            'hobby' => 'nullable|string|max:255',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'marital_status' => 'nullable|string|max:50',
            'residence_status' => 'nullable|string|max:50',
            'health_condition' => 'nullable|string',
            'degenerative_diseases' => 'nullable|string',
            'education_history' => 'nullable|string',
            'training_history' => 'nullable|string',
            'family_structure' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $userData = [
                'name' => $request->full_name,
                'email' => $request->email,
                'role_id' => $request->role_id,
                'is_active' => $request->has('is_active'),
            ];

            if ($request->filled('password')) {
                $request->validate(['password' => 'min:8']);
                $userData['password'] = Hash::make($request->password);
            }

            $employee->user->update($userData);

            $photoPath = $employee->photo;
            if ($request->hasFile('photo')) {
                if ($employee->photo) {
                    Storage::disk('public')->delete($employee->photo);
                }
                $photoPath = $request->file('photo')->store('employee_photos', 'public');
            }

            $employee->update([
                'employee_id' => $request->employee_id,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'nik_ktp' => $request->input('nik_ktp'),
                'jabatan' => $request->input('jabatan'),
                'address_ktp' => $request->input('address_ktp'),
                'address_domisili' => $request->input('address_domisili'),
                'mobile' => $request->input('mobile'),
                'gender' => $request->input('gender'),
                'height_cm' => $request->input('height_cm'),
                'weight_kg' => $request->input('weight_kg'),
                'hobby' => $request->input('hobby'),
                'birth_place' => $request->input('birth_place'),
                'birth_date' => $request->input('birth_date'),
                'marital_status' => $request->input('marital_status'),
                'residence_status' => $request->input('residence_status'),
                'health_condition' => $request->input('health_condition'),
                'degenerative_diseases' => $request->input('degenerative_diseases'),
                'education_history' => $this->parseJsonField($request->input('education_history')),
                'training_history' => $this->parseJsonField($request->input('training_history')),
                'family_structure' => $this->parseJsonField($request->input('family_structure')),
                'emergency_contact' => $this->parseJsonField($request->input('emergency_contact')),
                'hire_date' => $request->hire_date,
                'salary' => $request->salary,
                'photo' => $photoPath,
                'is_active' => $request->has('is_active'),
                'allow_remote_attendance' => (int) $request->input('allow_remote_attendance', 0),
                // Keep employee.email in sync with user.email when updating
                'email' => $request->email,
            ]);

            DB::commit();
            return redirect()->route('admin.employees.index')
                ->with('success', 'Data karyawan berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal mengupdate karyawan: ' . $e->getMessage());
        }
    }

    /**
     * Try to parse a JSON-like field submitted from the form.
     * If the value is valid JSON and decodes to an array, return the array.
     * Otherwise return a fallback array with a single 'raw' key containing the string (if present), or null.
     */
    private function parseJsonField($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        // Try decode
        $decoded = null;
        try {
            $decoded = json_decode($value, true);
        } catch (\Throwable $e) {
            $decoded = null;
        }

        if (is_array($decoded)) {
            return $decoded;
        }

        // If not valid JSON, store as raw text so UI can migrate later
        return ['raw' => $value];
    }

    public function destroy(Employee $employee)
    {
        DB::beginTransaction();
        try {
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }

            $user = $employee->user;
            $employee->delete();
            $user->delete();

            DB::commit();
            return redirect()->route('admin.employees.index')
                ->with('success', 'Karyawan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus karyawan: ' . $e->getMessage());
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,change_department,change_position,change_role',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'department_id' => 'required_if:action,change_department|exists:departments,id',
            'position_id' => 'required_if:action,change_position|exists:positions,id',
            'role_id' => 'required_if:action,change_role|exists:roles,id',
        ]);

        $employees = Employee::whereIn('id', $request->employee_ids)->with('user')->get();
        $count = 0;

        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                switch ($request->action) {
                    case 'activate':
                        $employee->user->update(['is_active' => true]);
                        $employee->update(['is_active' => true]);
                        $count++;
                        break;
                    case 'deactivate':
                        $employee->user->update(['is_active' => false]);
                        $employee->update(['is_active' => false]);
                        $count++;
                        break;
                    case 'delete':
                        if ($employee->photo) {
                            Storage::disk('public')->delete($employee->photo);
                        }
                        $user = $employee->user;
                        $employee->delete();
                        $user->delete();
                        $count++;
                        break;
                    case 'change_department':
                        $employee->update(['department_id' => $request->department_id]);
                        $count++;
                        break;
                    case 'change_position':
                        $employee->update(['position_id' => $request->position_id]);
                        $count++;
                        break;
                    case 'change_role':
                        $employee->user->update(['role_id' => $request->role_id]);
                        $count++;
                        break;
                }
            }

            DB::commit();
            return back()->with('success', "Berhasil memproses {$count} karyawan.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function analytics(Request $request)
    {
        $period = $request->get('period', 'month');

        $analyticsData = [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::active()->count(),
            'inactive_employees' => Employee::whereHas('user', function ($q) {
                $q->where('is_active', false);
            })->count(),
            'by_department' => Employee::with('department')
                ->get()
                ->groupBy('department.name')
                ->map(function ($employees) {
                    return $employees->count();
                }),
            'by_position' => Employee::with('position')
                ->get()
                ->groupBy('position.name')
                ->map(function ($employees) {
                    return $employees->count();
                }),
            'by_role' => Employee::with('user.role')
                ->get()
                ->groupBy('user.role.name')
                ->map(function ($employees) {
                    return $employees->count();
                }),
            'recent_hires' => Employee::where('hire_date', '>=', now()->subMonths(3))
                ->orderBy('hire_date', 'desc')
                ->take(10)
                ->get(),
        ];

        return view('employees.analytics', compact('analyticsData', 'period'));
    }

    public function completeProfile()
    {
        $user = Auth::user();

        // If the user already has a complete employee record, redirect away
        if ($user && !$user->employee) {
            // Try to auto-attach an existing employee row that matches the user's email
            try {
                $existing = Employee::whereNotNull('email')
                    ->where('email', $user->email)
                    ->first();
                if ($existing) {
                    $existing->user_id = $user->id;
                    $existing->save();
                    // set relation in memory so view can access it without another query
                    $user->employee = $existing;
                    Log::info("Auto-attached employee id={$existing->id} to user_id={$user->id}");
                }
            } catch (\Exception $e) {
                Log::warning('Failed to auto-attach employee for user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        if ($user && $user->employee) {
            $employee = $user->employee;
            // Use the same comprehensive required list the UI checks for
            $required = [
                'employee_id',
                'full_name',
                'department_id',
                'position_id',
                'work_schedule_id',
                'email',
                'phone',
                'mobile',
                'address',
                'address_ktp',
                'address_domisili',
                'nik_ktp',
                'gender',
                'birth_place',
                'birth_date',
                'marital_status',
                'residence_status',
                'hire_date',
                'photo',
                'education_history',
                'training_history',
                'family_structure',
                'emergency_contact'
            ];

            $missing = false;
            foreach ($required as $field) {
                $val = $employee->{$field} ?? null;
                if (is_array($val) || $val instanceof \Illuminate\Contracts\Support\Arrayable) {
                    if (empty($val)) {
                        $missing = true;
                        break;
                    }
                } else {
                    if (empty($val) && !is_numeric($val)) {
                        $missing = true;
                        break;
                    }
                }
            }

            if (!$missing) {
                // Already complete, send them to dashboard/profile
                return redirect()->route('dashboard');
            }
        }

        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();

        // Determine current position id to help the view preselect option when legacy 'position' string exists
        $currentPositionId = null;
        $employee = $user->employee ?? null;
        if ($employee) {
            if (!empty($employee->position_id)) {
                $currentPositionId = $employee->position_id;
            } elseif (!empty($employee->position)) {
                // try to find position by name (case-insensitive)
                $found = Position::whereRaw('lower(name) = ?', [mb_strtolower($employee->position)])->first();
                if ($found) {
                    $currentPositionId = $found->id;
                }
            }
        }

        return view('employees.complete-profile', compact('departments', 'positions', 'currentPositionId'));
    }

    public function storeProfile(Request $request)
    {
        $user = Auth::user();
        // If the user already has an employee record, we'll update it instead of creating a duplicate.
        $employee = $user->employee;

        $employeeIdRule = 'required|string|unique:employees,employee_id';
        if ($employee) {
            // ignore current employee when validating unique employee_id
            $employeeIdRule = 'required|string|unique:employees,employee_id,' . $employee->id;
        }

        $request->validate([
            'employee_id' => $employeeIdRule,
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'hire_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            // personal
            'nik_ktp' => 'nullable|digits_between:1,20',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:M,F',
            'height_cm' => 'nullable|integer|min:0',
            'weight_kg' => 'nullable|integer|min:0',
            'hobby' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:50',
            'residence_status' => 'nullable|string|max:50',
            // health
            'health_condition' => 'nullable|string',
            'degenerative_diseases' => 'nullable|string',
            'has_medical_history' => 'nullable|boolean',
            // JSON/text areas (allow string or JSON)
            'education_history' => 'nullable|string',
            'training_history' => 'nullable|string',
            'family_structure' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            // financing
            'has_credit_issue' => 'nullable|in:0,1,yes,no,YA,TIDAK,Yes,No',
            'credit_institution' => 'nullable|string|max:255',
            'credit_plafond' => 'nullable|numeric',
            'credit_monthly_installment' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('employee_photos', 'public');
            }

            if ($employee) {
                // update existing
                $updateData = [
                    'employee_id' => $request->employee_id,
                    'department_id' => $request->department_id,
                    'full_name' => $request->full_name,
                    'phone' => $request->phone,
                    'mobile' => $request->mobile,
                    'address' => $request->input('address'),
                    'address_ktp' => $request->input('address_ktp'),
                    'address_domisili' => $request->input('address_domisili'),
                    'hire_date' => $request->hire_date,
                    'email' => $user->email,
                    'is_active' => true,
                    // personal
                    'nik_ktp' => $request->input('nik_ktp'),
                    'birth_place' => $request->input('birth_place'),
                    'birth_date' => $request->input('birth_date'),
                    'gender' => $request->input('gender'),
                    'height_cm' => $request->input('height_cm'),
                    'weight_kg' => $request->input('weight_kg'),
                    'hobby' => $request->input('hobby'),
                    'marital_status' => $request->input('marital_status'),
                    'residence_status' => $request->input('residence_status'),
                    // health
                    'health_condition' => $request->input('health_condition'),
                    'degenerative_diseases' => $request->input('degenerative_diseases'),
                    'has_medical_history' => $request->input('has_medical_history') ? 1 : 0,
                    // financing
                    'has_credit_issue' => $request->input('has_credit_issue'),
                    'credit_institution' => $request->input('credit_institution'),
                    'credit_plafond' => $request->input('credit_plafond'),
                    'credit_monthly_installment' => $request->input('credit_monthly_installment'),
                ];

                // keep both position_id and position string consistent
                if ($request->filled('position_id')) {
                    $positionModel = Position::find($request->position_id);
                    $updateData['position_id'] = $request->position_id;
                    $updateData['position'] = $positionModel ? $positionModel->name : null;
                } elseif ($request->filled('position')) {
                    $updateData['position'] = $request->position;
                }

                if ($photoPath) {
                    if ($employee->photo) {
                        Storage::disk('public')->delete($employee->photo);
                    }
                    $updateData['photo'] = $photoPath;
                }

                // Parse JSON-like fields
                $updateData['education_history'] = $this->parseJsonField($request->input('education_history'));
                $updateData['training_history'] = $this->parseJsonField($request->input('training_history'));
                $updateData['family_structure'] = $this->parseJsonField($request->input('family_structure'));
                $updateData['emergency_contact'] = $this->parseJsonField($request->input('emergency_contact'));

                $employee->update($updateData);

                // Sync repeatable related records if provided (update case)
                if ($request->has('education') && is_array($request->input('education'))) {
                    $employee->educationRecords()->delete();
                    foreach ($request->input('education') as $row) {
                        if (empty($row['school_name']) && empty($row['major'])) continue;
                        $employee->educationRecords()->create([
                            'school_name' => $row['school_name'] ?? null,
                            'city' => $row['city'] ?? null,
                            'major' => $row['major'] ?? null,
                            'start_year' => $row['start_year'] ?? null,
                            'end_year' => $row['end_year'] ?? null,
                            'status' => $row['status'] ?? null,
                        ]);
                    }
                }

                if ($request->has('training') && is_array($request->input('training'))) {
                    $employee->trainingRecords()->delete();
                    foreach ($request->input('training') as $row) {
                        if (empty($row['course_name'])) continue;
                        $employee->trainingRecords()->create([
                            'course_name' => $row['course_name'] ?? null,
                            'organizer' => $row['organizer'] ?? null,
                            'city' => $row['city'] ?? null,
                            'duration' => $row['duration'] ?? null,
                            'year' => $row['year'] ?? null,
                            'paid_by' => $row['paid_by'] ?? null,
                        ]);
                    }
                }

                if ($request->has('family') && is_array($request->input('family'))) {
                    $employee->familyMembers()->delete();
                    foreach ($request->input('family') as $row) {
                        if (empty($row['name'])) continue;
                        $employee->familyMembers()->create([
                            'relation' => $row['relation'] ?? null,
                            'name' => $row['name'] ?? null,
                            'gender' => $row['gender'] ?? null,
                            'last_education' => $row['last_education'] ?? null,
                            'last_job' => $row['last_job'] ?? null,
                            'age' => $row['age'] ?? null,
                        ]);
                    }
                }

                if ($request->has('emergency') && is_array($request->input('emergency'))) {
                    $employee->emergencyContacts()->delete();
                    foreach ($request->input('emergency') as $idx => $row) {
                        if (empty($row['name'])) continue;
                        $employee->emergencyContacts()->create([
                            'name' => $row['name'] ?? null,
                            'address' => $row['address'] ?? null,
                            'relation' => $row['relation'] ?? null,
                            'phone' => $row['phone'] ?? null,
                            'priority' => ($row['priority'] ?? ($idx + 1)),
                        ]);
                    }
                }
            } else {
                // create new
                $positionName = null;
                if ($request->filled('position_id')) {
                    $pos = Position::find($request->position_id);
                    $positionName = $pos ? $pos->name : null;
                }
                $createData = [
                    'employee_id' => $request->employee_id,
                    'user_id' => $user->id,
                    'department_id' => $request->department_id,
                    'position_id' => $request->position_id ?? null,
                    'position' => $positionName,
                    'full_name' => $request->full_name,
                    'phone' => $request->phone,
                    'mobile' => $request->mobile,
                    'address' => $request->input('address'),
                    'address_ktp' => $request->input('address_ktp'),
                    'address_domisili' => $request->input('address_domisili'),
                    'hire_date' => $request->hire_date,
                    'photo' => $photoPath,
                    'email' => $user->email,
                    'is_active' => true,
                    'nik_ktp' => $request->input('nik_ktp'),
                    'birth_place' => $request->input('birth_place'),
                    'birth_date' => $request->input('birth_date'),
                    'gender' => $request->input('gender'),
                    'height_cm' => $request->input('height_cm'),
                    'weight_kg' => $request->input('weight_kg'),
                    'hobby' => $request->input('hobby'),
                    'marital_status' => $request->input('marital_status'),
                    'residence_status' => $request->input('residence_status'),
                    'health_condition' => $request->input('health_condition'),
                    'degenerative_diseases' => $request->input('degenerative_diseases'),
                    'has_medical_history' => $request->input('has_medical_history') ? 1 : 0,
                    'has_credit_issue' => $request->input('has_credit_issue'),
                    'credit_institution' => $request->input('credit_institution'),
                    'credit_plafond' => $request->input('credit_plafond'),
                    'credit_monthly_installment' => $request->input('credit_monthly_installment'),
                ];

                $createData['education_history'] = $this->parseJsonField($request->input('education_history'));
                $createData['training_history'] = $this->parseJsonField($request->input('training_history'));
                $createData['family_structure'] = $this->parseJsonField($request->input('family_structure'));
                $createData['emergency_contact'] = $this->parseJsonField($request->input('emergency_contact'));

                $employee = Employee::create($createData);

                // Sync repeatable related records if provided
                if ($request->has('education') && is_array($request->input('education'))) {
                    $employee->educationRecords()->delete();
                    foreach ($request->input('education') as $row) {
                        if (empty($row['school_name']) && empty($row['major'])) continue;
                        $employee->educationRecords()->create([
                            'school_name' => $row['school_name'] ?? null,
                            'city' => $row['city'] ?? null,
                            'major' => $row['major'] ?? null,
                            'start_year' => $row['start_year'] ?? null,
                            'end_year' => $row['end_year'] ?? null,
                            'status' => $row['status'] ?? null,
                        ]);
                    }
                }

                if ($request->has('training') && is_array($request->input('training'))) {
                    $employee->trainingRecords()->delete();
                    foreach ($request->input('training') as $row) {
                        if (empty($row['course_name'])) continue;
                        $employee->trainingRecords()->create([
                            'course_name' => $row['course_name'] ?? null,
                            'organizer' => $row['organizer'] ?? null,
                            'city' => $row['city'] ?? null,
                            'duration' => $row['duration'] ?? null,
                            'year' => $row['year'] ?? null,
                            'paid_by' => $row['paid_by'] ?? null,
                        ]);
                    }
                }

                if ($request->has('family') && is_array($request->input('family'))) {
                    $employee->familyMembers()->delete();
                    foreach ($request->input('family') as $row) {
                        if (empty($row['name'])) continue;
                        $employee->familyMembers()->create([
                            'relation' => $row['relation'] ?? null,
                            'name' => $row['name'] ?? null,
                            'gender' => $row['gender'] ?? null,
                            'last_education' => $row['last_education'] ?? null,
                            'last_job' => $row['last_job'] ?? null,
                            'age' => $row['age'] ?? null,
                        ]);
                    }
                }

                if ($request->has('emergency') && is_array($request->input('emergency'))) {
                    $employee->emergencyContacts()->delete();
                    foreach ($request->input('emergency') as $idx => $row) {
                        if (empty($row['name'])) continue;
                        $employee->emergencyContacts()->create([
                            'name' => $row['name'] ?? null,
                            'address' => $row['address'] ?? null,
                            'relation' => $row['relation'] ?? null,
                            'phone' => $row['phone'] ?? null,
                            'priority' => ($row['priority'] ?? ($idx + 1)),
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('dashboard')
                ->with('success', 'Profil berhasil dilengkapi. Selamat datang!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal melengkapi profil: ' . $e->getMessage());
        }
    }

    /**
     * Show bulk import form
     */
    public function showImport()
    {
        return view('admin.employees.import');
    }

    /**
     * Download template for bulk import
     */
    public function downloadTemplate()
    {
        try {
            Log::info('Template download attempt started');

            // Use fixed CSV template
            $csvPath = public_path('template_karyawan_fixed.csv');

            if (file_exists($csvPath)) {
                Log::info('Returning fixed CSV template');
                return response()->download($csvPath, 'template_karyawan.csv', [
                    'Content-Type' => 'text/csv; charset=utf-8',
                    'Content-Disposition' => 'attachment; filename="template_karyawan.csv"'
                ]);
            }

            // Fallback to original CSV
            $csvPath = public_path('template_karyawan.csv');
            if (file_exists($csvPath)) {
                Log::info('Returning original CSV template');
                return response()->download($csvPath, 'template_karyawan.csv', [
                    'Content-Type' => 'text/csv; charset=utf-8',
                ]);
            }

            // Final fallback: create dynamic CSV
            Log::info('Creating dynamic CSV template');
            return $this->downloadTemplateCSV();
        } catch (\Exception $e) {
            Log::error('Template download failed: ' . $e->getMessage());

            // Return instructions as fallback
            $instructions = "# Template Import Karyawan\n\n";
            $instructions .= "Buat file Excel/CSV dengan kolom berikut:\n\n";
            $instructions .= "id_karyawan,nama_lengkap,email,password,departemen,posisi,telepon,alamat,tanggal_masuk,gaji,status_aktif,remote_attendance\n\n";
            $instructions .= "Contoh data:\n";
            $instructions .= "EMP001,John Doe,john.doe@email.com,password,IT,Software Developer,+6281234567890,\"Jl. Contoh No. 123 Jakarta\",2024-01-15,5000000,aktif,ya\n\n";
            $instructions .= "Catatan:\n";
            $instructions .= "- status_aktif: aktif/nonaktif\n";
            $instructions .= "- remote_attendance: ya/tidak\n";
            $instructions .= "- tanggal_masuk: YYYY-MM-DD\n";
            $instructions .= "- gaji: angka saja\n";

            return response($instructions, 200)
                ->header('Content-Type', 'text/plain; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="template_karyawan_instructions.txt"');
        }
    }

    /**
     * Fallback: Download CSV template
     */
    private function downloadTemplateCSV()
    {
        $export = new EmployeeTemplateExport;
        $headings = $export->headings();
        $array = $export->array();

        $csvContent = implode(',', $headings) . "\n";
        foreach ($array as $row) {
            $csvContent .= '"' . implode('","', $row) . '"' . "\n";
        }

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="template_karyawan.csv"');
    }

    /**
     * Process bulk import
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240' // Max 10MB
        ]);

        try {
            $import = new EmployeesImport();
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getImportedCount();
            $skippedCount = $import->getSkippedCount();
            $failureCount = count($import->failures());
            $errorCount = count($import->errors());

            $message = "Import selesai! ";
            $message .= "Berhasil: {$importedCount} karyawan, ";
            $message .= "Dilewati: {$skippedCount} baris";

            if ($failureCount > 0 || $errorCount > 0) {
                $message .= ", Gagal: " . ($failureCount + $errorCount) . " baris";
            }

            // Check if there are failures and show them
            if ($import->failures()->isNotEmpty() || $import->errors()->isNotEmpty()) {
                $failures = $import->failures();
                $errors = $import->errors();

                return redirect()->route('admin.employees.import')
                    ->with('warning', $message)
                    ->with('failures', $failures)
                    ->with('import_errors', $errors);
            }

            return redirect()->route('admin.employees.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }
}
