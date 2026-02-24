<?php

namespace App\Notifications;

use App\Models\Todo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeadlineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $todo;
    protected $minutes;

    public function __construct(Todo $todo, int $minutes)
    {
        $this->todo = $todo;
        $this->minutes = $minutes;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $deadline = $this->todo->reminder_at->format('d F Y, H:i');
        $category = ucfirst($this->todo->metadata['category'] ?? 'Lainnya');
        
        $subject = $this->minutes > 0 
            ? "⏰ {$this->minutes} Menit Lagi: {$this->todo->title}" 
            : "🚨 Deadline Tiba: {$this->todo->title}";

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line("Ini pengingat untuk catatan Anda:")
            ->line("**Judul:** {$this->todo->title}")
            ->line("**Kategori:** {$category}")
            ->line("**Deadline:** {$deadline}")
            ->line($this->minutes > 0 
                ? "Tersisa {$this->minutes} menit lagi untuk menyelesaikannya!" 
                : "Waktu deadline telah tiba! Segera selesaikan tugas Anda.")
            ->action('Lihat Catatan Saya', url('/dashboard'))
            ->line('Jangan sampai terlewat! 📅')
            ->salutation('Salam Produktif, Tim To-Do List SMK');
    }
}