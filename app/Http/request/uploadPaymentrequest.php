<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

        'bukti_pembayaran' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048'
            ],
            'metode_pembayaran' => 'required|in:QRIS,cash',
            'jumlah_bayar' => 'required|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            'bukti_pembayaran.required' => 'Wajib melampirkan bukti struk pembayaran.',
            'bukti_pembayaran.image' => 'Berkas harus berupa gambar foto yang valid.',
            'bukti_pembayaran.mimes' => 'Format file yang diperbolehkan hanya JPG, JPEG, PNG, atau WEBP.',
            'bukti_pembayaran.max' => 'Ukuran berkas bukti pembayaran maksimal 2MB.',
            'metode_pembayaran.required' => 'Pilih metode pembayaran yang valid.'
        ];
    }
}