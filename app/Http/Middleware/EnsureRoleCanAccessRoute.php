<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleCanAccessRoute
{
    private const TECHNICIAN_ROUTES = [
        'home',
        'home.op-tickets',
        'home.machines',
        'home.tickets',
        'area-op.create',
        'area-op.store',
        'area-op.claim',
        'area-op.close',
        'home.submissions.op',
        'home.submissions.op.edit',
        'home.submissions.op.update',
        'home.submissions.preventive',
        'home.submissions.preventive.edit',
        'home.submissions.preventive.update',
        'home.submissions.conveyor',
        'home.submissions.conveyor.edit',
        'home.submissions.conveyor.update',
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

        if ($request->routeIs('login', 'logout', 'password.*', 'verification.*', 'mfa.*')) {
            return $next($request);
        }

        if ($user->isOperator()) {
            if ($request->routeIs('operator.home', 'operator.running', 'area-op.create', 'area-op.store', 'logout', 'profile.edit', 'profile.update')) {
                return $next($request);
            }

            return redirect()->route('operator.home');
        }

        if ($user->isPpic()) {
            $viewRoutes = [
                'admin.dashboard',
                'admin.dashboard.stats',
                'admin.machines.show',
                'admin.operator-reports.show',
                'admin.report.op-mesin',
                'admin.report.op-area',
                'admin.report.findings',
                'admin.preventive.show',
                'admin.preventive.export',
                'logout',
                'profile.edit',
            ];

            if ($request->isMethod('GET') && $request->routeIs(...$viewRoutes)) {
                return $next($request);
            }

            return redirect()->route('admin.dashboard');
        }

        if ($user->canAccessAdmin()) {
            if ($request->routeIs('reports.*', 'preventive.*', 'preventive-conveyor.*', 'profile.*', 'dashboard', 'home', 'home.op-tickets', 'home.machines', 'home.tickets', 'home.submissions.*')) {
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
