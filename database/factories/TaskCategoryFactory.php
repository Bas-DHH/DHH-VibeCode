<?php

namespace Database\Factories;

use App\Models\TaskCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskCategoryFactory extends Factory
{
    protected $model = TaskCategory::class;

    public function definition(): array
    {
        return [
            'name_nl' => $this->faker->word,
            'name_en' => $this->faker->word,
            'description_nl' => $this->faker->sentence,
            'description_en' => $this->faker->sentence,
            'icon' => $this->faker->randomElement(['mdi-broom', 'mdi-thermometer', 'mdi-food', 'mdi-truck']),
            'color' => $this->faker->hexColor,
            'is_active' => $this->faker->boolean(90),
        ];
    }

    public function cleaning(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'name_nl' => 'Schoonmaak',
                'name_en' => 'Cleaning',
                'icon' => 'mdi-broom',
                'color' => '#4CAF50',
            ];
        });
    }

    public function temperature(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'name_nl' => 'Temperatuur',
                'name_en' => 'Temperature',
                'icon' => 'mdi-thermometer',
                'color' => '#2196F3',
            ];
        });
    }

    public function criticalCooking(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'name_nl' => 'Kritisch Koken',
                'name_en' => 'Critical Cooking',
                'icon' => 'mdi-food',
                'color' => '#F44336',
            ];
        });
    }

    public function goodsReceiving(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'name_nl' => 'Goederen Ontvangst',
                'name_en' => 'Goods Receiving',
                'icon' => 'mdi-truck',
                'color' => '#FF9800',
            ];
        });
    }
} 