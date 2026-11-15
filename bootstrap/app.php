<?php

use App\Http\Middleware\EnsureApiRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_PROTO,
        );

        $middleware->validateCsrfTokens(except: [
            '/midtrans/notification',
        ]);

        $middleware->alias([
            'api.role' => EnsureApiRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Bentuk error API v1 dibuat konsisten: selalu ada success=false & message.
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/v1/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang diberikan tidak valid.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/v1/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Sertakan Bearer token yang valid.',
                ], 401);
            }
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/v1/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Anda tidak memiliki wewenang untuk aksi ini.',
                ], 403);
            }
        });

        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e, Request $request) {
            if ($request->is('api/v1/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            }
        });

        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/v1/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
                ], 429)->withHeaders($e->getHeaders());
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            // Pelanggan yang datang dari QR meja tidak diarahkan ke halaman
            // login staf. Tampilkan halaman "Akses Ditolak" dan sediakan
            // tautan kembali ke QR meja yang sedang dipakai.
            if ($e instanceof AuthenticationException) {
                $mejaId = ($request->hasSession() ? $request->session()->get('meja_id') : null)
                    ?? $request->cookie('qr_meja_id');

                if ($mejaId) {
                    $mejaNomor = ($request->hasSession() ? $request->session()->get('meja_nomor') : null)
                        ?? $request->cookie('qr_meja_nomor');

                    return response()->view('errors.403', [
                        'status' => 403,
                        'meja_id' => $mejaId,
                        'meja_nomor' => $mejaNomor,
                    ], 403);
                }

                return null;
            }

            if ($e instanceof ValidationException) {
                return null;
            }

            if (config('app.debug')) {
                return null;
            }

            $status = $e instanceof HttpException
                ? $e->getStatusCode()
                : 500;

            $view = View::exists('errors.'.$status)
                ? 'errors.'.$status
                : 'errors.generic';

            return response()->view($view, [
                'exception' => $e,
                'status' => $status,
            ], $status);
        });
    })->create();
