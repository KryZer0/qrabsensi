<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {    
        // Disable CSRF protection for specific API routes
        $middleware->validateCsrfTokens(except: [
            '/check/*',
            '/checkout/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception, $request) {
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint tidak ditemukan',
                    'status' => 404
                ], 404);
            }
    
            $status = $exception instanceof HttpException
                ? $exception->getStatusCode()
                : 500;
    
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage() ?: 'Terjadi kesalahan pada server',
                'status' => $status
            ], $status);
        });
    })->create();
