<?php

namespace App\Support;

use App\Models\Transaksi;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class DashboardSalesChart
{
    /**
     * @return array{sales: array<int, float>, volume: array<int, int>, peak_hour: int|null, updated_at: string}
     */
    public static function forDay(CarbonInterface $date): array
    {
        $day = Carbon::instance($date)->setTimezone('Asia/Jakarta')->startOfDay();
        return self::forRange($day, $day->copy()->endOfDay());
    }

    /**
     * @return array{sales: array<int, float>, volume: array<int, int>, peak_hour: int|null, updated_at: string}
     */
    public static function forRange(CarbonInterface $start, CarbonInterface $end): array
    {
        $startUtc = Carbon::instance($start)->setTimezone('Asia/Jakarta')->utc();
        $endUtc = Carbon::instance($end)->setTimezone('Asia/Jakarta')->utc();

        $hourExpression = match (DB::connection()->getDriverName()) {
            'mysql', 'mariadb' => 'HOUR(waktu_transaksi)',
            'sqlite' => "CAST(strftime('%H', waktu_transaksi) AS INTEGER)",
            'pgsql' => 'EXTRACT(HOUR FROM waktu_transaksi)',
            default => throw new \RuntimeException('Database driver tidak didukung untuk grafik penjualan per jam.'),
        };

        $sales = array_fill(0, 24, 0.0);
        $volume = array_fill(0, 24, 0);

        $rows = Transaksi::query()
            ->whereBetween('waktu_transaksi', [$startUtc, $endUtc])
            ->where('status_pesanan', '!=', 'cancelled')
            ->selectRaw("{$hourExpression} as jam, COUNT(*) as jumlah, SUM(total_harga) as total")
            ->groupBy('jam')
            ->get();

        foreach ($rows as $row) {
            $hour = (((int) $row->jam) + 7) % 24;
            $sales[$hour] = (float) $row->total;
            $volume[$hour] = (int) $row->jumlah;
        }

        $peakHour = null;
        if (max($sales) > 0) {
            $peakHour = (int) array_search(max($sales), $sales, true);
        }

        return [
            'sales' => $sales,
            'volume' => $volume,
            'peak_hour' => $peakHour,
            'updated_at' => Carbon::now('Asia/Jakarta')->format('H:i:s'),
        ];
    }
}
