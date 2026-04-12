<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $onVercel = (string) ($_SERVER['VERCEL'] ?? $_ENV['VERCEL'] ?? getenv('VERCEL') ?: '') === '1'
            || (string) ($_SERVER['VERCEL_ENV'] ?? $_ENV['VERCEL_ENV'] ?? getenv('VERCEL_ENV') ?: '') !== '';

        if ($onVercel) {
            $exceptions->report(function (\Throwable $e) {
                error_log(sprintf(
                    'VERCEL_EXCEPTION_SUMMARY %s: %s @ %s:%d',
                    $e::class,
                    str_replace(["\r", "\n"], ' ', $e->getMessage()),
                    $e->getFile(),
                    $e->getLine()
                ));
            });
        }
    })->create();
