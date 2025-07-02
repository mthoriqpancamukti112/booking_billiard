<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Admin;
use App\Models\Pelanggan;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    public function index()
    {
        $user = Auth::user();
        return view('dashboard.profile', compact('user'));
    }

    /**
     * Memperbarui data profil pengguna.
     */
    public function update(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId); // Mengambil instance User secara eksplisit

        // Aturan validasi umum
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'password' => 'nullable|string|min:8|confirmed',
        ];

        // Aturan validasi spesifik berdasarkan role
        if ($user->role == 'admin') {
            $rules['nama_admin'] = 'required|string|max:255';
            $rules['alamat'] = 'required|string';
            $rules['no_hp'] = 'required|string|max:13';
        } elseif ($user->role == 'pelanggan') {
            $rules['nama_lengkap'] = 'required|string|max:255';
            $rules['jenis_kelamin'] = 'required|string';
            $rules['nomor_telepon'] = 'required|string|max:13';
        }

        $request->validate($rules);

        // Update data di tabel users
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        $user->update($userData);

        // PERBAIKAN: Update data profil menggunakan metode statis pada model
        if ($user->role == 'admin') {
            Admin::updateOrCreate(
                ['user_id' => $userId], // Kondisi untuk mencari profil
                [ // Data untuk diupdate atau dibuat
                    'nama_admin' => $request->nama_admin,
                    'alamat' => $request->alamat,
                    'no_hp' => $request->no_hp,
                ]
            );
        } elseif ($user->role == 'pelanggan') {
            Pelanggan::updateOrCreate(
                ['user_id' => $userId], // Kondisi untuk mencari profil
                [ // Data untuk diupdate atau dibuat
                    'nama_lengkap' => $request->nama_lengkap,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'nomor_telepon' => $request->nomor_telepon,
                ]
            );
        }

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui.');
    }
}
