<?php

namespace Tests\Feature\Crew;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Application;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPosition;
use App\Models\OrganizerProfile;
use App\Models\User;
use App\Models\VolunteerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    private function createVolunteer(array $userAttributes = []): array
    {
        $user = User::factory()->create(array_merge([
            'role' => UserRole::Volunteer,
            'status' => UserStatus::Active,
        ], $userAttributes));

        $profile = VolunteerProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        return [$user, $profile];
    }

    private function createEventWithPosition(): array
    {
        $category = EventCategory::first() ?? EventCategory::factory()->create();

        $eo = User::factory()->create([
            'role' => UserRole::Eo,
            'status' => UserStatus::Active,
        ]);

        $organizer = OrganizerProfile::factory()->verified()->create([
            'user_id' => $eo->id,
            'organization_name' => 'Komunitas Kreatif Indonesia',
        ]);

        $event = Event::factory()->active()->create([
            'organizer_id' => $organizer->id,
            'category_id' => $category->id,
            'title' => 'Festival Komunitas Nusantara 2026',
            'location' => 'Balai Kartini',
            'city' => 'Jakarta',
        ]);

        $position = EventPosition::factory()->create([
            'event_id' => $event->id,
            'name' => 'Liaison Officer (LO)',
            'quota' => 5,
        ]);

        return [$event, $position];
    }

    public function test_crew_can_view_own_applications_from_database(): void
    {
        [$user, $profile] = $this->createVolunteer();
        [$event, $position] = $this->createEventWithPosition();

        Application::factory()->create([
            'volunteer_profile_id' => $profile->id,
            'event_id' => $event->id,
            'position_id' => $position->id,
            'status' => ApplicationStatus::Pending,
            'cover_letter' => 'Saya sangat bersemangat untuk bergabung sebagai volunteer pada acara ini.',
        ]);

        $response = $this->actingAs($user)->get(route('applications.index'));

        $response->assertOk();
        $response->assertSee('Festival Komunitas Nusantara 2026');
        $response->assertSee('Liaison Officer (LO)');
        $response->assertSee('Komunitas Kreatif Indonesia');
        $response->assertSee('Menunggu Seleksi');
    }

    public function test_crew_does_not_see_applications_of_other_volunteers(): void
    {
        [$user, $profile] = $this->createVolunteer();
        [$otherUser, $otherProfile] = $this->createVolunteer();
        [$event, $position] = $this->createEventWithPosition();

        Application::factory()->create([
            'volunteer_profile_id' => $otherProfile->id,
            'event_id' => $event->id,
            'position_id' => $position->id,
            'status' => ApplicationStatus::Pending,
        ]);

        $response = $this->actingAs($user)->get(route('applications.index'));

        $response->assertOk();
        $response->assertDontSee('Festival Komunitas Nusantara 2026');
        $response->assertSee('Belum Ada Lamaran');
    }

    public function test_crew_can_submit_application_to_position(): void
    {
        [$user, $profile] = $this->createVolunteer();
        [$event, $position] = $this->createEventWithPosition();

        $response = $this->actingAs($user)->postJson(route('applications.store'), [
            'position_id' => $position->id,
            'motivation' => 'Saya memiliki pengalaman memimpin divisi acara dan ingin berkontribusi di event ini.',
            'experience' => 'Pernah menjadi LO di 2 event kampus.',
            'availability' => 'Bisa hadir penuh selama 3 hari acara.',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('applications', [
            'volunteer_profile_id' => $profile->id,
            'position_id' => $position->id,
            'status' => 'pending',
        ]);
    }

    public function test_crew_cannot_apply_twice_for_same_position(): void
    {
        [$user, $profile] = $this->createVolunteer();
        [$event, $position] = $this->createEventWithPosition();

        Application::factory()->create([
            'volunteer_profile_id' => $profile->id,
            'event_id' => $event->id,
            'position_id' => $position->id,
            'status' => ApplicationStatus::Pending,
        ]);

        $response = $this->actingAs($user)->postJson(route('applications.store'), [
            'position_id' => $position->id,
            'motivation' => 'Saya ingin mengajukan lagi untuk posisi ini.',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Anda sudah mengajukan lamaran untuk posisi ini.',
        ]);
    }

    public function test_applications_page_shows_only_view_event_detail_for_pending_application(): void
    {
        [$user, $profile] = $this->createVolunteer();
        [$event, $position] = $this->createEventWithPosition();

        Application::factory()->create([
            'volunteer_profile_id' => $profile->id,
            'event_id' => $event->id,
            'position_id' => $position->id,
            'status' => ApplicationStatus::Pending,
        ]);

        $response = $this->actingAs($user)->get(route('applications.index'));

        $response->assertOk();
        $response->assertSee('Lihat Detail Event');
        $response->assertDontSee('Unduh Bukti Penerimaan');
        $response->assertDontSee('Batalkan Lamaran');
    }

    public function test_applications_page_shows_download_acceptance_and_view_event_detail_when_accepted(): void
    {
        [$user, $profile] = $this->createVolunteer();
        [$event, $position] = $this->createEventWithPosition();

        Application::factory()->create([
            'volunteer_profile_id' => $profile->id,
            'event_id' => $event->id,
            'position_id' => $position->id,
            'status' => ApplicationStatus::Accepted,
        ]);

        $response = $this->actingAs($user)->get(route('applications.index'));

        $response->assertOk();
        $response->assertSee('Lihat Detail Event');
        $response->assertSee('Unduh Bukti Penerimaan');
        $response->assertDontSee('Batalkan Lamaran');
    }

    public function test_crew_can_download_acceptance_letter_when_accepted(): void
    {
        [$user, $profile] = $this->createVolunteer();
        [$event, $position] = $this->createEventWithPosition();

        $application = Application::factory()->create([
            'volunteer_profile_id' => $profile->id,
            'event_id' => $event->id,
            'position_id' => $position->id,
            'status' => ApplicationStatus::Accepted,
            'applied_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('applications.download-acceptance', $application->id));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_crew_cannot_download_acceptance_letter_when_pending(): void
    {
        [$user, $profile] = $this->createVolunteer();
        [$event, $position] = $this->createEventWithPosition();

        $application = Application::factory()->create([
            'volunteer_profile_id' => $profile->id,
            'event_id' => $event->id,
            'position_id' => $position->id,
            'status' => ApplicationStatus::Pending,
        ]);

        $response = $this->actingAs($user)->get(route('applications.download-acceptance', $application->id));

        $response->assertSessionHasErrors('error');
    }
}
