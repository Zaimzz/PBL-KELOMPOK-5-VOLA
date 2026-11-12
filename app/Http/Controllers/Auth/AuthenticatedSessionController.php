<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === UserRole::Admin) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Admin tidak dapat masuk melalui halaman ini.']);
        }

        $remember = $request->boolean('remember');
        $request->session()->put('auth_remember', $remember);

        if ($remember) {
            Cookie::queue('vola_remember', '1', 525600 * 5, '/', null, null, false, false, 'Lax');
            Cookie::queue(Cookie::forget('vola_session_active'));
        } else {
            Cookie::queue('vola_session_active', '1', 0, '/', null, null, false, false, 'Lax');
            Cookie::queue(Cookie::forget('vola_remember'));
        }

        $dashboardRoute = match ($user->role) {
            UserRole::Eo => route('eo.dashboard', absolute: false),
            default => route('dashboard', absolute: false),
        };

        return redirect()->intended($dashboardRoute);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        Cookie::queue(Cookie::forget('vola_remember'));
        Cookie::queue(Cookie::forget('vola_session_active'));

        return redirect('/');
    }
}
