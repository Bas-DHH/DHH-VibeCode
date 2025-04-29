<?php

namespace Tests\Feature;

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
} 