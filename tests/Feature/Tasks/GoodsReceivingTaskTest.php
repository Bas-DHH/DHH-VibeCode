<?php

namespace Tests\Feature\Tasks;

use Tests\TestCase;
use App\Models\User;
use App\Models\GoodsReceivingTask;
use App\Models\TaskCompletion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

class GoodsReceivingTaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private GoodsReceivingTask $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->task = GoodsReceivingTask::factory()->create([
            'business_id' => $this->user->business_id,
            'temperature_check_required' => true,
            'min_temperature' => 2,
            'max_temperature' => 8,
            'visual_check_required' => true,
        ]);
    }

    /** @test */
    public function user_can_complete_goods_receiving_task()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.goods-receiving.complete'), [
                'task_id' => $this->task->id,
                'supplier_name' => 'Test Supplier',
                'product_name' => 'Test Product',
                'batch_number' => 'BATCH123',
                'expiry_date' => Carbon::tomorrow()->format('Y-m-d'),
                'temperature' => 5,
                'packaging_intact' => true,
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
    public function task_is_marked_as_warning_when_temperature_out_of_range()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.goods-receiving.complete'), [
                'task_id' => $this->task->id,
                'supplier_name' => 'Test Supplier',
                'product_name' => 'Test Product',
                'batch_number' => 'BATCH123',
                'expiry_date' => Carbon::tomorrow()->format('Y-m-d'),
                'temperature' => 10,
                'packaging_intact' => true,
                'visual_check_passed' => true,
                'corrective_action' => 'Temperature too high, product rejected',
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
    public function task_is_marked_as_warning_when_packaging_not_intact()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.goods-receiving.complete'), [
                'task_id' => $this->task->id,
                'supplier_name' => 'Test Supplier',
                'product_name' => 'Test Product',
                'batch_number' => 'BATCH123',
                'expiry_date' => Carbon::tomorrow()->format('Y-m-d'),
                'temperature' => 5,
                'packaging_intact' => false,
                'visual_check_passed' => true,
                'corrective_action' => 'Packaging damaged, product rejected',
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
            ->post(route('tasks.goods-receiving.complete'), [
                'task_id' => $this->task->id,
                'supplier_name' => 'Test Supplier',
                'product_name' => 'Test Product',
                'batch_number' => 'BATCH123',
                'expiry_date' => Carbon::tomorrow()->format('Y-m-d'),
                'temperature' => 5,
                'packaging_intact' => true,
                'visual_check_passed' => false,
                'corrective_action' => 'Product appears spoiled, rejected',
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
        $otherTask = GoodsReceivingTask::factory()->create([
            'business_id' => $otherBusiness->business_id,
        ]);

        $this->actingAs($this->user)
            ->post(route('tasks.goods-receiving.complete'), [
                'task_id' => $otherTask->id,
                'supplier_name' => 'Test Supplier',
                'product_name' => 'Test Product',
                'batch_number' => 'BATCH123',
                'expiry_date' => Carbon::tomorrow()->format('Y-m-d'),
                'temperature' => 5,
                'packaging_intact' => true,
                'visual_check_passed' => true,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $otherTask->id,
        ]);
    }

    /** @test */
    public function expiry_date_must_be_in_future()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.goods-receiving.complete'), [
                'task_id' => $this->task->id,
                'supplier_name' => 'Test Supplier',
                'product_name' => 'Test Product',
                'batch_number' => 'BATCH123',
                'expiry_date' => Carbon::yesterday()->format('Y-m-d'),
                'temperature' => 5,
                'packaging_intact' => true,
                'visual_check_passed' => true,
            ])
            ->assertSessionHasErrors('expiry_date');

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $this->task->id,
        ]);
    }

    /** @test */
    public function corrective_action_is_required_when_requirements_not_met()
    {
        $this->actingAs($this->user)
            ->post(route('tasks.goods-receiving.complete'), [
                'task_id' => $this->task->id,
                'supplier_name' => 'Test Supplier',
                'product_name' => 'Test Product',
                'batch_number' => 'BATCH123',
                'expiry_date' => Carbon::tomorrow()->format('Y-m-d'),
                'temperature' => 10, // Above maximum
                'packaging_intact' => true,
                'visual_check_passed' => true,
            ])
            ->assertSessionHasErrors('corrective_action');

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $this->task->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('tasks.goods-receiving.complete'), [
                'task_id' => $this->task->id,
                'supplier_name' => 'Test Supplier',
                'product_name' => 'Test Product',
                'batch_number' => 'BATCH123',
                'expiry_date' => Carbon::tomorrow()->format('Y-m-d'),
                'temperature' => 5,
                'packaging_intact' => false, // Failed packaging check
                'visual_check_passed' => true,
            ])
            ->assertSessionHasErrors('corrective_action');

        $this->assertDatabaseMissing('task_completions', [
            'task_id' => $this->task->id,
        ]);
    }
} 