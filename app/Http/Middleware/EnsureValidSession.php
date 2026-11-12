<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidSession
{
    /**
     * Handle an incoming request.
     *
     * Memastikan sesi non-ingat-saya (temporary session) otomatis berakhir
     * ketika browser/tab ditutup (cookie session 'vola_session_active' hilang).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('web')->check()) {
            // Jika user secara eksplisit masuk tanpa mencentang 'Ingat saya'
            if ($request->session()->has('auth_remember') && $request->session()->get('auth_remember') === false) {
                // Periksa apakah browser masih mengirimkan cookie sesi aktif
                if (! $request->hasCookie('vola_session_active')) {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }
            }
        }

        return $next($request);
    }
}
