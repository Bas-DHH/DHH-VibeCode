<?php

namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaskScheduler
{
    public function generateDailyTasks()
    {
        $today = Carbon::today();
        
        // Get all active tasks that need to be generated today
        $tasks = Task::where('frequency', 'daily')
            ->whereDoesntHave('tasks', function ($query) use ($today) {
                $query->whereDate('due_date', $today);
            })
            ->get();

        foreach ($tasks as $task) {
            $this->createTaskInstance($task, $today);
        }
    }

    public function generateWeeklyTasks()
    {
        $today = Carbon::today();
        $dayOfWeek = $today->dayOfWeek + 1; // Carbon uses 0-6, we use 1-7

        $tasks = Task::where('frequency', 'weekly')
            ->where('day_of_week', $dayOfWeek)
            ->whereDoesntHave('tasks', function ($query) use ($today) {
                $query->whereDate('due_date', $today);
            })
            ->get();

        foreach ($tasks as $task) {
            $this->createTaskInstance($task, $today);
        }
    }

    public function generateMonthlyTasks()
    {
        $today = Carbon::today();
        $dayOfMonth = $today->day;

        $tasks = Task::where('frequency', 'monthly')
            ->where('day_of_month', $dayOfMonth)
            ->whereDoesntHave('tasks', function ($query) use ($today) {
                $query->whereDate('due_date', $today);
            })
            ->get();

        foreach ($tasks as $task) {
            $this->createTaskInstance($task, $today);
        }
    }

    protected function createTaskInstance(Task $template, Carbon $date)
    {
        $dueDate = $date->copy();
        
        if ($template->scheduled_time) {
            $dueDate->setTimeFromTimeString($template->scheduled_time);
        } else {
            $dueDate->endOfDay(); // Default to end of day if no specific time
        }

        return Task::create([
            'business_id' => $template->business_id,
            'task_category_id' => $template->task_category_id,
            'title' => $template->title,
            'description' => $template->description,
            'due_date' => $dueDate,
            'status' => 'pending',
            'frequency' => $template->frequency,
            'scheduled_time' => $template->scheduled_time,
            'day_of_week' => $template->day_of_week,
            'day_of_month' => $template->day_of_month
        ]);
    }

    public function generateAllTasks()
    {
        DB::transaction(function () {
            $this->generateDailyTasks();
            $this->generateWeeklyTasks();
            $this->generateMonthlyTasks();
        });
    }
}
