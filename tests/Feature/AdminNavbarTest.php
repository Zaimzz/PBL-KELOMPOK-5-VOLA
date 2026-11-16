<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNavbarTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_navbar_api(): void
    {
        $this->getJson(route('admin.api.global-search'))->assertUnauthorized();
        $this->getJson(route('admin.api.notifications'))->assertUnauthorized();
    }

    public function test_volunteer_and_eo_cannot_access_admin_navbar_api(): void
    {
        $volunteer = User::factory()->create(['role' => UserRole::Volunteer]);
        $eo = User::factory()->create(['role' => UserRole::Eo]);

        $this->actingAs($volunteer)->getJson(route('admin.api.global-search'))->assertForbidden();
        $this->actingAs($eo)->getJson(route('admin.api.notifications'))->assertForbidden();
    }

    public function test_admin_can_use_global_search(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        // 1. Search Beranda
        $response = $this->actingAs($admin)->getJson(route('admin.api.global-search', ['q' => 'beranda']));
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'query',
                'total_results',
                'categories',
            ]);
        $this->assertGreaterThanOrEqual(1, $response->json('total_results'));

        // 2. Partial lowercase search (case-insensitive & partial match)
        $responsePartial = $this->actingAs($admin)->getJson(route('admin.api.global-search', ['q' => 'kont']));
        $responsePartial->assertOk();
        $this->assertGreaterThanOrEqual(1, $responsePartial->json('total_results'));

        // 3. Ticket prefix with '#' search
        $responseTicket = $this->actingAs($admin)->getJson(route('admin.api.global-search', ['q' => '#rpt']));
        $responseTicket->assertOk();
    }

    public function test_admin_can_search_eo_by_pic_and_city(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $eoUser = User::factory()->create(['role' => UserRole::Eo, 'name' => 'Budi Santoso']);
        $profile = OrganizerProfile::create([
            'user_id' => $eoUser->id,
            'organization_name' => 'Yayasan Peduli Nusantara',
            'city' => 'Malang',
            'pic_name' => 'Budi Santoso',
            'pic_phone' => '081234567890',
            'verification_status' => VerificationStatus::Pending,
        ]);

        // 1. Search partial city 'mal' in global search
        $searchCity = $this->actingAs($admin)->getJson(route('admin.api.global-search', ['q' => 'mal']));
        $searchCity->assertOk();
        $categories = collect($searchCity->json('categories'));
        $eoCategory = $categories->firstWhere('name', 'Verifikasi EO & Legalitas');
        $this->assertNotNull($eoCategory);

        // 2. Search partial PIC name 'budi'
        $searchPic = $this->actingAs($admin)->getJson(route('admin.api.global-search', ['q' => 'budi']));
        $searchPic->assertOk();

        // 3. Search in EO verification page
        $pageResponse = $this->actingAs($admin)->get(route('admin.eo-verifications', ['search' => 'Malang']));
        $pageResponse->assertOk()->assertSee('Yayasan Peduli Nusantara');
    }

    public function test_admin_can_fetch_and_mark_notifications_as_read(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $fetchResponse = $this->actingAs($admin)->getJson(route('admin.api.notifications'));
        $fetchResponse->assertOk()
            ->assertJsonStructure([
                'success',
                'unread_count',
                'total_count',
                'notifications',
            ]);

        $notifications = $fetchResponse->json('notifications');

        if (! empty($notifications)) {
            $firstId = $notifications[0]['id'];

            $readResponse = $this->actingAs($admin)->postJson(route('admin.api.notifications.read', $firstId));
            $readResponse->assertOk()
                ->assertJsonStructure(['success', 'redirect_url']);
        }

        $markAllResponse = $this->actingAs($admin)->postJson(route('admin.api.notifications.mark-all-read'));
        $markAllResponse->assertOk()->assertJson(['success' => true]);
    }

    public function test_global_search_requires_minimum_two_characters(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        // Single character query should immediately return 0 results
        $response1 = $this->actingAs($admin)->getJson(route('admin.api.global-search', ['q' => 'a']));
        $response1->assertOk()
            ->assertJson([
                'success' => true,
                'total_results' => 0,
                'categories' => [],
            ]);

        // Two characters query is processed
        $response2 = $this->actingAs($admin)->getJson(route('admin.api.global-search', ['q' => 'be']));
        $response2->assertOk();
        $this->assertGreaterThanOrEqual(1, $response2->json('total_results'));
    }

    public function test_transaction_search_and_period_filter(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        // Access transactions index with period 'all' (default)
        $responseAll = $this->actingAs($admin)->get(route('admin.transactions.index', [
            'period' => 'all',
        ]));
        $responseAll->assertOk();

        // Access with period '1_year'
        $response1Year = $this->actingAs($admin)->get(route('admin.transactions.index', [
            'period' => '1_year',
        ]));
        $response1Year->assertOk();
    }
}
