<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\TaskSchedulerService;
use Illuminate\Console\Command;

class GenerateTaskInstances extends Command
{
    protected $signature = 'tasks:generate-instances';
    protected $description = 'Generate task instances for the current day';

    public function handle(TaskSchedulerService $scheduler)
    {
        $this->info('Generating task instances...');

        $tasks = Task::where('is_active', true)->get();
        $count = 0;

        foreach ($tasks as $task) {
            if ($scheduler->shouldGenerateInstance($task)) {
                $scheduler->generateDailyInstance($task);
                $count++;
            }
        }

        $this->info("Generated {$count} task instances.");
    }
} 