<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

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
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            // Pelanggan yang datang dari QR meja tidak diarahkan ke halaman
            // login staf. Tampilkan halaman "Akses Ditolak" dan sediakan
            // tautan kembali ke QR meja yang sedang dipakai.
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
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

            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return null;
            }

            if (config('app.debug')) {
                return null;
            }

            $status = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                ? $e->getStatusCode()
                : 500;

            $view = \Illuminate\Support\Facades\View::exists('errors.' . $status)
                ? 'errors.' . $status
                : 'errors.generic';

            return response()->view($view, [
                'exception' => $e,
                'status' => $status,
            ], $status);
        });
    })->create();
