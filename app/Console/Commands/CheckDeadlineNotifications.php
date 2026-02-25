<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\DeadlineNotification;
use Illuminate\Support\Facades\Log;

class CheckDeadlineNotifications extends Command
{
    protected $signature = 'check:deadline-notifications';
    protected $description = 'Check for overdue todos and send notifications';

    public function handle()
    {
        $now = Carbon::now();

        $overdueTodos = Todo::where('completed', false)
            ->whereNotNull('reminder_at')
            ->where('reminder_at', '<=', $now)
            ->where('reminder_at', '>=', $now->subMinutes(5))
            ->get();

        foreach ($overdueTodos as $todo) {
            try {
                $user = User::find($todo->user_id);

                if ($user) {
                    $user->notify(new DeadlineNotification($todo, 0));

                    Log::info('Deadline notification sent', [
                        'user_id' => $user->id,
                        'todo_id' => $todo->id,
                        'title' => $todo->title,
                        'deadline' => $todo->reminder_at
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send deadline notification', [
                    'todo_id' => $todo->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info('Deadline notifications checked: ' . $overdueTodos->count() . ' todos');
    }
}
