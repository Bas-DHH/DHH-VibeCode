<?php

namespace Tests\Console;

use App\Console\Commands\SendDailyTaskSummary;
use App\Mail\DailySummary;
use App\Models\Business;
use App\Models\Task;
use App\Models\TaskInstance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendDailyTaskSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_daily_summary_to_admins()
    {
        Mail::fake();

        // Create a business
        $business = Business::factory()->create();

        // Create an admin user
        $admin = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        // Create a task
        $task = Task::factory()->create([
            'business_id' => $business->id,
        ]);

        // Create task instances
        TaskInstance::factory()->create([
            'task_id' => $task->id,
            'scheduled_for' => now()->startOfDay(),
            'status' => 'pending',
        ]);

        TaskInstance::factory()->create([
            'task_id' => $task->id,
            'scheduled_for' => now()->startOfDay(),
            'status' => 'completed',
        ]);

        TaskInstance::factory()->create([
            'task_id' => $task->id,
            'scheduled_for' => now()->subDay(),
            'status' => 'pending',
        ]);

        // Run the command
        $this->artisan('tasks:send-daily-summary')
            ->assertExitCode(0);

        // Assert that the email was sent
        Mail::assertSent(DailySummary::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }

    public function test_does_not_send_to_non_admin_users()
    {
        Mail::fake();

        // Create a business
        $business = Business::factory()->create();

        // Create a staff user
        $staff = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
            'email' => 'staff@example.com',
        ]);

        // Create a task
        $task = Task::factory()->create([
            'business_id' => $business->id,
        ]);

        // Create task instances
        TaskInstance::factory()->create([
            'task_id' => $task->id,
            'scheduled_for' => now()->startOfDay(),
            'status' => 'pending',
        ]);

        // Run the command
        $this->artisan('tasks:send-daily-summary')
            ->assertExitCode(0);

        // Assert that no email was sent
        Mail::assertNotSent(DailySummary::class);
    }

    public function test_handles_business_without_admins()
    {
        Mail::fake();

        // Create a business without admins
        $business = Business::factory()->create();

        // Create a task
        $task = Task::factory()->create([
            'business_id' => $business->id,
        ]);

        // Create task instances
        TaskInstance::factory()->create([
            'task_id' => $task->id,
            'scheduled_for' => now()->startOfDay(),
            'status' => 'pending',
        ]);

        // Run the command
        $this->artisan('tasks:send-daily-summary')
            ->assertExitCode(0);

        // Assert that no email was sent
        Mail::assertNotSent(DailySummary::class);
    }
} 