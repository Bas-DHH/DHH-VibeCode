<?php

namespace App\Console\Commands;

use App\Mail\DailySummary;
use App\Models\Business;
use App\Models\TaskInstance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyTaskSummary extends Command
{
    protected $signature = 'tasks:send-daily-summary';
    protected $description = 'Send daily task summary emails to business admins';

    public function handle()
    {
        $today = Carbon::today();
        $businesses = Business::with(['users' => function ($query) {
            $query->where('role', 'admin');
        }])->get();

        foreach ($businesses as $business) {
            $stats = [
                'total' => TaskInstance::where('business_id', $business->id)
                    ->whereDate('scheduled_for', $today)
                    ->count(),
                'completed' => TaskInstance::where('business_id', $business->id)
                    ->whereDate('scheduled_for', $today)
                    ->whereNotNull('completed_at')
                    ->count(),
                'overdue' => TaskInstance::where('business_id', $business->id)
                    ->whereDate('scheduled_for', '<', $today)
                    ->whereNull('completed_at')
                    ->count(),
            ];

            $todayTasks = TaskInstance::where('business_id', $business->id)
                ->whereDate('scheduled_for', $today)
                ->with(['task', 'assignedUser'])
                ->get();

            $overdueTasks = TaskInstance::where('business_id', $business->id)
                ->whereDate('scheduled_for', '<', $today)
                ->whereNull('completed_at')
                ->with(['task', 'assignedUser'])
                ->get();

            $completedTasks = TaskInstance::where('business_id', $business->id)
                ->whereDate('completed_at', $today)
                ->with(['task', 'assignedUser', 'completedBy'])
                ->get();

            foreach ($business->users as $admin) {
                Mail::to($admin->email)->send(new DailySummary(
                    stats: $stats,
                    todayTasks: $todayTasks,
                    overdueTasks: $overdueTasks,
                    completedTasks: $completedTasks,
                    language: $admin->language ?? 'en'
                ));
            }
        }

        $this->info('Daily task summaries sent successfully.');
    }
} 