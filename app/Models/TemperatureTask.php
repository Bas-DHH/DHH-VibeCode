<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemperatureTask extends Task
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
        'min_temperature',
        'max_temperature',
        'temperature_unit',
        'location',
        'equipment_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'day_of_week' => 'integer',
        'day_of_month' => 'integer',
        'scheduled_time' => 'datetime',
        'min_temperature' => 'float',
        'max_temperature' => 'float',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function validateTemperature(float $temperature): bool
    {
        return $temperature >= $this->min_temperature && $temperature <= $this->max_temperature;
    }

    public function getTemperatureRange(): string
    {
        return "{$this->min_temperature} - {$this->max_temperature} {$this->temperature_unit}";
    }
} 