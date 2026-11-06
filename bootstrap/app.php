<?php

use App\Http\Middleware\EnsureRoleCanAccessRoute;
use App\Http\Middleware\EnsureUserCanManageUsers;
use App\Http\Middleware\EnsureUserHasFullAdminAccess;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\HardenPublicScan;
use App\Http\Middleware\PreventSensitivePageCache;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('login'));

        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();

            return $user ? route($user->homeRouteName()) : route('login');
        });

        $middleware->web(append: [
            EnsureRoleCanAccessRoute::class,
            PreventSensitivePageCache::class,
        ]);

        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'admin.full' => EnsureUserHasFullAdminAccess::class,
            'admin.users' => EnsureUserCanManageUsers::class,
            'scan.public' => HardenPublicScan::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
