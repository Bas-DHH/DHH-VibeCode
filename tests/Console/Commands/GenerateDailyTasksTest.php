<?php

namespace Tests\Console\Commands;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateDailyTasksTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_generates_daily_tasks()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'frequency' => 'daily',
            'is_active' => true,
            'assigned_user_id' => $user->id,
        ]);

        $this->artisan('tasks:generate-daily')
            ->assertExitCode(0);

        $this->assertDatabaseHas('task_instances', [
            'task_id' => $task->id,
            'status' => 'pending',
        ]);
    }

    public function test_command_handles_errors_gracefully()
    {
        // Mock a task that will cause an error
        $task = Task::factory()->create([
            'frequency' => 'invalid_frequency', // This should cause an error
            'is_active' => true,
        ]);

        $this->artisan('tasks:generate-daily')
            ->assertExitCode(1);
    }
} 