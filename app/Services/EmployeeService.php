<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use App\Models\Position;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class EmployeeService
{
    /**
     * Create a new employee and their associated user account.
     */
    public function createEmployee(array $data, ?UploadedFile $photo = null): Employee
    {
        return DB::transaction(function () use ($data, $photo) {
            // Get default employee role
            $employeeRole = Role::where('name', 'employee')->first();
            $roleId = $employeeRole ? $employeeRole->id : 2;

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $roleId,
                'is_active' => true,
            ]);

            $photoPath = $this->handleFileUpload($photo, 'employee_photos');

            $employee = Employee::create([
                'employee_id' => $data['employee_id'],
                'user_id' => $user->id,
                'department_id' => $data['department_id'],
                'position_id' => $data['position_id'],
                'work_schedule_id' => $data['work_schedule_id'],
                'hire_date' => now(),
                'is_active' => true,
                'full_name' => $data['name'],
                'email' => $data['email'],
                'photo' => $photoPath,
                'nik_ktp' => $data['nik_ktp'] ?? null,
                'address_ktp' => $data['address_ktp'] ?? null,
                'address_domisili' => $data['address_domisili'] ?? null,
                'mobile' => $data['mobile'] ?? null,
                'gender' => $data['gender'] ?? null,
                'birth_place' => $data['birth_place'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'health_condition' => $data['health_condition'] ?? null,
                'education_history' => $data['education'] ?? null,
                'training_history' => $data['training'] ?? null,
                'family_structure' => $data['family'] ?? null,
                'emergency_contact' => $data['emergency'] ?? null,
            ]);

            return $employee;
        });
    }

    /**
     * Update an existing employee and their associated user account.
     */
    public function updateEmployee(Employee $employee, array $data, ?UploadedFile $photo = null): Employee
    {
        return DB::transaction(function () use ($employee, $data, $photo) {
            $userData = [
                'name' => $data['full_name'],
                'email' => $data['email'],
                'role_id' => $data['role_id'],
                'is_active' => $data['is_active'] ?? false,
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $employee->user->update($userData);

            $photoPath = $this->handleFileUpload($photo, 'employee_photos', $employee->photo);

            $employee->update([
                'employee_id' => $data['employee_id'],
                'department_id' => $data['department_id'],
                'position_id' => $data['position_id'],
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'nik_ktp' => $data['nik_ktp'] ?? null,
                'jabatan' => $data['jabatan'] ?? null,
                'address_ktp' => $data['address_ktp'] ?? null,
                'address_domisili' => $data['address_domisili'] ?? null,
                'mobile' => $data['mobile'] ?? null,
                'gender' => $data['gender'] ?? null,
                'height_cm' => $data['height_cm'] ?? null,
                'weight_kg' => $data['weight_kg'] ?? null,
                'hobby' => $data['hobby'] ?? null,
                'birth_place' => $data['birth_place'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'marital_status' => $data['marital_status'] ?? null,
                'residence_status' => $data['residence_status'] ?? null,
                'health_condition' => $data['health_condition'] ?? null,
                'degenerative_diseases' => $data['degenerative_diseases'] ?? null,
                'education_history' => $data['education'] ?? null,
                'training_history' => $data['training'] ?? null,
                'family_structure' => $data['family'] ?? null,
                'emergency_contact' => $data['emergency'] ?? null,
                'hire_date' => $data['hire_date'],
                'salary' => $data['salary'] ?? null,
                'photo' => $photoPath,
                'is_active' => $data['is_active'] ?? false,
                'allow_remote_attendance' => (int) ($data['allow_remote_attendance'] ?? 0),
                'email' => $data['email'],
            ]);

            return $employee;
        });
    }

    /**
     * Store or update profile completed by the employee themselves.
     */
    public function saveProfile(User $user, array $data, ?UploadedFile $photo = null): Employee
    {
        return DB::transaction(function () use ($user, $data, $photo) {
            $employee = $user->employee;
            $photoPath = $this->handleFileUpload($photo, 'employee_photos', $employee ? $employee->photo : null);
            $ktpPath = $this->handleFileUpload($data['ktp_file'] ?? null, 'ktp_docs', $employee ? $employee->ktp_path : null);
            $kkPath = $this->handleFileUpload($data['kk_file'] ?? null, 'kk_docs', $employee ? $employee->kk_path : null);
            $marriageCertPath = $this->handleFileUpload($data['marriage_certificate_file'] ?? null, 'marriage_docs', $employee ? $employee->marriage_certificate_path : null);

            $employeeData = [
                'employee_id' => $data['employee_id'],
                'department_id' => $data['department_id'],
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'mobile' => $data['mobile'] ?? null,
                'address' => $data['address'] ?? null,
                'address_ktp' => $data['address_ktp'] ?? null,
                'address_domisili' => $data['address_domisili'] ?? null,
                'hire_date' => $data['hire_date'],
                'email' => $user->email,
                'is_active' => true,
                'nik_ktp' => $data['nik_ktp'] ?? null,
                'birth_place' => $data['birth_place'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'height_cm' => $data['height_cm'] ?? null,
                'weight_kg' => $data['weight_kg'] ?? null,
                'hobby' => $data['hobby'] ?? null,
                'marital_status' => $data['marital_status'] ?? null,
                'residence_status' => $data['residence_status'] ?? null,
                'health_condition' => $data['health_condition'] ?? null,
                'degenerative_diseases' => $data['degenerative_diseases'] ?? null,
                'has_medical_history' => !empty($data['has_medical_history']) ? 1 : 0,
                'financing_history'    => $data['financing'] ?? null,
                'education_history' => $this->processNestedFiles($data['education'] ?? [], 'education', 'education_docs', $employee ? $employee->education_history : []),
                'training_history' => $this->processNestedFiles($data['training'] ?? [], 'training', 'training_docs', $employee ? $employee->training_history : []),
                'family_structure' => $data['family'] ?? null,
                'emergency_contact' => $data['emergency'] ?? null,
                'photo' => $photoPath,
                'ktp_path' => $ktpPath,
                'kk_path' => $kkPath,
                'marriage_certificate_path' => $marriageCertPath,
            ];


            if (!empty($data['position_id'])) {
                $positionModel = Position::find($data['position_id']);
                $employeeData['position_id'] = $data['position_id'];
                $employeeData['position'] = $positionModel ? $positionModel->name : null;
            } elseif (!empty($data['position'])) {
                $employeeData['position'] = $data['position'];
            }

            if ($employee) {
                // Update
                $employee->update($employeeData);
            } else {
                // Create
                $employeeData['user_id'] = $user->id;
                $employee = Employee::create($employeeData);
            }

            // syncRelatedRecords is disabled because it relies on separate tables (education_records, etc.)
            // which do not exist in the current database schema. Data is already stored as JSON 
            // in the employees table for flexibility and mobile-friendliness.
            // $this->syncRelatedRecords($employee, $data);

            return $employee;
        });
    }

    /**
     * Delete an employee and their user record.
     */
    public function deleteEmployee(Employee $employee): void
    {
        DB::transaction(function () use ($employee) {
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }

            $user = $employee->user;
            $employee->delete();
            if ($user) {
                $user->delete();
            }
        });
    }

    /**
     * Handle file upload logic, including deleting the old file if necessary.
     */
    protected function handleFileUpload(?UploadedFile $file, string $directory, ?string $oldFilePath = null): ?string
    {
        if ($file) {
            if ($oldFilePath) {
                Storage::disk('public')->delete($oldFilePath);
            }
            return $file->store($directory, 'public');
        }

        return $oldFilePath;
    }

    /**
     * Sync related repeatable records like education, training, etc.
     * DEPRECATED: Relying on JSON storage in employees table instead.
     */
    protected function syncRelatedRecords(Employee $employee, array $data): void
    {
        // Method body commented out to prevent crashes on non-existent tables
        /*
        $employee->educationRecords()->delete();
        ...
        */
    }

    /**
     * Process nested file uploads within array data (repeaters).
     */
    protected function processNestedFiles(array $items, string $keyPrefix, string $directory, $oldItems = []): array
    {
        if (!is_array($items)) return [];
        
        $oldItemsArray = is_array($oldItems) ? $oldItems : (is_string($oldItems) ? json_decode($oldItems, true) : []);
        if (!is_array($oldItemsArray)) $oldItemsArray = [];

        foreach ($items as $i => &$item) {
            // Check for file in request using dot notation
            $fileKey = "{$keyPrefix}.{$i}.certificate";
            if (request()->hasFile($fileKey)) {
                $oldPath = $oldItemsArray[$i]['certificate_path'] ?? null;
                $item['certificate_path'] = $this->handleFileUpload(request()->file($fileKey), $directory, $oldPath);
            } elseif (isset($oldItemsArray[$i]['certificate_path'])) {
                // Keep old path if no new file uploaded
                $item['certificate_path'] = $oldItemsArray[$i]['certificate_path'];
            }
        }
        
        return $items;
    }

    /**
     * Try to parse a JSON-like field submitted from the form.
     */
    public function parseJsonField($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        try {
            $decoded = json_decode($value, true);
        } catch (\Throwable $e) {
            $decoded = null;
        }

        if (is_array($decoded)) {
            return $decoded;
        }

        return ['raw' => $value];
    }
}
