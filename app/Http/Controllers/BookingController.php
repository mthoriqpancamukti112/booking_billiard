<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MejaBilliard;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

// DIHAPUS: WaitingList tidak digunakan lagi
// use App\Models\WaitingList;

class BookingController extends Controller
{
    /**
     * Menampilkan halaman booking meja untuk pelanggan.
     */
    public function index()
    {
        // BAGIAN INI TETAP: Mengambil data dasar
        $settings = Setting::all()->keyBy('key');
        $jam_buka = $settings->get('jam_buka')?->value ?? '08:00';
        $jam_tutup = $settings->get('jam_tutup')?->value ?? '22:00';

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $meja = MejaBilliard::with(['bookings' => function ($query) use ($today, $tomorrow) {
            $query->where('status_booking', '!=', 'dibatalkan')
                ->whereDate('waktu_mulai', '>=', $today)
                ->whereDate('waktu_mulai', '<=', $tomorrow);
        }, 'activeBooking'])->orderBy('nomor_meja', 'asc')->get();

        $meja->each(function ($item) use ($jam_buka, $jam_tutup, $today, $tomorrow) {
            $item->booked_slots_today = $item->bookings
                ->filter(fn($booking) => Carbon::parse($booking->waktu_mulai)->isToday())
                ->pluck('waktu_mulai')->map(fn($time) => Carbon::parse($time)->format('H:i'))->toArray();

            $item->booked_slots_tomorrow = $item->bookings
                ->filter(fn($booking) => Carbon::parse($booking->waktu_mulai)->isTomorrow())
                ->pluck('waktu_mulai')->map(fn($time) => Carbon::parse($time)->format('H:i'))->toArray();

            $item->is_full_today = false;
            if ($item->status == 'tersedia') {
                $startHour = (int)explode(':', $jam_buka)[0];
                $endHour = (int)explode(':', $jam_tutup)[0];
                $currentHour = max((int)Carbon::now()->format('H'), $startHour);
                $availableSlotsCount = 0;
                for ($hour = $currentHour; $hour < $endHour; $hour++) {
                    if (!in_array(str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00', $item->booked_slots_today)) {
                        $availableSlotsCount++;
                    }
                }
                if ($availableSlotsCount === 0) {
                    $item->is_full_today = true;
                }
            }
        });

        $userId = Auth::id();
        $active_booking = Booking::with('meja', 'pembayaran')->where('user_id', $userId)->whereIn('status_booking', ['dipesan', 'berlangsung'])->first();

        // BAGIAN INI TETAP: Logika untuk menampilkan tombol booking besok
        $show_book_tomorrow_button = false;
        if (!$active_booking) {
            // --- PERBAIKAN: Logika untuk menyembunyikan kartu jika meja hanya 'diperbaiki' ---

            // 1. Ambil hanya meja yang bisa beroperasi (bukan 'perbaikan')
            $operable_tables = $meja->where('status', '!=', 'perbaikan');

            // 2. Dari meja yang bisa beroperasi, cek apakah SEMUANYA sudah penuh hari ini
            $all_operable_tables_are_full = $operable_tables->every(fn($m) => $m->is_full_today);

            // 3. Tampilkan tombol "Booking Besok" HANYA JIKA:
            //    - Ada meja yang bisa beroperasi (tidak semuanya 'perbaikan').
            //    - DAN semua meja yang bisa beroperasi itu sudah penuh slotnya.
            if ($operable_tables->isNotEmpty() && $all_operable_tables_are_full) {
                $show_book_tomorrow_button = true;
            }
        }

        $current_player_booking = null;
        if ($active_booking && $active_booking->status_booking == 'dipesan') {
            $current_player_booking = Booking::where('meja_id', $active_booking->meja_id)
                ->where('status_booking', 'berlangsung')
                ->where('waktu_mulai', '<', $active_booking->waktu_mulai)
                ->first();
        }

        // DIHAPUS: Variabel waiting list tidak diperlukan lagi
        $all_unavailable = $meja->every(fn($m) => $m->status != 'tersedia');

        // DIUBAH: compact() disederhanakan
        return view('dashboard.booking_meja', compact('meja', 'active_booking', 'all_unavailable', 'jam_buka', 'jam_tutup', 'current_player_booking', 'show_book_tomorrow_button'));
    }

    /**
     * Menyimpan data booking baru.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'meja_id' => 'required|exists:meja_billiards,meja_id',
            'waktu_mulai' => 'required|date_format:Y-m-d H:i:s',
            'durasi' => 'required|integer|min:1|max:4',
        ]);

        $meja = MejaBilliard::find($request->meja_id);
        $waktuMulai = Carbon::parse($request->waktu_mulai);
        $durasiJam = (int)$request->durasi;
        $waktuSelesai = $waktuMulai->copy()->addHours($durasiJam);

        // Validasi meja rusak
        if ($meja->status === 'perbaikan') {
            return redirect()->route('booking_meja.index')->with('error', 'Meja yang Anda pilih sedang dalam perbaikan dan tidak bisa dibooking.');
        }

        // Validasi user masih punya booking aktif
        if (Booking::where('user_id', $userId)->whereIn('status_booking', ['berlangsung', 'dipesan'])->exists()) {
            return redirect()->route('booking_meja.index')->with('error', 'Anda sudah memiliki sesi booking yang aktif.');
        }

        // Validasi bentrok waktu dengan booking lain (OVERLAP)
        $adaBentrok = Booking::where('meja_id', $meja->meja_id)
            // Hanya periksa booking yang benar-benar akan menempati meja
            ->whereIn('status_booking', ['dipesan', 'berlangsung'])
            // Logika standar dan paling akurat untuk memeriksa overlap waktu
            ->where(function ($query) use ($waktuMulai, $waktuSelesai) {
                $query->where('waktu_mulai', '<', $waktuSelesai)
                    ->where('waktu_selesai', '>', $waktuMulai);
            })
            ->exists();

        if ($adaBentrok) {
            return redirect()->route('booking_meja.index')->with('error', 'Slot waktu tidak tersedia karena bentrok dengan booking lain.');
        }

        // Hitung durasi dan biaya
        $durasiMenit = $durasiJam * 60;
        $totalBiaya = $meja->harga_per_jam * $durasiJam;

        // Simpan booking
        $booking = Booking::create([
            'user_id' => $userId,
            'meja_id' => $meja->meja_id,
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'durasi_menit' => $durasiMenit,
            'total_biaya' => $totalBiaya,
            'status_booking' => 'dipesan',
            'status_pembayaran' => 'belum_bayar',
        ]);

        // Simpan pembayaran
        $referenceCode = 'BOOK-' . $booking->booking_id . '-' . time();
        $jumlah_dp = $totalBiaya * 0.5;

        Pembayaran::create([
            'booking_id' => $booking->booking_id,
            'reference_code' => $referenceCode,
            'jumlah' => $jumlah_dp,
            'status' => 'pending',
        ]);

        // Redirect ke halaman pembayaran (generate SnapToken nanti)
        return redirect()->route('booking.pay', Crypt::encrypt($booking->booking_id));
    }

    /**
     * Menampilkan riwayat booking pelanggan (TETAP).
     */
    public function history()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['meja', 'pembayaran'])
            ->latest('waktu_mulai')
            ->paginate(10);

        return view('dashboard.riwayat_booking', compact('bookings'));
    }


    /**
     * Menampilkan halaman data booking untuk Admin (TETAP).
     */
    public function adminIndex(Request $request)
    {
        $search = $request->input('search');

        $activeBookingsCount = Booking::whereIn('status_booking', ['berlangsung', 'dipesan'])->count();

        $bookings = Booking::with(['user.pelanggan', 'meja', 'pembayaran'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->whereHas('user.pelanggan', function ($pelangganQuery) use ($search) {
                        $pelangganQuery->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('nomor_telepon', 'like', "%{$search}%");
                    })
                        ->orWhereHas('meja', function ($mejaQuery) use ($search) {
                            $mejaQuery->where('nomor_meja', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByRaw("
            CASE
                WHEN status_booking = 'berlangsung' THEN 1
                WHEN status_booking = 'dipesan' THEN 2
                ELSE 3
            END ASC,
            CASE
                WHEN status_booking IN ('berlangsung', 'dipesan') THEN waktu_mulai
            END ASC,
            waktu_mulai DESC
        ")
            ->paginate(10);

        return view('dashboard.data_booking', compact('bookings', 'search', 'activeBookingsCount'));
    }

    public function pay($encrypted_id)
    {
        try {
            $booking_id = Crypt::decrypt($encrypted_id);
        } catch (DecryptException $e) {
            abort(403, 'Link tidak valid atau kadaluarsa.');
        }

        // Cari booking berdasarkan ID
        $booking = Booking::with(['pembayaran', 'meja', 'user.pelanggan'])->findOrFail($booking_id);

        // Ambil data pembayaran
        $pembayaran = $booking->pembayaran;
        if (!$pembayaran) {
            return redirect()->route('booking.history')->with('error', 'Data pembayaran untuk booking ini tidak ditemukan.');
        }

        // Gunakan policy untuk otorisasi
        $this->authorize('view', $pembayaran);

        // Pastikan booking masih dalam status menunggu pembayaran
        if ($booking->status_pembayaran != 'belum_bayar') {
            return redirect()->route('booking.history')->with('error', 'Booking ini tidak lagi memerlukan pembayaran.');
        }

        // Ambil data pembayaran yang sudah ada
        $pembayaran = $booking->pembayaran;
        if (!$pembayaran) {
            return redirect()->route('booking.history')->with('error', 'Data pembayaran untuk booking ini tidak ditemukan.');
        }

        // Set konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('services.midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Siapkan parameter untuk membuat Snap Token baru
        $params = [
            'transaction_details' => [
                'order_id' => $pembayaran->reference_code,
                'gross_amount' => $pembayaran->jumlah,
            ],
            'enabled_payments' => ['qris', 'gopay', 'bank_transfer', 'shopeepay'],
            'customer_details' => [
                'first_name' => $booking->user->pelanggan->nama_lengkap ?? $booking->user->name,
                'email' => $booking->user->email,
                'phone' => $booking->user->pelanggan->nomor_telepon ?? null,
            ],
        ];

        // Buat Snap Token baru
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        // Tampilkan halaman pembayaran dengan Snap Token yang baru
        return view('pembayaran.index', compact('snapToken', 'encrypted_id', 'booking'));
    }

    // public function adminCancel($booking_id)
    // {
    //     // Cari booking berdasarkan ID
    //     $booking = Booking::findOrFail($booking_id);

    //     // Validasi sederhana, pastikan hanya booking yang belum bayar yang bisa dibatalkan oleh admin
    //     if ($booking->status_pembayaran != 'belum_bayar') {
    //         return redirect()
    //             ->route('admin.booking.index')
    //             ->with('error', 'Booking ini tidak dapat dibatalkan karena sudah diproses.');
    //     }

    //     // Ubah status booking
    //     $booking->status_booking = 'dibatalkan';
    //     $booking->save();

    //     // Redirect kembali dengan pesan sukses
    //     return redirect()
    //         ->route('admin.booking.index')
    //         ->with('success', 'Booking berhasil dibatalkan.');
    // }
}
