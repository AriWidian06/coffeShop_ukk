<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoKode;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PromoController extends Controller
{
    public function validatePromo(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50',
            'subtotal' => 'required|numeric|min:0',
            'meja_id' => 'nullable|integer|exists:mejas,id',
            'session_id' => 'nullable|string|max:128',
        ]);

        $promo = PromoKode::where('kode', PromoKode::normalized($validated['kode']))->first();

        if (!$promo) {
            throw ValidationException::withMessages([
                'kode' => 'Kode promo tidak ditemukan.',
            ]);
        }

        $result = $promo->eligibleFor(
            (float) $validated['subtotal'],
            isset($validated['meja_id']) ? (int) $validated['meja_id'] : null,
            $validated['session_id'] ?? null
        );

        if (!$result['ok']) {
            throw ValidationException::withMessages([
                'kode' => $result['message'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode promo valid.',
            'data' => [
                'kode' => $promo->kode,
                'nama_promo' => $promo->nama_promo,
                'diskon' => $result['diskon'],
                'total' => $result['total'],
            ],
        ]);
    }
}