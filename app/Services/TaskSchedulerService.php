<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskInstance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TaskSchedulerService
{
    /**
     * Generate task instances for all active tasks
     */
    public function generateDailyTasks()
    {
        $tasks = Task::where('is_active', true)->get();
        
        foreach ($tasks as $task) {
            try {
                $this->generateTaskInstances($task);
            } catch (\Exception $e) {
                Log::error("Failed to generate task instances for task {$task->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Generate task instances for a specific task based on its frequency
     */
    protected function generateTaskInstances(Task $task)
    {
        $today = Carbon::today();
        
        switch ($task->frequency) {
            case 'daily':
                $this->generateDailyInstance($task, $today);
                break;
            case 'weekly':
                if ($this->isScheduledDay($task, $today)) {
                    $this->generateDailyInstance($task, $today);
                }
                break;
            case 'monthly':
                if ($this->isScheduledDayOfMonth($task, $today)) {
                    $this->generateDailyInstance($task, $today);
                }
                break;
        }
    }

    /**
     * Generate a single task instance for a specific day
     */
    protected function generateDailyInstance(Task $task, Carbon $date)
    {
        // Check if instance already exists
        $existingInstance = TaskInstance::where('task_id', $task->id)
            ->whereDate('scheduled_for', $date)
            ->first();

        if ($existingInstance) {
            return;
        }

        // Create new instance
        TaskInstance::create([
            'task_id' => $task->id,
            'scheduled_for' => $date->copy()->setTimeFromTimeString($task->scheduled_time ?? '00:00:00'),
            'status' => 'pending',
            'assigned_user_id' => $task->assigned_user_id,
        ]);
    }

    /**
     * Check if today is a scheduled day for weekly tasks
     */
    protected function isScheduledDay(Task $task, Carbon $date): bool
    {
        return $task->day_of_week === $date->dayOfWeek;
    }

    /**
     * Check if today is a scheduled day for monthly tasks
     */
    protected function isScheduledDayOfMonth(Task $task, Carbon $date): bool
    {
        return $task->day_of_month === $date->day;
    }
} 