<?php

namespace Tests\Console\Commands;

use App\Console\Commands\SendDailyTaskSummary;
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

    public function test_daily_summary_is_sent_to_admins()
    {
        Mail::fake();

        // Create a business
        $business = Business::factory()->create();

        // Create an admin user
        $admin = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'admin',
        ]);

        // Create a task
        $task = Task::factory()->create([
            'business_id' => $business->id,
        ]);

        // Create task instances
        TaskInstance::factory()->create([
            'task_id' => $task->id,
            'business_id' => $business->id,
            'scheduled_for' => now()->startOfDay(),
            'completed_at' => now(),
        ]);

        TaskInstance::factory()->create([
            'task_id' => $task->id,
            'business_id' => $business->id,
            'scheduled_for' => now()->startOfDay(),
            'completed_at' => null,
        ]);

        TaskInstance::factory()->create([
            'task_id' => $task->id,
            'business_id' => $business->id,
            'scheduled_for' => now()->subDay(),
            'completed_at' => null,
        ]);

        $this->artisan('tasks:send-daily-summary')
            ->assertExitCode(0);

        Mail::assertSent(\App\Mail\DailySummary::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }

    public function test_daily_summary_is_not_sent_to_non_admins()
    {
        Mail::fake();

        // Create a business
        $business = Business::factory()->create();

        // Create a staff user
        $staff = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);

        $this->artisan('tasks:send-daily-summary')
            ->assertExitCode(0);

        Mail::assertNotSent(\App\Mail\DailySummary::class, function ($mail) use ($staff) {
            return $mail->hasTo($staff->email);
        });
    }
} 