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
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // TEMP diagnostic for Vercel, remove after fix.
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->header('X-Debug') === 'ppbl') {
                return response()->json(['class' => get_class($e), 'message' => $e->getMessage(), 'db' => [config('database.default'), config('database.connections.pgsql.host'), extension_loaded('pdo_pgsql')]], 500);
            }
        });
    })->create();
