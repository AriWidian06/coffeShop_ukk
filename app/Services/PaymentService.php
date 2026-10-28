<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Snap;
use Midtrans\Transaction;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
    }

    public function createSnapToken(Transaksi $transaksi)
    {
        $transaksi->loadMissing('karyawan');

        $total = (int) round((float) $transaksi->total_harga);
        $itemDetails = $this->buildItemDetails($transaksi, $total);

        $orderId = 'TRX-' . $transaksi->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $transaksi->karyawan?->nama_karyawan ?? 'Customer',
                'email' => 'customer@example.com',
            ],
        ];

        if (!empty($itemDetails)) {
            $params['item_details'] = $itemDetails;
        }

        try {
            $token = Snap::getSnapToken($params);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            throw $e;
        }

        return [
            'token' => $token,
            'order_id' => $orderId,
        ];
    }

    public function createQrisCharge(Transaksi $transaksi): array
    {
        $total = (int) round((float) $transaksi->total_harga);
        $itemDetails = $this->buildItemDetails($transaksi, $total);

        $orderId = 'TRX-' . $transaksi->id . '-' . time();

        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],
        ];

        if (!empty($itemDetails)) {
            $params['item_details'] = $itemDetails;
        }

        try {
            $response = CoreApi::charge($params);
        } catch (\Exception $e) {
            Log::error('Midtrans QRIS Charge Error: ' . $e->getMessage());
            throw $e;
        }

        $actions = is_array($response) ? ($response['actions'] ?? []) : ($response->actions ?? []);
        $qrUrl = null;

        foreach ($actions as $action) {
            $name = is_array($action) ? ($action['name'] ?? '') : ($action->name ?? '');

            if ($name === 'generate-qr-code') {
                $qrUrl = is_array($action) ? ($action['url'] ?? null) : ($action->url ?? null);
                break;
            }
        }

        return [
            'order_id' => $orderId,
            'qr_url' => $qrUrl,
            'qr_string' => is_array($response) ? ($response['qr_string'] ?? null) : ($response->qr_string ?? null),
            'transaction_status' => is_array($response)
                ? ($response['transaction_status'] ?? 'pending')
                : ($response->transaction_status ?? 'pending'),
        ];
    }

    protected function buildItemDetails(Transaksi $transaksi, int $total): array
    {
        $transaksi->loadMissing('detail_transaksis.produk');

        $itemDetails = $transaksi->detail_transaksis->map(function ($detail) {
            $qty = max((int) $detail->QTY, 1);

            return [
                'id' => (string) $detail->produk_id,
                'price' => (int) round($detail->subtotal / $qty),
                'quantity' => $qty,
                'name' => mb_substr($detail->produk->nama_produk ?? 'Produk', 0, 50),
            ];
        })->values()->toArray();

        // Midtrans menimpa gross_amount dengan jumlah item_details.
        // Pastikan total item_details selalu sama dengan total_harga (termasuk pajak).
        $itemsTotal = array_sum(array_map(fn ($item) => $item['price'] * $item['quantity'], $itemDetails));

        if ($itemsTotal !== $total && !empty($itemDetails)) {
            $diff = $total - $itemsTotal;

            if ($diff > 0) {
                $itemDetails[] = [
                    'id' => 'ADJUSTMENT',
                    'price' => $diff,
                    'quantity' => 1,
                    'name' => 'Pajak / Biaya Tambahan',
                ];
            } else {
                $lastIndex = count($itemDetails) - 1;
                $itemDetails[$lastIndex]['price'] += $diff;
            }
        }

        return $itemDetails;
    }

    public function syncStatus(Transaksi $transaksi, string $orderId): string
    {
        try {
            $response = Transaction::status($orderId);
            $status = is_array($response)
                ? ($response['transaction_status'] ?? 'unknown')
                : ($response->transaction_status ?? 'unknown');
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), '404') || str_contains($e->getMessage(), "doesn't exist")) {
                return 'pending';
            }

            Log::error('Midtrans Status Check Error: ' . $e->getMessage());
            return 'error';
        }

        if (in_array($status, ['capture', 'settlement'], true)) {
            $this->markAsPaid($transaksi);
        }

        return $status;
    }

    public function handleNotification(array $notification)
    {
        $orderId = $notification['order_id'] ?? null;
        $transactionStatus = $notification['transaction_status'] ?? null;
        $fraudStatus = $notification['fraud_status'] ?? null;

        if (!$orderId || !$transactionStatus) {
            Log::error('Invalid Midtrans notification payload', $notification);
            return false;
        }

        if (!$this->isValidSignature($notification)) {
            Log::warning('Invalid Midtrans signature for order: ' . $orderId);
            return false;
        }

        $parts = explode('-', $orderId);
        if (count($parts) < 2 || !is_numeric($parts[1])) {
            Log::error('Invalid Midtrans Order ID: ' . $orderId);
            return false;
        }

        $transaksi = Transaksi::find($parts[1]);
        if (!$transaksi) {
            Log::error('Transaction not found for Midtrans order: ' . $orderId);
            return false;
        }

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            if ($fraudStatus === 'challenge') {
                Log::info('Transaction challenged: ' . $orderId);
                return true;
            }

            $this->markAsPaid($transaksi);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            Log::info('Transaction failed: ' . $orderId . ' status: ' . $transactionStatus);
        }

        return true;
    }

    protected function isValidSignature(array $notification)
    {
        if (empty($notification['signature_key'])) {
            return false;
        }

        $expected = hash(
            'sha512',
            ($notification['order_id'] ?? '') .
            ($notification['status_code'] ?? '') .
            ($notification['gross_amount'] ?? '') .
            config('services.midtrans.server_key')
        );

        return hash_equals($expected, $notification['signature_key']);
    }

    protected function markAsPaid(Transaksi $transaksi)
    {
        $transaksi->update(['status_pesanan' => 'completed']);

        Pembayaran::updateOrCreate(
            ['transaksi_id' => $transaksi->id],
            [
                'jumlah_bayar' => $transaksi->total_harga,
                'metode_pembayaran' => 'QRIS',
                'status_pembayaran' => 'paid',
                'kembalian' => 0,
                'waktu_pembayaran' => now(),
            ]
        );
    }
}
