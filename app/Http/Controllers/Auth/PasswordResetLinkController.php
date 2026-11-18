<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if (is_string($request->email)) {
            $request->merge([
                'email' => Str::lower(trim($request->email)),
            ]);
        }

        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email'],
            'role' => ['nullable', 'string', Rule::in(['volunteer', 'eo'])],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'role.in' => 'Role tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (
            ! $user ||
            $user->role === UserRole::Admin ||
            $user->status === UserStatus::Suspended ||
            ($request->filled('role') && $user->role->value !== $request->role)
        ) {
            return back()->with('status', __('passwords.sent'));
        }

        if (Password::broker()->getRepository()->recentlyCreatedToken($user)) {
            return back()->withInput($request->only('email', 'role'))
                ->withErrors(['email' => __('passwords.throttled')]);
        }

        $token = Password::broker()->createToken($user);

        try {
            $user->notify(new ResetPassword($token));
        } catch (Throwable $exception) {
            Password::broker()->deleteToken($user);

            Log::error('Gagal mengirim email pengaturan ulang kata sandi.', [
                'user_id' => $user->id,
                'mailer' => config('mail.default'),
                'error' => $exception->getMessage(),
            ]);

            return back()->withInput($request->only('email', 'role'))
                ->withErrors(['email' => 'Gagal mengirim tautan reset kata sandi. Silakan coba beberapa saat lagi.']);
        }

        return back()->with('status', __('passwords.sent'));
    }
}
