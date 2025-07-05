<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email', 'ends_with:@gmail.com'],
            'password' => ['required'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email yang Anda masukkan tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'email.ends_with' => 'Hanya alamat email @gmail.com yang diizinkan.',
        ]);

        // 2. Coba untuk login
        if (Auth::attempt($credentials)) {
            // Jika berhasil, buat session baru
            $request->session()->regenerate();

            // Arahkan ke halaman dashboard
            return redirect()->intended('/dashboard');
        }

        // 3. Jika login gagal
        return back()->with('error', 'Login gagal! Email atau Password salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login.index');
    }
}
