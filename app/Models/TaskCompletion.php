<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_instance_id',
        'completed_by_id',
        'notes',
        'input_data',
    ];

    protected $casts = [
        'input_data' => 'array',
    ];

    public function taskInstance(): BelongsTo
    {
        return $this->belongsTo(TaskInstance::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by_id');
    }
} 