<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class TaskAuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_instance_id',
        'user_id',
        'old_values',
        'new_values',
        'action',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['user'];

    /**
     * Get the task instance that owns the audit log.
     */
    public function taskInstance(): BelongsTo
    {
        return $this->belongsTo(TaskInstance::class)->select(['id', 'task_id', 'scheduled_for', 'status', 'completed_at']);
    }

    /**
     * Get the user that created the audit log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->select(['id', 'name', 'email']);
    }

    /**
     * Scope a query to only include audit logs for a specific task instance.
     */
    public function scopeForTaskInstance(Builder $query, int $taskInstanceId): Builder
    {
        return $query->where('task_instance_id', $taskInstanceId);
    }

    /**
     * Scope a query to only include audit logs for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include audit logs for a specific action.
     */
    public function scopeForAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to only include audit logs within a date range.
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
} 