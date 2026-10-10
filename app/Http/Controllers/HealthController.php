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
        try {
            DB::connection()->getPdo();
            $dbStatus = 'CONNECTED';
            $roomCount = DB::table('rooms')->count();
        } catch (Exception $e) {
            $dbStatus = 'DISCONNECTED: '.$e->getMessage();
            $roomCount = 0;
        }

        return response()->json([
            'system_name' => 'PerkociEatery Core System',
            'sprint_stage' => 'Sprint 1 - Foundation Ready',
            'database_status' => $dbStatus,
            'active_rooms_seeded' => $roomCount,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}