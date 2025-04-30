<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckOverdueTasks extends Command
{
    protected $signature = 'tasks:check-overdue';
    protected $description = 'Check for overdue tasks and send notifications';

    public function __construct(
        private readonly NotificationService $notificationService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $overdueTasks = Task::query()
            ->where('is_active', true)
            ->whereHas('instances', function ($query) {
                $query->where('status', 'pending')
                    ->where('scheduled_for', '<', now());
            })
            ->with(['instances' => function ($query) {
                $query->where('status', 'pending')
                    ->where('scheduled_for', '<', now());
            }])
            ->get();

        if ($overdueTasks->isEmpty()) {
            $this->info('No overdue tasks found.');
            return self::SUCCESS;
        }

        $this->info("Found {$overdueTasks->count()} overdue tasks.");

        $this->notificationService->sendBulkTaskOverdueNotifications($overdueTasks);

        $this->info('Notifications sent successfully.');

        return self::SUCCESS;
    }
} 