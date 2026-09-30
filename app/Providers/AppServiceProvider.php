<?php

namespace App\Providers;

use App\Models\Karyawan;
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

        Gate::define('access-pos', function (Karyawan $user) {
            return in_array($user->role, ['kasir', 'admin'], true);
        });
    }
}
