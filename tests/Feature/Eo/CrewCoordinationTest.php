<?php

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Eo\CrewCoordinationController;
use App\Models\Application;
use App\Models\Event;
use App\Models\EventPosition;
use App\Models\OrganizerProfile;
use App\Models\User;
use App\Models\VolunteerProfile;

// ============================================================
// Helpers
// ============================================================

function makeCrewEoWithEvent(): array
{
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->active()->for($profile, 'organizer')->create();
    $position = EventPosition::factory()->for($event)->create(['quota' => 5]);

    return [$eo, $profile, $event, $position];
}

function makeCrewVolunteer(?string $phone = null): array
{
    $volunteer = User::factory()->volunteer()->create(['phone' => $phone]);
    $volunteerProfile = VolunteerProfile::factory()->create(['user_id' => $volunteer->id]);

    return [$volunteer, $volunteerProfile];
}

function acceptApplication(VolunteerProfile $vp, EventPosition $position): Application
{
    return Application::factory()->accepted()->create([
        'volunteer_profile_id' => $vp->id,
        'position_id' => $position->id,
    ]);
}

// ============================================================
// 1. Access Control
// ============================================================

test('guest tidak bisa membuka halaman koordinasi crew', function () {
    $this->get(route('eo.crew.index'))
        ->assertRedirect(route('login'));
});

test('volunteer tidak bisa membuka halaman koordinasi crew', function () {
    $volunteer = User::factory()->volunteer()->create();

    $this->actingAs($volunteer)
        ->get(route('eo.crew.index'))
        ->assertForbidden();
});

test('eo tanpa profil melihat pesan profil diperlukan', function () {
    $eo = User::factory()->eo()->create();

    $this->actingAs($eo)
        ->get(route('eo.crew.index'))
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
        ->get(route('eo.crew.index'))
        ->assertOk()
        ->assertSee('Belum Ada Event');
});

// ============================================================
// 3. Event ada tapi belum ada crew diterima
// ============================================================

test('eo dengan event tapi tanpa crew diterima melihat empty state', function () {
    [$eo] = makeCrewEoWithEvent();

    $this->actingAs($eo)
        ->get(route('eo.crew.index'))
        ->assertOk()
        ->assertSee('Belum Ada Crew yang Diterima');
});

// ============================================================
// 4. Volunteer pending tidak muncul di koordinasi crew
// ============================================================

test('volunteer berstatus pending tidak muncul di koordinasi crew', function () {
    [$eo, , , $position] = makeCrewEoWithEvent();
    [, $vp] = makeCrewVolunteer();

    Application::factory()->create([
        'volunteer_profile_id' => $vp->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending,
    ]);

    $this->actingAs($eo)
        ->get(route('eo.crew.index'))
        ->assertOk()
        ->assertDontSee($vp->user->name);
});

// ============================================================
// 5. Volunteer diterima muncul di koordinasi crew
// ============================================================

test('volunteer yang diterima muncul di koordinasi crew', function () {
    [$eo, , , $position] = makeCrewEoWithEvent();
    [, $vp] = makeCrewVolunteer();

    acceptApplication($vp, $position);

    $this->actingAs($eo)
        ->get(route('eo.crew.index'))
        ->assertOk()
        ->assertSee($vp->user->name);
});

// ============================================================
// 6. Volunteer ditolak tidak muncul di koordinasi crew
// ============================================================

test('volunteer yang ditolak tidak muncul di koordinasi crew', function () {
    [$eo, , , $position] = makeCrewEoWithEvent();
    [, $vp] = makeCrewVolunteer();

    Application::factory()->rejected()->create([
        'volunteer_profile_id' => $vp->id,
        'position_id' => $position->id,
    ]);

    $this->actingAs($eo)
        ->get(route('eo.crew.index'))
        ->assertOk()
        ->assertDontSee($vp->user->name);
});

// ============================================================
// 7. Data dikelompokkan berdasarkan posisi
// ============================================================

test('crew ditampilkan berkelompok berdasarkan posisi', function () {
    [$eo, , $event] = makeCrewEoWithEvent();

    $posA = EventPosition::factory()->for($event)->create(['name' => 'Divisi Alpha', 'quota' => 5]);
    $posB = EventPosition::factory()->for($event)->create(['name' => 'Divisi Beta', 'quota' => 5]);

    [, $vp1] = makeCrewVolunteer();
    [, $vp2] = makeCrewVolunteer();

    acceptApplication($vp1, $posA);
    acceptApplication($vp2, $posB);

    $this->actingAs($eo)
        ->get(route('eo.crew.index'))
        ->assertOk()
        ->assertSee('Divisi Alpha')
        ->assertSee('Divisi Beta');
});

// ============================================================
// 8. Filter divisi berjalan
// ============================================================

test('filter posisi hanya menampilkan crew dari posisi tersebut', function () {
    [$eo, , $event] = makeCrewEoWithEvent();

    $posA = EventPosition::factory()->for($event)->create(['name' => 'Usher', 'quota' => 5]);
    $posB = EventPosition::factory()->for($event)->create(['name' => 'LO', 'quota' => 5]);

    [, $vp1] = makeCrewVolunteer();
    [, $vp2] = makeCrewVolunteer();

    acceptApplication($vp1, $posA);
    acceptApplication($vp2, $posB);

    $this->actingAs($eo)
        ->get(route('eo.crew.index', ['position_id' => $posA->id]))
        ->assertOk()
        ->assertSee($vp1->user->name)
        ->assertDontSee($vp2->user->name);
});

// ============================================================
// 9. Statistik total crew sesuai database
// ============================================================

test('statistik total crew sesuai dengan data database', function () {
    [$eo, , , $position] = makeCrewEoWithEvent();

    [, $vp1] = makeCrewVolunteer();
    [, $vp2] = makeCrewVolunteer();
    [, $vp3] = makeCrewVolunteer();

    acceptApplication($vp1, $position);
    acceptApplication($vp2, $position);
    // vp3 pending — should not count
    Application::factory()->create([
        'volunteer_profile_id' => $vp3->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending,
    ]);

    $this->actingAs($eo)
        ->get(route('eo.crew.index'))
        ->assertOk()
        ->assertViewHas('totalAccepted', 2);
});

// ============================================================
// 10. Perhitungan sisa kuota
// ============================================================

test('sisa kuota dihitung dengan benar', function () {
    // Create EO with event but NO positions yet (use fresh event without default position)
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->active()->for($profile, 'organizer')->create();

    // Single position: quota 3, 1 accepted → 2 remaining → openQuotaCount = 1
    $position = EventPosition::factory()->for($event)->create(['quota' => 3]);
    [, $vp] = makeCrewVolunteer();
    acceptApplication($vp, $position);

    $this->actingAs($eo)
        ->get(route('eo.crew.index'))
        ->assertOk()
        ->assertViewHas('openQuotaCount', 1);
});

test('posisi yang kuotanya terpenuhi tidak dihitung dalam sisa kuota', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->active()->for($profile, 'organizer')->create();

    // Single position: quota 1, 1 accepted → 0 remaining → NOT open
    $position = EventPosition::factory()->for($event)->create(['quota' => 1]);
    [, $vp] = makeCrewVolunteer();
    acceptApplication($vp, $position);

    $this->actingAs($eo)
        ->get(route('eo.crew.index'))
        ->assertOk()
        ->assertViewHas('openQuotaCount', 0);
});

// ============================================================
// 11. Nomor WhatsApp: normalizePhone helper
// ============================================================

test('normalisasi nomor telepon format 08xx menjadi 628xx', function () {
    expect(CrewCoordinationController::normalizePhone('081234567890'))->toBe('6281234567890');
});

test('normalisasi nomor telepon format +628xx menjadi 628xx', function () {
    expect(CrewCoordinationController::normalizePhone('+6281234567890'))->toBe('6281234567890');
});

test('nomor telepon kosong menghasilkan string kosong', function () {
    expect(CrewCoordinationController::normalizePhone(''))->toBe('');
});

test('normalisasi nomor dengan spasi dan tanda hubung', function () {
    expect(CrewCoordinationController::normalizePhone('+62 812-3456-7890'))->toBe('6281234567890');
});

// ============================================================
// 12. WhatsApp URL terbentuk dengan benar
// ============================================================

test('url whatsapp terbentuk dengan benar menggunakan wa.me', function () {
    $url = CrewCoordinationController::buildWhatsAppUrl('081234567890', 'Budi', 'Event Keren');

    expect($url)->toStartWith('https://wa.me/6281234567890?text=');
    expect($url)->toContain('Budi');
    expect($url)->toContain('Event+Keren');
});

test('url whatsapp kosong jika nomor tidak tersedia', function () {
    $url = CrewCoordinationController::buildWhatsAppUrl('', 'Budi', 'Event Keren');

    expect($url)->toBe('');
});

// ============================================================
// 13. Tombol WhatsApp di halaman: aktif / nonaktif
// ============================================================

test('tombol hubungi whatsapp aktif jika nomor tersedia', function () {
    [$eo, , , $position] = makeCrewEoWithEvent();
    [, $vp] = makeCrewVolunteer('081234567890');

    acceptApplication($vp, $position);

    $this->actingAs($eo)
        ->get(route('eo.crew.index'))
        ->assertOk()
        ->assertSee('Hubungi WhatsApp')
        ->assertSee('wa.me');
});

test('tombol hubungi whatsapp nonaktif jika nomor tidak tersedia', function () {
    [$eo, , , $position] = makeCrewEoWithEvent();
    [, $vp] = makeCrewVolunteer(null); // no phone

    acceptApplication($vp, $position);

    $this->actingAs($eo)
        ->get(route('eo.crew.index'))
        ->assertOk()
        ->assertSee('Nomor WhatsApp Belum Tersedia');
});

// ============================================================
// 14. EO tidak melihat data crew milik EO lain
// ============================================================

test('eo tidak melihat crew event milik eo lain', function () {
    [$eo] = makeCrewEoWithEvent();

    // Another EO with their own event and crew
    [, , , $otherPosition] = makeCrewEoWithEvent();
    [, $vp] = makeCrewVolunteer();
    acceptApplication($vp, $otherPosition);

    $this->actingAs($eo)
        ->get(route('eo.crew.index'))
        ->assertOk()
        ->assertDontSee($vp->user->name);
});

// ============================================================
// 15. Data kontak tidak dapat diakses publik
// ============================================================

test('guest tidak bisa mengakses halaman koordinasi crew', function () {
    $this->get(route('eo.crew.index'))
        ->assertRedirect(route('login'));
});

// ============================================================
// 16. Export CSV hanya untuk EO pemilik event
// ============================================================

test('eo bisa mengunduh csv crew event miliknya', function () {
    [$eo, , $event, $position] = makeCrewEoWithEvent();
    [, $vp] = makeCrewVolunteer('081234567890');
    acceptApplication($vp, $position);

    $response = $this->actingAs($eo)
        ->get(route('eo.crew.export', ['event_id' => $event->id]));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('guest tidak bisa mengunduh csv crew', function () {
    $this->get(route('eo.crew.export'))
        ->assertRedirect(route('login'));
});

test('volunteer tidak bisa mengunduh csv crew', function () {
    $volunteer = User::factory()->volunteer()->create();

    $this->actingAs($volunteer)
        ->get(route('eo.crew.export'))
        ->assertForbidden();
});

// ============================================================
// 17. Tidak ada fitur atau route grup WhatsApp
// ============================================================

test('tidak ada route pengelolaan grup whatsapp', function () {
    $routes = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
        ->map(fn ($r) => $r->getName())
        ->filter()
        ->values()
        ->toArray();

    // No route name should contain 'whatsapp' or 'group' in EO namespace
    $eoWhatsappRoutes = array_filter($routes, fn ($name) =>
        str_contains($name, 'eo.whatsapp') || str_contains($name, 'eo.group')
    );

    expect($eoWhatsappRoutes)->toBeEmpty();
});

// ============================================================
// 18. Empty state filter tampil saat filter tidak ada hasil
// ============================================================

test('empty state khusus filter tampil saat divisi tidak memiliki crew', function () {
    [$eo, , $event] = makeCrewEoWithEvent();

    $posA = EventPosition::factory()->for($event)->create(['quota' => 5]);
    $posB = EventPosition::factory()->for($event)->create(['quota' => 5]);

    [, $vp] = makeCrewVolunteer();
    acceptApplication($vp, $posA); // only posA has crew

    $this->actingAs($eo)
        ->get(route('eo.crew.index', ['position_id' => $posB->id]))
        ->assertOk()
        ->assertSee('Tidak Ada Crew pada Divisi Ini');
});

