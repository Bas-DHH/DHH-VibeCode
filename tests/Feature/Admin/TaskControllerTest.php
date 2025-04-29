<?php

namespace Tests\Feature\Admin;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user with a business
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'business_id' => 1, // Assuming business_id 1 exists
        ]);
    }

    public function test_admin_can_create_task(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/tasks/store', [
            'title' => 'Test Task',
            'category' => 'temperature',
            'frequency' => 'daily',
            'due_date' => '2024-12-31',
        ]);

        $response->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'category' => 'temperature',
            'frequency' => 'daily',
            'status' => 'pending',
            'business_id' => $this->admin->business_id,
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_task_creation_requires_valid_data(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/tasks/store', [
            'title' => '',
            'category' => 'invalid',
            'frequency' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['title', 'category', 'frequency']);
    }

    public function test_non_admin_cannot_create_tasks(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff);

        $response = $this->post('/admin/tasks/store', [
            'title' => 'Test Task',
            'category' => 'temperature',
            'frequency' => 'daily',
        ]);

        $response->assertForbidden();
    }
} 