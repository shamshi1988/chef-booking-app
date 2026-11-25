<?php

namespace App\Providers;

use App\Models\ServiceCategory;
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
        // Services list for the website header/footer.
        View::composer('components.frontend-layout', function ($view) {
            $view->with('navServices', ServiceCategory::active()->get(['id', 'name', 'slug', 'icon']));
        });
    }
}
