<?php

namespace App\Console\Commands;

use App\Services\TaskScheduler;
use Illuminate\Console\Command;

class GenerateTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily, weekly, and monthly tasks';

    /**
     * Execute the console command.
     */
    public function handle(TaskScheduler $scheduler)
    {
        $this->info('Starting task generation...');
        
        try {
            $scheduler->generateAllTasks();
            $this->info('Tasks generated successfully!');
        } catch (\Exception $e) {
            $this->error('Error generating tasks: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
