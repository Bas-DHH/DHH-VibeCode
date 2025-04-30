<?php

namespace Tests\Unit\Models;

use App\Models\Business;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_has_required_fields()
    {
        $task = Task::factory()->create([
            'title' => 'Test Task',
            'name_nl' => 'Test Taak',
            'name_en' => 'Test Task',
            'description' => 'Test description',
            'instructions_nl' => 'Test instructies',
            'instructions_en' => 'Test instructions',
            'frequency' => 'daily',
            'scheduled_time' => '09:00:00',
            'is_active' => true,
        ]);

        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals('Test Taak', $task->name_nl);
        $this->assertEquals('Test Task', $task->name_en);
        $this->assertEquals('daily', $task->frequency);
        $this->assertEquals('09:00:00', $task->scheduled_time);
        $this->assertTrue($task->is_active);
    }

    public function test_task_belongs_to_business()
    {
        $business = Business::factory()->create();
        $task = Task::factory()->create(['business_id' => $business->id]);

        $this->assertInstanceOf(Business::class, $task->business);
        $this->assertEquals($business->id, $task->business->id);
    }

    public function test_task_belongs_to_category()
    {
        $category = TaskCategory::factory()->create();
        $task = Task::factory()->create(['task_category_id' => $category->id]);

        $this->assertInstanceOf(TaskCategory::class, $task->category);
        $this->assertEquals($category->id, $task->category->id);
    }

    public function test_task_belongs_to_creator()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['created_by_id' => $user->id]);

        $this->assertInstanceOf(User::class, $task->creator);
        $this->assertEquals($user->id, $task->creator->id);
    }

    public function test_task_can_be_assigned_to_user()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['assigned_user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $task->assignedUser);
        $this->assertEquals($user->id, $task->assignedUser->id);
    }

    public function test_task_has_instances()
    {
        $task = Task::factory()->create();
        $instance = $task->instances()->create([
            'scheduled_for' => now(),
            'status' => 'pending',
        ]);

        $this->assertTrue($task->instances->contains($instance));
    }

    public function test_task_can_be_inactive()
    {
        $task = Task::factory()->create(['is_active' => false]);
        $this->assertFalse($task->is_active);
    }

    public function test_task_can_have_weekly_schedule()
    {
        $task = Task::factory()->create([
            'frequency' => 'weekly',
            'day_of_week' => 1, // Monday
        ]);

        $this->assertEquals('weekly', $task->frequency);
        $this->assertEquals(1, $task->day_of_week);
    }

    public function test_task_can_have_monthly_schedule()
    {
        $task = Task::factory()->create([
            'frequency' => 'monthly',
            'day_of_month' => 15,
        ]);

        $this->assertEquals('monthly', $task->frequency);
        $this->assertEquals(15, $task->day_of_month);
    }

    public function test_task_requires_name_in_default_language()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Task::factory()->create([
            'name_nl' => null,
            'name_en' => null,
        ]);
    }
} 