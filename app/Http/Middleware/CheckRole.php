<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Periksa apakah pengguna telah login
        if (!Auth::check()) {
            // Jika belum login, arahkan kembali ke halaman login
            return redirect()->route('login.index');
        }

        // Periksa apakah pengguna memiliki role yang sesuai
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // Jika tidak memiliki role yang sesuai, abort dengan status 403
        abort(403, 'Unauthorized');
    }
}
