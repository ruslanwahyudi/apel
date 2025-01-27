<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Pastikan pengguna sudah login
        if (!Auth::check()) {
            return redirect('/'); // Arahkan ke halaman utama jika belum login
        }

        // Periksa apakah role pengguna cocok dengan salah satu role yang diterima
        $user = Auth::user();
        if (!in_array($user->role, $roles)) {
            return abort(403, 'Access Denied'); // Tampilkan 403 jika tidak memiliki akses
        }

        return $next($request); // Lanjutkan jika role sesuai
    }
}
