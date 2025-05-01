<?php

namespace Tests\Feature\Tasks;

use Tests\TestCase;
use App\Models\User;
use App\Models\CleaningVerificationTask;
use App\Models\TaskCompletion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CleaningTaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private CleaningVerificationTask $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->task = CleaningVerificationTask::factory()->create([
            'business_id' => $this->user->business_id,
        ]);
    }

    /** @test */
    public function user_can_complete_cleaning_task()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.cleaning.complete'), [
                'task_id' => $this->task->id,
                'cleaned' => true,
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
    public function user_must_provide_corrective_action_when_not_cleaned()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.cleaning.complete'), [
                'task_id' => $this->task->id,
                'cleaned' => false,
            ])
            ->assertSessionHasErrors('corrective_action');

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $this->task->id,
        ]);
    }

    /** @test */
    public function task_is_marked_as_warning_when_not_cleaned()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.cleaning.complete'), [
                'task_id' => $this->task->id,
                'cleaned' => false,
                'corrective_action' => 'Area was blocked',
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
        $otherTask = CleaningVerificationTask::factory()->create([
            'business_id' => $otherBusiness->business_id,
        ]);

        $this->actingAs($this->user)
            ->post(route('tasks.cleaning.complete'), [
                'task_id' => $otherTask->id,
                'cleaned' => true,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $otherTask->id,
        ]);
    }

    /** @test */
    public function disinfection_is_required_when_configured()
    {
        $task = CleaningVerificationTask::factory()->create([
            'business_id' => $this->user->business_id,
            'disinfection_required' => true,
        ]);

        $this->actingAs($this->user)
            ->post(route('tasks.cleaning.complete'), [
                'task_id' => $task->id,
                'cleaned' => true,
                'disinfected' => false,
            ])
            ->assertSessionHasErrors('corrective_action');

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $task->id,
        ]);
    }
} 