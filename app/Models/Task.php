<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_id',
        'user_id',
        'task_category_id',
        'title',
        'description',
        'due_date',
        'completed_at',
        'status',
        'input_data',
        'frequency',
        'scheduled_time',
        'day_of_week',
        'day_of_month',
        'assigned_user_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'input_data' => 'array',
        'scheduled_time' => 'datetime'
    ];

    /**
     * Get the business that owns the task.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assigned user that owns the task.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * Get the category that owns the task.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    /**
     * Get the audit logs for the task.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(TaskAuditLog::class);
    }

    /**
     * Log an audit entry for the task.
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
     * Check if the task is overdue.
     */
    public function isOverdue(): bool
    {
        return !$this->completed_at && $this->due_date->isPast();
    }

    /**
     * Complete the task.
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
}
