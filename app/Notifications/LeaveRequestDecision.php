<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveRequestDecision extends Notification
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
            'title' => 'Keputusan Pengajuan Izin',
            'message' => 'Pengajuan Anda ' . ($this->leaveRequest->status === 'verified' ? 'disetujui' : 'ditolak') . '.',
            'leave_request_id' => $this->leaveRequest->id,
            'status' => $this->leaveRequest->status,
        ];
    }
}
