<?php

namespace Tests\Feature\Crew;

use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Event;
use App\Models\EventPosition;
use App\Models\OrganizerProfile;
use App\Models\User;
use App\Models\VolunteerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CrewPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private function createVolunteer(): array
    {
        $user = User::factory()->create(['role' => UserRole::Volunteer]);
        $profile = VolunteerProfile::factory()->create(['user_id' => $user->id]);

        return [$user, $profile];
    }

    public function test_crew_pages_load_successfully(): void
    {
        [$user, $profile] = $this->createVolunteer();

        $routes = ['dashboard', 'search.index', 'applications.index', 'my-events.index', 'history.index', 'profile.show', 'settings.index'];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get(route($route));
            $response->assertOk();
        }
    }

    public function test_crew_layout_caches_application_counts(): void
    {
        [$user, $profile] = $this->createVolunteer();

        $event = Event::factory()->active()->create([
            'organizer_id' => OrganizerProfile::factory()->verified()->create()->id,
        ]);
        $position = EventPosition::factory()->create(['event_id' => $event->id]);

        Application::factory()->create([
            'volunteer_profile_id' => $profile->id,
            'position_id' => $position->id,
            'status' => 'accepted',
        ]);

        $this->actingAs($user)->get(route('dashboard'))->assertOk();

        $this->assertTrue(Cache::has("crew_nav_counts_{$profile->id}"));
        $cached = Cache::get("crew_nav_counts_{$profile->id}");
        $this->assertEquals(1, $cached['total']);
        $this->assertEquals(1, $cached['accepted']);

        $position2 = EventPosition::factory()->create(['event_id' => $event->id]);
        Application::factory()->create([
            'volunteer_profile_id' => $profile->id,
            'position_id' => $position2->id,
            'status' => 'pending',
        ]);

        $this->assertFalse(Cache::has("crew_nav_counts_{$profile->id}"));
    }

    public function test_history_page_aggregates_counts_correctly(): void
    {
        [$user, $profile] = $this->createVolunteer();

        $response = $this->actingAs($user)->get(route('history.index'));
        $response->assertOk();
        $response->assertViewHas('counts', [
            'all' => 0,
            'festival' => 0,
            'seminar' => 0,
            'sosial' => 0,
            'kampus' => 0,
            'komunitas' => 0,
            'olahraga' => 0,
            'musik' => 0,
        ]);
    }
}
