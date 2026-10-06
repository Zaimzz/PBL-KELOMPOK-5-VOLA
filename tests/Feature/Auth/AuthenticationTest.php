<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            // admin login tanpa memilih role
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_not_authenticate_when_choosing_a_role(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'role' => 'eo',
        ]);

        $this->assertGuest();
        $response->assertInvalid(['role']);
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

    public function test_crew_can_not_authenticate_without_choosing_a_role(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertInvalid(['role']);
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
