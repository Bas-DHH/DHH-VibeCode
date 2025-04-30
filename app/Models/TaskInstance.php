<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class TaskInstance extends Model
{
    use HasFactory, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'scheduled_for',
        'completed_at',
        'status',
        'input_data',
        'assigned_user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_for' => 'datetime',
        'completed_at' => 'datetime',
        'input_data' => 'array',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['task'];

    /**
     * Get the task that owns the instance.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class)->select(['id', 'title', 'name_nl', 'name_en', 'description', 'frequency']);
    }

    /**
     * Get the assigned user that owns the instance.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id')->select(['id', 'name', 'email']);
    }

    /**
     * Get the audit logs for the instance.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(TaskInstanceAuditLog::class);
    }

    /**
     * Log an audit entry for the instance.
     */
    public function logAudit(string $action, ?array $oldValues = null, ?array $newValues = null, ?string $notes = null): void
    {
        $this->auditLogs()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'notes' => $notes
        ]);
    }

    /**
     * Check if the instance is overdue.
     */
    public function isOverdue(): bool
    {
        return !$this->completed_at && $this->scheduled_for->isPast();
    }

    /**
     * Complete the instance.
     */
    public function complete(array $inputData = []): void
    {
        $oldValues = [
            'status' => $this->status,
            'completed_at' => $this->completed_at,
            'input_data' => $this->input_data
        ];

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'input_data' => $inputData
        ]);

        $this->logAudit('completed', $oldValues, [
            'status' => 'completed',
            'completed_at' => now(),
            'input_data' => $inputData
        ]);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'pending')
            ->where('scheduled_for', '<', now());
    }

    public function scopeForTask(Builder $query, int $taskId): Builder
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_user_id', $userId);
    }

    public function scopeScheduledBetween(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('scheduled_for', [$startDate, $endDate]);
    }
} 