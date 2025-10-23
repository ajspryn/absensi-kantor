<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    private $importedCount = 0;
    private $skippedCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Debug: Log the row data
        Log::info('Processing row: ' . json_encode($row));

        // Check if essential fields are empty
        if (empty($row['nama_lengkap']) || empty($row['email']) || empty($row['id_karyawan'])) {
            Log::warning('Skipping row due to missing essential fields: ' . json_encode([
                'nama_lengkap' => $row['nama_lengkap'] ?? 'MISSING',
                'email' => $row['email'] ?? 'MISSING',
                'id_karyawan' => $row['id_karyawan'] ?? 'MISSING'
            ]));
            $this->skippedCount++;
            return null;
        }

        // Additional validation
        if (empty(trim($row['nama_lengkap'])) || empty(trim($row['email'])) || empty(trim($row['id_karyawan']))) {
            Log::warning('Skipping row due to empty fields after trim');
            $this->skippedCount++;
            return null;
        }

        DB::beginTransaction();

        try {
            // Find or create department
            $department = null;
            if (!empty($row['departemen'])) {
                $department = Department::where('name', $row['departemen'])->first();
                if (!$department) {
                    $department = Department::create([
                        'name' => $row['departemen'],
                        'description' => 'Departemen ' . $row['departemen'],
                        'is_active' => true
                    ]);
                }
            }

            // Find or create position
            $position = null;
            if (!empty($row['posisi'])) {
                $position = Position::where('name', $row['posisi'])->first();
                if (!$position) {
                    $position = Position::create([
                        'name' => $row['posisi'],
                        'description' => 'Posisi ' . $row['posisi'],
                        'department_id' => $department ? $department->id : null,
                        'is_active' => true
                    ]);
                }
            }

            // Find role
            $role = Role::where('name', 'employee')->first();
            if (!$role) {
                $role = Role::where('name', 'Employee')->first();
            }

            // Create user first
            $user = User::create([
                'name' => $row['nama_lengkap'],
                'email' => $row['email'],
                'password' => Hash::make($row['password'] ?? 'password'),
                'role_id' => $role ? $role->id : 2, // Default to employee role
                'is_active' => isset($row['status_aktif']) ?
                    (strtolower($row['status_aktif']) === 'aktif' || $row['status_aktif'] === '1') : true,
            ]);

            // Create employee
            $employee = Employee::create([
                'employee_id' => $row['id_karyawan'],
                'user_id' => $user->id,
                'department_id' => $department ? $department->id : 1, // Default to department 1 if not found
                'position_id' => $position ? $position->id : null,
                // 'position' column removed; keep position relation via position_id
                'full_name' => $row['nama_lengkap'],
                'phone' => !empty($row['telepon']) ? (string)$row['telepon'] : null, // Convert to string
                'email' => $row['email'],
                'address' => $row['alamat'] ?? null,
                'hire_date' => isset($row['tanggal_masuk']) ?
                    \Carbon\Carbon::parse($row['tanggal_masuk']) : now(),
                'salary' => isset($row['gaji']) ?
                    (float) str_replace([',', '.'], ['', ''], $row['gaji']) : 0,
                'is_active' => isset($row['status_aktif']) ?
                    (strtolower($row['status_aktif']) === 'aktif' || $row['status_aktif'] === '1') : true,
                'allow_remote_attendance' => isset($row['remote_attendance']) ?
                    (strtolower($row['remote_attendance']) === 'ya' || $row['remote_attendance'] === '1') : false,
            ]);

            DB::commit();
            $this->importedCount++;

            return $employee;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Import error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            $this->skippedCount++;
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'id_karyawan' => 'required|string|unique:employees,employee_id',
            'departemen' => 'nullable|string|max:255',
            'posisi' => 'nullable|string|max:255',
            'telepon' => 'nullable|max:20', // Allow both string and numeric
            'alamat' => 'nullable|string',
            'tanggal_masuk' => 'nullable|date',
            'gaji' => 'nullable|numeric|min:0',
            'status_aktif' => 'nullable|in:aktif,tidak aktif,1,0',
            'remote_attendance' => 'nullable|in:ya,tidak,1,0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'id_karyawan.required' => 'ID Karyawan wajib diisi',
            'id_karyawan.unique' => 'ID Karyawan sudah terdaftar',
            'tanggal_masuk.date' => 'Format tanggal masuk tidak valid',
            'gaji.numeric' => 'Gaji harus berupa angka',
            'gaji.min' => 'Gaji tidak boleh negatif',
        ];
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::error('Validation failure at row ' . $failure->row() . ': ' . implode(', ', $failure->errors()));
        }
    }
}
