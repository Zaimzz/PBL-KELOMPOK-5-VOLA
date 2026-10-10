<?php

namespace Tests\Feature\Admin;

use App\Enums\VerificationStatus;
use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EoVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $eo;

    private OrganizerProfile $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->eo = User::factory()->create(['role' => 'eo']);

        $this->profile = OrganizerProfile::factory()->create([
            'user_id' => $this->eo->id,
            'organization_name' => 'Organisasi Test',
            'pic_name' => 'Budi PIC',
            'verification_status' => VerificationStatus::Pending,
        ]);
    }

    public function test_admin_can_access_eo_verifications_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.eo-verifications'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.eo-verifications.index');
        $response->assertSee('Organisasi Test');
    }

    public function test_search_by_organization_name()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.eo-verifications', ['search' => 'Organisasi Test']));
        $response->assertSee('Organisasi Test');

        $response = $this->actingAs($this->admin)->get(route('admin.eo-verifications', ['search' => 'Tidak Ditemukan']));
        $response->assertDontSee('Budi PIC');
        $response->assertSee('Tidak ada data ditemukan');
    }

    public function test_filter_by_status()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.eo-verifications', ['status' => 'verified']));
        $response->assertDontSee('Organisasi Test');

        $response = $this->actingAs($this->admin)->get(route('admin.eo-verifications', ['status' => 'pending']));
        $response->assertSee('Organisasi Test');
    }

    public function test_admin_can_approve_eo()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.eo-verifications.approve', $this->profile));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->profile->refresh();
        $this->assertEquals(VerificationStatus::Verified, $this->profile->verification_status);
        $this->assertEquals($this->admin->id, $this->profile->verified_by);
        $this->assertNotNull($this->profile->verified_at);
    }

    public function test_admin_can_reject_eo_with_reason()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.eo-verifications.reject', $this->profile), [
            'rejection_reason' => 'Dokumen tidak valid',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->profile->refresh();
        $this->assertEquals(VerificationStatus::Rejected, $this->profile->verification_status);
        $this->assertEquals('Dokumen tidak valid', $this->profile->rejection_reason);
        $this->assertNull($this->profile->verified_by);
    }

    public function test_admin_cannot_reject_eo_without_reason()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.eo-verifications.reject', $this->profile), [
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors('rejection_reason');

        $this->profile->refresh();
        $this->assertEquals(VerificationStatus::Pending, $this->profile->verification_status);
    }

    public function test_non_admin_cannot_approve_or_reject()
    {
        $volunteer = User::factory()->create(['role' => 'volunteer']);

        $response = $this->actingAs($volunteer)->post(route('admin.eo-verifications.approve', $this->profile));
        $response->assertStatus(403);

        $response = $this->actingAs($volunteer)->post(route('admin.eo-verifications.reject', $this->profile), [
            'rejection_reason' => 'Tolak',
        ]);
        $response->assertStatus(403);
    }

    public function test_admin_can_download_document_if_exists()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $path = $file->store('organizer-documents', 'local');

        $this->profile->update(['document_path' => $path]);

        $response = $this->actingAs($this->admin)->get(route('admin.eo-verifications.document', $this->profile));
        $response->assertStatus(200);
    }

    public function test_download_document_returns_404_if_missing()
    {
        Storage::fake('local');
        $this->profile->update(['document_path' => 'not-exists.pdf']);

        $response = $this->actingAs($this->admin)->get(route('admin.eo-verifications.document', $this->profile));
        $response->assertStatus(404);
    }

    public function test_pagination_works()
    {
        // Setup creates 1 EO. Let's create 10 more to make it 11 total.
        $eos = User::factory()->count(10)->create(['role' => 'eo']);
        foreach ($eos as $user) {
            OrganizerProfile::factory()->create([
                'user_id' => $user->id,
                'verification_status' => VerificationStatus::Pending,
            ]);
        }

        // Page 1
        $response = $this->actingAs($this->admin)->get(route('admin.eo-verifications'));
        $response->assertStatus(200);
        $response->assertViewHas('organizers');
        $this->assertCount(5, $response->original->getData()['organizers']);

        // Page 2
        $response2 = $this->actingAs($this->admin)->get(route('admin.eo-verifications', ['page' => 2]));
        $response2->assertStatus(200);
        $this->assertCount(5, $response2->original->getData()['organizers']);

        // Page 3
        $response3 = $this->actingAs($this->admin)->get(route('admin.eo-verifications', ['page' => 3]));
        $response3->assertStatus(200);
        $this->assertCount(1, $response3->original->getData()['organizers']);
    }
}
