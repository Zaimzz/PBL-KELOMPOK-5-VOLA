<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_crew_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Crew Name',
            'email' => 'crew@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'volunteer',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $response->assertCookie('vola_session_active');

        $user = User::where('email', 'crew@example.com')->first();
        $this->assertEquals(UserRole::Volunteer, $user->role);
        $this->assertEquals('Crew Name', $user->name);
        $this->assertNotNull($user->volunteerProfile);
    }

    public function test_eo_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'EO Org Name',
            'email' => 'eo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'eo',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('eo.dashboard', absolute: false));
        $response->assertCookie('vola_session_active');

        $user = User::where('email', 'eo@example.com')->first();
        $this->assertEquals(UserRole::Eo, $user->role);
        $this->assertEquals('EO Org Name', $user->organizerProfile->organization_name);
    }

    public function test_admin_cannot_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertInvalid(['role']);
        $this->assertGuest();
    }

    public function test_registration_rejects_duplicate_email_case_insensitively(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Duplicate User',
            'email' => '  EXISTING@EXAMPLE.COM  ',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'volunteer',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Email sudah terdaftar.',
        ]);
        $this->assertGuest();
        $this->assertSame(1, User::count());
    }

    public function test_registration_validates_required_fields_and_password_rules(): void
    {
        $emptyResponse = $this->post('/register', []);
        $emptyResponse->assertSessionHasErrors(['name', 'email', 'password', 'role']);

        $weakPasswordResponse = $this->post('/register', [
            'name' => 'Weak Pass',
            'email' => 'weak@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'role' => 'volunteer',
        ]);
        $weakPasswordResponse->assertSessionHasErrors([
            'password' => 'Kata sandi harus minimal 8 karakter dan mengandung huruf serta angka.',
        ]);

        $mismatchResponse = $this->post('/register', [
            'name' => 'Mismatch Pass',
            'email' => 'mismatch@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
            'role' => 'volunteer',
        ]);
        $mismatchResponse->assertSessionHasErrors([
            'password' => 'Konfirmasi kata sandi tidak sesuai.',
        ]);

        $this->assertGuest();
    }
}
