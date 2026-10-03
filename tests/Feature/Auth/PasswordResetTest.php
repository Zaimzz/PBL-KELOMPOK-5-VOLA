<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_password_can_be_reset_directly(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
            'role' => 'volunteer',
            'password' => 'newPassword123',
            'password_confirmation' => 'newPassword123',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        $response->assertSessionHas('status', 'Kata sandi berhasil diubah, silakan masuk.');

        $this->assertTrue(Hash::check('newPassword123', $user->fresh()->password));
    }

    public function test_password_reset_fails_if_role_mismatch(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
            'role' => 'eo',
            'password' => 'newPassword123',
            'password_confirmation' => 'newPassword123',
        ]);

        $response->assertSessionHasErrors(['email']);

        $this->assertFalse(Hash::check('newPassword123', $user->fresh()->password));
    }

    public function test_password_reset_fails_if_email_not_found(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'notfound@example.com',
            'role' => 'volunteer',
            'password' => 'newPassword123',
            'password_confirmation' => 'newPassword123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_password_reset_fails_if_password_does_not_meet_requirements(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Eo,
        ]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
            'role' => 'eo',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);

        $response->assertSessionHasErrors(['password']);
    }
}
