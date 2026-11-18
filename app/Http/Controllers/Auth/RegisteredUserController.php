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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if (is_string($request->email)) {
            $request->merge(['email' => Str::lower(trim($request->email))]);
        }

        if (is_string($request->name)) {
            $request->merge(['name' => trim($request->name)]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['volunteer', 'eo'])],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
            'password.min' => 'Kata sandi harus minimal 8 karakter dan mengandung huruf serta angka.',
            'password.letters' => 'Kata sandi harus minimal 8 karakter dan mengandung huruf serta angka.',
            'password.numbers' => 'Kata sandi harus minimal 8 karakter dan mengandung huruf serta angka.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'status' => UserStatus::Active,
            ]);

            if ($validated['role'] === 'eo') {
                $user->organizerProfile()->create([
                    'organization_name' => $validated['name'],
                    // verification_status dibiarkan null; EO harus melengkapi data
                    // dan mengajukan verifikasi secara manual sebelum Admin memrosesnya
                ]);
            } else {
                $user->volunteerProfile()->create([]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
            $request->session()->put('auth_remember', false);
        }

        Cookie::queue('vola_session_active', '1', 0, '/', null, null, false, false, 'Lax');
        Cookie::queue(Cookie::forget('vola_remember'));

        $dashboardRoute = match ($user->role) {
            UserRole::Admin => route('admin.dashboard', absolute: false),
            UserRole::Eo => route('eo.dashboard', absolute: false),
            default => route('dashboard', absolute: false),
        };

        return redirect($dashboardRoute);
    }
}
