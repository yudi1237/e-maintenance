<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleCanAccessRoute
{
    private const TECHNICIAN_ROUTES = [
        'home',
        'home.tickets',
        'dashboard',
        'reports.create',
        'reports.store',
        'reports.tickets.claim',
        'reports.tickets.identify',
        'reports.tickets.close',
        'reports.step2',
        'reports.step2.store',
        'preventive.menu',
        'preventive.schedules',
        'preventive.create',
        'preventive.categories',
        'preventive.form',
        'preventive.store',
        'preventive.pic',
        'preventive-conveyor.create',
        'preventive-conveyor.store',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($request->routeIs('scan.*')) {
            return $next($request);
        }

        if ($request->routeIs('login', 'logout', 'password.*', 'verification.*')) {
            return $next($request);
        }

        if ($user->canAccessAdmin()) {
            if ($request->routeIs('reports.*', 'preventive.*', 'preventive-conveyor.*', 'profile.*', 'dashboard', 'home', 'home.tickets')) {
                return redirect()->route('admin.dashboard');
            }

            return $next($request);
        }

        if (! $request->routeIs(self::TECHNICIAN_ROUTES)) {
            return redirect()
                ->route('home')
                ->with('status', 'Akses tidak diizinkan untuk akun teknisi.');
        }

        return $next($request);
    }
}
