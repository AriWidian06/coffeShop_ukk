<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Hanya peran staf yang boleh masuk ke dashboard.
     * Pelanggan yang scan QR meja tidak memiliki akun di tabel karyawans,
     * dan akun non-staf (jika ada) tetap ditolak di sini.
     */
    private const STAFF_ROLES = ['admin', 'kasir', 'manager'];

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('karyawan')->attempt($credentials)) {
            $user = Auth::guard('karyawan')->user();

            // Tolak akun yang bukan staf (mis. akun pelanggan) agar tidak
            // pernah mendapat sesi dashboard admin/kasir/manager.
            if (! in_array(strtolower(trim((string) $user->role)), self::STAFF_ROLES, true)) {
                Auth::guard('karyawan')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Akun ini tidak memiliki akses ke dashboard staf.',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::guard('karyawan')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
