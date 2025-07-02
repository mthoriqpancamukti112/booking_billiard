<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MejaBilliard;
use App\Models\WaitingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Cek role pengguna yang sedang login
        if (Auth::user()->role == 'admin') {

            // --- DATA UNTUK ADMIN ---
            $total_meja = MejaBilliard::count();
            $meja_tersedia = MejaBilliard::where('status', 'tersedia')->count();
            $meja_digunakan = MejaBilliard::where('status', 'digunakan')->count();
            $antrian_menunggu = WaitingList::where('status_antrian', 'menunggu')->count();

            // Ambil 5 booking terbaru untuk ditampilkan di tabel
            $recent_bookings = Booking::with('user.pelanggan', 'meja')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Kirim semua data admin ke view
            return view('dashboard.index', compact(
                'total_meja',
                'meja_tersedia',
                'meja_digunakan',
                'antrian_menunggu',
                'recent_bookings'
            ));
        } elseif (Auth::user()->role == 'pelanggan') {

            // --- DATA UNTUK PELANGGAN ---
            $userId = Auth::id();

            $active_booking = Booking::where('user_id', $userId)
                ->where('status_booking', 'berlangsung')
                ->with('meja')
                ->first();

            $my_queue = WaitingList::where('user_id', $userId)
                ->whereIn('status_antrian', ['menunggu', 'dipanggil'])
                ->first();

            $position = 0;
            if ($my_queue && $my_queue->status_antrian == 'menunggu') {
                $position = WaitingList::where('status_antrian', 'menunggu')
                    ->where('waktu_masuk', '<=', $my_queue->waktu_masuk)
                    ->count();
            }

            // --- DATA BARU UNTUK DASHBOARD PELANGGAN ---
            $total_bookings = Booking::where('user_id', $userId)->count();
            $total_time_played = Booking::where('user_id', $userId)->where('status_booking', 'selesai')->sum('durasi_menit');
            $last_booking = Booking::where('user_id', $userId)->where('status_booking', 'selesai')->latest('waktu_selesai')->first();


            return view('dashboard.index', compact(
                'active_booking',
                'my_queue',
                'position',
                'total_bookings',
                'total_time_played',
                'last_booking'
            ));
        }

        // Fallback jika ada role lain atau tidak terdefinisi
        return view('dashboard.index');
    }
}
