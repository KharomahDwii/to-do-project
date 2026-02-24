<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActivityLogNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $activity;
    protected $action;

    public function __construct($activity, $action)
    {
        $this->activity = $activity;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $actionText = [
            'profile_updated' => 'memperbarui profil',
            'logged_out' => 'keluar dari akun',
            'category_created' => 'membuat kategori baru',
            'category_deleted' => 'menghapus kategori'
        ][$this->action] ?? $this->action;

        return (new MailMessage)
            ->subject('📊 Aktivitas Akun: ' . ucfirst($actionText))
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Kami ingin memberitahu Anda bahwa aktivitas berikut terjadi di akun Anda:')
            ->line('**Aktivitas:** ' . ucfirst($actionText))
            ->line('**Waktu:** ' . now()->format('d F Y, H:i'))
            ->line('**Detail:** ' . $this->activity)
            ->line('Jika ini bukan Anda, segera ubah password Anda.')
            ->action('Lihat Activity Log', url('/dashboard?view=activity-log'))
            ->line('Terima kasih telah menggunakan To-Do List! 🔒')
            ->salutation('Salam, Tim To-Do List SMK');
    }
}