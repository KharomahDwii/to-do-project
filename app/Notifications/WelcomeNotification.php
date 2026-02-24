<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
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
            ->subject('Selamat Datang di To-Do List SMK!')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Terima kasih telah mendaftar di aplikasi To-Do List kami.')
            ->line('Aplikasi ini akan membantu Anda mengelola tugas sekolah dengan lebih baik.')
            ->action('Mulai Gunakan Aplikasi', url('/dashboard'))
            ->line('Terima kasih, Tim To-Do List SMK');
    }
}