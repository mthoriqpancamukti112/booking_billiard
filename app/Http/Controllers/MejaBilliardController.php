<?php

namespace App\Http\Controllers;

use App\Models\MejaBilliard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MejaBilliardController extends Controller
{
    /**
     * Menampilkan halaman daftar meja billiard.
     */
    public function index()
    {
        // Mengambil semua data dari model MejaBilliard
        $data = MejaBilliard::all();
        // Mengirim data ke view
        return view('dashboard.meja_billiard.index', compact('data'));
    }

    /**
     * Menyimpan data meja baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $request->validate([
            'nomor_meja' => 'required|string|max:10|unique:meja_billiards,nomor_meja',
            'tipe_meja' => 'nullable|string|max:50',
            'harga_per_jam' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,digunakan,perbaikan',
        ], [
            // Pesan custom untuk validasi
            'nomor_meja.required' => 'Nomor meja wajib diisi.',
            'nomor_meja.unique' => 'Nomor meja ini sudah terdaftar.',
            'harga_per_jam.required' => 'Harga per jam wajib diisi.',
            'harga_per_jam.numeric' => 'Harga harus berupa angka.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // 2. Simpan data ke database
        MejaBilliard::create($request->all());

        // 3. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('meja_billiard.index')->with('success', 'Data meja berhasil ditambahkan.');
    }

    /**
     * Memperbarui data di database.
     */
    public function update(Request $request, MejaBilliard $mejaBilliard)
    {
        $request->validate([
            'nomor_meja' => [
                'required',
                'string',
                'max:10',
                // Validasi unique, tapi abaikan data meja yang sedang diedit
                Rule::unique('meja_billiards')->ignore($mejaBilliard->meja_id, 'meja_id'),
            ],
            'tipe_meja' => 'required|string|max:50',
            'harga_per_jam' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,digunakan,perbaikan',
        ]);

        // Update data
        $mejaBilliard->update($request->all());

        return redirect()->route('meja_billiard.index')->with('success', 'Data meja berhasil diperbarui.');
    }

    /**
     * Menghapus data dari database.
     */
    public function destroy(MejaBilliard $mejaBilliard)
    {
        // Hapus data
        $mejaBilliard->delete();

        return redirect()->route('meja_billiard.index')->with('success', 'Data meja berhasil dihapus.');
    }
}
