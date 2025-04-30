<?php

namespace App\Models;

use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    protected $fillable = [
        'business_id',
        'name',
        'mollie_id',
        'mollie_status',
        'mollie_plan',
        'quantity',
        'trial_ends_at',
        'ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function isActive()
    {
        return $this->mollie_status === 'active' && 
               (!$this->ends_at || $this->ends_at->isFuture());
    }

    public function isCancelled()
    {
        return $this->ends_at && $this->ends_at->isPast();
    }
} 