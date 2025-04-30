<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'role' => 'staff',
            'is_active' => true,
            'language' => 'nl',
            'last_login_at' => null,
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'business_id' => Business::factory(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is a super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'super_admin',
                'business_id' => null, // Super admins don't belong to a business
            ];
        });
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
            ];
        });
    }

    /**
     * Indicate that the user is staff.
     */
    public function staff(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'staff',
            ];
        });
    }

    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    public function locked(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locked_until' => now()->addMinutes(30),
                'failed_login_attempts' => 5,
            ];
        });
    }

    public function withBusiness(Business $business): static
    {
        return $this->state(function (array $attributes) use ($business) {
            return [
                'business_id' => $business->id,
            ];
        });
    }
}
