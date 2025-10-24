<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyActivityRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tasks' => 'nullable|array',
            'tasks.*.title' => 'required_with:tasks|string|max:255',
            'tasks.*.notes' => 'nullable|string',
            'tasks.*.completed' => 'nullable|boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ];
    }
}
