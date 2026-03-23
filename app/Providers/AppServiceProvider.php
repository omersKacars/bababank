<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Render sits behind a proxy; force HTTPS URLs in production
        // so Vite assets and generated links are served over https.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
