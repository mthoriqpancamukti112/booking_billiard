<?php

namespace App\Http\Controllers;

use App\Models\WaitingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaitingListController extends Controller
{
    /**
     * Menampilkan halaman posisi antrian pengguna.
     */
    public function index()
    {
        $userId = Auth::id();
        $myQueue = null;
        $position = 0;

        // Cek apakah user ada di dalam daftar tunggu yang aktif
        $myQueue = WaitingList::where('user_id', $userId)
            ->where('status_antrian', 'menunggu')
            ->first();

        if ($myQueue) {
            // Hitung posisi antrian
            // Posisinya adalah jumlah orang yang masuk sebelum atau bersamaan dengan dia
            $position = WaitingList::where('status_antrian', 'menunggu')
                ->where('waktu_masuk', '<=', $myQueue->waktu_masuk)
                ->count();
        }

        return view('dashboard.posisi_antrian', compact('myQueue', 'position'));
    }

    /**
     * Membatalkan antrian pengguna.
     */
    public function cancel()
    {
        $myQueue = WaitingList::where('user_id', Auth::id())
            ->where('status_antrian', 'menunggu')
            ->first();

        if ($myQueue) {
            // Hapus dari daftar tunggu
            $myQueue->delete();
            return redirect()->route('waitinglist.status')->with('success', 'Anda telah berhasil keluar dari daftar tunggu.');
        }

        return redirect()->route('waitinglist.status')->with('error', 'Anda tidak ditemukan di dalam daftar tunggu.');
    }

    /**
     * Menampilkan halaman data waiting list untuk Admin.
     */
    public function adminIndex()
    {
        // Ambil semua data waiting list, eager load relasi user dan profil pelanggan
        // Urutkan berdasarkan yang paling lama menunggu
        $waiting_list = WaitingList::with('user.pelanggan')
            ->orderBy('waktu_masuk', 'asc')
            ->get();

        return view('dashboard.data_waiting_list', compact('waiting_list'));
    }
}
