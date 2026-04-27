<?php

namespace App\Http\Middleware;

use App\Services\UserActivity\UserActivityService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserActivityRequestLogger
{
    public function __construct(
        protected UserActivityService $service
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        $response = $next($request);
        $serverDurationMs = (int) max(0, (int) round((microtime(true) - $start) * 1000));

        if (! $request->user()) {
            return $response;
        }

        try {
            if ($this->service->shouldLogRequest($request)) {
                $this->service->logRequest($request, $serverDurationMs);
            }
        } catch (\Throwable $e) {
            Log::error('user_activity: logRequest failed: '.$e->getMessage(), [
                'path' => $request->path(),
                'route' => $request->route()?->getName(),
            ]);
        }

        return $response;
    }
}
