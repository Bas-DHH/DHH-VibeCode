<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskCompleted;
use App\Notifications\TaskOverdue;
use Illuminate\Support\Collection;

class NotificationService
{
    public function sendTaskAssignedNotification(Task $task, User $user): void
    {
        $user->notify(new TaskAssigned($task));
    }

    public function sendTaskCompletedNotification(Task $task, User $user): void
    {
        $user->notify(new TaskCompleted($task));
    }

    public function sendTaskOverdueNotification(Task $task, User $user): void
    {
        $user->notify(new TaskOverdue($task));
    }

    public function sendBulkTaskOverdueNotifications(Collection $tasks): void
    {
        foreach ($tasks as $task) {
            if ($task->assignedUser) {
                $this->sendTaskOverdueNotification($task, $task->assignedUser);
            }
        }
    }

    public function markNotificationAsRead(string $notificationId, User $user): void
    {
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllNotificationsAsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    public function getUnreadNotifications(User $user): Collection
    {
        return $user->unreadNotifications;
    }

    public function getReadNotifications(User $user): Collection
    {
        return $user->readNotifications;
    }

    public function getAllNotifications(User $user): Collection
    {
        return $user->notifications;
    }
} 