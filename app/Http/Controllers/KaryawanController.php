<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::latest()->paginate(10);
        return view('karyawans.index', compact('karyawans'));
    }

    public function create()
    {
        return view('karyawans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'role' => 'required|in:admin,kasir,manager',
            'username' => 'required|string|max:100|unique:karyawans,username',
            'password' => 'required|string|min:6',
        ]);

        Karyawan::create($validated);

        return redirect()->route('karyawans.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Karyawan $karyawan)
    {
        return view('karyawans.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'role' => 'required|in:admin,kasir,manager',
            'username' => 'required|string|max:100|unique:karyawans,username,' . $karyawan->id,
            'password' => 'nullable|string|min:6',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $karyawan->update($validated);

        return redirect()->route('karyawans.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('karyawans.index')->with('success', 'Data karyawan berhasil dihapus.');
    }
}
