<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\Request;

class MejaController extends Controller
{
    public function index()
    {
        $mejas = Meja::orderBy('nomor_meja', 'asc')->paginate(10);
        return view('mejas.index', compact('mejas'));
    }

    public function create()
    {
        return view('mejas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_meja' => 'required|integer|unique:mejas,nomor_meja',
            'kapasitas' => 'required|integer|min:1',
            'status_aktif' => 'nullable|boolean',
        ]);

        $validated['status_aktif'] = $request->has('status_aktif');

        Meja::create($validated);

        return redirect()->route('mejas.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function edit(Meja $meja)
    {
        return view('mejas.edit', compact('meja'));
    }

    public function update(Request $request, Meja $meja)
    {
        $validated = $request->validate([
            'nomor_meja' => 'required|integer|unique:mejas,nomor_meja,' . $meja->id,
            'kapasitas' => 'required|integer|min:1',
            'status_aktif' => 'nullable|boolean',
        ]);

        $validated['status_aktif'] = $request->has('status_aktif');

        $meja->update($validated);

        return redirect()->route('mejas.index')->with('success', 'Data meja berhasil diperbarui.');
    }

    public function destroy(Meja $meja)
    {
        $meja->delete();
        return redirect()->route('mejas.index')->with('success', 'Meja berhasil dihapus.');
    }
}
