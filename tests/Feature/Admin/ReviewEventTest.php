<?php

namespace Tests\Feature\Admin;

use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPosition;
use App\Models\OrganizerProfile;
use App\Models\User;
use App\Notifications\EventApprovedNotification;
use App\Notifications\EventRejectedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReviewEventTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $eoUser;

    private OrganizerProfile $organizer;

    private Event $pendingEvent;

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

        $category = EventCategory::factory()->create([
            'name' => 'MICE',
            'slug' => 'mice',
        ]);

        $this->pendingEvent = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'category_id' => $category->id,
            'title' => 'Indonesia Comic & Pop Culture Con 2025',
            'status' => EventStatus::PendingReview,
            'benefits' => ['Uang saku harian', 'makan 3x/hari', 'kaos panitia', 'sertifikat resmi'],
        ]);

        EventPosition::create([
            'event_id' => $this->pendingEvent->id,
            'name' => 'Liaison Officer',
            'description' => 'Mendampingi tamu VIP dan pengisi acara.',
            'requirements' => 'Fasih berbahasa Inggris.',
            'quota' => 8,
        ]);

        EventPosition::create([
            'event_id' => $this->pendingEvent->id,
            'name' => 'Usher',
            'description' => 'Mengarahkan pengunjung ke aula.',
            'requirements' => 'Ramah dan berpenampilan rapi.',
            'quota' => 15,
        ]);
    }

    public function test_admin_can_access_review_events_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.review-events.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.review-event.index');
        $response->assertSee('Review Event');
        $response->assertSee('Indonesia Comic & Pop Culture Con 2025');
        $response->assertSee('PT Pop Media Nusantara');
        $response->assertSee('Pengajuan Menunggu');
        $response->assertSee('Disetujui Bulan Ini');
    }

    public function test_singular_and_alias_routes_work()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.review-event.index'));
        $response->assertStatus(200);

        $response2 = $this->actingAs($this->admin)->get(route('admin.review-events'));
        $response2->assertStatus(200);
    }

    public function test_non_admin_cannot_access_review_events()
    {
        $volunteer = User::factory()->create(['role' => 'volunteer']);

        $response = $this->actingAs($volunteer)->get(route('admin.review-events.index'));
        $response->assertStatus(403);

        $response2 = $this->actingAs($this->eoUser)->get(route('admin.review-events.index'));
        $response2->assertStatus(403);
    }

    public function test_search_and_filter_review_events()
    {
        // Search by event title
        $response = $this->actingAs($this->admin)->get(route('admin.review-events.index', [
            'search' => 'Comic & Pop',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Indonesia Comic & Pop Culture Con 2025');

        // Search with non-matching keyword
        $responseEmpty = $this->actingAs($this->admin)->get(route('admin.review-events.index', [
            'search' => 'TidakAdaEventIni123',
        ]));
        $responseEmpty->assertStatus(200);
        $responseEmpty->assertDontSee('Indonesia Comic & Pop Culture Con 2025');
        $responseEmpty->assertSee('Tidak ada antrean pengajuan');

        // Filter status pending vs active
        $responseStatus = $this->actingAs($this->admin)->get(route('admin.review-events.index', [
            'status' => 'active',
        ]));
        $responseStatus->assertStatus(200);
        $responseStatus->assertDontSee('Indonesia Comic & Pop Culture Con 2025');
    }

    public function test_show_endpoint_returns_json_details()
    {
        $response = $this->actingAs($this->admin)->getJson(route('admin.review-events.show', $this->pendingEvent));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $this->pendingEvent->id,
            'title' => 'Indonesia Comic & Pop Culture Con 2025',
            'status' => 'pending_review',
            'status_label' => 'Menunggu Review',
            'category' => 'MICE',
            'organizer' => [
                'name' => 'PT Pop Media Nusantara',
            ],
            'total_quota' => 23,
        ]);
        $this->assertCount(2, $response->json('positions'));
    }

    public function test_admin_can_approve_event()
    {
        Notification::fake();

        $response = $this->actingAs($this->admin)->post(route('admin.review-events.approve', $this->pendingEvent));

        $response->assertRedirect(route('admin.review-events.index'));
        $response->assertSessionHas('success');

        $this->pendingEvent->refresh();
        $this->assertEquals(EventStatus::AwaitingPayment, $this->pendingEvent->status);
        $this->assertEquals($this->admin->id, $this->pendingEvent->reviewed_by);
        $this->assertNotNull($this->pendingEvent->reviewed_at);

        Notification::assertSentTo(
            $this->eoUser,
            EventApprovedNotification::class,
            function (EventApprovedNotification $notification) {
                return $notification->event->id === $this->pendingEvent->id;
            }
        );
    }

    public function test_admin_can_reject_event_with_alasan()
    {
        Notification::fake();

        $response = $this->actingAs($this->admin)->post(route('admin.review-events.reject', $this->pendingEvent), [
            'alasan' => 'Rincian deskripsi kegiatan kurang jelas dan surat izin belum terlampir.',
        ]);

        $response->assertRedirect(route('admin.review-events.index'));
        $response->assertSessionHas('success');

        $this->pendingEvent->refresh();
        $this->assertEquals(EventStatus::Rejected, $this->pendingEvent->status);
        $this->assertEquals('Rincian deskripsi kegiatan kurang jelas dan surat izin belum terlampir.', $this->pendingEvent->rejection_reason);
        $this->assertEquals($this->admin->id, $this->pendingEvent->reviewed_by);

        Notification::assertSentTo(
            $this->eoUser,
            EventRejectedNotification::class,
            function (EventRejectedNotification $notification) {
                return $notification->event->id === $this->pendingEvent->id
                    && $notification->reason === 'Rincian deskripsi kegiatan kurang jelas dan surat izin belum terlampir.';
            }
        );
    }

    public function test_admin_cannot_reject_event_without_alasan()
    {
        Notification::fake();

        $response = $this->actingAs($this->admin)->post(route('admin.review-events.reject', $this->pendingEvent), [
            'alasan' => '',
        ]);

        $response->assertSessionHasErrors('alasan');

        $this->pendingEvent->refresh();
        $this->assertEquals(EventStatus::PendingReview, $this->pendingEvent->status);

        Notification::assertNothingSent();
    }

    public function test_review_events_index_hides_non_curation_statuses()
    {
        // Buat event dengan status di luar tahap kurasi
        $draftEvent = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event Draft Rahasia',
            'status' => EventStatus::Draft,
        ]);

        $finishedEvent = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event Sudah Selesai',
            'status' => EventStatus::Finished,
        ]);

        // 1. Uji akses antrean default (tanpa parameter filter)
        $response = $this->actingAs($this->admin)->get(route('admin.review-events.index'));
        $response->assertStatus(200);
        $response->assertSee('Indonesia Comic & Pop Culture Con 2025'); // Event pending harus muncul
        $response->assertDontSee('Event Draft Rahasia');
        $response->assertDontSee('Event Sudah Selesai');

        // 2. Uji akses dengan filter "Semua Status" (status=all)
        $responseAll = $this->actingAs($this->admin)->get(route('admin.review-events.index', ['status' => 'all']));
        $responseAll->assertStatus(200);
        $responseAll->assertSee('Indonesia Comic & Pop Culture Con 2025');
        $responseAll->assertDontSee('Event Draft Rahasia');
        $responseAll->assertDontSee('Event Sudah Selesai');
    }

    public function test_default_filter_is_all_and_reset_button_behavior()
    {
        // Active event
        $activeEvent = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Active Event Ready',
            'status' => EventStatus::Active,
        ]);

        // 1. Tanpa query string ?status= -> default all, munculkan event pending dan active
        $responseDefault = $this->actingAs($this->admin)->get(route('admin.review-events.index'));
        $responseDefault->assertStatus(200);
        $responseDefault->assertSee('Indonesia Comic & Pop Culture Con 2025');
        $responseDefault->assertSee('Active Event Ready');
        // Reset button harus tersembunyi
        $responseDefault->assertDontSee('<span>Reset</span>', false);

        // 2. Dengan status=all dan search kosong -> Reset button tetap tersembunyi
        $responseAll = $this->actingAs($this->admin)->get(route('admin.review-events.index', ['status' => 'all']));
        $responseAll->assertStatus(200);
        $responseAll->assertDontSee('<span>Reset</span>', false);

        // 3. Dengan status=pending_review -> Reset button harus muncul
        $responsePending = $this->actingAs($this->admin)->get(route('admin.review-events.index', ['status' => 'pending_review']));
        $responsePending->assertStatus(200);
        $responsePending->assertSee('<span>Reset</span>', false);

        // 4. Dengan search aktif -> Reset button harus muncul
        $responseSearch = $this->actingAs($this->admin)->get(route('admin.review-events.index', ['search' => 'Comic']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('<span>Reset</span>', false);
    }

    public function test_pagination_is_five_items_per_page_with_query_string()
    {
        Event::factory()->count(6)->create([
            'organizer_id' => $this->organizer->id,
            'status' => EventStatus::PendingReview,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.review-events.index', [
            'status' => 'pending_review',
        ]));
        $response->assertStatus(200);

        /** @var LengthAwarePaginator $events */
        $events = $response->viewData('events');
        $this->assertEquals(5, $events->perPage());
        $this->assertEquals(7, $events->total()); // 1 pending di setUp + 6 baru
        $this->assertCount(5, $events->items());
    }

    public function test_poster_url_synchronization_uses_valid_event_poster_url()
    {
        $eventWithPoster = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Senam Pagi Bersama',
            'poster_path' => 'posters/senam-sample.jpg',
            'status' => EventStatus::PendingReview,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.review-events.index', [
            'selected' => $eventWithPoster->id,
        ]));
        $response->assertStatus(200);

        $selectedEvent = $response->viewData('selectedEvent');
        $this->assertNotNull($selectedEvent);
        $this->assertTrue($selectedEvent['has_poster']);
        $this->assertEquals($eventWithPoster->poster_url, $selectedEvent['poster_url']);
        $this->assertStringNotContainsString('/storage/posters/senam-sample.jpg', $selectedEvent['poster_url']);

        // Uji endpoint show JSON
        $showResponse = $this->actingAs($this->admin)->getJson(route('admin.review-events.show', $eventWithPoster));
        $showResponse->assertStatus(200);
        $showResponse->assertJson([
            'has_poster' => true,
            'poster_url' => $eventWithPoster->poster_url,
        ]);
    }
}
