<?php

namespace Tests\Notifications;

use App\Mail\DailySummary;
use App\Models\Business;
use App\Models\Task;
use App\Models\TaskInstance;
use App\Models\User;
use App\Notifications\TaskCompletedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TaskCompletedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_sent_to_assigned_user()
    {
        Mail::fake();

        $business = Business::factory()->create();
        $user = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);
        $task = Task::factory()->create([
            'business_id' => $business->id,
        ]);
        $taskInstance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'business_id' => $business->id,
            'assigned_user_id' => $user->id,
            'completed_at' => now(),
            'completed_by_id' => $user->id,
        ]);

        $notification = new TaskCompletedNotification($taskInstance);
        $user->notify($notification);

        Mail::assertSent(\App\Mail\DailySummary::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_notification_is_sent_to_admins_when_corrective_actions_needed()
    {
        Mail::fake();

        $business = Business::factory()->create();
        $admin = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'admin',
        ]);
        $user = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);
        $task = Task::factory()->create([
            'business_id' => $business->id,
        ]);
        $taskInstance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'business_id' => $business->id,
            'assigned_user_id' => $user->id,
            'completed_at' => now(),
            'completed_by_id' => $user->id,
            'corrective_actions' => 'Some corrective actions needed',
        ]);

        $notification = new TaskCompletedNotification($taskInstance, true);
        $admin->notify($notification);

        Mail::assertSent(\App\Mail\DailySummary::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }
} 