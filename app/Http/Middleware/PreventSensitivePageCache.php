<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventSensitivePageCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldPreventCache($request)) {
            $this->applyNoCacheHeaders($response);
        }

        return $response;
    }

    public static function applyNoCacheHeaders(Response $response): void
    {
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        $response->headers->set('Vary', 'Cookie');
    }

    private function shouldPreventCache(Request $request): bool
    {
        if ($request->user()) {
            return true;
        }

        return $request->routeIs(
            'login',
            'logout',
            'password.request',
            'password.email',
            'password.reset',
            'password.store',
            'password.confirm',
            'scan.*',
        );
    }
}
