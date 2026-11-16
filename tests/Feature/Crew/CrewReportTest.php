<?php

namespace Tests\Feature\Crew;

use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Event;
use App\Models\OrganizerProfile;
use App\Models\Report;
use App\Models\User;
use App\Models\VolunteerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CrewReportTest extends TestCase
{
    use RefreshDatabase;

    private function createVolunteer(array $userAttributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => UserRole::Volunteer,
            'status' => UserStatus::Active,
        ], $userAttributes));

        VolunteerProfile::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    private function createEoWithEvent(): array
    {
        $eoUser = User::factory()->create([
            'role' => UserRole::Eo,
            'status' => UserStatus::Active,
        ]);

        $organizer = OrganizerProfile::factory()->verified()->create([
            'user_id' => $eoUser->id,
        ]);

        $event = Event::factory()->active()->create([
            'organizer_id' => $organizer->id,
        ]);

        return [$eoUser, $organizer, $event];
    }

    public function test_crew_can_submit_report_against_eo_for_an_event(): void
    {
        Storage::fake('public');

        $crew = $this->createVolunteer();
        [$eoUser, $organizer, $event] = $this->createEoWithEvent();

        $file = UploadedFile::fake()->create('bukti.pdf', 500, 'application/pdf');

        $response = $this->actingAs($crew)->post(route('crew.reports.store'), [
            'event_id' => $event->id,
            'reason' => 'Penipuan / Ketidaksesuaian Informasi Event',
            'description' => 'Event tidak menyediakan konsumsi dan akomodasi seperti yang dijanjikan pada deskripsi.',
            'evidence' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'reporter_id' => $crew->id,
            'reported_id' => $eoUser->id,
            'reason' => 'Penipuan / Ketidaksesuaian Informasi Event',
            'status' => ReportStatus::Pending->value,
        ]);

        $report = Report::where('reporter_id', $crew->id)->first();
        $this->assertNotNull($report);
        $this->assertStringStartsWith('REP-', $report->ticket_number);
        $this->assertStringContainsString($event->title, $report->description);
        $this->assertNotNull($report->evidence_path);
        Storage::disk('public')->assertExists($report->evidence_path);
    }

    public function test_crew_cannot_submit_report_without_required_fields(): void
    {
        $crew = $this->createVolunteer();

        $response = $this->actingAs($crew)->post(route('crew.reports.store'), [
            'reason' => '',
            'description' => 'Pendek',
        ]);

        $response->assertSessionHasErrors(['reason', 'description']);
    }

    public function test_crew_cannot_report_themselves(): void
    {
        $crew = $this->createVolunteer();

        $response = $this->actingAs($crew)->post(route('crew.reports.store'), [
            'reported_id' => $crew->id,
            'reason' => 'Lainnya',
            'description' => 'Mencoba melaporkan diri sendiri dengan sengaja untuk uji sistem.',
        ]);

        $response->assertSessionHasErrors(['reported_id']);
    }

    public function test_suspended_crew_is_automatically_logged_out_by_middleware(): void
    {
        $crew = $this->createVolunteer(['status' => UserStatus::Suspended]);

        $response = $this->actingAs($crew)->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
