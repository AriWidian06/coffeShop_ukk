<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PromoKode extends Model
{
    protected $table = 'promo_kodes';

    protected $fillable = [
        'kode',
        'nama_promo',
        'nilai_diskon',
        'min_belanja',
        'maks_diskon',
        'tanggal_mulai',
        'tanggal_berakhir',
        'kuota',
        'kuota_terpakai',
        'is_aktif',
    ];

    protected $casts = [
        'nilai_diskon' => 'decimal:2',
        'min_belanja' => 'decimal:2',
        'maks_diskon' => 'decimal:2',
        'tanggal_mulai' => 'datetime',
        'tanggal_berakhir' => 'datetime',
        'is_aktif' => 'boolean',
    ];

    public function usages()
    {
        return $this->hasMany(PromoUsage::class);
    }

    public function usagesAktif()
    {
        return $this->hasMany(PromoUsage::class)->where('status', 'active');
    }

    public static function normalized(string $kode): string
    {
        return strtoupper(trim($kode));
    }

    public function eligibleFor(float $subtotal, ?int $mejaId = null, ?string $sessionId = null): array
    {
        if (! $this->is_aktif) {
            return ['ok' => false, 'message' => 'Kode promo tidak aktif.'];
        }

        if ($this->tanggal_mulai && $this->tanggal_mulai->isFuture()) {
            return ['ok' => false, 'message' => 'Kode promo belum berlaku.'];
        }

        if ($this->tanggal_berakhir && $this->tanggal_berakhir->isPast()) {
            return ['ok' => false, 'message' => 'Kode promo sudah berakhir.'];
        }

        if ($this->kuota !== null && $this->kuota_terpakai >= $this->kuota) {
            return ['ok' => false, 'message' => 'Kuota kode promo sudah habis.'];
        }

        if ($subtotal < (float) $this->min_belanja) {
            return ['ok' => false, 'message' => 'Minimal belanja ' . number_format((float) $this->min_belanja, 0, ',', '.') . '.'];
        }

        // Satu meja = maksimal satu kode promo aktif.
        if ($mejaId !== null) {
            $locked = PromoUsage::query()
                ->where('meja_id', $mejaId)
                ->where('status', 'active')
                ->exists();

            if ($locked) {
                return ['ok' => false, 'message' => 'Meja ini sudah menggunakan kode promo pada pesanan aktif.'];
            }
        }

        // Fallback untuk take-away: satu browser/session hanya satu kode promo aktif.
        if ($mejaId === null && $sessionId !== null) {
            $locked = PromoUsage::query()
                ->where('session_id', $sessionId)
                ->where('meja_id', null)
                ->where('status', 'active')
                ->exists();

            if ($locked) {
                return ['ok' => false, 'message' => 'Sesi ini sudah menggunakan kode promo pada pesanan aktif.'];
            }
        }

        $diskon = round($subtotal * ((float) $this->nilai_diskon / 100), 2);

        if ($this->maks_diskon !== null) {
            $diskon = min($diskon, (float) $this->maks_diskon);
        }

        $diskon = max(0, min($diskon, $subtotal));

        return [
            'ok' => true,
            'diskon' => $diskon,
            'total' => round($subtotal - $diskon, 2),
        ];
    }

    public function useForTransaction(\App\Models\Transaksi $transaksi, float $subtotal, ?int $mejaId, ?string $sessionId): PromoUsage
    {
        $diskon = $this->eligibleFor($subtotal, $mejaId, $sessionId)['diskon'] ?? 0;

        $this->increment('kuota_terpakai');

        return $this->usages()->create([
            'meja_id' => $mejaId,
            'transaksi_id' => $transaksi->id,
            'session_id' => $sessionId,
            'discount_amount' => $diskon,
            'status' => 'active',
            'used_at' => now(),
        ]);
    }
}