<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence;
        $description = $this->faker->paragraph;

        return [
            'title' => $title,
            'name_nl' => $title,
            'name_en' => $title,
            'description' => $description,
            'instructions_nl' => $description,
            'instructions_en' => $description,
            'task_category_id' => TaskCategory::factory(),
            'assigned_user_id' => User::factory(),
            'business_id' => Business::factory(),
            'created_by_id' => User::factory(),
            'frequency' => $this->faker->randomElement(['daily', 'weekly', 'monthly']),
            'scheduled_time' => $this->faker->time('H:i:s'),
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'day_of_month' => $this->faker->numberBetween(1, 31),
            'is_active' => true,
        ];
    }

    public function daily()
    {
        return $this->state(function (array $attributes) {
            return [
                'frequency' => 'daily',
                'day_of_week' => null,
                'day_of_month' => null,
            ];
        });
    }

    public function weekly()
    {
        return $this->state(function (array $attributes) {
            return [
                'frequency' => 'weekly',
                'day_of_week' => $this->faker->numberBetween(0, 6),
                'day_of_month' => null,
            ];
        });
    }

    public function monthly()
    {
        return $this->state(function (array $attributes) {
            return [
                'frequency' => 'monthly',
                'day_of_week' => null,
                'day_of_month' => $this->faker->numberBetween(1, 31),
            ];
        });
    }

    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
} 