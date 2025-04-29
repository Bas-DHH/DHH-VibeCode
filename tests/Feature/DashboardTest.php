<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_tasks_grouped_by_category()
    {
        // Create a test user
        $user = User::factory()->create();

        // Create tasks in different categories
        $tasks = [
            Task::factory()->create([
                'category' => 'goods_receiving',
                'user_id' => $user->id,
            ]),
            Task::factory()->create([
                'category' => 'goods_receiving',
                'user_id' => $user->id,
            ]),
            Task::factory()->create([
                'category' => 'temperature',
                'user_id' => $user->id,
            ]),
            Task::factory()->create([
                'category' => 'cleaning',
                'user_id' => $user->id,
            ]),
        ];

        // Act as the user and visit the dashboard
        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        // Assert the response is successful and renders the Dashboard component
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('tasks', 4)
            ->where('tasks.0.category', 'goods_receiving')
            ->where('tasks.1.category', 'goods_receiving')
            ->where('tasks.2.category', 'temperature')
            ->where('tasks.3.category', 'cleaning')
        );
    }

    public function test_dashboard_shows_no_tasks_when_empty()
    {
        // Create a test user
        $user = User::factory()->create();

        // Act as the user and visit the dashboard
        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        // Assert the response is successful and has empty tasks array
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('tasks', 0)
        );
    }
} 