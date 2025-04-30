<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification implements ShouldQueue
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
            ->subject(__('New Task Assigned'))
            ->line(__('You have been assigned a new task: :task', ['task' => $this->task->title]))
            ->line(__('Task Description: :description', ['description' => $this->task->description]))
            ->line(__('Scheduled Time: :time', ['time' => $this->task->scheduled_time->format('Y-m-d H:i')]))
            ->action(__('View Task'), route('tasks.show', $this->task))
            ->line(__('Thank you for using our application!'));
    }

    public function toArray($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'scheduled_time' => $this->task->scheduled_time,
            'message' => __('You have been assigned a new task: :task', ['task' => $this->task->title]),
        ];
    }
} 