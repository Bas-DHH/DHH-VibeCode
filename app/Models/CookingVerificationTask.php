<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CookingVerificationTask extends Task
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'name_nl',
        'name_en',
        'description',
        'instructions_nl',
        'instructions_en',
        'frequency',
        'scheduled_time',
        'day_of_week',
        'day_of_month',
        'is_active',
        'business_id',
        'task_category_id',
        'assigned_user_id',
        'created_by_id',
        'cooking_method',
        'cooking_time',
        'internal_temperature',
        'visual_criteria',
        'taste_criteria',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'day_of_week' => 'integer',
        'day_of_month' => 'integer',
        'scheduled_time' => 'datetime',
        'cooking_time' => 'integer',
        'internal_temperature' => 'float',
        'visual_criteria' => 'array',
        'taste_criteria' => 'array',
    ];

    public function validateCookingTime(int $time): bool
    {
        return $time >= $this->cooking_time;
    }

    public function validateInternalTemperature(float $temperature): bool
    {
        return $temperature >= $this->internal_temperature;
    }

    public function validateVisualCriteria(array $criteria): bool
    {
        return empty(array_diff($this->visual_criteria, $criteria));
    }

    public function validateTasteCriteria(array $criteria): bool
    {
        return empty(array_diff($this->taste_criteria, $criteria));
    }
} 