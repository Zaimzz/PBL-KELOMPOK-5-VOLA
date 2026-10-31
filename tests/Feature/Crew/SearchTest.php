<?php

namespace Tests\Feature\Crew;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\VolunteerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    private function volunteer(): User
    {
        $user = User::factory()->create(['role' => UserRole::Volunteer]);
        VolunteerProfile::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('search.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_volunteer_can_access_search_page(): void
    {
        $user = $this->volunteer();

        $response = $this->actingAs($user)->get(route('search.index'));

        $response->assertOk();
        $response->assertViewIs('crew.search');
        $response->assertViewHasAll([
            'paginatedEvents',
            'totalEvents',
            'totalPages',
            'page',
            'perPage',
            'query',
            'category',
            'location',
            'position',
            'time',
            'status',
            'categories',
            'locations',
        ]);
    }

    public function test_search_with_query_filters_events(): void
    {
        $user = $this->volunteer();

        $response = $this->actingAs($user)->get(route('search.index', ['q' => 'Comic']));

        $response->assertOk();
        $response->assertSee('Indonesia Comic & Pop Culture Con 2025');
    }

    public function test_search_with_time_filter(): void
    {
        $user = $this->volunteer();

        $response = $this->actingAs($user)->get(route('search.index', ['time' => 'bulan-ini']));

        $response->assertOk();
        $paginatedEvents = $response->viewData('paginatedEvents');
        foreach ($paginatedEvents as $event) {
            $this->assertLessThanOrEqual(30, $event['days_left']);
        }
    }

    public function test_search_with_multiple_filters_at_once(): void
    {
        $user = $this->volunteer();

        $response = $this->actingAs($user)->get(route('search.index', [
            'category' => 'Festival',
            'location' => 'Jakarta',
            'status' => ['Open Volunteer'],
            'position' => ['LO'],
            'time' => 'bulan-ini',
        ]));

        $response->assertOk();
        $response->assertSee('Indonesia Comic & Pop Culture Con 2025');
    }

    public function test_volunteer_can_view_event_detail(): void
    {
        $user = $this->volunteer();

        $response = $this->actingAs($user)->get(route('events.detail', ['event' => 1]));

        $response->assertOk();
        $response->assertViewIs('crew.event-detail');
        $response->assertSee('Indonesia Comic & Pop Culture Con 2025');

        // Test event 2 shows its own specific detail
        $response2 = $this->actingAs($user)->get(route('events.detail', ['event' => 2]));
        $response2->assertOk();
        $response2->assertSee('Jogja International Street Festival 2025');
    }
}

