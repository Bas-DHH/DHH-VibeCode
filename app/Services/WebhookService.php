<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskInstance;
use App\Models\Webhook;
use Illuminate\Support\Facades\Http;

class WebhookService
{
    public function sendTaskCreatedWebhook(Task $task): void
    {
        $webhooks = Webhook::where('business_id', $task->business_id)
            ->where('event', 'task.created')
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            $this->sendWebhook($webhook->url, [
                'event' => 'task.created',
                'task' => $task->load(['category', 'assignedUser'])->toArray(),
            ]);
        }
    }

    public function sendTaskCompletedWebhook(TaskInstance $instance): void
    {
        $webhooks = Webhook::where('business_id', $instance->task->business_id)
            ->where('event', 'task.completed')
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            $this->sendWebhook($webhook->url, [
                'event' => 'task.completed',
                'task_instance' => $instance->load(['task', 'completedBy'])->toArray(),
            ]);
        }
    }

    public function sendTaskOverdueWebhook(TaskInstance $instance): void
    {
        $webhooks = Webhook::where('business_id', $instance->task->business_id)
            ->where('event', 'task.overdue')
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            $this->sendWebhook($webhook->url, [
                'event' => 'task.overdue',
                'task_instance' => $instance->load(['task', 'assignedUser'])->toArray(),
            ]);
        }
    }

    private function sendWebhook(string $url, array $data): void
    {
        try {
            Http::post($url, $data);
        } catch (\Exception $e) {
            // Log the error but don't throw it
            logger()->error('Webhook failed', [
                'url' => $url,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function registerWebhook(array $data): Webhook
    {
        return Webhook::create([
            'business_id' => $data['business_id'],
            'url' => $data['url'],
            'event' => $data['event'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function updateWebhook(Webhook $webhook, array $data): Webhook
    {
        $webhook->update([
            'url' => $data['url'] ?? $webhook->url,
            'event' => $data['event'] ?? $webhook->event,
            'description' => $data['description'] ?? $webhook->description,
            'is_active' => $data['is_active'] ?? $webhook->is_active,
        ]);

        return $webhook;
    }

    public function deleteWebhook(Webhook $webhook): void
    {
        $webhook->delete();
    }

    public function getWebhooksByBusiness(int $businessId): array
    {
        return Webhook::where('business_id', $businessId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getAvailableEvents(): array
    {
        return [
            'task.created' => __('Task Created'),
            'task.completed' => __('Task Completed'),
            'task.overdue' => __('Task Overdue'),
        ];
    }
} 