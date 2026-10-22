<?php

namespace App\Providers;

use App\Models\Technician;
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

            if ($user && ! $user->canAccessAdmin()) {
                $view->with(
                    'picTechnicians',
                    Technician::query()->active()->orderBy('name')->get()
                );

                return;
            }

            $view->with('picTechnicians', collect());
        });
    }
}
