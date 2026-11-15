<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Support\ApiPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $karyawan = Karyawan::where('username', $validated['username'])->first();

        if (! $karyawan || ! Hash::check($validated['password'], $karyawan->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        $token = $karyawan
            ->createToken($validated['device_name'] ?? 'api-client', ['role:'.strtolower((string) $karyawan->role)])
            ->plainTextToken;

        return $this->ok([
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user' => ApiPresenter::karyawan($karyawan),
        ], 'Login berhasil.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->ok(ApiPresenter::karyawan($request->user()), 'Data pengguna aktif.');
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return $this->ok(null, 'Logout berhasil. Token telah dicabut.');
    }
}
