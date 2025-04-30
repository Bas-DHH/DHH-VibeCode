<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class Business extends Model
{
    use HasFactory, Billable;

    protected $fillable = [
        'name',
        'email',
        'trial_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function isSubscribed()
    {
        return $this->subscription && $this->subscription->isActive();
    }

    public function hasActiveSubscription()
    {
        return $this->isSubscribed() || $this->isOnTrial();
    }
} 