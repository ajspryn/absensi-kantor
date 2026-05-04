<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('emergency') && is_array($this->input('emergency'))) {
                if (count(array_filter($this->input('emergency'), function ($r) {
                    return ! empty($r['name'] ?? null);
                })) < 2) {
                    $validator->errors()->add('emergency', 'Harap masukkan minimal 2 kontak darurat.');
                }
            }
        });
    }
}
