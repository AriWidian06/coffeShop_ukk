<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_validation_errors_render_as_global_alert(): void
    {
        $admin = Karyawan::where('username', 'admin')->firstOrFail();

        $this->actingAs($admin, 'karyawan')
            ->from(route('produks.create'))
            ->post(route('produks.store'), [])
            ->assertRedirect(route('produks.create'))
            ->assertSessionHasErrors();

        // Request berikutnya harus menampilkan ringkasan error yang tidak
        // langsung hilang seperti toast.
        $this->actingAs($admin, 'karyawan')
            ->get(route('produks.create'))
            ->assertOk()
            ->assertSee('data-app-alerts', false)
            ->assertSee('Periksa kembali data berikut');
    }

    public function test_flash_error_renders_on_admin_page(): void
    {
        $admin = Karyawan::where('username', 'admin')->firstOrFail();

        $this->actingAs($admin, 'karyawan')
            ->withSession(['error' => 'Pesan error uji admin'])
            ->get(route('produks.index'))
            ->assertOk()
            ->assertSee('data-app-alerts', false)
            ->assertSee('Pesan error uji admin');
    }

    public function test_flash_error_renders_on_kasir_and_manager_pages(): void
    {
        $kasir = Karyawan::create([
            'nama_karyawan' => 'Kasir Error',
            'jabatan' => 'Kasir',
            'role' => 'kasir',
            'username' => 'kasir_error',
            'password' => 'password123',
        ]);

        $manager = Karyawan::create([
            'nama_karyawan' => 'Manager Error',
            'jabatan' => 'Manager',
            'role' => 'manager',
            'username' => 'manager_error',
            'password' => 'password123',
        ]);

        $this->actingAs($kasir, 'karyawan')
            ->withSession(['error' => 'Pesan error uji kasir'])
            ->get(route('transaksis.create'))
            ->assertOk()
            ->assertSee('data-app-alerts', false)
            ->assertSee('Pesan error uji kasir');

        $this->actingAs($manager, 'karyawan')
            ->withSession(['error' => 'Pesan error uji manager'])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-app-alerts', false)
            ->assertSee('Pesan error uji manager');
    }

    public function test_all_custom_error_pages_render(): void
    {
        $pages = [
            403 => 'Akses Ditolak',
            404 => 'Halaman Tidak Ditemukan',
            419 => 'Sesi Kedaluwarsa',
            429 => 'Terlalu Banyak Permintaan',
            500 => 'Mohon Maaf, Terjadi Kendala',
            503 => 'Layanan Sedang Maintenance',
        ];

        foreach ($pages as $status => $text) {
            $this->view('errors.'.$status, ['status' => $status])
                ->assertSee($text);
        }

        $this->view('errors.generic', ['status' => 500])
            ->assertSee('Mohon Maaf, Terjadi Kendala');
    }

    public function test_flash_error_renders_on_customer_and_login_pages(): void
    {
        $this->withSession(['error' => 'Pesan error uji pelanggan'])
            ->get('/')
            ->assertOk()
            ->assertSee('data-app-alerts', false)
            ->assertSee('Pesan error uji pelanggan');

        $this->withSession(['error' => 'Pesan error uji login'])
            ->get('/login')
            ->assertOk()
            ->assertSee('data-app-alerts', false)
            ->assertSee('Pesan error uji login');
    }
}
