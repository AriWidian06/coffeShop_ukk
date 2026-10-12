<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\Request;

class MejaController extends Controller
{
    public function index()
    {
        $mejas = Meja::orderBy('nomor_meja', 'asc')->paginate(12);
        
        // Hitung statistik
        $totalMeja = Meja::count();
        $mejaAktif = Meja::where('status_aktif', true)->count();
        $mejaNonaktif = Meja::where('status_aktif', false)->count();
        
        return view('mejas.index', compact('mejas', 'totalMeja', 'mejaAktif', 'mejaNonaktif'));
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
            'status_aktif' => 'nullable|in:0,1', // Validasi eksplisit 0 atau 1
        ]);

        // PERBAIKAN: Gunakan $request->boolean() untuk mengonversi '0'/'1' menjadi true/false
        Meja::create([
            'nomor_meja' => $validated['nomor_meja'],
            'kapasitas' => $validated['kapasitas'],
            'status_aktif' => $request->boolean('status_aktif'), 
        ]);

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
            'status_aktif' => 'required|in:0,1', // Validasi eksplisit 0 atau 1
        ]);

        // PERBAIKAN: Gunakan $request->boolean() agar nilai '0' dari form dibaca sebagai false
        $meja->update([
            'nomor_meja' => $validated['nomor_meja'],
            'kapasitas' => $validated['kapasitas'],
            'status_aktif' => $request->boolean('status_aktif'),
        ]);

        return redirect()->route('mejas.index')->with('success', 'Data meja berhasil diperbarui.');
    }

    public function destroy(Meja $meja)
    {
        $meja->delete();
        return redirect()->route('mejas.index')->with('success', 'Meja berhasil dihapus.');
    }
}