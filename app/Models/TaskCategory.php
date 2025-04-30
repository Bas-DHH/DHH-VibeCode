<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class TaskCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_nl',
        'name_en',
        'description',
        'description_nl',
        'description_en',
        'icon',
        'color',
        'is_active',
        'business_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['business'];

    /**
     * Get the business that owns the category.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class)->select(['id', 'name']);
    }

    /**
     * Get the tasks for the category.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->select(['id', 'title', 'name_nl', 'name_en', 'description', 'frequency', 'is_active']);
    }

    /**
     * Get the localized name attribute.
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"name_{$locale}"} ?? $this->name_en;
    }

    /**
     * Get the localized description attribute.
     */
    public function getDescriptionAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"description_{$locale}"} ?? $this->description_en;
    }

    /**
     * Get the validation rules for the category.
     */
    public static function validationRules(): array
    {
        return [
            'name_nl' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_nl' => 'nullable|string',
            'description_en' => 'nullable|string',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
            'business_id' => 'required|exists:businesses,id',
        ];
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include categories for a specific business.
     */
    public function scopeForBusiness(Builder $query, int $businessId): Builder
    {
        return $query->where('business_id', $businessId);
    }
}
