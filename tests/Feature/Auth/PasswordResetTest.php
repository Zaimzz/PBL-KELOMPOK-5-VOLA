<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    public function test_reset_link_can_be_requested(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
            'role' => 'volunteer',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    public function test_password_reset_fails_if_role_mismatch(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
            'role' => 'eo',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_password_reset_fails_if_email_not_found(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'notfound@example.com',
            'role' => 'volunteer',
        ]);

        $response->assertSessionHasErrors(['email']);
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

        $response->assertSessionHasErrors(['password']);
    }
}