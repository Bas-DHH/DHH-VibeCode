<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Task;
use App\Models\TaskInstance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_edit_completed_task()
    {
        $business = Business::factory()->create();
        $admin = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'admin',
        ]);
        $task = Task::factory()->create([
            'business_id' => $business->id,
        ]);
        $taskInstance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'business_id' => $business->id,
            'completed_at' => now(),
            'notes' => 'Original notes',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('tasks.update', $taskInstance->id), [
                'notes' => 'Updated notes',
                'audit_notes' => 'Correcting documentation',
            ]);

        $response->assertRedirect();
        
        // Check if task was updated
        $this->assertEquals('Updated notes', $taskInstance->fresh()->notes);

        // Check if audit log was created
        $this->assertDatabaseHas('task_audit_logs', [
            'task_instance_id' => $taskInstance->id,
            'user_id' => $admin->id,
            'action' => 'updated',
            'notes' => 'Correcting documentation',
        ]);
    }

    public function test_staff_cannot_edit_completed_task()
    {
        $business = Business::factory()->create();
        $staff = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);
        $task = Task::factory()->create([
            'business_id' => $business->id,
        ]);
        $taskInstance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'business_id' => $business->id,
            'completed_at' => now(),
            'notes' => 'Original notes',
        ]);

        $response = $this->actingAs($staff)
            ->put(route('tasks.update', $taskInstance->id), [
                'notes' => 'Updated notes',
            ]);

        $response->assertForbidden();
        
        // Check if task was not updated
        $this->assertEquals('Original notes', $taskInstance->fresh()->notes);

        // Check that no audit log was created
        $this->assertDatabaseMissing('task_audit_logs', [
            'task_instance_id' => $taskInstance->id,
        ]);
    }

    public function test_completion_is_logged()
    {
        $business = Business::factory()->create();
        $staff = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);
        $task = Task::factory()->create([
            'business_id' => $business->id,
        ]);
        $taskInstance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'business_id' => $business->id,
            'completed_at' => null,
        ]);

        $response = $this->actingAs($staff)
            ->post(route('tasks.complete', $taskInstance->id), [
                'completion_notes' => 'Task completed successfully',
            ]);

        $response->assertRedirect();
        
        // Check if task was completed
        $this->assertNotNull($taskInstance->fresh()->completed_at);

        // Check if audit log was created
        $this->assertDatabaseHas('task_audit_logs', [
            'task_instance_id' => $taskInstance->id,
            'user_id' => $staff->id,
            'action' => 'completed',
            'notes' => 'Task completed successfully',
        ]);
    }
} 