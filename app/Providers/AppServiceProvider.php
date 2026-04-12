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
        if (getenv('VERCEL') === '1') {
            foreach (['DB_HOST', 'DB_URL', 'DATABASE_URL', 'POSTGRES_URL', 'POSTGRES_PRISMA_URL', 'POSTGRES_URL_NON_POOLING'] as $key) {
                $value = $_SERVER[$key] ?? $_ENV[$key] ?? getenv($key);
                if (! is_string($value) || $value === '') {
                    continue;
                }
                $clean = preg_replace('/[\r\n]+/', '', $value);
                if ($clean !== $value) {
                    $_ENV[$key] = $clean;
                    $_SERVER[$key] = $clean;
                }
            }
        }
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
