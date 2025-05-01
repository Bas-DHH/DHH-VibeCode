<?php

namespace Tests\Feature\Tasks;

use Tests\TestCase;
use App\Models\User;
use App\Models\TemperatureVerificationTask;
use App\Models\TaskCompletion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TemperatureTaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private TemperatureVerificationTask $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->task = TemperatureVerificationTask::factory()->create([
            'business_id' => $this->user->business_id,
            'min_temperature' => 2,
            'max_temperature' => 8,
            'location' => 'Walk-in Cooler',
        ]);
    }

    /** @test */
    public function user_can_complete_temperature_task()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.temperature.complete'), [
                'task_id' => $this->task->id,
                'temperature' => 5,
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
    public function task_is_marked_as_warning_when_temperature_below_minimum()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.temperature.complete'), [
                'task_id' => $this->task->id,
                'temperature' => 1,
                'corrective_action' => 'Temperature too low, adjusting thermostat',
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
    public function task_is_marked_as_warning_when_temperature_above_maximum()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.temperature.complete'), [
                'task_id' => $this->task->id,
                'temperature' => 10,
                'corrective_action' => 'Temperature too high, checking cooling system',
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
        $otherTask = TemperatureVerificationTask::factory()->create([
            'business_id' => $otherBusiness->business_id,
        ]);

        $this->actingAs($this->user)
            ->post(route('tasks.temperature.complete'), [
                'task_id' => $otherTask->id,
                'temperature' => 5,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $otherTask->id,
        ]);
    }

    /** @test */
    public function corrective_action_is_required_when_temperature_out_of_range()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.temperature.complete'), [
                'task_id' => $this->task->id,
                'temperature' => 1, // Below minimum
            ])
            ->assertSessionHasErrors('corrective_action');

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $this->task->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('tasks.temperature.complete'), [
                'task_id' => $this->task->id,
                'temperature' => 10, // Above maximum
            ])
            ->assertSessionHasErrors('corrective_action');

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $this->task->id,
        ]);
    }

    /** @test */
    public function temperature_must_be_within_valid_range()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.temperature.complete'), [
                'task_id' => $this->task->id,
                'temperature' => -60, // Below minimum allowed
            ])
            ->assertSessionHasErrors('temperature');

        $this->actingAs($this->user)
            ->post(route('tasks.temperature.complete'), [
                'task_id' => $this->task->id,
                'temperature' => 250, // Above maximum allowed
            ])
            ->assertSessionHasErrors('temperature');
    }
} 