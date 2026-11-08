<?php

namespace App\Policies;

use App\Models\Karyawan;
use App\Models\Transaksi;

class TransaksiPolicy
{
    /**
     * Menentukan apakah user boleh melihat detail transaksi.
     */
    public function view(Karyawan $user, Transaksi $transaksi): bool
    {
        return $this->canAccess($user, $transaksi);
    }

    /**
     * Menentukan apakah user boleh mengubah transaksi.
     */
    public function update(Karyawan $user, Transaksi $transaksi): bool
    {
        return $this->canAccess($user, $transaksi);
    }

    /**
     * Menentukan apakah user boleh menghapus transaksi.
     */
    public function delete(Karyawan $user, Transaksi $transaksi): bool
    {
        return $this->canAccess($user, $transaksi);
    }

    /**
     * Aturan akses berbasis kepemilikan data (anti-IDOR).
     */
    private function canAccess(Karyawan $user, Transaksi $transaksi): bool
    {
        $role = strtolower((string) $user->role);

        // Admin dan manager boleh mengakses seluruh transaksi.
        if (in_array($role, ['admin', 'manager'], true)) {
            return true;
        }

        // Kasir hanya boleh mengakses transaksi miliknya sendiri
        // atau transaksi yang berasal dari pemesanan web.
        if ($role === 'kasir') {
            return (int) $transaksi->karyawan_id === (int) $user->id
                || $transaksi->sumber_pesanan === 'web';
        }

        return false;
    }
}
