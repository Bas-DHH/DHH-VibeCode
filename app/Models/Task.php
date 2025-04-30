<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    use HasFactory, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'day_of_week' => 'integer',
        'day_of_month' => 'integer',
        'scheduled_time' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['category'];

    /**
     * Get the business that owns the task.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class)->select(['id', 'name']);
    }

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->select(['id', 'name', 'email']);
    }

    /**
     * Get the assigned user that owns the task.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id')->select(['id', 'name', 'email']);
    }

    /**
     * Get the category that owns the task.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id')->select(['id', 'name_nl', 'name_en', 'icon', 'color']);
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id')->select(['id', 'name', 'email']);
    }

    public function instances(): HasMany
    {
        return $this->hasMany(TaskInstance::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDaily(Builder $query): Builder
    {
        return $query->where('frequency', 'daily');
    }

    public function scopeWeekly(Builder $query): Builder
    {
        return $query->where('frequency', 'weekly');
    }

    public function scopeMonthly(Builder $query): Builder
    {
        return $query->where('frequency', 'monthly');
    }

    public function scopeForBusiness(Builder $query, int $businessId): Builder
    {
        return $query->where('business_id', $businessId);
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'nl' ? $this->name_nl : $this->name_en;
    }

    public function getInstructionsAttribute(): string
    {
        return app()->getLocale() === 'nl' ? $this->instructions_nl : $this->instructions_en;
    }
}
