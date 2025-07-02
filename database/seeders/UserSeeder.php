<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ===========================================
        // |         MEMBUAT DATA UNTUK ADMIN        |
        // ===========================================

        // 1. Buat data di tabel 'users' untuk admin
        $adminUser = User::create([
            'name' => 'Admin Billiard',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('@admin123'),
            'role' => 'admin',
        ]);

        // 2. Buat data di tabel 'admins' yang terhubung dengan user admin
        Admin::create([
            'user_id' => $adminUser->id,
            'nama_admin' => 'Administrator Utama',
            'alamat' => 'Jl. Merdeka No. 1, Makassar',
            'no_hp' => '081234567890',
        ]);


        // ===========================================
        // |       MEMBUAT DATA UNTUK PELANGGAN      |
        // ===========================================

        // 1. Buat data di tabel 'users' untuk pelanggan
        $pelangganUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('budi123'),
            'role' => 'pelanggan',
        ]);

        // 2. Buat data di tabel 'pelanggans' yang terhubung dengan user pelanggan
        Pelanggan::create([
            'user_id' => $pelangganUser->id,
            'nama_lengkap' => 'Budi Santoso',
            'jenis_kelamin' => 'Laki-laki',
            'nomor_telepon' => '089876543210',
        ]);
    }
}
