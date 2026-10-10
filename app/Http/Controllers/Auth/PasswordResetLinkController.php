<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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
        $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'string', Rule::in(['volunteer', 'eo'])],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withInput($request->only('email', 'role'))
                ->withErrors(['email' => 'Email belum terdaftar.']);
        }
        
        if ($user->role->value !== $request->role) {
            return back()->withInput($request->only('email', 'role'))
                ->withErrors(['email' => 'Role tidak sesuai dengan akun ini.']);
        }

        $token = Password::broker()->createToken($user);

        $user->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));

        return back()->with('status', __('passwords.sent'));
    }
}
