<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyActivityRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    protected function prepareForValidation()
    {
        $merge = [];
        
        if ($this->has('start_time') && $this->start_time) {
            $merge['start_time'] = substr($this->start_time, 0, 5);
        }
        if ($this->has('end_time') && $this->end_time) {
            $merge['end_time'] = substr($this->end_time, 0, 5);
        }

        if ($this->has('tasks') && is_array($this->tasks)) {
            $tasks = array_filter($this->tasks, function($task) {
                return isset($task['title']) && trim($task['title']) !== '';
            });
            $merge['tasks'] = empty($tasks) ? null : array_values($tasks);
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
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
