<?php

namespace App\Notifications;

use App\Models\PasswordResetRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetApproved extends Notification
{
    use Queueable;

    public function __construct(public PasswordResetRequest $request) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', $this->request->token);

        return (new MailMessage)
            ->subject('Password Reset Approved')
            ->greeting('Halo ' . ($notifiable->name ?? ''))
            ->line('Permintaan reset password Anda telah disetujui oleh admin.')
            ->action('Reset Password', $url)
            ->line('Link ini berlaku sampai: ' . $this->request->expires_at->toDateTimeString())
            ->line('Jika Anda tidak meminta reset password, abaikan pesan ini.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Reset Password Disetujui',
            'message' => 'Permintaan reset password Anda telah disetujui. Klik untuk mereset password.',
            'token' => $this->request->token,
        ];
    }
}
