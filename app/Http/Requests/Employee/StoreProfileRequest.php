<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
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
        $user = auth()->user();
        $employee = $user ? $user->employee : null;

        $employeeIdRule = 'required|string|unique:employees,employee_id';
        if ($employee) {
            $employeeIdRule = 'required|string|unique:employees,employee_id,'.$employee->id;
        }

        return [
            'employee_id' => $employeeIdRule,
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'work_schedule_id' => 'nullable|exists:work_schedules,id',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'hire_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            // address
            'address' => 'nullable|string',
            'address_ktp' => 'nullable|string',
            'address_domisili' => 'nullable|string',
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
        ];
    }
}
