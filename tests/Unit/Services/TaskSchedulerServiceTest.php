<?php

namespace Tests\Unit\Services;

use App\Models\Task;
use App\Models\TaskInstance;
use App\Models\User;
use App\Services\TaskSchedulerService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskSchedulerServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaskSchedulerService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TaskSchedulerService();
    }

    public function test_generates_daily_task_instances()
    {
        $task = Task::factory()->create([
            'frequency' => 'daily',
            'scheduled_time' => '09:00:00',
            'is_active' => true,
        ]);

        $this->service->generateDailyTasks();

        $this->assertDatabaseHas('task_instances', [
            'task_id' => $task->id,
            'status' => 'pending',
        ]);
    }

    public function test_generates_weekly_task_instances_on_scheduled_day()
    {
        $today = Carbon::today();
        $task = Task::factory()->create([
            'frequency' => 'weekly',
            'day_of_week' => $today->dayOfWeek,
            'scheduled_time' => '09:00:00',
            'is_active' => true,
        ]);

        $this->service->generateDailyTasks();

        $this->assertDatabaseHas('task_instances', [
            'task_id' => $task->id,
            'status' => 'pending',
        ]);
    }

    public function test_does_not_generate_weekly_task_instances_on_unscheduled_day()
    {
        $today = Carbon::today();
        $task = Task::factory()->create([
            'frequency' => 'weekly',
            'day_of_week' => ($today->dayOfWeek + 1) % 7, // Next day
            'scheduled_time' => '09:00:00',
            'is_active' => true,
        ]);

        $this->service->generateDailyTasks();

        $this->assertDatabaseMissing('task_instances', [
            'task_id' => $task->id,
        ]);
    }

    public function test_generates_monthly_task_instances_on_scheduled_day()
    {
        $today = Carbon::today();
        $task = Task::factory()->create([
            'frequency' => 'monthly',
            'day_of_month' => $today->day,
            'scheduled_time' => '09:00:00',
            'is_active' => true,
        ]);

        $this->service->generateDailyTasks();

        $this->assertDatabaseHas('task_instances', [
            'task_id' => $task->id,
            'status' => 'pending',
        ]);
    }

    public function test_does_not_generate_monthly_task_instances_on_unscheduled_day()
    {
        $today = Carbon::today();
        $task = Task::factory()->create([
            'frequency' => 'monthly',
            'day_of_month' => ($today->day % 28) + 1, // Next day, wrapping around
            'scheduled_time' => '09:00:00',
            'is_active' => true,
        ]);

        $this->service->generateDailyTasks();

        $this->assertDatabaseMissing('task_instances', [
            'task_id' => $task->id,
        ]);
    }

    public function test_does_not_generate_instances_for_inactive_tasks()
    {
        $task = Task::factory()->create([
            'frequency' => 'daily',
            'scheduled_time' => '09:00:00',
            'is_active' => false,
        ]);

        $this->service->generateDailyTasks();

        $this->assertDatabaseMissing('task_instances', [
            'task_id' => $task->id,
        ]);
    }

    public function test_does_not_create_duplicate_instances()
    {
        $task = Task::factory()->create([
            'frequency' => 'daily',
            'scheduled_time' => '09:00:00',
            'is_active' => true,
        ]);

        // Create an instance for today
        TaskInstance::factory()->create([
            'task_id' => $task->id,
            'scheduled_for' => Carbon::today()->setTimeFromTimeString('09:00:00'),
        ]);

        $this->service->generateDailyTasks();

        $this->assertDatabaseCount('task_instances', 1);
    }
} 