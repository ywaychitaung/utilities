<?php

namespace App\Providers;

use Illuminate\Http\Middleware\TrustProxies;
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
        if (getenv('VERCEL') === '1') {
            TrustProxies::at('*');
            config(['database.default' => 'pgsql']);
        }

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
