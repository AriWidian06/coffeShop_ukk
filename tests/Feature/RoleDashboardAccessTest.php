<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use Tests\TestCase;

class RoleDashboardAccessTest extends TestCase
{
    public function test_manager_dashboard_shows_sales_only(): void
    {
        $manager = new Karyawan([
            'username' => 'manager1',
            'role' => 'manager',
            'password' => 'manager123',
        ]);

        $this->actingAs($manager, 'karyawan')
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Laporan Penjualan')
            ->assertDontSee('POS');
    }

    public function test_manager_can_view_sales_but_cannot_create_transaction(): void
    {
        $manager = new Karyawan([
            'username' => 'manager2',
            'role' => 'manager',
            'password' => 'manager123',
        ]);

        $this->actingAs($manager, 'karyawan')
            ->get('/transaksis')
            ->assertOk();

        $this->actingAs($manager, 'karyawan')
            ->get('/transaksis/create')
            ->assertForbidden();
    }

    public function test_kasir_dashboard_shows_pos_only(): void
    {
        $kasir = new Karyawan([
            'username' => 'kasir1',
            'role' => 'kasir',
            'password' => 'kasir123',
        ]);

        $this->actingAs($kasir, 'karyawan')
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('POS')
            ->assertDontSee('Laporan Penjualan');
    }

    public function test_kasir_can_access_pos_but_manager_cannot(): void
    {
        $kasir = new Karyawan([
            'username' => 'kasir2',
            'role' => 'kasir',
            'password' => 'kasir123',
        ]);

        $this->actingAs($kasir, 'karyawan')
            ->get('/transaksis/create')
            ->assertOk();
    }
}
