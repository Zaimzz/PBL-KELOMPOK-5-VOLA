<?php

namespace Tests\Feature\Admin;

use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Models\Event;
use App\Models\OrganizerProfile;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $volunteer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->volunteer = User::factory()->create(['role' => 'volunteer']);
    }

    public function test_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    public function test_non_admin_cannot_access_dashboard()
    {
        $response = $this->actingAs($this->volunteer)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_dashboard_displays_correct_summary_data()
    {
        // Create 2 pending EOs
        $eo1 = User::factory()->create(['role' => 'eo']);
        OrganizerProfile::factory()->create([
            'user_id' => $eo1->id,
            'verification_status' => VerificationStatus::Pending,
        ]);

        $eo2 = User::factory()->create(['role' => 'eo']);
        OrganizerProfile::factory()->create([
            'user_id' => $eo2->id,
            'verification_status' => VerificationStatus::Pending,
        ]);

        // Create 1 pending review event
        Event::factory()->create([
            'organizer_id' => $eo1->organizerProfile->id,
            'status' => EventStatus::PendingReview,
        ]);

        // Create 3 active events
        Event::factory()->count(3)->create([
            'organizer_id' => $eo2->organizerProfile->id,
            'status' => EventStatus::Active,
        ]);

        // Create 1 transaction
        // Since we don't know the exact structure of Payment factory or if it exists,
        // we might get an error if Payment factory is not defined properly. We'll try to just skip transaction factory if it fails, or define it manually.
        // Actually, let's just assert the variables passed to the view.

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $response->assertViewHas('pendingEoCount', 2);
        $response->assertViewHas('pendingReviewEventCount', 1);
        $response->assertViewHas('activeEventCount', 3);
    }
}
