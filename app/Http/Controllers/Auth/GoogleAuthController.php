<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (blank(config('services.google.client_id')) || blank(config('services.google.client_secret'))) {
            return redirect()->route('login')->withErrors([
                'email' => 'Layanan masuk dengan Google belum dikonfigurasi.',
            ]);
        }

        return Socialite::driver('google')->with(['prompt' => 'select_account'])->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if (blank(config('services.google.client_id')) || blank(config('services.google.client_secret'))) {
            return redirect()->route('login')->withErrors([
                'email' => 'Layanan masuk dengan Google belum dikonfigurasi.',
            ]);
        }

        if ($request->filled('error')) {
            $request->session()->forget('google_user');

            return redirect()->route('login')->withErrors([
                'email' => 'Autentikasi Google dibatalkan.',
            ]);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            $request->session()->forget('google_user');

            Log::warning('Google OAuth callback gagal.', [
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'email' => 'Gagal masuk menggunakan akun Google. Silakan coba lagi.',
            ]);
        }

        $googleEmail = Str::lower(trim((string) $googleUser->getEmail()));
        $googleId = (string) $googleUser->getId();

        if ($googleEmail === '' || $googleId === '') {
            $request->session()->forget('google_user');

            return redirect()->route('login')->withErrors([
                'email' => 'Informasi akun Google tidak lengkap. Silakan gunakan akun lain.',
            ]);
        }

        $user = User::where('google_id', $googleId)
            ->orWhere('email', $googleEmail)
            ->first();

        if ($user) {
            if ($user->role === UserRole::Admin) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Admin tidak dapat masuk melalui akun Google. Silakan gunakan portal Administrator.',
                ]);
            }

            if ($user->status === UserStatus::Suspended) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun ini ditangguhkan.',
                ]);
            }

            if (blank($user->google_id)) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Email ini sudah terdaftar menggunakan kata sandi. Silakan masuk menggunakan email dan kata sandi Anda.',
                ]);
            }

            if ((string) $user->google_id !== $googleId) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Google tidak sesuai dengan akun yang terdaftar.',
                ]);
            }

            if (! $user->email_verified_at) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            Auth::login($user);

            if ($request->hasSession()) {
                $request->session()->regenerate();
                $request->session()->put('auth_remember', false);
            }

            Cookie::queue('vola_session_active', '1', 0, '/', null, null, false, false, 'Lax');
            Cookie::queue(Cookie::forget('vola_remember'));

            $dashboardRoute = match ($user->role) {
                UserRole::Eo => route('eo.dashboard', absolute: false),
                default => route('dashboard', absolute: false),
            };

            return redirect()->intended($dashboardRoute);
        }

        // Store Google user info in session for the next step
        session(['google_user' => [
            'name' => trim((string) ($googleUser->getName() ?: Str::before($googleEmail, '@'))),
            'email' => $googleEmail,
            'google_id' => $googleId,
            'avatar_path' => $googleUser->getAvatar(),
        ]]);

        return redirect()->route('auth.google.chooseRole');
    }

    public function showChooseRole(): RedirectResponse|View
    {
        if (! session()->has('google_user')) {
            return redirect()->route('login');
        }

        return view('auth.google-choose-role');
    }

    public function chooseRole(Request $request): RedirectResponse
    {
        if (! session()->has('google_user')) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(['volunteer', 'eo'])],
        ], [
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        $googleData = session('google_user');

        if (! is_array($googleData) || empty($googleData['email']) || empty($googleData['google_id'])) {
            session()->forget('google_user');

            return redirect()->route('login')->withErrors([
                'email' => 'Sesi pendaftaran Google telah berakhir. Silakan ulangi proses masuk dengan Google.',
            ]);
        }

        $email = Str::lower(trim((string) $googleData['email']));
        $googleId = (string) $googleData['google_id'];
        $name = trim((string) ($googleData['name'] ?? Str::before($email, '@')));

        $existingUser = User::where('email', $email)
            ->orWhere('google_id', $googleId)
            ->first();

        if ($existingUser) {
            session()->forget('google_user');

            if ($existingUser->role === UserRole::Admin) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Admin tidak dapat masuk melalui akun Google. Silakan gunakan portal Administrator.',
                ]);
            }

            if (blank($existingUser->google_id) || (string) $existingUser->google_id !== $googleId) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Email ini sudah terdaftar. Silakan masuk menggunakan akun Anda.',
                ]);
            }

            Auth::login($existingUser);

            if ($request->hasSession()) {
                $request->session()->regenerate();
                $request->session()->put('auth_remember', false);
            }

            Cookie::queue('vola_session_active', '1', 0, '/', null, null, false, false, 'Lax');
            Cookie::queue(Cookie::forget('vola_remember'));

            $dashboardRoute = match ($existingUser->role) {
                UserRole::Eo => route('eo.dashboard', absolute: false),
                default => route('dashboard', absolute: false),
            };

            return redirect()->intended($dashboardRoute);
        }

        $user = DB::transaction(function () use ($name, $email, $googleId, $googleData, $validated): User {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => null,
                'google_id' => $googleId,
                'avatar_path' => $googleData['avatar_path'] ?? null,
                'email_verified_at' => now(),
                'role' => $validated['role'],
                'status' => UserStatus::Active,
            ]);

            if ($validated['role'] === 'eo') {
                $user->organizerProfile()->create([
                    'organization_name' => $name,
                ]);
            } else {
                $user->volunteerProfile()->create([]);
            }

            return $user;
        });

        session()->forget('google_user');

        event(new Registered($user));

        Auth::login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
            $request->session()->put('auth_remember', false);
        }

        Cookie::queue('vola_session_active', '1', 0, '/', null, null, false, false, 'Lax');
        Cookie::queue(Cookie::forget('vola_remember'));

        $dashboardRoute = match ($user->role) {
            UserRole::Eo => route('eo.dashboard', absolute: false),
            default => route('dashboard', absolute: false),
        };

        return redirect()->intended($dashboardRoute);
    }
}
