<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskInstance;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportingService
{
    public function getTaskCompletionStats(int $businessId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        $totalTasks = Task::where('business_id', $businessId)
            ->where('is_active', true)
            ->count();

        $completedTasks = TaskInstance::whereHas('task', function ($query) use ($businessId) {
            $query->where('business_id', $businessId);
        })
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->count();

        $overdueTasks = TaskInstance::whereHas('task', function ($query) use ($businessId) {
            $query->where('business_id', $businessId);
        })
            ->where('status', 'pending')
            ->where('scheduled_for', '<', now())
            ->count();

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'overdue_tasks' => $overdueTasks,
            'completion_rate' => $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0,
        ];
    }

    public function getTaskPerformanceByUser(int $businessId, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        return TaskInstance::whereHas('task', function ($query) use ($businessId) {
            $query->where('business_id', $businessId);
        })
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->with('completedBy')
            ->get()
            ->groupBy('completed_by_id')
            ->map(function ($instances) {
                $user = $instances->first()->completedBy;
                return [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'completed_tasks' => $instances->count(),
                    'average_completion_time' => $instances->avg(function ($instance) {
                        return $instance->completed_at->diffInMinutes($instance->scheduled_for);
                    }),
                ];
            });
    }

    public function getTaskPerformanceByCategory(int $businessId, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        return TaskInstance::whereHas('task', function ($query) use ($businessId) {
            $query->where('business_id', $businessId);
        })
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->with('task.category')
            ->get()
            ->groupBy('task.category_id')
            ->map(function ($instances) {
                $category = $instances->first()->task->category;
                return [
                    'category_id' => $category->id,
                    'category_name' => $category->name,
                    'completed_tasks' => $instances->count(),
                    'average_completion_time' => $instances->avg(function ($instance) {
                        return $instance->completed_at->diffInMinutes($instance->scheduled_for);
                    }),
                ];
            });
    }

    public function getTaskTrends(int $businessId, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        return TaskInstance::whereHas('task', function ($query) use ($businessId) {
            $query->where('business_id', $businessId);
        })
            ->whereBetween('scheduled_for', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($instance) {
                return $instance->scheduled_for->format('Y-m-d');
            })
            ->map(function ($instances) {
                return [
                    'date' => $instances->first()->scheduled_for->format('Y-m-d'),
                    'total_tasks' => $instances->count(),
                    'completed_tasks' => $instances->where('status', 'completed')->count(),
                    'overdue_tasks' => $instances->where('status', 'pending')
                        ->where('scheduled_for', '<', now())
                        ->count(),
                ];
            });
    }

    public function generateComplianceReport(int $businessId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        $stats = $this->getTaskCompletionStats($businessId, $startDate, $endDate);
        $userPerformance = $this->getTaskPerformanceByUser($businessId, $startDate, $endDate);
        $categoryPerformance = $this->getTaskPerformanceByCategory($businessId, $startDate, $endDate);
        $trends = $this->getTaskTrends($businessId, $startDate, $endDate);

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'overall_stats' => $stats,
            'user_performance' => $userPerformance,
            'category_performance' => $categoryPerformance,
            'trends' => $trends,
            'compliance_score' => $this->calculateComplianceScore($stats),
        ];
    }

    private function calculateComplianceScore(array $stats): float
    {
        $completionWeight = 0.7;
        $overdueWeight = 0.3;

        $completionScore = $stats['completion_rate'];
        $overdueScore = $stats['total_tasks'] > 0
            ? (1 - ($stats['overdue_tasks'] / $stats['total_tasks'])) * 100
            : 100;

        return ($completionScore * $completionWeight) + ($overdueScore * $overdueWeight);
    }
} 