<?php

namespace App\Notifications;

use App\Models\Todo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TodoCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $todo;

    public function __construct(Todo $todo)
    {
        $this->todo = $todo;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $deadline = $this->todo->reminder_at 
            ? $this->todo->reminder_at->format('d F Y, H:i') 
            : 'Tidak ada deadline';
        
        $category = ucfirst($this->todo->metadata['category'] ?? 'Lainnya');
        
        return (new MailMessage)
            ->subject('✅ Catatan Baru Dibuat: ' . $this->todo->title)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Anda baru saja membuat catatan baru:')
            ->line('**Judul:** ' . $this->todo->title)
            ->line('**Kategori:** ' . $category)
            ->line('**Deadline:** ' . $deadline)
            ->line('**Deskripsi:** ' . Str::limit($this->todo->description, 100))
            ->action('Lihat Semua Catatan', url('/dashboard'))
            ->line('Terima kasih telah menggunakan To-Do List! 📋')
            ->salutation('Salam, Tim To-Do List SMK');
    }
}