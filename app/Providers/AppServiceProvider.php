<?php

namespace App\Providers;

use App\Models\Karyawan;
use App\Models\Transaksi;
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
        Gate::define('access-admin-panel', function (Karyawan $user) {
            return in_array($user->role, ['admin'], true);
        });

        Gate::define('view-sales-report', function (Karyawan $user) {
            return in_array($user->role, ['manager', 'admin'], true);
        });

        Gate::define('view-transactions', function (Karyawan $user) {
            return in_array($user->role, ['kasir', 'manager', 'admin'], true);
        });

        Gate::define('view-transaction', function (Karyawan $user, Transaksi $transaksi) {
            if (in_array($user->role, ['manager', 'admin'], true)) {
                return true;
            }

            return $user->role === 'kasir'
                && ($transaksi->karyawan_id === $user->id || $transaksi->sumber_pesanan === 'web');
        });

        Gate::define('access-pos', function (Karyawan $user) {
            return in_array($user->role, ['kasir', 'admin'], true);
        });
    }
}
