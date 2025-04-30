<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskInstance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskInstanceFactory extends Factory
{
    protected $model = TaskInstance::class;

    public function definition()
    {
        return [
            'task_id' => Task::factory(),
            'scheduled_for' => Carbon::now(),
            'status' => 'pending',
            'assigned_user_id' => User::factory(),
        ];
    }

    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'completed_at' => Carbon::now(),
            ];
        });
    }

    public function overdue()
    {
        return $this->state(function (array $attributes) {
            return [
                'scheduled_for' => Carbon::now()->subDay(),
                'status' => 'pending',
            ];
        });
    }
} 