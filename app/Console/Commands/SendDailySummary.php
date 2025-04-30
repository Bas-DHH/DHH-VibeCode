<?php

namespace App\Console\Commands;

use App\Mail\DailySummary;
use App\Models\Business;
use App\Models\TaskInstance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailySummary extends Command
{
    protected $signature = 'tasks:send-daily-summary';
    protected $description = 'Send daily task summary emails to business admins';

    public function handle()
    {
        $this->info('Sending daily task summaries...');

        $businesses = Business::with(['users' => function ($query) {
            $query->where('is_admin', true);
        }])->get();

        foreach ($businesses as $business) {
            $today = now()->startOfDay();
            $tomorrow = $today->copy()->addDay();

            $stats = [
                'total' => TaskInstance::whereHas('task', function ($query) use ($business) {
                    $query->where('business_id', $business->id);
                })
                    ->where('scheduled_for', '>=', $today)
                    ->where('scheduled_for', '<', $tomorrow)
                    ->count(),
                'completed' => TaskInstance::whereHas('task', function ($query) use ($business) {
                    $query->where('business_id', $business->id);
                })
                    ->where('scheduled_for', '>=', $today)
                    ->where('scheduled_for', '<', $tomorrow)
                    ->where('status', 'completed')
                    ->count(),
                'overdue' => TaskInstance::whereHas('task', function ($query) use ($business) {
                    $query->where('business_id', $business->id);
                })
                    ->where('scheduled_for', '<', now())
                    ->where('status', 'pending')
                    ->count(),
            ];

            $todayTasks = TaskInstance::with(['task.category', 'task.assignedUser'])
                ->whereHas('task', function ($query) use ($business) {
                    $query->where('business_id', $business->id);
                })
                ->where('scheduled_for', '>=', $today)
                ->where('scheduled_for', '<', $tomorrow)
                ->get();

            $overdueTasks = TaskInstance::with(['task.category', 'task.assignedUser'])
                ->whereHas('task', function ($query) use ($business) {
                    $query->where('business_id', $business->id);
                })
                ->where('scheduled_for', '<', now())
                ->where('status', 'pending')
                ->get();

            $completedTasks = TaskInstance::with(['task.category', 'task.assignedUser'])
                ->whereHas('task', function ($query) use ($business) {
                    $query->where('business_id', $business->id);
                })
                ->where('scheduled_for', '>=', $today)
                ->where('scheduled_for', '<', $tomorrow)
                ->where('status', 'completed')
                ->get();

            foreach ($business->users as $user) {
                Mail::to($user->email)->send(new DailySummary(
                    stats: $stats,
                    todayTasks: $todayTasks,
                    overdueTasks: $overdueTasks,
                    completedTasks: $completedTasks,
                    language: $user->language
                ));
            }
        }

        $this->info('Daily task summaries sent successfully.');
    }
} 