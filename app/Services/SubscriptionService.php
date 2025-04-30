<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Mollie\Laravel\Facades\Mollie;

class SubscriptionService
{
    public function createTrial(Business $business): void
    {
        $business->update([
            'trial_ends_at' => now()->addDays(14),
        ]);
    }

    public function createSubscription(Business $business, string $planId): Subscription
    {
        $subscription = $business->newSubscription('default', $planId)
            ->trialUntil($business->trial_ends_at)
            ->create();

        return $subscription;
    }

    public function cancelSubscription(Subscription $subscription): void
    {
        $subscription->cancel();
    }

    public function resumeSubscription(Subscription $subscription): void
    {
        $subscription->resume();
    }

    public function updateSubscription(Subscription $subscription, string $planId): void
    {
        $subscription->swap($planId);
    }

    public function checkSubscriptionStatus(Business $business): void
    {
        if (!$business->hasActiveSubscription()) {
            // Handle expired subscription
            $this->handleExpiredSubscription($business);
        }
    }

    private function handleExpiredSubscription(Business $business): void
    {
        // Disable business features or show warning
        Log::warning("Business {$business->id} subscription expired");
        
        // You might want to send notifications or take other actions
    }

    public function getAvailablePlans(): array
    {
        return [
            'monthly' => [
                'id' => 'monthly',
                'name' => 'Monthly Plan',
                'price' => 29.99,
                'interval' => 'month',
            ],
            'yearly' => [
                'id' => 'yearly',
                'name' => 'Yearly Plan',
                'price' => 299.99,
                'interval' => 'year',
            ],
        ];
    }
} 