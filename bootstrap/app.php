<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'tenant' => \App\Http\Middleware\TenantMiddleware::class,
            'landlord' => \App\Http\Middleware\LandlordMiddleware::class,
            'investor' => \App\Http\Middleware\InvestorMiddleware::class,
            'agent' => \App\Http\Middleware\AgentMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
