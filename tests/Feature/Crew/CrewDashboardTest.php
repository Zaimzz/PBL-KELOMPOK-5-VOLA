<?php

namespace Tests\Feature\Crew;

use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPosition;
use App\Models\OrganizerProfile;
use App\Models\User;
use App\Models\VolunteerProfile;
use Database\Seeders\EventCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrewDashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0: User, 1: VolunteerProfile} */
    private function volunteer(): array
    {
        $user = User::factory()->create(['role' => UserRole::Volunteer]);
        $profile = VolunteerProfile::factory()->create(['user_id' => $user->id]);

        return [$user, $profile];
    }

    private function openEvent(array $attributes = []): Event
    {
        return Event::factory()->active()->create(array_merge([
            'organizer_id' => OrganizerProfile::factory()->verified()->create()->id,
            'registration_deadline' => today()->addDays(10),
        ], $attributes));
    }

    public function test_dashboard_shows_only_own_application_counts(): void
    {
        [$user, $profile] = $this->volunteer();
        [, $otherProfile] = $this->volunteer();

        $positions = EventPosition::factory()->count(4)->create([
            'event_id' => $this->openEvent()->id,
        ]);

        Application::factory()->create(['volunteer_profile_id' => $profile->id, 'position_id' => $positions[0]->id, 'status' => 'pending']);
        Application::factory()->create(['volunteer_profile_id' => $profile->id, 'position_id' => $positions[1]->id, 'status' => 'pending']);
        Application::factory()->create(['volunteer_profile_id' => $profile->id, 'position_id' => $positions[2]->id, 'status' => 'accepted']);

        // Lamaran volunteer lain tidak boleh ikut terhitung
        Application::factory()->create(['volunteer_profile_id' => $otherProfile->id, 'position_id' => $positions[0]->id, 'status' => 'accepted']);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertViewHas('pendingReviewCount', 2)
            ->assertViewHas('acceptedCount', 1)
            ->assertViewHas('activeApplicationsCount', 3);
    }

    public function test_dashboard_shows_zero_when_volunteer_has_no_applications(): void
    {
        [$user] = $this->volunteer();

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertViewHas('activeApplicationsCount', 0)
            ->assertViewHas('pendingReviewCount', 0)
            ->assertViewHas('acceptedCount', 0);
    }

    public function test_dashboard_lists_only_open_events(): void
    {
        [$user] = $this->volunteer();

        $this->openEvent(['title' => 'Acara Terbuka Uji']);
        $this->openEvent(['title' => 'Acara Kedaluwarsa Uji', 'registration_deadline' => today()->subDay()]);
        Event::factory()->create([
            'organizer_id' => OrganizerProfile::factory()->verified()->create()->id,
            'title' => 'Acara Draft Uji',
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Acara Terbuka Uji')
            ->assertDontSee('Acara Kedaluwarsa Uji')
            ->assertDontSee('Acara Draft Uji');
    }

    public function test_dashboard_can_filter_events_by_category(): void
    {
        [$user] = $this->volunteer();

        $this->seed(EventCategorySeeder::class);
        $festival = EventCategory::where('name', 'Festival')->firstOrFail();
        $seminar = EventCategory::where('name', 'Seminar')->firstOrFail();

        $this->openEvent(['title' => 'Acara Festival Uji', 'category_id' => $festival->id]);
        $this->openEvent(['title' => 'Acara Seminar Uji', 'category_id' => $seminar->id]);

        $this->actingAs($user)->get('/dashboard?category='.$festival->slug)
            ->assertOk()
            ->assertSee('Acara Festival Uji')
            ->assertDontSee('Acara Seminar Uji');
    }

    public function test_eo_and_guest_cannot_access_crew_dashboard(): void
    {
        $eo = User::factory()->create(['role' => UserRole::Eo]);

        $this->actingAs($eo)->get('/dashboard')->assertForbidden();

        auth()->logout();

        $this->get('/dashboard')->assertRedirect('/login');
    }
}