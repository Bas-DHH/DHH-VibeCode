<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskInstance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class TaskSchedulerService
{
    /**
     * Generate task instances for all active recurring tasks
     */
    public function generateTaskInstances(): void
    {
        try {
            DB::beginTransaction();

            $tasks = Task::active()
                ->with(['business', 'category'])
                ->get();

            foreach ($tasks as $task) {
                try {
                    $this->generateInstancesForTask($task);
                } catch (\Exception $e) {
                    Log::error("Failed to generate instances for task {$task->id}: " . $e->getMessage(), [
                        'task_id' => $task->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to generate task instances: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate instances for a specific task
     */
    private function generateInstancesForTask(Task $task): void
    {
        $startDate = now()->startOfDay();
        $endDate = $startDate->copy()->addDays(7)->endOfDay();

        $existingInstances = $this->getExistingInstances($task, $startDate, $endDate);

        switch ($task->frequency) {
            case 'daily':
                $this->generateDailyInstances($task, $startDate, $endDate, $existingInstances);
                break;
            case 'weekly':
                $this->generateWeeklyInstances($task, $startDate, $endDate, $existingInstances);
                break;
            case 'monthly':
                $this->generateMonthlyInstances($task, $startDate, $endDate, $existingInstances);
                break;
        }
    }

    /**
     * Get existing instances for a task within a date range
     */
    private function getExistingInstances(Task $task, Carbon $startDate, Carbon $endDate): Collection
    {
        return TaskInstance::where('task_id', $task->id)
            ->whereBetween('scheduled_for', [$startDate, $endDate])
            ->pluck('scheduled_for')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'));
    }

    /**
     * Generate daily task instances
     */
    private function generateDailyInstances(Task $task, Carbon $startDate, Carbon $endDate, Collection $existingInstances): void
    {
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if (!$existingInstances->contains($date->format('Y-m-d'))) {
                $this->createTaskInstance($task, $date->copy());
            }
        }
    }

    /**
     * Generate weekly task instances
     */
    private function generateWeeklyInstances(Task $task, Carbon $startDate, Carbon $endDate, Collection $existingInstances): void
    {
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->dayOfWeek === $task->day_of_week && !$existingInstances->contains($date->format('Y-m-d'))) {
                $this->createTaskInstance($task, $date->copy());
            }
        }
    }

    /**
     * Generate monthly task instances
     */
    private function generateMonthlyInstances(Task $task, Carbon $startDate, Carbon $endDate, Collection $existingInstances): void
    {
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->day === $task->day_of_month && !$existingInstances->contains($date->format('Y-m-d'))) {
                $this->createTaskInstance($task, $date->copy());
            }
        }
    }

    /**
     * Create a task instance if it doesn't already exist
     */
    private function createTaskInstance(Task $task, Carbon $date): void
    {
        if ($task->scheduled_time) {
            [$hours, $minutes] = explode(':', $task->scheduled_time);
            $date->setTime((int)$hours, (int)$minutes, 0);
        }

        TaskInstance::create([
            'task_id' => $task->id,
            'scheduled_for' => $date,
            'status' => 'pending',
            'assigned_user_id' => $task->assigned_user_id
        ]);
    }
} 