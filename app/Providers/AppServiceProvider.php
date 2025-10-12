<?php

namespace App\Providers;

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
        // Autoload pliku helpera
        if (file_exists($file = app_path('Helpers/AddressHelper.php'))) {
            require $file;
        }
        if (file_exists($file = app_path('Helpers/PriceFormatHelper.php'))) {
            require $file;
        }
    }
}
