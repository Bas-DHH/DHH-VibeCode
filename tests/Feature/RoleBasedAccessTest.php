<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskInstance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_super_admin_dashboard()
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)
            ->get(route('super-admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_super_admin_dashboard()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->get(route('super-admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_super_admin_dashboard()
    {
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($staff)
            ->get(route('super-admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_super_admin_can_access_admin_dashboard()
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_admin_dashboard()
    {
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($staff)
            ->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_staff_can_access_staff_dashboard()
    {
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($staff)
            ->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_all_roles_can_access_profile()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();

        $this->actingAs($superAdmin)
            ->get(route('profile.edit'))
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get(route('profile.edit'))
            ->assertStatus(200);

        $this->actingAs($staff)
            ->get(route('profile.edit'))
            ->assertStatus(200);
    }

    public function test_staff_cannot_create_tasks()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff);

        $response = $this->post(route('tasks.store'), [
            'title' => 'New Task',
            'description' => 'Task Description',
            'frequency' => 'daily',
            'scheduled_time' => '09:00',
        ]);

        $response->assertForbidden();
    }

    public function test_staff_cannot_edit_tasks()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff);

        $task = Task::factory()->create([
            'business_id' => $staff->business_id,
        ]);

        $response = $this->put(route('tasks.update', $task), [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'frequency' => 'daily',
            'scheduled_time' => '09:00',
        ]);

        $response->assertForbidden();
    }

    public function test_staff_cannot_delete_tasks()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff);

        $task = Task::factory()->create([
            'business_id' => $staff->business_id,
        ]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertForbidden();
    }

    public function test_staff_cannot_edit_completed_tasks()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff);

        $task = Task::factory()->create([
            'business_id' => $staff->business_id,
        ]);

        $instance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'status' => 'completed',
        ]);

        $response = $this->put(route('tasks.edit-completed', $instance), [
            'input_data' => ['new' => 'data'],
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_create_tasks()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->post(route('tasks.store'), [
            'title' => 'New Task',
            'description' => 'Task Description',
            'frequency' => 'daily',
            'scheduled_time' => '09:00',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'business_id' => $admin->business_id,
        ]);
    }

    public function test_admin_can_edit_completed_tasks()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $task = Task::factory()->create([
            'business_id' => $admin->business_id,
        ]);

        $instance = TaskInstance::factory()->create([
            'task_id' => $task->id,
            'status' => 'completed',
        ]);

        $response = $this->put(route('tasks.edit-completed', $instance), [
            'input_data' => ['new' => 'data'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('task_instances', [
            'id' => $instance->id,
            'input_data' => json_encode(['new' => 'data']),
        ]);
    }

    public function test_super_admin_can_manage_all_businesses()
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($superAdmin);

        $otherBusiness = Business::factory()->create();
        $task = Task::factory()->create([
            'business_id' => $otherBusiness->id,
        ]);

        $response = $this->put(route('tasks.update', $task), [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'frequency' => 'daily',
            'scheduled_time' => '09:00',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
        ]);
    }
} 