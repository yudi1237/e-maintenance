<?php

namespace App\Providers;

use App\Models\Technician;
use App\Support\ReportMenu;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer('layouts.app', function ($view) {
            $user = auth()->user();

            if ($user && ! $user->canAccessAdmin() && ! $user->isOperator()) {
                $view->with(
                    'picTechnicians',
                    Technician::query()->active()->onCurrentShift()->orderBy('name')->get()
                );

                return;
            }

            $view->with('picTechnicians', collect());
        });

        View::composer('layouts.navigation', function ($view) {
            $user = auth()->user();
            if (! $user?->canAccessAdmin()) {
                return;
            }

            $view->with('reportMenuCounts', ReportMenu::counts());
        });
    }
}
