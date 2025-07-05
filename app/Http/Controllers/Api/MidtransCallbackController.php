<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    public function callback(Request $request)
    {
        // Catat notifikasi yang masuk
        Log::info('Midtrans Notification Received:', $request->all());

        // Konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $notification = new Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }

        $pembayaran = Pembayaran::where('reference_code', $notification->order_id)->first();

        if ($pembayaran) {
            // Verifikasi Signature Key (SANGAT PENTING)
            $expectedSignatureKey = hash('sha512', $notification->order_id . $notification->status_code . $notification->gross_amount . config('services.midtrans.serverKey'));
            if ($notification->signature_key !== $expectedSignatureKey) {
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            // Update status berdasarkan transaksi
            if (in_array($notification->transaction_status, ['settlement', 'capture'])) {
                $pembayaran->status = 'paid';
                $pembayaran->booking->status_pembayaran = 'dp_lunas';
            } elseif (in_array($notification->transaction_status, ['deny', 'expire', 'cancel'])) {
                $pembayaran->status = 'failed';
                $pembayaran->booking->status_pembayaran = 'gagal';
            }

            // ================================================================
            // LOGIKA BARU YANG SUDAH TERVERIFIKASI
            // ================================================================
            $pembayaran->metode_pembayaran = $notification->payment_type ?? 'unknown';

            $notifArray = $request->json()->all();
            $issuerName = '-';

            if (isset($notifArray['issuer'])) {
                $issuerName = $notifArray['issuer'];
            } elseif (isset($notifArray['va_numbers']) && !empty($notifArray['va_numbers'])) {
                $issuerName = $notifArray['va_numbers'][0]['bank'];
            } elseif (isset($notifArray['bank'])) {
                $issuerName = $notifArray['bank'];
            } elseif (isset($notifArray['store'])) {
                $issuerName = $notifArray['store'];
            }

            $pembayaran->issuer = strtoupper($issuerName);
            // ================================================================

            $pembayaran->save();
            $pembayaran->booking->save();
        } else {
            Log::warning('Payment record not found.', ['order_id' => $notification->order_id]);
        }

        return response()->json(['message' => 'Notification successfully processed.']);
    }
}
