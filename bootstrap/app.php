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
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }
            return '/admin/login';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->reportable(function (Throwable $e) {
            Log::error($e->getMessage(), [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'error'   => 'Authentication required'
                ], 401);
            }
            return redirect()->to('/admin/login');
        });

        $exceptions->renderable(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage() ?: 'This action is unauthorized',
                    'error'   => 'Insufficient permissions'
                ], 403);
            }
            abort(403, $e->getMessage() ?: 'This action is unauthorized');
        });

        $exceptions->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'error'   => 'Model not found'
                ], 404);
            }
            abort(404, 'Resource not found');
        });

        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'error'   => 'Resource not found'
                ], 404);
            }
            abort(404, 'Page not found');
        });

        $exceptions->renderable(function (RouteNotFoundException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'error'   => 'Route not found'
                ], 404);
            }
            abort(404, 'Route not found');
        });

        $exceptions->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'error'   => 'Method not allowed'
                ], 405);
            }
            abort(405, 'Method not allowed');
        });

        $exceptions->renderable(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'error'   => 'Token expired'
                ], 419);
            }
            return redirect()
                ->back()
                ->withInput($request->except('_token'))
                ->with('error', 'Your session has expired. Please try again.');
        });

        $exceptions->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        $exceptions->renderable(function (ThrottleRequestsException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 0;

                $time = $retryAfter < 60
                    ? "{$retryAfter} seconds"
                    : ceil($retryAfter / 60) . " minute" . (ceil($retryAfter / 60) > 1 ? 's' : '');

                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'retry_after_seconds' => (int)$retryAfter
                ], 429)->header('Retry-After', $retryAfter);
            }

            return redirect()->back()->with('error', 'Too many requests. Please slow down.');
        });

        $exceptions->renderable(function (DecryptException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                    'error' => 'Decryption failed'
                ], 400);
            }

            return redirect()->back()->with('error', 'Invalid or expired link.');
        });

        $exceptions->renderable(function (QueryException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'message' => app()->environment('production')
                        ? 'A database error occurred. Please try again later.'
                        : $e->getMessage(),
                    'error'   => 'Database error'
                ], 500);
            }

            if (app()->environment('production')) {
                abort(500, 'Database error occurred');
            }
        });

        $exceptions->renderable(function (HttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'error'   => 'HTTP Exception'
                ], $e->getStatusCode());
            }
        });

        $exceptions->renderable(function (Throwable $e, Request $request) {
            if (
                $e instanceof AuthenticationException ||
                $e instanceof AuthorizationException ||
                $e instanceof ModelNotFoundException ||
                $e instanceof NotFoundHttpException ||
                $e instanceof RouteNotFoundException ||
                $e instanceof MethodNotAllowedHttpException ||
                $e instanceof TokenMismatchException ||
                $e instanceof ValidationException ||
                $e instanceof ThrottleRequestsException ||
                $e instanceof DecryptException ||
                $e instanceof QueryException ||
                $e instanceof HttpException
            ) {
                return null;
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => false,
                    'message' => app()->environment('production')
                        ? 'An unexpected error occurred. Please try again later.'
                        : $e->getMessage(),
                    'error'   => 'Internal server error'
                ], 500);
            }

            if (app()->environment('production')) {
                abort(500, 'Something went wrong');
            }
        });
    })->create();
