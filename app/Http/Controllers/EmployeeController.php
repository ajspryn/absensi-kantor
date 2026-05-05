<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeTemplateExport;
use App\Imports\EmployeesImport;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\StoreProfileRequest;
use App\Http\Requests\Employee\BulkActionEmployeeRequest;
use App\Services\EmployeeService;

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

    public function store(StoreEmployeeRequest $request, EmployeeService $employeeService)
    {
        try {
            $employeeService->createEmployee($request->all(), $request->file('photo'));

            return redirect()->route('admin.employees.index')
                ->with('success', 'Karyawan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan karyawan: '.$e->getMessage());
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

    public function update(UpdateEmployeeRequest $request, Employee $employee, EmployeeService $employeeService)
    {
        try {
            $employeeService->updateEmployee($employee, $request->all(), $request->file('photo'));

            return redirect()->route('admin.employees.index')
                ->with('success', 'Data karyawan berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal mengupdate karyawan: '.$e->getMessage());
        }
    }

    public function destroy(Employee $employee, EmployeeService $employeeService)
    {
        try {
            $employeeService->deleteEmployee($employee);

            return redirect()->route('admin.employees.index')
                ->with('success', 'Karyawan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus karyawan: '.$e->getMessage());
        }
    }

    public function bulkAction(BulkActionEmployeeRequest $request)
    {

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

            return back()->with('error', 'Gagal memproses: '.$e->getMessage());
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
        if ($user && ! $user->employee) {
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
                Log::warning('Failed to auto-attach employee for user '.$user->id.': '.$e->getMessage());
            }
        }

        if ($user && $user->employee) {
            $employee = $user->employee;
            // Use a sensible list of required fields to consider a profile "complete".
            // We do not require training_history or education_history as not everyone has them.
            $required = [
                'employee_id',
                'full_name',
                'department_id',
                'position_id',
                'email',
                'phone',
                'hire_date',
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
                    if (empty($val) && ! is_numeric($val)) {
                        $missing = true;
                        break;
                    }
                }
            }

            if (! $missing) {
                // Already complete, send them to dashboard/profile
                // Dimatikan sementara untuk testing agar user bisa melihat form
                // return redirect()->route('dashboard');
            }
        }

        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();

        // Determine current position id to help the view preselect option when legacy 'position' string exists
        $currentPositionId = null;
        $employee = $user->employee ?? null;
        if ($employee) {
            if (! empty($employee->position_id)) {
                $currentPositionId = $employee->position_id;
            } elseif (! empty($employee->position)) {
                // try to find position by name (case-insensitive)
                $found = Position::whereRaw('lower(name) = ?', [mb_strtolower($employee->position)])->first();
                if ($found) {
                    $currentPositionId = $found->id;
                }
            }
        }

        return view('employees.complete-profile', compact('departments', 'positions', 'currentPositionId'));
    }

    public function storeProfile(StoreProfileRequest $request, EmployeeService $employeeService)
    {
        $user = Auth::user();

        try {
            $data = $request->all();

            // Merge uploaded files into data array so saveProfile() can process them
            $data['ktp_file'] = $request->file('ktp_file');
            $data['kk_file'] = $request->file('kk_file');
            $data['marriage_certificate_file'] = $request->file('marriage_certificate_file');

            $employeeService->saveProfile($user, $data, $request->file('photo'));

            return redirect()->route('dashboard')
                ->with('success', 'Profil berhasil dilengkapi. Selamat datang!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal melengkapi profil: '.$e->getMessage());
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
                    'Content-Disposition' => 'attachment; filename="template_karyawan.csv"',
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
            Log::error('Template download failed: '.$e->getMessage());

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

        $csvContent = implode(',', $headings)."\n";
        foreach ($array as $row) {
            $csvContent .= '"'.implode('","', $row).'"'."\n";
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
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            $import = new EmployeesImport;
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getImportedCount();
            $skippedCount = $import->getSkippedCount();
            $failureCount = count($import->failures());
            $errorCount = count($import->errors());

            $message = 'Import selesai! ';
            $message .= "Berhasil: {$importedCount} karyawan, ";
            $message .= "Dilewati: {$skippedCount} baris";

            if ($failureCount > 0 || $errorCount > 0) {
                $message .= ', Gagal: '.($failureCount + $errorCount).' baris';
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
            return back()->with('error', 'Gagal mengimpor data: '.$e->getMessage());
        }
    }
}
