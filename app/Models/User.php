<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'language',
        'last_login_at',
        'failed_login_attempts',
        'locked_until',
        'business_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['business'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
        ];
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is staff
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function incrementFailedLoginAttempts(): void
    {
        $this->increment('failed_login_attempts');
        
        if ($this->failed_login_attempts >= 5) {
            $this->locked_until = now()->addMinutes(30);
            $this->save();
        }
    }

    public function resetFailedLoginAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    public function updateLastLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class)->select(['id', 'name']);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_user_id')->select(['id', 'title', 'name_nl', 'name_en', 'description', 'frequency']);
    }

    public function taskInstances(): HasMany
    {
        return $this->hasMany(TaskInstance::class, 'assigned_user_id')->select(['id', 'task_id', 'scheduled_for', 'status', 'completed_at']);
    }

    /**
     * Scope a query to only include super admins.
     */
    public function scopeSuperAdmins(Builder $query): Builder
    {
        return $query->where('role', 'super_admin');
    }

    /**
     * Scope a query to only include admins.
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope a query to only include staff.
     */
    public function scopeStaff(Builder $query): Builder
    {
        return $query->where('role', 'staff');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopeLocked(Builder $query): Builder
    {
        return $query->whereNotNull('locked_until')
            ->where('locked_until', '>', now());
    }

    public function scopeForBusiness(Builder $query, int $businessId): Builder
    {
        return $query->where('business_id', $businessId);
    }

    public function canManageUser(User $user): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isAdmin()) {
            return $this->business_id === $user->business_id;
        }

        return false;
    }

    public function canManageBusiness(Business $business): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->isAdmin() && $this->business_id === $business->id;
    }
}
