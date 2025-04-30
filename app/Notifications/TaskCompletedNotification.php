<?php

namespace App\Notifications;

use App\Models\TaskInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private TaskInstance $task,
        private bool $isAdminNotification = false
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->getSubject())
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->getMainMessage())
            ->line('Task: ' . $this->task->task->title)
            ->line('Completed by: ' . $this->task->completedBy->name)
            ->line('Completed at: ' . $this->task->completed_at->format('Y-m-d H:i:s'));

        if ($this->isAdminNotification && $this->task->corrective_actions) {
            $message->line('Corrective Actions: ' . $this->task->corrective_actions);
        }

        return $message->action('View Task', route('tasks.show', $this->task->id));
    }

    public function toArray($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->task->title,
            'completed_by' => $this->task->completedBy->name,
            'completed_at' => $this->task->completed_at,
            'has_corrective_actions' => (bool) $this->task->corrective_actions,
            'is_admin_notification' => $this->isAdminNotification,
        ];
    }

    private function getSubject(): string
    {
        if ($this->isAdminNotification) {
            return 'Task Completed with Corrective Actions';
        }
        return 'Task Completed Successfully';
    }

    private function getMainMessage(): string
    {
        if ($this->isAdminNotification) {
            return 'A task has been completed with corrective actions that require your attention.';
        }
        return 'A task assigned to you has been completed successfully.';
    }
} 