<?php

namespace App\Console\Commands;

use App\Services\TaskSchedulerService;
use Illuminate\Console\Command;

class GenerateDailyTasks extends Command
{
    protected $signature = 'tasks:generate-daily';
    protected $description = 'Generate daily task instances for all active tasks';

    public function handle(TaskSchedulerService $scheduler)
    {
        $this->info('Starting daily task generation...');
        
        try {
            $scheduler->generateDailyTasks();
            $this->info('Daily tasks generated successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to generate daily tasks: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 