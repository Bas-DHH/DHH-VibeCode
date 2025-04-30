<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Task $task
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Task Completed'))
            ->line(__('The following task has been completed: :task', ['task' => $this->task->title]))
            ->line(__('Task Description: :description', ['description' => $this->task->description]))
            ->line(__('Completed By: :user', ['user' => $this->task->completedBy->name]))
            ->line(__('Completion Time: :time', ['time' => now()->format('Y-m-d H:i')]))
            ->action(__('View Task'), route('tasks.show', $this->task))
            ->line(__('Thank you for using our application!'));
    }

    public function toArray($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'completed_by' => $this->task->completedBy->name,
            'completed_at' => now(),
            'message' => __('The following task has been completed: :task', ['task' => $this->task->title]),
        ];
    }
} 