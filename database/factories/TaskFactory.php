<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'done', 'overdue'];
        $categories = ['temperature', 'goods_receiving', 'cooking', 'verification', 'cleaning'];
        $frequencies = ['daily', 'weekly', 'monthly'];

        $dueDate = Carbon::now()->addDays(rand(1, 7));
        $completedAt = $this->faker->boolean(50) 
            ? Carbon::now()->subDays(rand(1, 3)) 
            : null;

        return [
            'title' => $this->faker->sentence(3),
            'status' => $this->faker->randomElement($statuses),
            'category' => $this->faker->randomElement($categories),
            'frequency' => $this->faker->randomElement($frequencies),
            'due_date' => $dueDate,
            'completed_at' => $completedAt,
        ];
    }
} 