<?php

/**
 * Excerpt dari: bootstrap/app.php
 * Fokus: middleware CSRF dan pengecualian callback eksternal.
 */

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware): void {
        // Seluruh endpoint web mutation dilindungi CSRF secara default.
        // Pengecualian hanya untuk callback Midtrans notification.
        $middleware->validateCsrfTokens(except: [
            '/midtrans/notification',
        ]);
    })->create();
```