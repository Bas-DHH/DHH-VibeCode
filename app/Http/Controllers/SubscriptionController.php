<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscription = auth()->user()->subscription();
        $plans = [
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

        return Inertia::render('Subscriptions/Index', [
            'business' => [
                'name' => auth()->user()->business->name,
                'trial_ends_at' => auth()->user()->trialEndsAt(),
            ],
            'subscription' => $subscription ? [
                'mollie_status' => $subscription->status(),
                'mollie_plan' => $subscription->stripe_price,
                'ends_at' => $subscription->ends_at,
            ] : null,
            'plans' => $plans,
            'isOnTrial' => auth()->user()->onTrial(),
            'trialEndsAt' => auth()->user()->trialEndsAt(),
        ]);
    }

    public function subscribe(Request $request, string $plan)
    {
        $user = auth()->user();
        $plans = [
            'monthly' => 'price_monthly',
            'yearly' => 'price_yearly',
        ];

        if (!isset($plans[$plan])) {
            return back()->with('error', 'Invalid plan selected.');
        }

        $user->newSubscription('default', $plans[$plan])->create();

        return back()->with('success', 'Subscription created successfully.');
    }

    public function cancel()
    {
        auth()->user()->subscription()->cancel();

        return back()->with('success', 'Subscription cancelled successfully.');
    }

    public function resume()
    {
        auth()->user()->subscription()->resume();

        return back()->with('success', 'Subscription resumed successfully.');
    }

    public function update(Request $request, string $plan)
    {
        $user = auth()->user();
        $plans = [
            'monthly' => 'price_monthly',
            'yearly' => 'price_yearly',
        ];

        if (!isset($plans[$plan])) {
            return back()->with('error', 'Invalid plan selected.');
        }

        $user->subscription()->swap($plans[$plan]);

        return back()->with('success', 'Subscription updated successfully.');
    }
} 