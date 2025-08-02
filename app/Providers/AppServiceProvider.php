<?php

namespace App\Providers;

use Blade;
use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrapFive();

        Blade::directive("active", function ($route) {
            return "<?php echo request()->routeIs($route) ? 'active' : ''; ?>";
        });
    }
}
