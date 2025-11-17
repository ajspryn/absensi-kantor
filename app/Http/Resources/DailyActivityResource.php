<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'date' => $this->date->format('Y-m-d'), // Format date as Y-m-d string
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'title' => $this->title,
            'description' => $this->description,
            'tasks' => $this->tasks,
            'attachments' => $this->attachments,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'employee' => $this->whenLoaded('employee'),
            // Add file URLs
            'employee_photo_url' => $this->whenLoaded('employee', function () {
                return $this->employee && $this->employee->photo
                    ? url("api/files/employee-photos/{$this->employee->photo}")
                    : null;
            }),
            'attachment_urls' => $this->attachments && is_array($this->attachments)
                ? collect($this->attachments)->map(function ($attachment) {
                    return url("api/files/daily-activity-attachments/{$attachment}");
                })->toArray()
                : null,
        ];
    }
}
