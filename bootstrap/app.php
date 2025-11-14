<?php

use App\Http\Middleware\DriverOnly;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
          $middleware->alias([
            'driver' => DriverOnly::class,
            // ... other middleware aliases
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'error' => 'Authentication token is missing or invalid.'
            ], 401);
        });
    })->create();