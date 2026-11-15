<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi endpoint API v1 berdasarkan role karyawan.
 *
 * Dipakai setelah middleware auth:sanctum sehingga $request->user()
 * berisi Karyawan pemilik token.
 */
class EnsureApiRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $role = strtolower((string) $user->role);

        if ($roles !== [] && ! in_array($role, array_map('strtolower', $roles), true)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: role Anda tidak memiliki wewenang untuk endpoint ini.',
            ], 403);
        }

        return $next($request);
    }
}
