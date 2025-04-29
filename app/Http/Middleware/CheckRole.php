<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || !$this->hasRole($request->user(), $role)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }

    /**
     * Check if the user has the required role.
     */
    private function hasRole($user, string $role): bool
    {
        return match ($role) {
            'super_admin' => $user->isSuperAdmin(),
            'admin' => $user->isAdmin() || $user->isSuperAdmin(),
            'staff' => true, // All authenticated users can access staff routes
            default => false,
        };
    }
} 