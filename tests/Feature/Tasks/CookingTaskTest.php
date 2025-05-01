<?php

namespace Tests\Feature\Tasks;

use Tests\TestCase;
use App\Models\User;
use App\Models\CookingVerificationTask;
use App\Models\TaskCompletion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CookingTaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private CookingVerificationTask $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->task = CookingVerificationTask::factory()->create([
            'business_id' => $this->user->business_id,
            'temperature_norm' => 75,
            'cooking_time_required' => true,
            'visual_checks_required' => true,
        ]);
    }

    /** @test */
    public function user_can_complete_cooking_task()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.cooking.complete'), [
                'task_id' => $this->task->id,
                'product_name' => 'Test Product',
                'temperature' => 80,
                'cooking_time' => 30,
                'visual_check_passed' => true,
                'notes' => 'Test notes',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('task_completions', [
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function task_is_marked_as_warning_when_temperature_below_norm()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.cooking.complete'), [
                'task_id' => $this->task->id,
                'product_name' => 'Test Product',
                'temperature' => 70,
                'cooking_time' => 30,
                'visual_check_passed' => true,
                'corrective_action' => 'Temperature was too low, food was reheated',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('task_completions', [
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'status' => 'warning',
        ]);
    }

    /** @test */
    public function task_is_marked_as_warning_when_visual_check_fails()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.cooking.complete'), [
                'task_id' => $this->task->id,
                'product_name' => 'Test Product',
                'temperature' => 80,
                'cooking_time' => 30,
                'visual_check_passed' => false,
                'corrective_action' => 'Food was overcooked, preparing new batch',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('task_completions', [
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'status' => 'warning',
        ]);
    }

    /** @test */
    public function user_cannot_complete_task_from_different_business()
    {
        $otherBusiness = User::factory()->create();
        $otherTask = CookingVerificationTask::factory()->create([
            'business_id' => $otherBusiness->business_id,
        ]);

        $this->actingAs($this->user)
            ->post(route('tasks.cooking.complete'), [
                'task_id' => $otherTask->id,
                'product_name' => 'Test Product',
                'temperature' => 80,
                'cooking_time' => 30,
                'visual_check_passed' => true,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $otherTask->id,
        ]);
    }

    /** @test */
    public function cooking_time_is_required_when_configured()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.cooking.complete'), [
                'task_id' => $this->task->id,
                'product_name' => 'Test Product',
                'temperature' => 80,
                'visual_check_passed' => true,
            ])
            ->assertSessionHasErrors('cooking_time');

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $this->task->id,
        ]);
    }

    /** @test */
    public function visual_check_is_required_when_configured()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.cooking.complete'), [
                'task_id' => $this->task->id,
                'product_name' => 'Test Product',
                'temperature' => 80,
                'cooking_time' => 30,
            ])
            ->assertSessionHasErrors('visual_check_passed');

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $this->task->id,
        ]);
    }

    /** @test */
    public function corrective_action_is_required_when_requirements_not_met()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.cooking.complete'), [
                'task_id' => $this->task->id,
                'product_name' => 'Test Product',
                'temperature' => 70, // Below norm
                'cooking_time' => 30,
                'visual_check_passed' => true,
            ])
            ->assertSessionHasErrors('corrective_action');

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $this->task->id,
        ]);
    }
} 