<?php

namespace App\Notifications;

use App\Models\AttendanceCorrection;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceCorrectionSubmitted extends Notification
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
               'title' => 'Pengajuan Koreksi Absensi Baru',
               'message' => 'Ada pengajuan koreksi pada tanggal ' . $this->correction->date->format('Y-m-d'),
               'correction_id' => $this->correction->id,
          ];
     }
}
