<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $data = Pelanggan::with('user')
            ->when($search, function ($query, $search) {
                return $query->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nomor_telepon', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->latest() // Urutkan berdasarkan data terbaru
            ->paginate(10); // Ambil 10 data per halaman

        return view('dashboard.pelanggan.index', compact('data', 'search'));
    }

    /**
     * Memperbarui data pelanggan dan user terkait.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $user = $pelanggan->user;

        // Aturan validasi
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id), // Abaikan email user saat ini
            ],
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string',
            'nomor_telepon' => 'required|string|max:13',
        ];

        // Validasi dengan error bag 'update'
        // Tanda '\' sebelum Validator dihapus karena sudah di-import di atas
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('pelanggan.index')
                ->withErrors($validator, 'update')
                ->with('failed_id', $pelanggan->pelanggan_id); // Kirim ID yang gagal
        }

        // Gunakan transaksi untuk keamanan data
        DB::beginTransaction();
        try {
            // Update data di tabel users
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update data di tabel pelanggans
            $pelanggan->update([
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_telepon' => $request->nomor_telepon,
            ]);

            DB::commit();
            return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pelanggan.index')->with('error', 'Gagal memperbarui data pelanggan.');
        }
    }

    /**
     * Menghapus data pelanggan dan user terkait.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        DB::beginTransaction();
        try {
            $user = $pelanggan->user;
            $pelanggan->delete(); // Hapus data pelanggan
            if ($user) {
                $user->delete(); // Hapus data user
            }
            DB::commit();
            return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pelanggan.index')->with('error', 'Gagal menghapus data. Pelanggan mungkin memiliki data booking.');
        }
    }
}
