<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * Default role to volunteer (Crew) if empty, and normalize email.
     */
    protected function prepareForValidation(): void
    {
        $merged = [];

        if (blank($this->role)) {
            $merged['role'] = 'volunteer';
        }

        if (is_string($this->email)) {
            $merged['email'] = Str::lower(trim($this->email));
        }

        if ($merged !== []) {
            $this->merge($merged);
        }
    }

    /**
     * Role bernilai default Crew (volunteer) dan opsional.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'role' => ['nullable', 'string', Rule::in(['volunteer', 'eo'])],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'role.in' => 'Role tidak valid.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $invalidCredentialsMessage = 'Email atau kata sandi tidak valid.';

        if (! Auth::attempt(
            $this->only('email', 'password'),
            $this->boolean('remember')
        )) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => $invalidCredentialsMessage,
                'password' => $invalidCredentialsMessage,
            ]);
        }

        $user = Auth::user();

        // Admin tidak diperbolehkan masuk melalui halaman login Crew/EO.
        if ($user->role === UserRole::Admin) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => $invalidCredentialsMessage,
                'password' => $invalidCredentialsMessage,
            ]);
        }

        $selectedRole = $this->role ?: 'volunteer';

        // Kredensial benar, tetapi role yang dipilih tidak sesuai.
        // Gunakan pesan umum agar informasi role akun tidak bocor.
        if ($user->role->value !== $selectedRole) {
            Auth::logout();
            RateLimiter::clear($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => $invalidCredentialsMessage,
                'password' => $invalidCredentialsMessage,
            ]);
        }

        // Akun yang ditangguhkan tidak boleh melanjutkan login.
        if ($user->status === UserStatus::Suspended) {
            Auth::logout();
            RateLimiter::clear($this->throttleKey());

            throw ValidationException::withMessages([
                'auth' => 'Akun tidak dapat diakses saat ini. Silakan hubungi administrator.',
            ]);
        }

        // Login berhasil, hapus hitungan percobaan login.
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->string('email')) . '|' . $this->ip()
        );
    }
}
