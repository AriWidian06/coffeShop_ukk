<?php

namespace App\Providers;

use App\Models\Transaksi;
use App\Policies\TransaksiPolicy;
use Illuminate\Support\Facades\Gate;
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
    }
}