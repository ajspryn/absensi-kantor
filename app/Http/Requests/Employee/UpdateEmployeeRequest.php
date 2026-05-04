<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
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
        ];
    }
}
