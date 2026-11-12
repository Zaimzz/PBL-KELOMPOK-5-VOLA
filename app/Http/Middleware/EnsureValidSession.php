<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
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
     * ketika browser/tab ditutup (cookie session 'vola_session_active' hilang),
     * serta menghentikan sesi akun yang telah ditangguhkan (suspended).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            if ($user && $user->status === UserStatus::Suspended) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Akun ini ditangguhkan.',
                ]);
            }

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
