<?php

namespace App\Support;

use App\Models\DetailTransaksi;
use App\Models\Karyawan;
use App\Models\KategoriProduk;
use App\Models\Meja;
use App\Models\Pembayaran;
use App\Models\Produk;
use App\Models\ProdukOpsi;
use App\Models\PromoKode;
use App\Models\Supplier;
use App\Models\Transaksi;

/**
 * Presenter JSON untuk API v1 agar bentuk response konsisten dan
 * tidak membocorkan kolom sensitif (mis. password karyawan).
 */
class ApiPresenter
{
    public static function produk(Produk $produk, bool $withRelations = true): array
    {
        $data = [
            'id' => $produk->id,
            'supplier_id' => $produk->supplier_id,
            'kategori_produk_id' => $produk->kategori_produk_id,
            'nama_produk' => $produk->nama_produk,
            'deskripsi' => $produk->deskripsi,
            'gambar' => $produk->gambar,
            'gambar_url' => $produk->gambar_url,
            'harga_jual' => (float) $produk->harga_jual,
            'harga_beli' => (float) $produk->harga_beli,
            'tipe' => $produk->tipe,
            'satuan' => $produk->satuan,
            'stock' => (int) $produk->stock,
            'status_aktif' => (bool) $produk->status_aktif,
            'is_customizable' => (bool) $produk->is_customizable,
            'created_at' => $produk->created_at?->toIso8601String(),
            'updated_at' => $produk->updated_at?->toIso8601String(),
        ];

        if ($withRelations) {
            $data['supplier'] = $produk->relationLoaded('supplier') && $produk->supplier
                ? self::supplier($produk->supplier)
                : null;
            $data['kategori_produk'] = $produk->relationLoaded('kategoriProduk') && $produk->kategoriProduk
                ? self::kategori($produk->kategoriProduk)
                : null;
            $data['opsis'] = $produk->relationLoaded('opsis')
                ? $produk->opsis->map(fn (ProdukOpsi $opsi) => self::produkOpsi($opsi))->values()->all()
                : [];
        }

        return $data;
    }

    public static function produkOpsi(ProdukOpsi $opsi): array
    {
        return [
            'id' => $opsi->id,
            'kategori' => $opsi->kategori,
            'tipe' => $opsi->tipe,
            'is_required' => (bool) $opsi->is_required,
            'nama_opsi' => $opsi->nama_opsi,
            'harga_tambahan' => (float) $opsi->harga_tambahan,
        ];
    }

    public static function kategori(KategoriProduk $kategori): array
    {
        return [
            'id' => $kategori->id,
            'nama_kategori' => $kategori->nama_kategori,
            'produks_count' => $kategori->produks_count ?? null,
            'created_at' => $kategori->created_at?->toIso8601String(),
            'updated_at' => $kategori->updated_at?->toIso8601String(),
        ];
    }

    public static function meja(Meja $meja, bool $occupied = false): array
    {
        return [
            'id' => $meja->id,
            'nomor_meja' => (int) $meja->nomor_meja,
            'kapasitas' => (int) $meja->kapasitas,
            'status_aktif' => (bool) $meja->status_aktif,
            'is_occupied' => $occupied,
            'created_at' => $meja->created_at?->toIso8601String(),
            'updated_at' => $meja->updated_at?->toIso8601String(),
        ];
    }

    public static function karyawan(Karyawan $karyawan): array
    {
        return [
            'id' => $karyawan->id,
            'nama_karyawan' => $karyawan->nama_karyawan,
            'jabatan' => $karyawan->jabatan,
            'no_telepon' => $karyawan->no_telepon,
            'alamat' => $karyawan->alamat,
            'role' => strtolower((string) $karyawan->role),
            'username' => $karyawan->username,
            'created_at' => $karyawan->created_at?->toIso8601String(),
            'updated_at' => $karyawan->updated_at?->toIso8601String(),
        ];
    }

    public static function supplier(Supplier $supplier): array
    {
        return [
            'id' => $supplier->id,
            'nama_supplier' => $supplier->nama_supplier,
            'alamat' => $supplier->alamat,
            'no_telp' => $supplier->no_telp,
            'created_at' => $supplier->created_at?->toIso8601String(),
            'updated_at' => $supplier->updated_at?->toIso8601String(),
        ];
    }

    public static function promo(PromoKode $promo): array
    {
        return [
            'id' => $promo->id,
            'kode' => $promo->kode,
            'nama_promo' => $promo->nama_promo,
            'nilai_diskon' => (float) $promo->nilai_diskon,
            'min_belanja' => (float) $promo->min_belanja,
            'maks_diskon' => $promo->maks_diskon !== null ? (float) $promo->maks_diskon : null,
            'tanggal_mulai' => $promo->tanggal_mulai?->toIso8601String(),
            'tanggal_berakhir' => $promo->tanggal_berakhir?->toIso8601String(),
            'kuota' => $promo->kuota !== null ? (int) $promo->kuota : null,
            'kuota_terpakai' => (int) $promo->kuota_terpakai,
            'is_aktif' => (bool) $promo->is_aktif,
            'created_at' => $promo->created_at?->toIso8601String(),
            'updated_at' => $promo->updated_at?->toIso8601String(),
        ];
    }

    public static function pembayaran(?Pembayaran $pembayaran): ?array
    {
        if (! $pembayaran) {
            return null;
        }

        return [
            'id' => $pembayaran->id,
            'metode_pembayaran' => $pembayaran->metode_pembayaran,
            'status_pembayaran' => $pembayaran->status_pembayaran,
            'jumlah_bayar' => (float) $pembayaran->jumlah_bayar,
            'kembalian' => (float) $pembayaran->kembalian,
            'waktu_pembayaran' => $pembayaran->waktu_pembayaran?->toIso8601String(),
        ];
    }

    public static function detail(DetailTransaksi $detail): array
    {
        return [
            'id' => $detail->id,
            'produk_id' => $detail->produk_id,
            'nama_produk' => $detail->produk?->nama_produk,
            'qty' => (int) $detail->QTY,
            'customization' => $detail->customization,
            'subtotal' => (float) $detail->subtotal,
        ];
    }

    public static function transaksi(Transaksi $transaksi): array
    {
        $transaksi->loadMissing(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran', 'promo']);

        return [
            'id' => $transaksi->id,
            'meja_id' => $transaksi->meja_id,
            'karyawan_id' => $transaksi->karyawan_id,
            'sumber_pesanan' => $transaksi->sumber_pesanan,
            'catatan' => $transaksi->catatan,
            'tipe_pesanan' => $transaksi->tipe_pesanan,
            'total_harga' => (float) $transaksi->total_harga,
            'status_pesanan' => $transaksi->status_pesanan,
            'waktu_transaksi' => $transaksi->waktu_transaksi?->toIso8601String(),
            'promo_kode_id' => $transaksi->promo_kode_id,
            'kode_promo' => $transaksi->promo?->kode,
            'diskon' => (float) $transaksi->diskon,
            'session_id' => $transaksi->session_id,
            'meja' => $transaksi->meja ? self::meja($transaksi->meja) : null,
            'karyawan' => $transaksi->karyawan ? self::karyawan($transaksi->karyawan) : null,
            'items' => $transaksi->detail_transaksis
                ->map(fn (DetailTransaksi $detail) => self::detail($detail))
                ->values()
                ->all(),
            'pembayaran' => self::pembayaran($transaksi->pembayaran),
        ];
    }

    public static function struk(Transaksi $transaksi): array
    {
        $transaksi->loadMissing(['meja', 'karyawan', 'detail_transaksis.produk', 'pembayaran']);

        $subtotal = (float) $transaksi->detail_transaksis->sum('subtotal');
        $diskon = (float) $transaksi->diskon;
        $total = (float) $transaksi->total_harga;
        $pajak = round(max(0, $total - $subtotal + $diskon), 2);

        return [
            'transaksi' => self::transaksi($transaksi),
            'ringkasan' => [
                'subtotal' => $subtotal,
                'pajak' => $pajak,
                'diskon' => $diskon,
                'total_harga' => $total,
            ],
            'pembayaran' => self::pembayaran($transaksi->pembayaran),
            'dicetak_pada' => now()->toIso8601String(),
        ];
    }
}
