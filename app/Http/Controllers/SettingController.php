<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // Menampilkan halaman pengaturan
    public function index()
    {
        // Ambil semua settings dan ubah menjadi format yang mudah diakses di view
        $settings = Setting::all()->keyBy('key');
        return view('dashboard.setting.index', compact('settings'));
    }

    // Menyimpan perubahan
    public function update(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'jam_buka' => 'required|date_format:H:i',
            'jam_tutup' => 'required|date_format:H:i',
        ], [
            'jam_buka.required' => 'Jam buka wajib diisi.',
            'jam_tutup.required' => 'Jam tutup wajib diisi.',
            'jam_buka.date_format' => 'Format jam buka tidak valid.',
            'jam_tutup.date_format' => 'Format jam tutup tidak valid.',
        ]);

        // 2. Simpan atau perbarui data menggunakan updateOrCreate
        // Metode ini akan mencari baris dengan 'key' yang sesuai.
        // Jika ditemukan, akan di-update. Jika tidak, akan dibuat baru.
        Setting::updateOrCreate(['key' => 'jam_buka'], ['value' => $request->jam_buka]);
        Setting::updateOrCreate(['key' => 'jam_tutup'], ['value' => $request->jam_tutup]);

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->route('settings.index')->with('success', 'Pengaturan jam operasional berhasil diperbarui.');
    }
}
