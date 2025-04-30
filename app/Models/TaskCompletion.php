<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class TaskCompletion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_instance_id',
        'completed_by_id',
        'notes',
        'input_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'input_data' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['completedBy'];

    /**
     * Get the task instance that owns the completion.
     */
    public function taskInstance(): BelongsTo
    {
        return $this->belongsTo(TaskInstance::class)->select(['id', 'task_id', 'scheduled_for', 'status', 'completed_at']);
    }

    /**
     * Get the user that completed the task.
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by_id')->select(['id', 'name', 'email']);
    }

    /**
     * Scope a query to only include completions for a specific task instance.
     */
    public function scopeForTaskInstance(Builder $query, int $taskInstanceId): Builder
    {
        return $query->where('task_instance_id', $taskInstanceId);
    }

    /**
     * Scope a query to only include completions by a specific user.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('completed_by_id', $userId);
    }

    /**
     * Scope a query to only include completions within a date range.
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
} 