<?php

namespace App\Notifications;

use App\Models\AttendanceCorrection;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceCorrectionDecision extends Notification
{
     use Queueable;

     public function __construct(public AttendanceCorrection $correction) {}

     public function via(object $notifiable): array
     {
          return ['database'];
     }

     public function toArray(object $notifiable): array
     {
          return [
               'title' => 'Keputusan Koreksi Absensi',
               'message' => 'Pengajuan Anda ' . ($this->correction->status === 'approved' ? 'disetujui' : 'ditolak') . '.',
               'correction_id' => $this->correction->id,
               'status' => $this->correction->status,
          ];
     }
}
