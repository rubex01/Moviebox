<?php

namespace App;

class Config
{
    public static $middleware = [];

    public static $routeMiddleware = [
        'auth' => \App\Middleware\Authentication::class,
    ];

    public static $exceptionHandler = [

    ];

    public static $enabledFunctions = [
        'authorization' => true,
    ];
}