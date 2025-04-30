<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Generate tasks daily at midnight
        $schedule->command('tasks:generate')
            ->dailyAt('00:00')
            ->withoutOverlapping();

        // Generate task instances daily at midnight
        $schedule->command('tasks:generate-instances')
            ->daily()
            ->at('00:00');

        // Generate daily tasks at midnight
        $schedule->command('tasks:generate-daily')
            ->daily()
            ->at('00:00')
            ->withoutOverlapping();

        // Send daily summaries at 6 PM
        $schedule->command('tasks:send-daily-summary')
            ->daily()
            ->at('18:00');

        // Cleanup exports daily
        $schedule->command('exports:cleanup')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 