<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.google.client_id' => 'test-google-client-id',
            'services.google.client_secret' => 'test-google-client-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);
    }

    private function mockSocialiteUser(string $id, string $name, string $email, ?string $avatar = 'https://example.com/avatar.jpg'): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn($id);
        $socialiteUser->shouldReceive('getName')->andReturn($name);
        $socialiteUser->shouldReceive('getNickname')->andReturn($name);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getAvatar')->andReturn($avatar);

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturnSelf()
            ->shouldReceive('user')
            ->andReturn($socialiteUser);
    }

    public function test_google_redirect_sends_user_to_google_oauth(): void
    {
        $response = $this->get(route('auth.google.redirect'));

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_new_google_user_is_redirected_to_choose_role_and_can_register_as_crew(): void
    {
        $this->mockSocialiteUser('google-crew-123', 'New Crew User', 'newcrew@example.com');

        $callbackResponse = $this->get(route('auth.google.callback'));
        $callbackResponse->assertRedirect(route('auth.google.chooseRole'));

        $roleResponse = $this->post(route('auth.google.chooseRole.store'), [
            'role' => 'volunteer',
        ]);

        $roleResponse->assertRedirect(route('dashboard'));
        $roleResponse->assertCookie('vola_session_active');
        $this->assertAuthenticated();

        $user = User::where('email', 'newcrew@example.com')->firstOrFail();
        $this->assertEquals(UserRole::Volunteer, $user->role);
        $this->assertEquals('google-crew-123', $user->google_id);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNotNull($user->volunteerProfile);
    }

    public function test_new_google_user_can_register_as_eo_with_organization_name(): void
    {
        $this->mockSocialiteUser('google-eo-456', 'New EO User', 'neweo@example.com');

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('auth.google.chooseRole'));

        $roleResponse = $this->post(route('auth.google.chooseRole.store'), [
            'role' => 'eo',
        ]);

        $roleResponse->assertRedirect(route('eo.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'neweo@example.com')->firstOrFail();
        $this->assertEquals(UserRole::Eo, $user->role);
        $this->assertEquals('google-eo-456', $user->google_id);
        $this->assertEquals('New EO User', $user->organizerProfile->organization_name);
    }

    public function test_google_choose_role_rejects_admin_role(): void
    {
        $this->withSession([
            'google_user' => [
                'name' => 'Attacker',
                'email' => 'attacker@example.com',
                'google_id' => 'google-attacker-999',
                'avatar' => null,
            ],
        ]);

        $response = $this->post(route('auth.google.chooseRole.store'), [
            'role' => 'admin',
        ]);

        $response->assertSessionHasErrors(['role']);
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'attacker@example.com']);
    }

    public function test_existing_google_linked_user_can_login_directly(): void
    {
        $user = User::factory()->create([
            'email' => 'linked@example.com',
            'google_id' => 'google-linked-789',
            'role' => UserRole::Volunteer,
        ]);

        $this->mockSocialiteUser('google-linked-789', $user->name, 'linked@example.com');

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard'));
        $response->assertCookie('vola_session_active');
        $this->assertAuthenticatedAs($user);
    }

    public function test_existing_password_registered_email_is_not_automatically_linked_to_google(): void
    {
        $user = User::factory()->create([
            'email' => 'password-only@example.com',
            'google_id' => null,
            'role' => UserRole::Volunteer,
        ]);

        $this->mockSocialiteUser('google-unlinked-111', 'Password User', 'password-only@example.com');

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
        $this->assertNull($user->fresh()->google_id);
    }

    public function test_admin_account_cannot_login_via_google(): void
    {
        User::factory()->create([
            'email' => 'admin@vola.id',
            'google_id' => 'google-admin-1',
            'role' => UserRole::Admin,
        ]);

        $this->mockSocialiteUser('google-admin-1', 'Super Admin', 'admin@vola.id');

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_suspended_account_cannot_login_via_google(): void
    {
        User::factory()->create([
            'email' => 'suspended@example.com',
            'google_id' => 'google-suspended-1',
            'role' => UserRole::Volunteer,
            'status' => UserStatus::Suspended,
        ]);

        $this->mockSocialiteUser('google-suspended-1', 'Suspended User', 'suspended@example.com');

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'email' => 'Akun ini ditangguhkan.',
        ]);
        $this->assertGuest();
    }

    public function test_cancelled_google_oauth_redirects_gracefully_to_login(): void
    {
        $response = $this->get(route('auth.google.callback', ['error' => 'access_denied']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'email' => 'Autentikasi Google dibatalkan.',
        ]);
        $this->assertGuest();
    }
}
