<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLogging
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (app()->environment('local', 'staging')) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    private function logRequest(Request $request, Response $response): void
    {
        $duration = defined('LARAVEL_START') ? round((microtime(true) - LARAVEL_START) * 1000, 2) : 0;

        Log::info('Request processed', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user' => $request->user()?->id,
            'ip' => $request->ip(),
            'status' => $response->getStatusCode(),
            'duration' => $duration . 'ms',
            'memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 1) . 'MB',
        ]);
    }
} 