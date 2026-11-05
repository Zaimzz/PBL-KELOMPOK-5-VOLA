<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Event;
use App\Models\EventPosition;
use App\Models\OrganizerProfile;
use App\Models\User;
use App\Models\VolunteerProfile;
use App\Notifications\ApplicationDecided;
use Illuminate\Support\Facades\Notification;

// ============================================================
// Helpers
// ============================================================

function makeVerifiedEoWithEvent(): array
{
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->active()->for($profile, 'organizer')->create();
    $position = EventPosition::factory()->for($event)->create();

    return [$eo, $profile, $event, $position];
}

function makeVolunteerWithProfile(): array
{
    $volunteer = User::factory()->volunteer()->create();
    $volunteerProfile = VolunteerProfile::factory()->create(['user_id' => $volunteer->id]);

    return [$volunteer, $volunteerProfile];
}

// ============================================================
// 1. Access Control — siapa yang bisa membuka halaman
// ============================================================

test('guest tidak bisa membuka halaman pendaftar EO', function () {
    $this->get(route('eo.applicants.index'))
        ->assertRedirect(route('login'));
});

test('volunteer tidak bisa membuka halaman pendaftar EO', function () {
    $volunteer = User::factory()->volunteer()->create();

    $this->actingAs($volunteer)
        ->get(route('eo.applicants.index'))
        ->assertForbidden();
});

test('eo tanpa profil organisasi melihat pesan profil diperlukan', function () {
    $eo = User::factory()->eo()->create();

    $this->actingAs($eo)
        ->get(route('eo.applicants.index'))
        ->assertOk()
        ->assertSee('Profil Organisasi Diperlukan');
});

// ============================================================
// 2. EO belum memiliki event
// ============================================================

test('eo tanpa event melihat empty state event', function () {
    $eo = User::factory()->eo()->create();
    OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);

    $this->actingAs($eo)
        ->get(route('eo.applicants.index'))
        ->assertOk()
        ->assertSee('Belum Ada Event');
});

// ============================================================
// 3. EO memiliki event tapi belum ada pendaftar
// ============================================================

test('eo dengan event tapi tanpa pendaftar melihat empty state pendaftar', function () {
    [$eo] = makeVerifiedEoWithEvent();

    $this->actingAs($eo)
        ->get(route('eo.applicants.index'))
        ->assertOk()
        ->assertSee('Belum Ada Pendaftar');
});

// ============================================================
// 4. Pendaftar muncul otomatis setelah volunteer mendaftar
// ============================================================

test('pendaftar muncul di halaman EO setelah volunteer mendaftar', function () {
    [$eo, , , $position] = makeVerifiedEoWithEvent();
    [, $volunteerProfile] = makeVolunteerWithProfile();

    Application::factory()->create([
        'volunteer_profile_id' => $volunteerProfile->id,
        'position_id' => $position->id,
        'applied_at' => now(),
    ]);

    $this->actingAs($eo)
        ->get(route('eo.applicants.index'))
        ->assertOk()
        ->assertSee($volunteerProfile->user->name);
});

// ============================================================
// 5. EO tidak melihat pendaftar event milik EO lain
// ============================================================

test('eo tidak melihat pendaftar event milik EO lain', function () {
    [$eo] = makeVerifiedEoWithEvent();

    // EO lain dengan eventnya sendiri
    $otherEo = User::factory()->eo()->create();
    $otherProfile = OrganizerProfile::factory()->verified()->create(['user_id' => $otherEo->id]);
    $otherEvent = Event::factory()->active()->for($otherProfile, 'organizer')->create();
    $otherPosition = EventPosition::factory()->for($otherEvent)->create();

    [, $volunteerProfile] = makeVolunteerWithProfile();
    Application::factory()->create([
        'volunteer_profile_id' => $volunteerProfile->id,
        'position_id' => $otherPosition->id,
    ]);

    $this->actingAs($eo)
        ->get(route('eo.applicants.index'))
        ->assertOk()
        ->assertDontSee($volunteerProfile->user->name);
});

// ============================================================
// 6. Informasi event dan posisi ditampilkan benar
// ============================================================

test('informasi posisi dan event tampil benar di tabel pendaftar', function () {
    [$eo, , $event, $position] = makeVerifiedEoWithEvent();
    [, $volunteerProfile] = makeVolunteerWithProfile();

    Application::factory()->create([
        'volunteer_profile_id' => $volunteerProfile->id,
        'position_id' => $position->id,
    ]);

    $this->actingAs($eo)
        ->get(route('eo.applicants.index'))
        ->assertOk()
        ->assertSee($position->name)
        ->assertSee($event->title);
});

// ============================================================
// 7. Pencarian nama volunteer berjalan
// ============================================================

test('pencarian nama volunteer menampilkan hasil yang sesuai', function () {
    [$eo, , , $position] = makeVerifiedEoWithEvent();

    [, $vp1] = makeVolunteerWithProfile();
    [, $vp2] = makeVolunteerWithProfile();

    Application::factory()->create(['volunteer_profile_id' => $vp1->id, 'position_id' => $position->id]);
    Application::factory()->create(['volunteer_profile_id' => $vp2->id, 'position_id' => $position->id]);

    $this->actingAs($eo)
        ->get(route('eo.applicants.index', ['search' => $vp1->user->name]))
        ->assertOk()
        ->assertSee($vp1->user->name)
        ->assertDontSee($vp2->user->name);
});

// ============================================================
// 8. Filter posisi berjalan
// ============================================================

test('filter divisi menampilkan hanya pendaftar divisi tersebut', function () {
    [$eo, , $event] = makeVerifiedEoWithEvent();

    $positionA = EventPosition::factory()->for($event)->create(['name' => 'Divisi A']);
    $positionB = EventPosition::factory()->for($event)->create(['name' => 'Divisi B']);

    [, $vp1] = makeVolunteerWithProfile();
    [, $vp2] = makeVolunteerWithProfile();

    Application::factory()->create(['volunteer_profile_id' => $vp1->id, 'position_id' => $positionA->id]);
    Application::factory()->create(['volunteer_profile_id' => $vp2->id, 'position_id' => $positionB->id]);

    $this->actingAs($eo)
        ->get(route('eo.applicants.index', ['position_id' => $positionA->id]))
        ->assertOk()
        ->assertSee($vp1->user->name)
        ->assertDontSee($vp2->user->name);
});

// ============================================================
// 9. Filter status berjalan
// ============================================================

test('filter status menampilkan hanya pendaftar dengan status tersebut', function () {
    [$eo, , , $position] = makeVerifiedEoWithEvent();

    [, $vp1] = makeVolunteerWithProfile();
    [, $vp2] = makeVolunteerWithProfile();

    Application::factory()->create(['volunteer_profile_id' => $vp1->id, 'position_id' => $position->id, 'status' => ApplicationStatus::Pending]);
    Application::factory()->accepted()->create(['volunteer_profile_id' => $vp2->id, 'position_id' => $position->id]);

    $this->actingAs($eo)
        ->get(route('eo.applicants.index', ['status' => 'pending']))
        ->assertOk()
        ->assertSee($vp1->user->name)
        ->assertDontSee($vp2->user->name);
});

// ============================================================
// 10. EO berhasil menerima pendaftar
// ============================================================

test('eo berhasil menerima pendaftar', function () {
    Notification::fake();

    [$eo, , , $position] = makeVerifiedEoWithEvent();
    [, $volunteerProfile] = makeVolunteerWithProfile();

    $application = Application::factory()->create([
        'volunteer_profile_id' => $volunteerProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending,
    ]);

    $this->actingAs($eo)
        ->patch(route('eo.applicants.accept', $application))
        ->assertRedirect();

    expect($application->fresh()->status)->toBe(ApplicationStatus::Accepted);
    expect($application->fresh()->decided_at)->not->toBeNull();
});

// ============================================================
// 11. Notifikasi dikirim setelah diterima
// ============================================================

test('notifikasi dikirim ke volunteer setelah diterima', function () {
    Notification::fake();

    [$eo, , , $position] = makeVerifiedEoWithEvent();
    [$volunteer, $volunteerProfile] = makeVolunteerWithProfile();

    $application = Application::factory()->create([
        'volunteer_profile_id' => $volunteerProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending,
    ]);

    $this->actingAs($eo)
        ->patch(route('eo.applicants.accept', $application));

    Notification::assertSentTo($volunteer, ApplicationDecided::class, function ($notification) {
        return $notification->decision === 'accepted';
    });
});

// ============================================================
// 12. EO berhasil menolak pendaftar
// ============================================================

test('eo berhasil menolak pendaftar dengan alasan', function () {
    Notification::fake();

    [$eo, , , $position] = makeVerifiedEoWithEvent();
    [, $volunteerProfile] = makeVolunteerWithProfile();

    $application = Application::factory()->create([
        'volunteer_profile_id' => $volunteerProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending,
    ]);

    $this->actingAs($eo)
        ->patch(route('eo.applicants.reject', $application), [
            'decision_note' => 'Kuota posisi sudah terpenuhi.',
        ])
        ->assertRedirect();

    expect($application->fresh()->status)->toBe(ApplicationStatus::Rejected);
    expect($application->fresh()->decision_note)->toBe('Kuota posisi sudah terpenuhi.');
});

// ============================================================
// 13. Notifikasi dikirim setelah ditolak
// ============================================================

test('notifikasi dikirim ke volunteer setelah ditolak', function () {
    Notification::fake();

    [$eo, , , $position] = makeVerifiedEoWithEvent();
    [$volunteer, $volunteerProfile] = makeVolunteerWithProfile();

    $application = Application::factory()->create([
        'volunteer_profile_id' => $volunteerProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending,
    ]);

    $this->actingAs($eo)
        ->patch(route('eo.applicants.reject', $application));

    Notification::assertSentTo($volunteer, ApplicationDecided::class, function ($notification) {
        return $notification->decision === 'rejected';
    });
});

// ============================================================
// 14. EO tidak dapat menerima/menolak pendaftar EO lain
// ============================================================

test('eo tidak dapat menerima pendaftaran milik EO lain', function () {
    // EO pertama
    $eo = User::factory()->eo()->create();
    OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);

    // EO kedua dengan event dan pendaftar
    [, , , $position] = makeVerifiedEoWithEvent();
    [, $volunteerProfile] = makeVolunteerWithProfile();

    $application = Application::factory()->create([
        'volunteer_profile_id' => $volunteerProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending,
    ]);

    $this->actingAs($eo)
        ->patch(route('eo.applicants.accept', $application))
        ->assertForbidden();

    expect($application->fresh()->status)->toBe(ApplicationStatus::Pending);
});

test('eo tidak dapat menolak pendaftaran milik EO lain', function () {
    $eo = User::factory()->eo()->create();
    OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);

    [, , , $position] = makeVerifiedEoWithEvent();
    [, $volunteerProfile] = makeVolunteerWithProfile();

    $application = Application::factory()->create([
        'volunteer_profile_id' => $volunteerProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending,
    ]);

    $this->actingAs($eo)
        ->patch(route('eo.applicants.reject', $application))
        ->assertForbidden();

    expect($application->fresh()->status)->toBe(ApplicationStatus::Pending);
});

// ============================================================
// 15. Empty state muncul saat filter tidak menemukan data
// ============================================================

test('empty state filter tampil ketika pencarian tidak menemukan data', function () {
    [$eo, , , $position] = makeVerifiedEoWithEvent();
    [, $volunteerProfile] = makeVolunteerWithProfile();

    Application::factory()->create([
        'volunteer_profile_id' => $volunteerProfile->id,
        'position_id' => $position->id,
    ]);

    $this->actingAs($eo)
        ->get(route('eo.applicants.index', ['search' => 'nama_yang_tidak_ada_sama_sekali_xyzxyz']))
        ->assertOk()
        ->assertSee('Tidak Ada Hasil');
});

// ============================================================
// 16. Statistik kartu dihitung dari database
// ============================================================

test('statistik kartu dihitung benar dari database', function () {
    [$eo, , , $position] = makeVerifiedEoWithEvent();

    [, $vp1] = makeVolunteerWithProfile();
    [, $vp2] = makeVolunteerWithProfile();
    [, $vp3] = makeVolunteerWithProfile();

    Application::factory()->create(['volunteer_profile_id' => $vp1->id, 'position_id' => $position->id, 'status' => ApplicationStatus::Pending]);
    Application::factory()->accepted()->create(['volunteer_profile_id' => $vp2->id, 'position_id' => $position->id]);
    Application::factory()->rejected()->create(['volunteer_profile_id' => $vp3->id, 'position_id' => $position->id]);

    $this->actingAs($eo)
        ->get(route('eo.applicants.index'))
        ->assertOk()
        ->assertViewHas('totalCount', 3)
        ->assertViewHas('pendingCount', 1)
        ->assertViewHas('acceptedCount', 1);
});

// ============================================================
// 17. Guest tidak bisa POST ke accept/reject
// ============================================================

test('guest tidak bisa menerima atau menolak pendaftar', function () {
    [$eo, , , $position] = makeVerifiedEoWithEvent();
    [, $volunteerProfile] = makeVolunteerWithProfile();

    $application = Application::factory()->create([
        'volunteer_profile_id' => $volunteerProfile->id,
        'position_id' => $position->id,
    ]);

    $this->patch(route('eo.applicants.accept', $application))
        ->assertRedirect(route('login'));

    $this->patch(route('eo.applicants.reject', $application))
        ->assertRedirect(route('login'));
});

