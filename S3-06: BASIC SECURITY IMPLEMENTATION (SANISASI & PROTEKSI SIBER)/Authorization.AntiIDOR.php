<?php

/**
 * Excerpt dari: app/Policies/TransaksiPolicy.php
 * Fokus: authorization object-level dan anti-IDOR (implementasi final).
 */

namespace App\Policies;

use App\Models\Karyawan;
use App\Models\Transaksi;

class TransaksiPolicy
{
    public function view(Karyawan $user, Transaksi $transaksi): bool
    {
        $role = strtolower((string) $user->role);

        if (in_array($role, ['admin', 'manager'], true)) {
            return true;
        }

        if ($role === 'kasir') {
            return (int) $transaksi->karyawan_id === (int) $user->id
                || $transaksi->sumber_pesanan === 'web';
        }

        return false;
    }

    public function delete(Karyawan $user, Transaksi $transaksi): bool
    {
        return $this->view($user, $transaksi);
    }
}

// Pola controller:
// $this->authorize('view', $transaksi);
// $this->authorize('delete', $transaksi);

// Endpoint QRIS juga memanggil:
// $this->authorize('view', $transaksi);
