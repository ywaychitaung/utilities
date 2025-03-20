<?php

namespace App\Http;

use App\Http\Middleware\ForceHttps;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        // other global middlewares
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // other web middlewares
            ForceHttps::class,
        ],

        'api' => [
            // api middlewares
        ],
    ];
}
