<?php

namespace Tests\Feature\Admin;

use App\Enums\ApplicationStatus;
use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Models\Application;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPosition;
use App\Models\OrganizerProfile;
use App\Models\User;
use App\Models\VolunteerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveEventTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $eoUser;

    private OrganizerProfile $organizer;

    private EventCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->eoUser = User::factory()->create(['role' => 'eo']);

        $this->organizer = OrganizerProfile::factory()->create([
            'user_id' => $this->eoUser->id,
            'organization_name' => 'PT Pop Media Nusantara',
            'verification_status' => VerificationStatus::Verified,
        ]);

        $this->category = EventCategory::factory()->create([
            'name' => 'Festival & Seni',
            'slug' => 'festival-seni',
        ]);
    }

    public function test_admin_can_access_active_events_index(): void
    {
        $event = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'category_id' => $this->category->id,
            'title' => 'Indonesia Comic Con 2025',
            'status' => EventStatus::Active,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.active-events.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.active-events.index');
        $response->assertSee('Event Aktif');
        $response->assertSee('Indonesia Comic Con 2025');
        $response->assertSee('PT Pop Media Nusantara');
        $response->assertSee('Total Event Aktif');
    }

    public function test_non_admin_cannot_access_active_events(): void
    {
        $volunteer = User::factory()->create(['role' => 'volunteer']);

        $response = $this->actingAs($volunteer)->get(route('admin.active-events.index'));
        $response->assertStatus(403);

        $responseEo = $this->actingAs($this->eoUser)->get(route('admin.active-events.index'));
        $responseEo->assertStatus(403);
    }

    public function test_active_events_query_never_leaks_draft_pending_or_rejected_events(): void
    {
        // Active event (should be visible)
        $activeEvent = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event Aktif Publik',
            'status' => EventStatus::Active,
        ]);

        // Draft event (must be hidden)
        $draftEvent = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event Rahasia Draft',
            'status' => EventStatus::Draft,
        ]);

        // Pending review event (must be hidden)
        $pendingEvent = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event Menunggu Kurasi',
            'status' => EventStatus::PendingReview,
        ]);

        // Rejected event (must be hidden)
        $rejectedEvent = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event Tertolak Admin',
            'status' => EventStatus::Rejected,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.active-events.index'));

        $response->assertStatus(200);
        $response->assertSee('Event Aktif Publik');
        $response->assertDontSee('Event Rahasia Draft');
        $response->assertDontSee('Event Menunggu Kurasi');
        $response->assertDontSee('Event Tertolak Admin');
    }

    public function test_search_and_filter_active_events(): void
    {
        $event1 = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'category_id' => $this->category->id,
            'title' => 'Bandung Creative Tech Summit',
            'city' => 'Bandung',
            'status' => EventStatus::Active,
        ]);

        $event2 = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'category_id' => $this->category->id,
            'title' => 'Jakarta Music Fest',
            'city' => 'Jakarta',
            'status' => EventStatus::Active,
        ]);

        // Search by keyword
        $responseSearch = $this->actingAs($this->admin)->get(route('admin.active-events.index', [
            'search' => 'Bandung',
        ]));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Bandung Creative Tech Summit');
        $responseSearch->assertDontSee('Jakarta Music Fest');

        // Filter by category
        $otherCategory = EventCategory::factory()->create(['name' => 'Olahraga', 'slug' => 'olahraga']);
        $event3 = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'category_id' => $otherCategory->id,
            'title' => 'Run For Earth Marathon',
            'status' => EventStatus::Active,
        ]);

        $responseCategory = $this->actingAs($this->admin)->get(route('admin.active-events.index', [
            'category' => $otherCategory->id,
        ]));
        $responseCategory->assertStatus(200);
        $responseCategory->assertSee('Run For Earth Marathon');
        $responseCategory->assertDontSee('Bandung Creative Tech Summit');
    }

    public function test_metrics_header_calculated_accurately(): void
    {
        // Event 1: Active with open registration
        $event1 = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'status' => EventStatus::Active,
            'registration_deadline' => now()->addDays(5),
        ]);

        // Event 2: Finished with closed registration
        $event2 = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'status' => EventStatus::Finished,
            'registration_deadline' => now()->subDays(5),
        ]);

        // Positions and applications
        $pos1 = EventPosition::factory()->create([
            'event_id' => $event1->id,
            'quota' => 10,
        ]);

        $volunteerUser = User::factory()->create(['role' => 'volunteer']);
        $volProfile = VolunteerProfile::factory()->create(['user_id' => $volunteerUser->id]);

        Application::create([
            'volunteer_profile_id' => $volProfile->id,
            'position_id' => $pos1->id,
            'status' => ApplicationStatus::Accepted,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.active-events.index'));

        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_active'] === 1
                && $stats['registered_volunteers'] === 1
                && $stats['open_registration'] === 1;
        });
    }

    public function test_show_endpoint_returns_json_and_aborts_for_draft(): void
    {
        $event = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'category_id' => $this->category->id,
            'title' => 'Web3 & AI Camp',
            'status' => EventStatus::Active,
        ]);

        $pos = EventPosition::factory()->create([
            'event_id' => $event->id,
            'name' => 'Frontend Crew',
            'quota' => 5,
        ]);

        // Show endpoint with JSON header
        $response = $this->actingAs($this->admin)->getJson(route('admin.active-events.show', $event));
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $event->id,
            'title' => 'Web3 & AI Camp',
            'status' => 'active',
            'total_quota' => 5,
        ]);

        // Draft event should return 404
        $draft = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'status' => EventStatus::Draft,
        ]);

        $responseDraft = $this->actingAs($this->admin)->get(route('admin.active-events.show', $draft));
        $responseDraft->assertStatus(404);
    }

    public function test_filter_active_events_by_ditutup_status(): void
    {
        // Event 1: Closed with future end_date (should appear under 'ditutup')
        $closedEvent = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Workshop UI/UX Terpadu',
            'status' => EventStatus::Closed,
            'end_date' => now()->addDays(2),
        ]);

        // Event 2: Active event (should not appear under 'ditutup')
        $activeEvent = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Festival Musik Tahunan',
            'status' => EventStatus::Active,
            'end_date' => now()->addDays(5),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.active-events.index', [
            'status' => 'ditutup',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Workshop UI/UX Terpadu');
        $response->assertDontSee('Festival Musik Tahunan');
    }

    public function test_show_endpoint_returns_has_poster_and_poster_url(): void
    {
        $eventWithPoster = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Konser Amal Peduli',
            'status' => EventStatus::Active,
            'poster_path' => 'events/posters/sample-poster.jpg',
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.active-events.show', $eventWithPoster));
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $eventWithPoster->id,
            'has_poster' => true,
        ]);
        $this->assertNotNull($response->json('poster_url'));

        $eventWithoutPoster = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Seminar Tanpa Poster',
            'status' => EventStatus::Active,
            'poster_path' => null,
        ]);

        $responseEmpty = $this->actingAs($this->admin)->getJson(route('admin.active-events.show', $eventWithoutPoster));
        $responseEmpty->assertStatus(200);
        $responseEmpty->assertJson([
            'id' => $eventWithoutPoster->id,
            'has_poster' => false,
            'poster_url' => null,
        ]);
    }

    public function test_table_and_modal_contain_poster_preview_lightbox_triggers(): void
    {
        $event = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Pameran Fotografi Nasional',
            'status' => EventStatus::Active,
            'poster_path' => 'events/posters/foto-expo.png',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.active-events.index'));
        $response->assertStatus(200);
        $response->assertSee('openAdminLightbox');
        $response->assertSee('Lihat Poster Event');
        $response->assertSee('Poster Event');
    }
}
