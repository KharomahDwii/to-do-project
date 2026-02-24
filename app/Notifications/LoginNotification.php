<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Login Berhasil - To-Do List SMK')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Kami ingin memberi tahu bahwa Anda telah berhasil login ke akun Anda.')
            ->line('Jika ini bukan Anda, segera ubah password Anda.')
            ->action('Lihat Akun', url('/dashboard'))
            ->line('Terima kasih, Tim To-Do List SMK');
    }
}