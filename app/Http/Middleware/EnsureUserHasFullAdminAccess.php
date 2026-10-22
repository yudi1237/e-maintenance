<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasFullAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->seesFullDashboard()) {
            abort(403, 'Menu ini hanya untuk Admin Maintenance.');
        }

        return $next($request);
    }
}
