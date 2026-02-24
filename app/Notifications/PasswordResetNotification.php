<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset Password - To-Do List SMK')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Kami menerima permintaan reset password untuk akun Anda.')
            ->line('Klik tombol di bawah ini untuk membuat password baru:')
            ->action('Reset Password', $resetUrl)
            ->line('Link ini akan kadaluarsa dalam 60 menit.')
            ->line('Jika Anda tidak meminta reset password, abaikan email ini dan pastikan akun Anda aman.')
            ->salutation('Salam, Tim To-Do List SMK');
    }
}