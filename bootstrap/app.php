<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Support\Facades\Log;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [__DIR__ . '/../routes/web.php', __DIR__ . '/../routes/admin.php'],
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->redirectGuestsTo('/admin/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Log all exceptions
        $exceptions->reportable(function (Throwable $e) {
            Log::error($e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        });

        // 401 - Unauthenticated
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'error'   => 'Authentication required'
                ], 401);
            }
        });

        // 403 - Unauthorized
        $exceptions->renderable(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'error'   => 'Insufficient permissions'
                ], 403);
            }
        });

        // 404 - Not Found
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'error'   => 'Resource not found'
                ], 404);
            }
        });

        // 405 - Method Not Allowed
        $exceptions->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'error'   => 'Method not allowed'
                ], 405);
            }
        });

        // 422 - Validation error
        $exceptions->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // 400 - Database Query Exception
        $exceptions->renderable(function (QueryException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => app()->environment('production')
                        ? 'Database error occurred.'
                        : $e->getMessage(),
                    'error'   => 'Bad Request'
                ], 400);
            }
        });

        // 404 - Route Not Found (Sanctum often triggers this)
        $exceptions->renderable(function (RouteNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'error'   => 'Route not found'
                ], 404);
            }
        });


        $exceptions->renderable(function (DecryptException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => "Decryption Error . " . $e->getMessage()
                ], 400);
            }

            return redirect()
                ->back()
                ->with('error', 'Invalid or expired link.');
        });
        $exceptions->renderable(function (ThrottleRequestsException $e, Request $request) {
            if ($request->expectsJson()) {

                // Get retry time from the exception headers (correct value)
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 0;

                // Format time
                $time = $retryAfter < 60
                    ? "{$retryAfter} seconds"
                    : ceil($retryAfter / 60) . " minutes";

                return response()->json([
                    'status'  => false,
                    'message' => "Too many attempts. Please try again after {$time}.",
                    'retry_after_seconds' => $retryAfter
                ], 429);
            }
        });
    })->create();
