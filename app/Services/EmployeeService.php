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

            $photoPath = $this->handlePhotoUpload($photo);

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
                'education_history' => $this->parseJsonField($data['education_history'] ?? null),
                'training_history' => $this->parseJsonField($data['training_history'] ?? null),
                'family_structure' => $this->parseJsonField($data['family_structure'] ?? null),
                'emergency_contact' => $this->parseJsonField($data['emergency_contact'] ?? null),
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

            $photoPath = $this->handlePhotoUpload($photo, $employee->photo);

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
                'education_history' => $this->parseJsonField($data['education_history'] ?? null),
                'training_history' => $this->parseJsonField($data['training_history'] ?? null),
                'family_structure' => $this->parseJsonField($data['family_structure'] ?? null),
                'emergency_contact' => $this->parseJsonField($data['emergency_contact'] ?? null),
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
            $photoPath = $this->handlePhotoUpload($photo, $employee ? $employee->photo : null);

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
                'has_credit_issue' => $data['has_credit_issue'] ?? null,
                'credit_institution' => $data['credit_institution'] ?? null,
                'credit_plafond' => $data['credit_plafond'] ?? null,
                'credit_monthly_installment' => $data['credit_monthly_installment'] ?? null,
                'education_history' => $this->parseJsonField($data['education_history'] ?? null),
                'training_history' => $this->parseJsonField($data['training_history'] ?? null),
                'family_structure' => $this->parseJsonField($data['family_structure'] ?? null),
                'emergency_contact' => $this->parseJsonField($data['emergency_contact'] ?? null),
            ];

            if ($photoPath) {
                $employeeData['photo'] = $photoPath;
            }

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

            $this->syncRelatedRecords($employee, $data);

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
     * Handle photo upload logic, including deleting the old photo if necessary.
     */
    protected function handlePhotoUpload(?UploadedFile $photo, ?string $oldPhotoPath = null): ?string
    {
        if ($photo) {
            if ($oldPhotoPath) {
                Storage::disk('public')->delete($oldPhotoPath);
            }
            return $photo->store('employee_photos', 'public');
        }

        return $oldPhotoPath;
    }

    /**
     * Sync related repeatable records like education, training, etc.
     */
    protected function syncRelatedRecords(Employee $employee, array $data): void
    {
        $employee->educationRecords()->delete();
        if (isset($data['education']) && is_array($data['education'])) {
            foreach ($data['education'] as $row) {
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

        $employee->trainingRecords()->delete();
        if (isset($data['training']) && is_array($data['training'])) {
            foreach ($data['training'] as $row) {
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

        $employee->familyMembers()->delete();
        if (isset($data['family']) && is_array($data['family'])) {
            foreach ($data['family'] as $row) {
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

        $employee->emergencyContacts()->delete();
        if (isset($data['emergency']) && is_array($data['emergency'])) {
            foreach ($data['emergency'] as $idx => $row) {
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
