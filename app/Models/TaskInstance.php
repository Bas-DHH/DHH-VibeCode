<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskInstance extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'task_id',
        'scheduled_for',
        'status',
        'completed_at',
        'completed_by_id',
        'input_data',
        'notes',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'completed_at' => 'datetime',
        'input_data' => 'array',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by_id');
    }

    public function completion(): HasOne
    {
        return $this->hasOne(TaskCompletion::class);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->scheduled_for->isPast();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(TaskAuditLog::class);
    }
} 