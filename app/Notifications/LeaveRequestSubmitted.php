<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveRequestSubmitted extends Notification
{
    use Queueable;

    public function __construct(public LeaveRequest $leaveRequest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pengajuan Izin Baru',
            'message' => 'Ada pengajuan izin dari ' . $this->leaveRequest->start_date->format('Y-m-d') . ' sampai ' . $this->leaveRequest->end_date->format('Y-m-d'),
            'leave_request_id' => $this->leaveRequest->id,
        ];
    }
}
