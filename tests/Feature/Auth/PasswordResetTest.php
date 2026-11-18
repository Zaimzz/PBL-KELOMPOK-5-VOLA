<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private const GENERIC_RESET_SENT_MESSAGE = 'Permintaan reset kata sandi telah diproses. Silakan periksa kotak masuk email kamu. Jika belum menerima email, pastikan alamat email yang dimasukkan benar.';

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    public function test_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
            'role' => 'volunteer',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('status', self::GENERIC_RESET_SENT_MESSAGE);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_reset_fails_if_role_mismatch(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
            'role' => 'eo',
        ]);

        // Mencegah account enumeration: mengembalikan pesan generik tanpa mengirim email
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status', self::GENERIC_RESET_SENT_MESSAGE);
        Notification::assertNothingSent();
    }

    public function test_password_reset_fails_if_email_not_found(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'notfound@example.com',
            'role' => 'volunteer',
        ]);

        // Mencegah account enumeration: mengembalikan pesan generik tanpa mengirim email
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status', self::GENERIC_RESET_SENT_MESSAGE);
        Notification::assertNothingSent();
    }

    public function test_forgot_password_validates_empty_and_invalid_email(): void
    {
        $this->post('/forgot-password', ['email' => ''])
            ->assertSessionHasErrors(['email' => 'Email wajib diisi.']);

        $this->post('/forgot-password', ['email' => 'invalid-email'])
            ->assertSessionHasErrors(['email' => 'Format email tidak valid.']);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newPassword123',
            'password_confirmation' => 'newPassword123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('newPassword123', $user->fresh()->password));
    }

    public function test_password_reset_fails_with_invalid_or_reused_token(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        // Reset pertama berhasil
        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newPassword123',
            'password_confirmation' => 'newPassword123',
        ])->assertRedirect(route('login'));

        // Token yang sama digunakan ulang harus gagal
        $reusedResponse = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'anotherPassword123',
            'password_confirmation' => 'anotherPassword123',
        ]);

        $reusedResponse->assertSessionHasErrors(['email']);
        $this->assertTrue(Hash::check('newPassword123', $user->fresh()->password));
    }

    public function test_password_reset_fails_with_expired_token(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->travel(61)->minutes();

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newPassword123',
            'password_confirmation' => 'newPassword123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(Hash::check('newPassword123', $user->fresh()->password));
    }

    public function test_password_reset_fails_if_password_does_not_meet_requirements(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        // Pengujian ini diarahkan ke /reset-password, bukan /forgot-password
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'Kata sandi minimal 8 karakter.',
        ]);
    }

    public function test_password_reset_fails_if_password_confirmation_mismatches(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newPassword123',
            'password_confirmation' => 'differentPassword123',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'Konfirmasi kata sandi tidak sesuai.',
        ]);
    }
}
