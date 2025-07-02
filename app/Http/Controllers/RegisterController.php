<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // 1. Definisikan aturan validasi
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string',
            'nomor_telepon' => 'required|string|max:13',
        ];

        // 2. Definisikan pesan custom untuk setiap aturan
        $messages = [
            'name.required' => 'Username wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar, silakan gunakan email lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'nomor_telepon.max' => 'Nomor telepon tidak boleh lebih dari 13 digit.',
        ];

        // 3. Jalankan validasi
        $request->validate($rules, $messages);

        // 2. Gunakan DB Transaction untuk memastikan kedua data berhasil disimpan
        DB::beginTransaction();
        try {
            // Buat data untuk tabel 'users'
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'pelanggan', // Role otomatis di-set sebagai 'pelanggan'
            ]);

            // Buat data untuk tabel 'pelanggans'
            Pelanggan::create([
                'user_id' => $user->id, // Ambil ID dari user yang baru dibuat
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_telepon' => $request->nomor_telepon,
            ]);

            // Jika semua berhasil, commit transaksi
            DB::commit();

            // 3. Redirect ke halaman login dengan pesan sukses
            return redirect()->route('login.index')->with('success', 'Registrasi berhasil! Silakan login.');
        } catch (\Exception $e) {
            // Jika ada error, batalkan semua query
            DB::rollBack();

            // Kembali ke halaman register dengan pesan error
            return back()->with('error', 'Registrasi gagal! Terjadi kesalahan.');
        }
    }
}
