<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
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
        $response->assertSessionHasErrors(['email']);
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

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
            'role' => 'volunteer',
        ]);

        $this->assertGuest();
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
        $response->assertInvalid(['email']);
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
}
