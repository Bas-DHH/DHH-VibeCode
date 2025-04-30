<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskInstance;
use App\Models\TaskCategory;
use App\Models\Business;
use App\Models\User;
use App\Services\TaskSchedulerService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskSchedulerTest extends TestCase
{
    use RefreshDatabase;

    private TaskSchedulerService $scheduler;
    private Business $business;
    private User $user;
    private TaskCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create minimal test data
        $this->business = Business::factory()->create(['name' => 'Test Business']);
        $this->user = User::factory()->create(['name' => 'Test User']);
        $this->category = TaskCategory::factory()->create([
            'name_nl' => 'Test Category NL',
            'name_en' => 'Test Category EN',
            'icon' => 'test-icon',
            'color' => '#000000'
        ]);
        
        $this->scheduler = new TaskSchedulerService();
    }

    protected function tearDown(): void
    {
        // Clean up after each test
        TaskInstance::query()->delete();
        Task::query()->delete();
        TaskCategory::query()->delete();
        User::query()->delete();
        Business::query()->delete();
        
        parent::tearDown();
    }

    public function test_generates_daily_task_instances(): void
    {
        $task = Task::factory()->create([
            'business_id' => $this->business->id,
            'task_category_id' => $this->category->id,
            'frequency' => 'daily',
            'is_active' => true,
            'scheduled_time' => '09:00:00'
        ]);

        $this->scheduler->generateTaskInstances();

        $instances = TaskInstance::where('task_id', $task->id)->get();
        $this->assertGreaterThan(0, $instances->count());
        $this->assertLessThanOrEqual(7, $instances->count());

        $firstInstance = $instances->first();
        $this->assertEquals('pending', $firstInstance->status);
        $this->assertEquals('09:00:00', $firstInstance->scheduled_for->format('H:i:s'));
    }

    public function test_generates_weekly_task_instances(): void
    {
        $task = Task::factory()->create([
            'business_id' => $this->business->id,
            'task_category_id' => $this->category->id,
            'frequency' => 'weekly',
            'day_of_week' => Carbon::MONDAY,
            'is_active' => true,
            'scheduled_time' => '09:00:00'
        ]);

        $this->scheduler->generateTaskInstances();

        $instances = TaskInstance::where('task_id', $task->id)->get();
        $this->assertGreaterThan(0, $instances->count());

        foreach ($instances as $instance) {
            $this->assertEquals(Carbon::MONDAY, $instance->scheduled_for->dayOfWeek);
        }
    }

    public function test_generates_monthly_task_instances(): void
    {
        $task = Task::factory()->create([
            'business_id' => $this->business->id,
            'task_category_id' => $this->category->id,
            'frequency' => 'monthly',
            'day_of_month' => 15,
            'is_active' => true,
            'scheduled_time' => '09:00:00'
        ]);

        $this->scheduler->generateTaskInstances();

        $instances = TaskInstance::where('task_id', $task->id)->get();
        $this->assertGreaterThan(0, $instances->count());

        foreach ($instances as $instance) {
            $this->assertEquals(15, $instance->scheduled_for->day);
        }
    }

    public function test_does_not_generate_instances_for_inactive_tasks(): void
    {
        $task = Task::factory()->create([
            'business_id' => $this->business->id,
            'task_category_id' => $this->category->id,
            'frequency' => 'daily',
            'is_active' => false
        ]);

        $this->scheduler->generateTaskInstances();

        $instances = TaskInstance::where('task_id', $task->id)->get();
        $this->assertCount(0, $instances);
    }

    public function test_does_not_create_duplicate_instances(): void
    {
        $task = Task::factory()->create([
            'business_id' => $this->business->id,
            'task_category_id' => $this->category->id,
            'frequency' => 'daily',
            'is_active' => true
        ]);

        // Generate instances twice
        $this->scheduler->generateTaskInstances();
        $this->scheduler->generateTaskInstances();

        $instances = TaskInstance::where('task_id', $task->id)->get();
        $this->assertCount(7, $instances);
    }
} 