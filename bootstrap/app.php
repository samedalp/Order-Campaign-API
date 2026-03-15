<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        $exceptions->render(function (\App\Exceptions\ProductNotFoundException $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        });

        $exceptions->render(function (\App\Exceptions\OrderNotFoundException $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        });

        $exceptions->render(function (\App\Exceptions\StockException $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        });

        $exceptions->render(function (\Exception $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        });
    })
    ->create();
