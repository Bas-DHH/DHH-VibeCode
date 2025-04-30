<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleaningVerificationTask extends Task
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
        'area',
        'cleaning_method',
        'chemicals_used',
        'verification_method',
        'acceptance_criteria',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'day_of_week' => 'integer',
        'day_of_month' => 'integer',
        'scheduled_time' => 'datetime',
        'chemicals_used' => 'array',
        'verification_method' => 'array',
        'acceptance_criteria' => 'array',
    ];

    public function validateCleaningMethod(string $method): bool
    {
        return $method === $this->cleaning_method;
    }

    public function validateChemicals(array $chemicals): bool
    {
        return empty(array_diff($this->chemicals_used, $chemicals));
    }

    public function validateVerification(array $methods): bool
    {
        return empty(array_diff($this->verification_method, $methods));
    }

    public function validateAcceptance(array $criteria): bool
    {
        return empty(array_diff($this->acceptance_criteria, $criteria));
    }
} 