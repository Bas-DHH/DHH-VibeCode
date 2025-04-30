<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_task()
    {
        $business = Business::factory()->create();
        $admin = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'admin',
        ]);
        $staff = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);
        $category = TaskCategory::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('tasks.store'), [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'category_id' => $category->id,
                'assigned_user_id' => $staff->id,
                'frequency' => 'daily',
                'scheduled_time' => '09:00',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'category_id' => $category->id,
            'assigned_user_id' => $staff->id,
            'frequency' => 'daily',
            'scheduled_time' => '09:00',
        ]);
    }

    public function test_admin_can_update_task()
    {
        $business = Business::factory()->create();
        $admin = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'admin',
        ]);
        $staff = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);
        $category = TaskCategory::factory()->create();
        $task = Task::factory()->create([
            'business_id' => $business->id,
            'category_id' => $category->id,
            'assigned_user_id' => $staff->id,
        ]);

        $response = $this->actingAs($admin)
            ->put(route('tasks.update', $task->id), [
                'title' => 'Updated Task',
                'description' => 'Updated Description',
                'category_id' => $category->id,
                'assigned_user_id' => $staff->id,
                'frequency' => 'weekly',
                'scheduled_time' => '10:00',
                'day_of_week' => 1, // Monday
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'category_id' => $category->id,
            'assigned_user_id' => $staff->id,
            'frequency' => 'weekly',
            'scheduled_time' => '10:00',
            'day_of_week' => 1,
        ]);
    }

    public function test_staff_cannot_create_task()
    {
        $business = Business::factory()->create();
        $staff = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);
        $category = TaskCategory::factory()->create();

        $response = $this->actingAs($staff)
            ->post(route('tasks.store'), [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'category_id' => $category->id,
                'assigned_user_id' => $staff->id,
                'frequency' => 'daily',
                'scheduled_time' => '09:00',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Test Task',
        ]);
    }

    public function test_staff_cannot_update_task()
    {
        $business = Business::factory()->create();
        $admin = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'admin',
        ]);
        $staff = User::factory()->create([
            'business_id' => $business->id,
            'role' => 'staff',
        ]);
        $category = TaskCategory::factory()->create();
        $task = Task::factory()->create([
            'business_id' => $business->id,
            'category_id' => $category->id,
            'assigned_user_id' => $staff->id,
        ]);

        $response = $this->actingAs($staff)
            ->put(route('tasks.update', $task->id), [
                'title' => 'Updated Task',
                'description' => 'Updated Description',
                'category_id' => $category->id,
                'assigned_user_id' => $staff->id,
                'frequency' => 'weekly',
                'scheduled_time' => '10:00',
                'day_of_week' => 1,
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
        ]);
    }
} 