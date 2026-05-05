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
            'nik_ktp' => 'required|digits_between:1,20',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:M,F',
            'height_cm' => 'required|integer|min:0',
            'weight_kg' => 'required|integer|min:0',
            'hobby' => 'nullable|string|max:255',
            'marital_status' => 'required|string|max:50',
            'residence_status' => 'required|string|max:50',
            // health
            'health_condition' => 'nullable|string',
            'degenerative_diseases' => 'nullable|string',
            'has_medical_history' => 'nullable|boolean',
            // JSON/text areas (allow string or JSON)
            'education' => 'nullable|array',
            'training' => 'nullable|array',
            'family' => 'nullable|array',
            'emergency' => 'required|array|min:2',
            'emergency.*.name' => 'required|string|max:255',
            'emergency.*.relation' => 'required|string|max:255',
            'emergency.*.phone' => 'required|string|max:255',
            // financing (repeater, optional)
            'financing' => 'nullable|array',
            'financing.*.institution' => 'nullable|string|max:255',
            'financing.*.plafond' => 'nullable|numeric',
            'financing.*.monthly_installment' => 'nullable|numeric',
            'financing.*.description' => 'nullable|string|max:500',
            // documents
            'ktp_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'kk_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'marriage_certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ];
    }
}
