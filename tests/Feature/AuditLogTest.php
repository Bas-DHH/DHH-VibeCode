<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Task;
use App\Models\TaskInstance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_audit_log_when_task_is_created()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $task = Task::factory()->create([
            'business_id' => $user->business_id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Task::class,
            'auditable_id' => $task->id,
            'action' => 'created',
            'user_id' => $user->id,
        ]);
    }

    public function test_creates_audit_log_when_task_is_updated()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $task = Task::factory()->create([
            'business_id' => $user->business_id,
        ]);

        $oldTitle = $task->title;
        $task->update(['title' => 'New Title']);

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Task::class,
            'auditable_id' => $task->id,
            'action' => 'updated',
            'user_id' => $user->id,
        ]);

        $log = AuditLog::where('auditable_id', $task->id)
            ->where('action', 'updated')
            ->first();

        $this->assertEquals(['title' => $oldTitle], $log->old_values);
        $this->assertEquals(['title' => 'New Title'], $log->new_values);
    }

    public function test_creates_audit_log_when_task_is_deleted()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $task = Task::factory()->create([
            'business_id' => $user->business_id,
        ]);

        $task->delete();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Task::class,
            'auditable_id' => $task->id,
            'action' => 'deleted',
            'user_id' => $user->id,
        ]);
    }

    public function test_creates_audit_log_when_task_instance_is_completed()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $task = Task::factory()->create([
            'business_id' => $user->business_id,
        ]);

        $instance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'status' => 'pending',
        ]);

        $instance->update([
            'status' => 'completed',
            'completed_by_id' => $user->id,
            'completed_at' => now(),
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => TaskInstance::class,
            'auditable_id' => $instance->id,
            'action' => 'updated',
            'user_id' => $user->id,
        ]);
    }

    public function test_creates_audit_log_when_completed_task_is_edited()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $task = Task::factory()->create([
            'business_id' => $admin->business_id,
        ]);

        $instance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'status' => 'completed',
            'completed_by_id' => $admin->id,
            'completed_at' => now(),
        ]);

        $oldInputData = $instance->input_data;
        $instance->update([
            'input_data' => ['new' => 'data'],
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => TaskInstance::class,
            'auditable_id' => $instance->id,
            'action' => 'updated',
            'user_id' => $admin->id,
        ]);

        $log = AuditLog::where('auditable_id', $instance->id)
            ->where('action', 'updated')
            ->first();

        $this->assertEquals(['input_data' => $oldInputData], $log->old_values);
        $this->assertEquals(['input_data' => ['new' => 'data']], $log->new_values);
    }
} 