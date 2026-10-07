<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->with(['prompt' => 'select_account'])->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            if (! $user->google_id) {
                $user->update(['google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                ]);

            }

            Auth::login($user);

            $dashboardRoute = match ($user->role) {
                UserRole::Admin => route('admin.dashboard', absolute: false),
                UserRole::Eo => route('eo.dashboard', absolute: false),
                default => route('dashboard', absolute: false),
            };

            return redirect()->intended($dashboardRoute);
        }

        // Store Google user info in session for the next step
        session(['google_user' => [
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar_path' => $googleUser->getAvatar(),
        ]]);

        return redirect()->route('auth.google.chooseRole');
    }

    public function showChooseRole()
    {
        if (! session()->has('google_user')) {
            return redirect()->route('login');
        }

        return view('auth.google-choose-role');
    }

    public function chooseRole(Request $request)
    {
        if (! session()->has('google_user')) {
            return redirect()->route('login');
        }

        $request->validate([
            'role' => ['required', 'string', Rule::in(['volunteer', 'eo'])],
        ]);

        $googleData = session('google_user');

        $user = User::create([
            'name' => $googleData['name'],
            'email' => $googleData['email'],
            'password' => null,
            'google_id' => $googleData['google_id'],
            'avatar_path' => $googleData['avatar_path'],
            'email_verified_at' => now(),
            'role' => $request->role,
        ]);

        if ($request->role === 'eo') {
            $user->organizerProfile()->create([
                'organization_name' => $googleData['name'],
            ]);
        } else {
            $user->volunteerProfile()->create([]);
        }

        session()->forget('google_user');

        Auth::login($user);

        $dashboardRoute = match ($user->role) {
            UserRole::Admin => route('admin.dashboard', absolute: false),
            UserRole::Eo => route('eo.dashboard', absolute: false),
            default => route('dashboard', absolute: false),
        };

        return redirect()->intended($dashboardRoute);
    }
}
