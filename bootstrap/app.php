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
        // Registering our Tenant Context and existing Tenant Middleware aliases
        $middleware->alias([
            'tenant' => \App\Http\Middleware\EnsureTenantContext::class,
            'perm' => \App\Http\Middleware\PermissionMiddleware::class,
        ]);
        
        // Remove the append if you want to control it via routes (recommended)
        // $middleware->append(\App\Http\Middleware\TenantMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
