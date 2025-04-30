<?php

namespace App\Console\Commands;

use App\Services\TaskSchedulerService;
use Illuminate\Console\Command;

class GenerateTaskInstances extends Command
{
    protected $signature = 'tasks:generate-instances';
    protected $description = 'Generate task instances for all active recurring tasks';

    public function handle(TaskSchedulerService $scheduler): int
    {
        $this->info('Starting task instance generation...');
        
        try {
            $scheduler->generateTaskInstances();
            $this->info('Task instances generated successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to generate task instances: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 