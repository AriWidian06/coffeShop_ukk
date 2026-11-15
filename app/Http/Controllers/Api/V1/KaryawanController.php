<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Support\ApiPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'in:admin,kasir,manager'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Karyawan::latest();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($query) use ($search) {
                $query->where('nama_karyawan', 'like', '%'.$search.'%')
                    ->orWhere('username', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        $karyawans = $query
            ->paginate($request->integer('per_page', 15))
            ->through(fn (Karyawan $karyawan) => ApiPresenter::karyawan($karyawan));

        return $this->paginated($karyawans, 'Daftar karyawan.');
    }

    public function show(Karyawan $karyawan): JsonResponse
    {
        return $this->ok(ApiPresenter::karyawan($karyawan), 'Detail karyawan.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_karyawan' => ['required', 'string', 'max:255'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'role' => ['required', 'in:admin,kasir,manager'],
            'username' => ['required', 'string', 'max:100', 'unique:karyawans,username'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $validated['jabatan'] = $this->jabatanFor($validated['role']);

        $karyawan = Karyawan::create($validated);

        return $this->created(ApiPresenter::karyawan($karyawan), 'Karyawan berhasil ditambahkan.');
    }

    public function update(Request $request, Karyawan $karyawan): JsonResponse
    {
        $validated = $request->validate([
            'nama_karyawan' => ['required', 'string', 'max:255'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'role' => ['required', 'in:admin,kasir,manager'],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('karyawans', 'username')->ignore($karyawan->id),
            ],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $validated['jabatan'] = $this->jabatanFor($validated['role']);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $karyawan->update($validated);

        return $this->ok(ApiPresenter::karyawan($karyawan->fresh()), 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Request $request, Karyawan $karyawan): JsonResponse
    {
        if ($request->user()?->id === $karyawan->id) {
            return $this->fail('Anda tidak dapat menghapus akun yang sedang digunakan.', 422);
        }

        $karyawan->tokens()->delete();
        $karyawan->delete();

        return $this->ok(null, 'Karyawan berhasil dihapus.');
    }

    private function jabatanFor(string $role): string
    {
        return match ($role) {
            'admin' => 'Admin',
            'kasir' => 'Kasir',
            'manager' => 'Manager',
            default => 'Karyawan',
        };
    }
}
