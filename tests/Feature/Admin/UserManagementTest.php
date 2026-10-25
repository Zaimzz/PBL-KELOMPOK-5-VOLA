<?php

namespace Tests\Feature\Admin;

use App\Enums\UserStatus;
use App\Models\OrganizerProfile;
use App\Models\User;
use App\Models\VolunteerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $volunteerUser;

    private User $eoUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);

        $this->volunteerUser = User::factory()->create([
            'name' => 'Budi Volunteer',
            'email' => 'budi@example.com',
            'role' => 'volunteer',
            'status' => 'active',
        ]);
        VolunteerProfile::factory()->create([
            'user_id' => $this->volunteerUser->id,
            'institution' => 'Institut Teknologi Bandung',
            'city' => 'Bandung',
        ]);

        $this->eoUser = User::factory()->create([
            'name' => 'Siti Organizer',
            'email' => 'siti@example.com',
            'role' => 'eo',
            'status' => 'active',
        ]);
        OrganizerProfile::factory()->create([
            'user_id' => $this->eoUser->id,
            'organization_name' => 'Komunitas Peduli Lingkungan',
            'city' => 'Jakarta',
        ]);
    }

    public function test_admin_can_access_users_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertSee('Manajemen Pengguna');
        $response->assertSee('Budi Volunteer');
        $response->assertSee('Siti Organizer');
        $response->assertSee('Total Pengguna');
        $response->assertSee('Volunteer Aktif');
        $response->assertSee('Event Organizer');
    }

    public function test_non_admin_cannot_access_users_index(): void
    {
        $response = $this->actingAs($this->volunteerUser)->get(route('admin.users.index'));
        $response->assertStatus(403);

        $responseEo = $this->actingAs($this->eoUser)->get(route('admin.users.index'));
        $responseEo->assertStatus(403);
    }

    public function test_filter_and_search_users(): void
    {
        // 1. Search by name
        $resSearch = $this->actingAs($this->admin)->get(route('admin.users.index', [
            'search' => 'Budi',
        ]));
        $resSearch->assertStatus(200);
        $resSearch->assertSee('Budi Volunteer');
        $resSearch->assertDontSee('Siti Organizer');

        // 2. Filter by role: volunteer
        $resRoleVol = $this->actingAs($this->admin)->get(route('admin.users.index', [
            'role' => 'volunteer',
        ]));
        $resRoleVol->assertStatus(200);
        $resRoleVol->assertSee('Budi Volunteer');
        $resRoleVol->assertDontSee('Siti Organizer');

        // 3. Filter by role: eo
        $resRoleEo = $this->actingAs($this->admin)->get(route('admin.users.index', [
            'role' => 'eo',
        ]));
        $resRoleEo->assertStatus(200);
        $resRoleEo->assertSee('Siti Organizer');
        $resRoleEo->assertDontSee('Budi Volunteer');
    }

    public function test_admin_can_view_user_detail_json(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('admin.users.show', $this->volunteerUser));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $this->volunteerUser->id,
                'name' => 'Budi Volunteer',
                'email' => 'budi@example.com',
                'role' => 'volunteer',
                'status' => 'active',
                'details' => [
                    'institution' => 'Institut Teknologi Bandung',
                    'city' => 'Bandung',
                ],
            ],
        ]);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        // 1. Suspend active volunteer
        $response = $this->actingAs($this->admin)->post(route('admin.users.toggle-status', $this->volunteerUser));

        $this->volunteerUser->refresh();
        $this->assertEquals(UserStatus::Suspended, $this->volunteerUser->status);

        // 2. Activate suspended volunteer
        $response2 = $this->actingAs($this->admin)->post(route('admin.users.toggle-status', $this->volunteerUser));

        $this->volunteerUser->refresh();
        $this->assertEquals(UserStatus::Active, $this->volunteerUser->status);
    }

    public function test_admin_cannot_toggle_admin_status(): void
    {
        $otherAdmin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($this->admin)->post(route('admin.users.toggle-status', $otherAdmin));

        $response->assertStatus(403);
    }
}
