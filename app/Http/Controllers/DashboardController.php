<?php

namespace App\Http\Controllers;

use App\Models\TaskInstance;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $business = $user->business;
        $language = LanguageService::getCurrentLanguage();

        $query = TaskInstance::with(['task.category', 'task.assignedUser'])
            ->whereHas('task', function ($query) use ($business) {
                $query->where('business_id', $business->id);
            });

        // Get today's tasks
        $todayQuery = clone $query;
        $todayTasks = $todayQuery->where('scheduled_for', '>=', now()->startOfDay())
            ->where('scheduled_for', '<=', now()->endOfDay())
            ->orderBy('scheduled_for')
            ->get();

        // Get overdue tasks
        $overdueQuery = clone $query;
        $overdueTasks = $overdueQuery->where('scheduled_for', '<', now()->startOfDay())
            ->where('status', 'pending')
            ->orderBy('scheduled_for')
            ->get();

        // Get completed tasks for today
        $completedQuery = clone $query;
        $completedTasks = $completedQuery->where('scheduled_for', '>=', now()->startOfDay())
            ->where('scheduled_for', '<=', now()->endOfDay())
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();

        // Group tasks by category
        $tasksByCategory = $todayTasks->groupBy(function ($instance) use ($language) {
            return $instance->task->category->{"name_{$language}"} ?? $instance->task->category->name_nl;
        });

        // Calculate statistics
        $stats = [
            'total_tasks' => $todayTasks->count(),
            'completed_tasks' => $completedTasks->count(),
            'overdue_tasks' => $overdueTasks->count(),
            'pending_tasks' => $todayTasks->where('status', 'pending')->count(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'tasksByCategory' => $tasksByCategory,
            'overdueTasks' => $overdueTasks,
            'completedTasks' => $completedTasks,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function sendDailySummary()
    {
        $businesses = \App\Models\Business::with(['users' => function ($query) {
            $query->where('is_admin', true);
        }])->get();

        foreach ($businesses as $business) {
            $query = TaskInstance::with(['task.category', 'task.assignedUser'])
                ->whereHas('task', function ($query) use ($business) {
                    $query->where('business_id', $business->id);
                });

            // Get today's tasks
            $todayTasks = $query->where('scheduled_for', '>=', now()->startOfDay())
                ->where('scheduled_for', '<=', now()->endOfDay())
                ->get();

            // Get overdue tasks
            $overdueTasks = $query->where('scheduled_for', '<', now()->startOfDay())
                ->where('status', 'pending')
                ->get();

            // Get completed tasks for today
            $completedTasks = $query->where('scheduled_for', '>=', now()->startOfDay())
                ->where('scheduled_for', '<=', now()->endOfDay())
                ->where('status', 'completed')
                ->get();

            // Calculate statistics
            $stats = [
                'total_tasks' => $todayTasks->count(),
                'completed_tasks' => $completedTasks->count(),
                'overdue_tasks' => $overdueTasks->count(),
                'pending_tasks' => $todayTasks->where('status', 'pending')->count(),
            ];

            // Send email to each admin
            foreach ($business->users as $admin) {
                \Illuminate\Support\Facades\Mail::to($admin->email)->send(
                    new \App\Mail\DailySummary($stats, $todayTasks, $overdueTasks, $completedTasks)
                );
            }
        }
    }
} 