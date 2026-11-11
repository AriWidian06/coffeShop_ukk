<?php

namespace App\Providers;

use App\Models\Transaksi;
use App\Policies\TransaksiPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Transaksi::class, TransaksiPolicy::class);

        Gate::define('access-admin-panel', function ($user = null) {
            $user = auth('karyawan')->user();
            return $user && strtolower($user->role) === 'admin';
        });

        Gate::define('view-sales-report', function ($user = null) {
            $user = auth('karyawan')->user();
            return $user && in_array(strtolower($user->role), ['manager', 'admin'], false);
        });

        Gate::define('view-transactions', function ($user = null) {
            $user = auth('karyawan')->user();
            return $user && in_array(strtolower($user->role), ['kasir', 'manager', 'admin'], false);
        });

        Gate::define('access-pos', function ($user = null) {
            $user = auth('karyawan')->user();
            return $user && in_array(strtolower($user->role), ['kasir', 'admin'], false);
        });

        // Pembatas laju untuk endpoint publik agar tidak bisa disalahgunakan
        // (mis. menguras stok lewat spam pesanan atau mencoba brute force login).
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip() ?: 'unknown');
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by(strtolower((string) $request->input('username')) . '|' . ($request->ip() ?: 'unknown'));
        });
    }
}