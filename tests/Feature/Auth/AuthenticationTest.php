<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private const GENERIC_CREDENTIAL_ERROR = 'Email atau kata sandi tidak valid.';

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_crew_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'role' => 'volunteer',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_eo_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Eo,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'role' => 'eo',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('eo.dashboard', absolute: false));
    }

    public function test_admin_can_authenticate_without_choosing_a_role(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_not_authenticate_on_user_login(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'role' => 'volunteer',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors([
            'email' => self::GENERIC_CREDENTIAL_ERROR,
        ]);
    }

    public function test_admin_role_can_not_be_sent_as_a_chosen_role(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->assertGuest();
        $response->assertInvalid(['role']);
    }

    public function test_crew_can_authenticate_with_default_role(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_user_can_authenticate_with_uppercase_email(): void
    {
        $user = User::factory()->create([
            'email' => 'crew.uppercase@example.com',
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/login', [
            'email' => '  CREW.UPPERCASE@EXAMPLE.COM  ',
            'password' => 'password',
            'role' => 'volunteer',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_user_can_authenticate_with_remember_me(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'role' => 'volunteer',
            'remember' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertCookie(Auth::guard()->getRecallerName());
        $response->assertCookie('vola_remember');
    }

    public function test_user_can_authenticate_without_remember_me(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'role' => 'volunteer',
        ]);

        $this->assertAuthenticated();
        $response->assertCookieMissing(Auth::guard()->getRecallerName());
        $response->assertCookie('vola_session_active');
    }

    public function test_session_expires_on_root_when_browser_closed_without_remember_me(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        // Simulasikan request tanpa cookie vola_session_active
        $response = $this->actingAs($user)
            ->withSession(['auth_remember' => false])
            ->get('/');

        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
            'role' => 'volunteer',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors([
            'email' => self::GENERIC_CREDENTIAL_ERROR,
            'password' => self::GENERIC_CREDENTIAL_ERROR,
        ]);
    }

    public function test_users_can_not_authenticate_with_unregistered_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'unregistered-user@example.com',
            'password' => 'SomeValidFormatPassword123!',
            'role' => 'volunteer',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors([
            'email' => self::GENERIC_CREDENTIAL_ERROR,
            'password' => self::GENERIC_CREDENTIAL_ERROR,
        ]);
    }

    public function test_admin_login_returns_generic_credential_error_for_invalid_credentials(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $unregisteredResponse = $this->post('/admin/login', [
            'email' => 'unknown-admin@example.com',
            'password' => 'wrong-password',
        ]);

        $unregisteredResponse->assertSessionHasErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak valid.',
        ]);

        $wrongPasswordResponse = $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ]);

        $wrongPasswordResponse->assertSessionHasErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak valid.',
        ]);
        $wrongPasswordResponse->assertSessionDoesntHaveErrors(['password']);
        $this->assertGuest();
    }

    public function test_non_admin_cannot_authenticate_via_admin_login(): void
    {
        $volunteer = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/admin/login', [
            'email' => $volunteer->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors([
            'email' => 'Akses ditolak. Hanya admin yang dapat masuk.',
        ]);
    }

    public function test_suspended_user_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
            'status' => UserStatus::Suspended,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'role' => 'volunteer',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors([
            'auth' => 'Akun ini ditangguhkan. Silakan hubungi administrator untuk informasi lebih lanjut.',
        ]);
    }

    public function test_login_validates_empty_and_invalid_format_fields(): void
    {
        $emptyResponse = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $emptyResponse->assertSessionHasErrors([
            'email' => 'Email wajib diisi.',
            'password' => 'Kata sandi wajib diisi.',
        ]);

        $invalidEmailResponse = $this->post('/login', [
            'email' => 'not-an-email',
            'password' => 'password123',
        ]);

        $invalidEmailResponse->assertSessionHasErrors([
            'email' => 'Format email tidak valid.',
        ]);
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        RateLimiter::clear('throttled@example.com|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'throttled@example.com',
                'password' => 'wrong-password',
                'role' => 'volunteer',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'throttled@example.com',
            'password' => 'wrong-password',
            'role' => 'volunteer',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertStringContainsString(
            'Terlalu banyak percobaan masuk',
            session('errors')->first('email')
        );
    }

    public function test_users_can_not_authenticate_with_mismatched_role(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'role' => 'eo',
        ]);

        $this->assertGuest();
        $response->assertInvalid(['auth']);
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/eo/dashboard')->assertRedirect('/login');
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_non_admin_users_cannot_access_admin_dashboard(): void
    {
        $crew = User::factory()->create(['role' => UserRole::Volunteer]);
        $eo = User::factory()->create(['role' => UserRole::Eo]);

        $this->actingAs($crew)
            ->withSession(['auth_remember' => true])
            ->get('/admin/dashboard')
            ->assertForbidden();

        $this->actingAs($eo)
            ->withSession(['auth_remember' => true])
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
    }
}
