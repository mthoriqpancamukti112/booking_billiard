<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class UpdateBookingStatus extends Command
{
    // Nama perintah yang bisa dijalankan lewat terminal
    protected $signature = 'booking:update-status';

    protected $description = 'Memperbarui status booking yang aktif, selesai, dan membatalkan yang tidak dibayar.';

    public function handle()
    {
        $now = Carbon::now();

        // === TUGAS 1: MEMULAI BOOKING YANG SUDAH DIBAYAR ===
        $bookingsToStart = Booking::where('status_booking', 'dipesan')
            ->where('status_pembayaran', 'paid')
            ->where('waktu_mulai', '<=', $now)
            ->get();

        foreach ($bookingsToStart as $booking) {
            $booking->status_booking = 'berlangsung';
            $booking->save();

            $booking->meja?->update(['status' => 'digunakan']);

            $this->info("Booking #{$booking->booking_id} dimulai.");
        }

        // === TUGAS 2: MENYELESAIKAN BOOKING YANG SUDAH HABIS WAKTUNYA ===
        $bookingsToFinish = Booking::where('status_booking', 'berlangsung')
            ->where('waktu_selesai', '<=', $now)
            ->get();

        foreach ($bookingsToFinish as $booking) {
            $booking->status_booking = 'selesai';
            $booking->save();

            $booking->meja?->update(['status' => 'tersedia']);

            $this->info("Booking #{$booking->booking_id} selesai.");
        }

        // === TUGAS 3: MEMBATALKAN BOOKING YANG TIDAK DIBAYAR DALAM 10 MENIT ===
        $expirationMinutes = 10;

        $bookingsToCancel = Booking::with('pembayaran')
            ->where('status_booking', 'dipesan')
            ->where('status_pembayaran', 'belum_bayar')
            ->where('created_at', '<=', now()->subMinutes($expirationMinutes))
            ->get();

        foreach ($bookingsToCancel as $booking) {
            $booking->setAttribute('status_booking', 'dibatalkan');
            $booking->setAttribute('status_pembayaran', 'gagal');

            if ($booking->pembayaran) {
                $booking->pembayaran->status = 'failed';
                $booking->pembayaran->save();
            }

            $booking->save();

            $this->info("Booking #{$booking->booking_id} dibatalkan karena DP tidak dibayar.");
        }

        $this->info('Pembaruan status booking selesai.');
    }
}
