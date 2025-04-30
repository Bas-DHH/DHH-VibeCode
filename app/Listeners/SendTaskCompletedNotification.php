<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Notifications\TaskCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskCompletedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TaskCompleted $event)
    {
        // Notify the assigned user
        $event->task->assignedUser->notify(new TaskCompletedNotification($event->task));

        // Notify admins if there are any corrective actions needed
        if ($event->task->corrective_actions) {
            $event->task->business->users()
                ->where('role', 'admin')
                ->get()
                ->each(function ($admin) use ($event) {
                    $admin->notify(new TaskCompletedNotification($event->task, true));
                });
        }
    }
} 