<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class BulkActionEmployeeRequest extends FormRequest
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
            'action' => 'required|in:activate,deactivate,delete,change_department,change_position,change_role',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'department_id' => 'required_if:action,change_department|exists:departments,id',
            'position_id' => 'required_if:action,change_position|exists:positions,id',
            'role_id' => 'required_if:action,change_role|exists:roles,id',
        ];
    }
}
