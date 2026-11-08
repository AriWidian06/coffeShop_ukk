<?php

/**
 * Excerpt dari: app/Http/Controllers/ProdukController.php
 * Fokus: validasi file, Storage `store()`, dan penghapusan file lama.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            // AMAN: store() menghasilkan nama file acak di disk public.
            $path = $request->file('gambar')->store('produks', 'public');
            $validated['gambar'] = $path;
        }

        // ... Produk::create($validated);
    }

    public function update(Request $request, $produk)
    {
        // ... validasi yang sama ...

        if ($request->hasFile('gambar')) {
            // Hapus file lama sebelum mengganti.
            if ($produk->gambar) {
                Storage::disk('public')->delete($produk->gambar);
            }

            $path = $request->file('gambar')->store('produks', 'public');
            $validated['gambar'] = $path;
        }

        // ... $produk->update($validated);
    }
}

// POLA BERBAHAYA (JANGAN DIPAKAI):
// $fileName = $request->file('gambar')->getClientOriginalName();
// $request->file('gambar')->move(public_path('uploads'), $fileName);
```