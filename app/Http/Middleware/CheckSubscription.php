<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user || !$user->business) {
            return redirect()->route('login');
        }

        // Allow access to subscription-related routes
        if ($request->is('subscriptions*')) {
            return $next($request);
        }

        // Check if business has active subscription or trial
        if (!$user->business->hasActiveSubscription()) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'Your subscription has expired. Please renew to continue using the service.');
        }

        return $next($request);
    }
} 