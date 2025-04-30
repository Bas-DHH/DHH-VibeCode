<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Task;
use App\Models\TaskInstance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCompletionFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_cleaning_task_completion()
    {
        $business = Business::factory()->create();
        $user = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);
        $task = Task::factory()->create([
            'business_id' => $business->id,
            'category_id' => 1, // Cleaning category
        ]);
        $taskInstance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'business_id' => $business->id,
            'assigned_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('tasks.complete', $taskInstance->id), [
                'is_clean' => true,
                'notes' => 'Everything is clean',
            ]);

        $response->assertRedirect();
        $this->assertNotNull($taskInstance->fresh()->completed_at);
        $this->assertEquals('Everything is clean', $taskInstance->fresh()->notes);
    }

    public function test_temperature_check_completion()
    {
        $business = Business::factory()->create();
        $user = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);
        $task = Task::factory()->create([
            'business_id' => $business->id,
            'category_id' => 2, // Temperature Control category
        ]);
        $taskInstance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'business_id' => $business->id,
            'assigned_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('tasks.complete', $taskInstance->id), [
                'temperature' => 4.5,
                'is_within_range' => true,
                'notes' => 'Temperature is good',
            ]);

        $response->assertRedirect();
        $this->assertNotNull($taskInstance->fresh()->completed_at);
        $this->assertEquals(4.5, $taskInstance->fresh()->temperature);
        $this->assertEquals('Temperature is good', $taskInstance->fresh()->notes);
    }

    public function test_critical_cooking_completion()
    {
        $business = Business::factory()->create();
        $user = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);
        $task = Task::factory()->create([
            'business_id' => $business->id,
            'category_id' => 3, // Critical Cooking category
        ]);
        $taskInstance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'business_id' => $business->id,
            'assigned_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('tasks.complete', $taskInstance->id), [
                'temperature' => 75,
                'is_within_range' => true,
                'cooking_time' => 30,
                'notes' => 'Cooking process completed successfully',
            ]);

        $response->assertRedirect();
        $this->assertNotNull($taskInstance->fresh()->completed_at);
        $this->assertEquals(75, $taskInstance->fresh()->temperature);
        $this->assertEquals(30, $taskInstance->fresh()->cooking_time);
        $this->assertEquals('Cooking process completed successfully', $taskInstance->fresh()->notes);
    }

    public function test_goods_receiving_completion()
    {
        $business = Business::factory()->create();
        $user = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);
        $task = Task::factory()->create([
            'business_id' => $business->id,
            'category_id' => 4, // Goods Receiving category
        ]);
        $taskInstance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'business_id' => $business->id,
            'assigned_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('tasks.complete', $taskInstance->id), [
                'temperature' => 2.5,
                'is_within_range' => true,
                'is_damaged' => false,
                'is_expired' => false,
                'notes' => 'Goods received in good condition',
            ]);

        $response->assertRedirect();
        $this->assertNotNull($taskInstance->fresh()->completed_at);
        $this->assertEquals(2.5, $taskInstance->fresh()->temperature);
        $this->assertFalse($taskInstance->fresh()->is_damaged);
        $this->assertFalse($taskInstance->fresh()->is_expired);
        $this->assertEquals('Goods received in good condition', $taskInstance->fresh()->notes);
    }
} 