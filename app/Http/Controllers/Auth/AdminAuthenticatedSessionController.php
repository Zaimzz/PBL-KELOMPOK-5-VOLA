<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class AdminAuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.admin-login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(AdminLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $remember = $request->boolean('remember');
        $request->session()->put('auth_remember', $remember);

        if ($remember) {
            Cookie::queue('vola_remember', '1', 525600 * 5, '/', null, null, false, false, 'Lax');
            Cookie::queue(Cookie::forget('vola_session_active'));
        } else {
            Cookie::queue('vola_session_active', '1', 0, '/', null, null, false, false, 'Lax');
            Cookie::queue(Cookie::forget('vola_remember'));
        }

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated admin session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        Cookie::queue(Cookie::forget('vola_remember'));
        Cookie::queue(Cookie::forget('vola_session_active'));

        return redirect()->route('admin.login');
    }
}
