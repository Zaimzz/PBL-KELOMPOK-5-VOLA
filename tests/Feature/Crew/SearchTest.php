<?php

namespace Tests\Feature\Crew;

use App\Enums\EventStatus;
use App\Enums\UserRole;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPosition;
use App\Models\User;
use App\Models\VolunteerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    private function volunteer(): User
    {
        $user = User::factory()->create([
            'role' => UserRole::Volunteer,
        ]);

        VolunteerProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        return $user;
    }

    private function createActiveEvent(array $attributes = []): Event
    {
        $category = EventCategory::factory()->create([
            'name' => 'Festival',
        ]);

        return Event::factory()
            ->active()
            ->create(array_merge([
                'category_id' => $category->id,
                'title' => 'Indonesia Comic & Pop Culture Con 2026',
                'city' => 'Jakarta',
                'location' => 'Jakarta Convention Center',
                'registration_deadline' => now()->addDays(10),
                'start_date' => now()->addDays(20),
                'end_date' => now()->addDays(21),
            ], $attributes));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('search.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_volunteer_can_access_search_page(): void
    {
        $user = $this->volunteer();

        $response = $this->actingAs($user)
            ->get(route('search.index'));

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
            'sort',
            'categories',
            'locations',
        ]);
    }

    public function test_search_with_query_filters_events(): void
    {
        $user = $this->volunteer();

        $this->createActiveEvent([
            'title' => 'Indonesia Comic & Pop Culture Con 2026',
        ]);

        $this->createActiveEvent([
            'title' => 'Festival Musik Nusantara 2026',
        ]);

        $response = $this->actingAs($user)
            ->get(route('search.index', ['q' => 'Comic']));

        $response->assertOk();
        $response->assertSee('Indonesia Comic & Pop Culture Con 2026');
        $response->assertDontSee('Festival Musik Nusantara 2026');
    }

    public function test_search_with_time_filter(): void
    {
        $user = $this->volunteer();

        $this->createActiveEvent([
            'title' => 'Event Dalam 10 Hari',
            'registration_deadline' => now()->addDays(10),
        ]);

        $this->createActiveEvent([
            'title' => 'Event Dalam 40 Hari',
            'registration_deadline' => now()->addDays(40),
        ]);

        $response = $this->actingAs($user)
            ->get(route('search.index', ['time' => 'bulan-ini']));

        $response->assertOk();

        $paginatedEvents = $response->viewData('paginatedEvents');

        foreach ($paginatedEvents as $event) {
            $this->assertLessThanOrEqual(30, $event['days_left']);
        }

        $response->assertSee('Event Dalam 10 Hari');
        $response->assertDontSee('Event Dalam 40 Hari');
    }

    public function test_search_with_multiple_filters_at_once(): void
    {
        $user = $this->volunteer();

        $event = $this->createActiveEvent([
            'title' => 'Indonesia Comic & Pop Culture Con 2026',
            'city' => 'Jakarta',
            'registration_deadline' => now()->addDays(10),
        ]);

        EventPosition::factory()->create([
            'event_id' => $event->id,
            'name' => 'LO - Liaison Officer',
        ]);

        $this->createActiveEvent([
            'title' => 'Festival Musik Bandung 2026',
            'city' => 'Bandung',
        ]);

        $response = $this->actingAs($user)->get(route('search.index', [
            'category' => 'Festival',
            'location' => 'Jakarta',
            'status' => ['Open Volunteer'],
            'position' => ['LO'],
            'time' => 'bulan-ini',
        ]));

        $response->assertOk();
        $response->assertSee('Indonesia Comic & Pop Culture Con 2026');
        $response->assertDontSee('Festival Musik Bandung 2026');
    }

    public function test_volunteer_can_view_event_detail(): void
    {
        $user = $this->volunteer();

        $eventOne = $this->createActiveEvent([
            'title' => 'Indonesia Comic & Pop Culture Con 2026',
        ]);

        $eventTwo = $this->createActiveEvent([
            'title' => 'Jogja International Street Festival 2026',
            'city' => 'Yogyakarta',
        ]);

        $response = $this->actingAs($user)
            ->get(route('events.detail', ['event' => $eventOne->id]));

        $response->assertOk();
        $response->assertViewIs('crew.event-detail');
        $response->assertSee('Indonesia Comic & Pop Culture Con 2026');

        $responseTwo = $this->actingAs($user)
            ->get(route('events.detail', ['event' => $eventTwo->id]));

        $responseTwo->assertOk();
        $responseTwo->assertSee('Jogja International Street Festival 2026');
    }
}