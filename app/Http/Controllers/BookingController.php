<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MejaBilliard;
use App\Models\WaitingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Menampilkan halaman booking meja untuk pelanggan.
     */
    public function index()
    {
        // Ambil semua data meja
        $meja = MejaBilliard::with(['bookings' => function ($query) {
            $query->where('status_booking', 'berlangsung')->with('user.pelanggan');
        }])->orderBy('nomor_meja', 'asc')->get();

        $userId = Auth::id();

        $has_active_booking = Booking::where('user_id', $userId)
            ->where('status_booking', 'berlangsung')
            ->exists();

        // Cek apakah user saat ini ada di dalam daftar tunggu
        $is_waiting = WaitingList::where('user_id', $userId)
            ->where('status_antrian', 'menunggu')
            ->exists();

        // Ambil data pelanggan yang berada di urutan PERTAMA daftar tunggu
        $first_in_line = WaitingList::where('status_antrian', 'menunggu')->orderBy('waktu_masuk')->first();

        // Cek apakah semua meja tidak tersedia
        $all_unavailable = MejaBilliard::where('status', 'tersedia')->doesntExist();

        return view('dashboard.booking_meja', compact('meja', 'has_active_booking', 'is_waiting', 'first_in_line', 'all_unavailable'));
    }

    /**
     * Menyimpan data booking baru.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        // Pengecekan keamanan di sisi server
        if (Booking::where('user_id', $userId)->where('status_booking', 'berlangsung')->exists()) {
            return redirect()->route('booking_meja.index')->with('error', 'Anda sudah memiliki sesi booking yang aktif!');
        }

        // Pengecekan keamanan di sisi server
        $first_in_line = WaitingList::where('status_antrian', 'menunggu')->orderBy('waktu_masuk')->first();
        if ($first_in_line && $first_in_line->user_id != $userId) {
            return redirect()->route('booking_meja.index')->with('error', 'Saat ini adalah giliran pelanggan lain dari daftar tunggu.');
        }

        $request->validate(['meja_id' => 'required|exists:meja_billiards,meja_id']);
        $meja = MejaBilliard::find($request->meja_id);

        if ($meja->status != 'tersedia') {
            return redirect()->route('booking_meja.index')->with('error', 'Maaf, meja sudah tidak tersedia!');
        }

        // Buat booking baru
        Booking::create([
            'user_id' => $userId,
            'meja_id' => $meja->meja_id,
            'waktu_mulai' => now(),
            'status_booking' => 'berlangsung',
        ]);

        // Update status meja
        $meja->update(['status' => 'digunakan']);

        // Jika user yang booking berasal dari waiting list, hapus dari antrian
        if ($first_in_line && $first_in_line->user_id == $userId) {
            $first_in_line->delete();
        }

        return redirect()->route('booking_meja.index')->with('success', 'Meja ' . $meja->nomor_meja . ' berhasil di-booking!');
    }

    /**
     * Menambahkan user ke daftar tunggu.
     */
    public function joinWaitingList()
    {
        if (Booking::where('user_id', Auth::id())->where('status_booking', 'berlangsung')->exists()) {
            return redirect()->route('booking_meja.index')->with('error', 'Anda tidak bisa masuk antrian karena sedang memiliki sesi aktif.');
        }

        if (WaitingList::where('user_id', Auth::id())->whereIn('status_antrian', ['menunggu', 'dipanggil'])->exists()) {
            return redirect()->route('booking_meja.index')->with('info', 'Anda sudah berada di dalam daftar tunggu.');
        }

        WaitingList::create(['user_id' => Auth::id()]);
        return redirect()->route('booking_meja.index')->with('success', 'Anda telah berhasil ditambahkan ke daftar tunggu.');
    }

    public function history()
    {
        // Ambil data booking milik user yang sedang login
        // Eager load relasi 'meja' untuk efisiensi query
        // Urutkan berdasarkan yang terbaru
        $bookings = Booking::where('user_id', Auth::id())
            ->with('meja')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.riwayat_booking', compact('bookings'));
    }

    /**
     * Menampilkan halaman data booking untuk Admin dengan fitur pencarian.
     */
    public function adminIndex(Request $request)
    {
        $search = $request->input('search');

        // Query data booking dengan pencarian dan paginasi
        $bookings = Booking::with(['user.pelanggan', 'meja'])
            ->when($search, function ($query, $search) {
                // Logika pencarian
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
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Menampilkan 10 data per halaman

        return view('dashboard.data_booking', compact('bookings', 'search'));
    }

    /**
     * Menyelesaikan sesi booking.
     */
    public function finish(Booking $booking)
    {
        // 1. Update waktu selesai dan status booking
        $booking->waktu_selesai = now();
        $booking->status_booking = 'selesai';

        // 2. Hitung durasi dan total biaya
        $waktuMulai = Carbon::parse($booking->waktu_mulai);
        $waktuSelesai = Carbon::parse($booking->waktu_selesai);
        $durasiMenit = $waktuSelesai->diffInMinutes($waktuMulai);

        $hargaPerMenit = $booking->meja->harga_per_jam / 60;
        $totalBiaya = ceil($durasiMenit * $hargaPerMenit); // Dibulatkan ke atas

        $booking->durasi_menit = $durasiMenit;
        $booking->total_biaya = $totalBiaya;
        $booking->save();

        // 3. Update status meja menjadi 'tersedia'
        $booking->meja->update(['status' => 'tersedia']);

        // 4. Panggil antrian berikutnya jika ada
        $nextInLine = WaitingList::where('status_antrian', 'menunggu')->orderBy('waktu_masuk')->first();
        if ($nextInLine) {
            $nextInLine->update(['status_antrian' => 'dipanggil']);
            // Di sini Anda bisa menambahkan logika notifikasi ke pelanggan berikutnya
        }

        return redirect()->route('admin.booking.index')->with('success', 'Sesi booking berhasil diselesaikan.');
    }
}
