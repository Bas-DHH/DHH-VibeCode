<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceivingTask extends Task
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
        'supplier_id',
        'delivery_time',
        'required_documents',
        'inspection_criteria',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'day_of_week' => 'integer',
        'day_of_month' => 'integer',
        'scheduled_time' => 'datetime',
        'delivery_time' => 'datetime',
        'required_documents' => 'array',
        'inspection_criteria' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function validateDocuments(array $documents): bool
    {
        return empty(array_diff($this->required_documents, $documents));
    }

    public function validateInspection(array $criteria): bool
    {
        return empty(array_diff($this->inspection_criteria, $criteria));
    }
} 