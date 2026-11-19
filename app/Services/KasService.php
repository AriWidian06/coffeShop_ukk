<?php

namespace App\Services;

use App\Models\KasAccount;
use App\Models\KasMovement;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KasService
{
    /**
     * Saldo terkini sebuah akun (diambil dari mutasi terakhir).
     */
    public function saldo(KasAccount $account): float
    {
        $last = KasMovement::where('kas_account_id', $account->id)
            ->orderByDesc('id')
            ->first();

        return $last ? (float) $last->saldo_sesudah : 0.0;
    }

    /**
     * Saldo akun pada akhir tanggal tertentu.
     */
    public function saldoPada(KasAccount $account, Carbon $tanggal): float
    {
        $last = KasMovement::where('kas_account_id', $account->id)
            ->whereDate('tanggal', '<=', $tanggal->toDateString())
            ->orderByDesc('id')
            ->first();

        return $last ? (float) $last->saldo_sesudah : 0.0;
    }

    /**
     * Catat satu mutasi kas. AMAN DIPANGGIL BERKALI-KALI untuk referensi yang sama.
     */
    public function catat(
        KasAccount $account,
        string $jenis,
        float $jumlah,
        string $arah,
        array $opsi = []
    ): KasMovement {
        $reffType = $opsi['reff_type'] ?? null;
        $reffId = $opsi['reff_id'] ?? null;

        if ($reffType && $reffId) {
            $existing = KasMovement::where('reff_type', $reffType)
                ->where('reff_id', $reffId)
                ->where('jenis', $jenis)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $jumlah = round(abs($jumlah), 2);
        $sebelum = $this->saldo($account);
        $debit = $arah === 'debit' ? $jumlah : 0.0;
        $kredit = $arah === 'kredit' ? $jumlah : 0.0;
        $sesudah = round($sebelum + $debit - $kredit, 2);

        return KasMovement::create([
            'kas_account_id' => $account->id,
            'tanggal' => $opsi['tanggal'] ?? now()->toDateString(),
            'jenis' => $jenis,
            'reff_type' => $reffType,
            'reff_id' => $reffId,
            'debit' => $debit,
            'kredit' => $kredit,
            'saldo_sebelum' => $sebelum,
            'saldo_sesudah' => $sesudah,
            'keterangan' => $opsi['keterangan'] ?? ucfirst($jenis),
            'karyawan_id' => $opsi['karyawan_id'] ?? null,
        ]);
    }

    /**
     * Pendapatan dari pembayaran yang sudah lunas masuk ke buku kas.
     * Tunai -> Kas Tunai, QRIS -> Bank / QRIS.
     */
    public function catatPenjualan(Pembayaran $pembayaran): ?KasMovement
    {
        if ($pembayaran->status_pembayaran !== 'paid' || ! $pembayaran->transaksi) {
            return null;
        }

        $jumlah = (float) $pembayaran->transaksi->total_harga;
        if ($jumlah <= 0) {
            return null;
        }

        $akun = strtoupper((string) $pembayaran->metode_pembayaran) === 'QRIS'
            ? KasAccount::bank()
            : KasAccount::tunai();

        $waktu = $pembayaran->waktu_pembayaran
            ? $pembayaran->waktu_pembayaran->copy()->timezone('Asia/Jakarta')->toDateString()
            : now()->toDateString();

        return $this->catat($akun, 'penjualan', $jumlah, 'debit', [
            'reff_type' => 'Pembayaran',
            'reff_id' => $pembayaran->id,
            'tanggal' => $waktu,
            'keterangan' => 'Penjualan #'.$pembayaran->transaksi_id
                .' ('.strtoupper((string) $pembayaran->metode_pembayaran).')',
            'karyawan_id' => $pembayaran->transaksi->karyawan_id,
        ]);
    }

    /**
     * Pengeluaran yang disetujui memotong saldo akun terkait.
     */
    public function catatPengeluaran(Pengeluaran $pengeluaran): ?KasMovement
    {
        if ($pengeluaran->status !== 'approved') {
            return null;
        }

        return $this->catat($pengeluaran->account, 'pengeluaran', (float) $pengeluaran->jumlah, 'kredit', [
            'reff_type' => 'Pengeluaran',
            'reff_id' => $pengeluaran->id,
            'tanggal' => $pengeluaran->tanggal->toDateString(),
            'keterangan' => $pengeluaran->kategori_label.' - '.$pengeluaran->deskripsi,
            'karyawan_id' => $pengeluaran->karyawan_id,
        ]);
    }

    /**
     * Saldo awal akun. Hanya tercatat sekali per akun.
     */
    public function catatSaldoAwal(KasAccount $account, float $jumlah, ?int $karyawanId = null): KasMovement
    {
        return $this->catat($account, 'saldo_awal', $jumlah, 'debit', [
            'reff_type' => 'SaldoAwal',
            'reff_id' => $account->id,
            'keterangan' => 'Saldo awal '.$account->nama,
            'karyawan_id' => $karyawanId,
        ]);
    }

    /**
     * Mutasi antar akun: kredit asal + debit tujuan.
     */
    public function transfer(
        KasAccount $dari,
        KasAccount $ke,
        float $jumlah,
        string $keterangan,
        ?int $karyawanId = null
    ): array {
        DB::transaction(function () use ($dari, $ke, $jumlah, $keterangan, $karyawanId) {
            $this->catat($dari, 'setoran', $jumlah, 'kredit', [
                'keterangan' => 'Keluar: '.$keterangan,
                'karyawan_id' => $karyawanId,
            ]);

            $this->catat($ke, 'setoran', $jumlah, 'debit', [
                'keterangan' => 'Masuk: '.$keterangan,
                'karyawan_id' => $karyawanId,
            ]);
        });

        return [$dari, $ke];
    }

    /**
     * Penyesuaian manual (misalnya selisih hasil tutup hari).
     */
    public function penyesuaian(
        KasAccount $account,
        float $jumlah,
        string $arah,
        string $keterangan,
        ?int $karyawanId = null
    ): KasMovement {
        return $this->catat($account, 'penyesuaian', $jumlah, $arah, [
            'keterangan' => $keterangan,
            'karyawan_id' => $karyawanId,
        ]);
    }

    /**
     * Angka-angka yang dipakai form Tutup Hari.
     */
    public function ringkasanHarian(KasAccount $account, Carbon $tanggal): array
    {
        $saldoAwal = $this->saldoPada($account, $tanggal->copy()->subDay()->endOfDay());

        $mutasiHariIni = KasMovement::where('kas_account_id', $account->id)
            ->whereDate('tanggal', $tanggal->toDateString())
            ->get();

        $totalMasuk = (float) $mutasiHariIni->sum('debit');
        $totalKeluar = (float) $mutasiHariIni->sum('kredit');

        return [
            'saldo_awal' => round($saldoAwal, 2),
            'total_masuk' => round($totalMasuk, 2),
            'total_keluar' => round($totalKeluar, 2),
            'saldo_buku' => round($saldoAwal + $totalMasuk - $totalKeluar, 2),
        ];
    }
}
