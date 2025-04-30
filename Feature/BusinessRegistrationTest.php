<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_businesses_can_register(): void
    {
        $response = $this->post('/register', [
            'business_name' => 'Test Business',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_business_name_must_be_unique(): void
    {
        // Create an existing business
        Business::factory()->create(['business_name' => 'Existing Business']);

        $response = $this->post('/register', [
            'business_name' => 'Existing Business',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('business_name');
    }

    public function test_email_must_be_unique(): void
    {
        // Create an existing user
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'business_name' => 'New Business',
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password_must_be_confirmed(): void
    {
        $response = $this->post('/register', [
            'business_name' => 'Test Business',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
    }
} 