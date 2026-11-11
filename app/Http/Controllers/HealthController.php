<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * Mengecek kesiapan server dan status konektivitas basis data MySQL.
     */
    public function check()
    {
        $dbStatus = 'CONNECTED';
        $activeMeja = 0;
        $activeProducts = 0;

        try {
            DB::connection()->getPdo();
            $activeMeja = DB::table('mejas')->where('status_aktif', true)->count();
            $activeProducts = DB::table('produks')->where('status_aktif', true)->count();
        } catch (Exception $e) {
            $dbStatus = 'DISCONNECTED: ' . $e->getMessage();
        }

        return response()->json([
            'system_name' => 'Coffee Shop Order System',
            'sprint_stage' => 'Sprint 3 - Functional, Security & Performance Refinement',
            'database_status' => $dbStatus,
            'active_tables' => $activeMeja,
            'active_products' => $activeProducts,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
