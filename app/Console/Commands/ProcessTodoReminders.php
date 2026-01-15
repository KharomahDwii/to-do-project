<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Todo;
use Carbon\Carbon;

class ProcessTodoReminders extends Command
{
    protected $signature = 'todo:process-reminders';
    protected $description = 'Proses semua todo yang waktunya sudah tiba';

    public function handle()
    {
        $overdueTodos = Todo::where('notified', false)
            ->where('completed', false)
            ->whereNotNull('reminder_at')
            ->where('reminder_at', '<=', now())
            ->get();

        foreach ($overdueTodos as $todo) {
            // Tandai sebagai sudah diingatkan
            $todo->notified = true;
            $todo->save();

            // Opsional: kirim email, log, dll
            $this->info("Notifikasi diproses untuk: {$todo->title}");
        }

        $this->info("Total: {$overdueTodos->count()} pengingat diproses.");
    }
}