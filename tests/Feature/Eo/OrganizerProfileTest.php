<?php

use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Models\Event;
use App\Models\EventPosition;
use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// ---------------------------------------------------------------------------
// Helper: buat EO user + profile terverifikasi
// ---------------------------------------------------------------------------

function makeEoWithProfile(array $profileAttributes = []): User
{
    $eo = User::factory()->eo()->create();
    OrganizerProfile::factory()->create(array_merge(
        ['user_id' => $eo->id],
        $profileAttributes,
    ));

    return $eo;
}

// ===========================================================================
// AKSES / AUTHORIZATION
// ===========================================================================

test('guest tidak bisa membuka halaman profil organisasi', function () {
    $this->get(route('eo.organization'))
        ->assertRedirect(route('login'));
});

test('volunteer tidak bisa membuka halaman profil organisasi', function () {
    $volunteer = User::factory()->volunteer()->create();

    $this->actingAs($volunteer)
        ->get(route('eo.organization'))
        ->assertForbidden();
});

test('eo bisa membuka halaman profil organisasi miliknya', function () {
    $eo = makeEoWithProfile(['verification_status' => VerificationStatus::Verified]);

    $this->actingAs($eo)
        ->get(route('eo.organization'))
        ->assertOk()
        ->assertSee($eo->organizerProfile->organization_name);
});

test('eo tanpa profil melihat halaman profil kosong tanpa error', function () {
    $eo = User::factory()->eo()->create();

    $this->actingAs($eo)
        ->get(route('eo.organization'))
        ->assertOk()
        ->assertSee('Profil Organisasi Belum Diisi');
});

test('eo tidak bisa mengakses profil eo lain — tidak ada route untuk itu', function () {
    $eo1 = makeEoWithProfile();
    $eo2 = makeEoWithProfile();

    // Route hanya melayani profil milik user yang login — tidak ada ID di URL
    // Test ini memastikan eo1 hanya melihat data miliknya
    $response = $this->actingAs($eo1)
        ->get(route('eo.organization'))
        ->assertOk();

    $response->assertSee($eo1->organizerProfile->organization_name);
    $response->assertDontSee($eo2->organizerProfile->organization_name);
});

// ===========================================================================
// UPDATE PROFIL
// ===========================================================================

test('eo bisa menyimpan profil organisasi', function () {
    $eo = User::factory()->eo()->create();

    $this->actingAs($eo)
        ->put(route('eo.organization.update'), [
            'organization_name' => 'Komunitas Kopi Malang',
            'organization_type' => 'Komunitas',
            'description' => 'Komunitas pecinta kopi di Malang Raya.',
            'city' => 'Malang',
            'pic_name' => 'Budi Santoso',
            'pic_phone' => '08123456789',
            'social_link' => 'https://instagram.com/kopimalang',
        ])
        ->assertRedirect(route('eo.organization'));

    $this->assertDatabaseHas('organizer_profiles', [
        'user_id' => $eo->id,
        'organization_name' => 'Komunitas Kopi Malang',
        'city' => 'Malang',
    ]);
});

test('eo bisa memperbarui profil yang sudah ada', function () {
    $eo = makeEoWithProfile(['organization_name' => 'Nama Lama']);

    $this->actingAs($eo)
        ->put(route('eo.organization.update'), [
            'organization_name' => 'Nama Baru Updated',
            'organization_type' => 'Perusahaan',
            'description' => 'Deskripsi baru.',
            'city' => 'Surabaya',
            'pic_name' => 'Siti Rahma',
            'pic_phone' => '08199999999',
        ])
        ->assertRedirect(route('eo.organization'));

    $this->assertDatabaseHas('organizer_profiles', [
        'user_id' => $eo->id,
        'organization_name' => 'Nama Baru Updated',
        'city' => 'Surabaya',
    ]);
});

test('gagal update profil tanpa nama organisasi', function () {
    $eo = makeEoWithProfile();

    $this->actingAs($eo)
        ->put(route('eo.organization.update'), [
            'organization_name' => '',
            'organization_type' => 'Komunitas',
            'description' => 'Deskripsi.',
            'city' => 'Jakarta',
            'pic_name' => 'PIC Name',
            'pic_phone' => '08123456789',
        ])
        ->assertSessionHasErrors('organization_name');
});

// ===========================================================================
// UPLOAD DOKUMEN
// ===========================================================================

test('eo bisa upload dokumen legal yang valid (PDF)', function () {
    Storage::fake('local');

    $eo = User::factory()->eo()->create();

    $this->actingAs($eo)
        ->put(route('eo.organization.update'), [
            'organization_name' => 'Test Org',
            'organization_type' => 'Komunitas',
            'description' => 'Deskripsi.',
            'city' => 'Bandung',
            'pic_name' => 'PIC',
            'pic_phone' => '08123456789',
            'document' => UploadedFile::fake()->create('dokumen.pdf', 200, 'application/pdf'),
        ])
        ->assertRedirect(route('eo.organization'));

    $profile = $eo->fresh()->organizerProfile;
    $this->assertNotNull($profile->document_path);
    Storage::disk('local')->assertExists($profile->document_path);
});

test('eo bisa upload dokumen legal yang valid (JPG)', function () {
    Storage::fake('local');

    $eo = User::factory()->eo()->create();

    $this->actingAs($eo)
        ->put(route('eo.organization.update'), [
            'organization_name' => 'Test Org',
            'organization_type' => 'Komunitas',
            'description' => 'Deskripsi.',
            'city' => 'Bandung',
            'pic_name' => 'PIC',
            'pic_phone' => '08123456789',
            'document' => UploadedFile::fake()->create('dokumen.jpg', 200, 'image/jpeg'),
        ])
        ->assertRedirect(route('eo.organization'));

    $profile = $eo->fresh()->organizerProfile;
    $this->assertNotNull($profile->document_path);
});

test('file dokumen lebih dari 5MB ditolak', function () {
    Storage::fake('local');

    $eo = makeEoWithProfile();

    $this->actingAs($eo)
        ->put(route('eo.organization.update'), [
            'organization_name' => 'Test Org',
            'organization_type' => 'Komunitas',
            'description' => 'Deskripsi.',
            'city' => 'Bandung',
            'pic_name' => 'PIC',
            'pic_phone' => '08123456789',
            'document' => UploadedFile::fake()->create('besar.pdf', 5200, 'application/pdf'), // 5.2 MB
        ])
        ->assertSessionHasErrors('document');
});

test('file dokumen dengan tipe tidak valid ditolak', function () {
    Storage::fake('local');

    $eo = makeEoWithProfile();

    $this->actingAs($eo)
        ->put(route('eo.organization.update'), [
            'organization_name' => 'Test Org',
            'organization_type' => 'Komunitas',
            'description' => 'Deskripsi.',
            'city' => 'Bandung',
            'pic_name' => 'PIC',
            'pic_phone' => '08123456789',
            'document' => UploadedFile::fake()->create('script.exe', 100, 'application/octet-stream'),
        ])
        ->assertSessionHasErrors('document');
});

// ===========================================================================
// EO TIDAK BISA MENGUBAH VERIFICATION STATUS SENDIRI
// ===========================================================================

test('eo tidak bisa mengubah verification_status sendiri via update profil', function () {
    $eo = makeEoWithProfile(['verification_status' => VerificationStatus::Pending]);

    $this->actingAs($eo)
        ->put(route('eo.organization.update'), [
            'organization_name' => 'Org Name',
            'organization_type' => 'Komunitas',
            'description' => 'Desc.',
            'city' => 'Jakarta',
            'pic_name' => 'PIC',
            'pic_phone' => '08123456789',
            'verification_status' => 'verified', // attempt to hack
        ])
        ->assertRedirect(route('eo.organization'));

    // Status harus tetap pending, tidak berubah jadi verified
    $this->assertDatabaseHas('organizer_profiles', [
        'user_id' => $eo->id,
        'verification_status' => 'pending',
    ]);
});

// ===========================================================================
// PENGAJUAN VERIFIKASI
// ===========================================================================

test('eo bisa mengajukan verifikasi pertama kali', function () {
    $eo = makeEoWithProfile([
        'verification_status' => VerificationStatus::Pending,
        'document_path' => 'organizer-documents/doc.pdf',
    ]);

    // Ubah status ke kondisi awal (null/baru) dengan cara buat profil tanpa status pending
    // Sebenarnya factory default = pending, tapi kita test submit verification untuk rejected
    // Kasus ini: profile sudah ada tapi status rejected
    $eo->organizerProfile->update(['verification_status' => VerificationStatus::Rejected]);

    $this->actingAs($eo)
        ->post(route('eo.organization.verify'))
        ->assertRedirect(route('eo.organization'));

    $this->assertDatabaseHas('organizer_profiles', [
        'user_id' => $eo->id,
        'verification_status' => 'pending',
    ]);
});

test('setelah pengajuan verifikasi status menjadi pending', function () {
    $eo = makeEoWithProfile([
        'verification_status' => VerificationStatus::Rejected,
        'document_path' => 'organizer-documents/doc.pdf',
        'rejection_reason' => 'Dokumen tidak lengkap.',
    ]);

    $this->actingAs($eo)
        ->post(route('eo.organization.verify'))
        ->assertRedirect(route('eo.organization'));

    $profile = $eo->fresh()->organizerProfile;

    expect($profile->verification_status)->toBe(VerificationStatus::Pending);
    expect($profile->rejection_reason)->toBeNull();
});

test('eo dengan status pending tidak bisa mengajukan verifikasi lagi', function () {
    $eo = makeEoWithProfile(['verification_status' => VerificationStatus::Pending]);

    $this->actingAs($eo)
        ->post(route('eo.organization.verify'))
        ->assertRedirect(route('eo.organization'))
        ->assertSessionHas('warning');

    // Status tetap pending
    $this->assertDatabaseHas('organizer_profiles', [
        'user_id' => $eo->id,
        'verification_status' => 'pending',
    ]);
});

test('eo dengan status verified tidak bisa submit ulang', function () {
    $eo = makeEoWithProfile(['verification_status' => VerificationStatus::Verified]);

    $this->actingAs($eo)
        ->post(route('eo.organization.verify'))
        ->assertRedirect(route('eo.organization'))
        ->assertSessionHas('warning');
});

test('eo rejected bisa melihat rejection reason di halaman profil', function () {
    $eo = makeEoWithProfile([
        'verification_status' => VerificationStatus::Rejected,
        'rejection_reason' => 'Dokumen tidak terbaca dengan jelas.',
    ]);

    $this->actingAs($eo)
        ->get(route('eo.organization'))
        ->assertOk()
        ->assertSee('Dokumen tidak terbaca dengan jelas.');
});

test('eo rejected bisa memperbarui profil', function () {
    $eo = makeEoWithProfile([
        'verification_status' => VerificationStatus::Rejected,
        'organization_name' => 'Nama Lama',
    ]);

    $this->actingAs($eo)
        ->put(route('eo.organization.update'), [
            'organization_name' => 'Nama Baru Setelah Rejected',
            'organization_type' => 'Perusahaan',
            'description' => 'Diperbaiki.',
            'city' => 'Surabaya',
            'pic_name' => 'PIC Baru',
            'pic_phone' => '08133333333',
        ])
        ->assertRedirect(route('eo.organization'));

    $this->assertDatabaseHas('organizer_profiles', [
        'user_id' => $eo->id,
        'organization_name' => 'Nama Baru Setelah Rejected',
    ]);

    // Status harus tetap rejected setelah update profil saja
    $this->assertDatabaseHas('organizer_profiles', [
        'user_id' => $eo->id,
        'verification_status' => 'rejected',
    ]);
});

test('rejected ke pending setelah ajukan kembali', function () {
    $eo = makeEoWithProfile([
        'verification_status' => VerificationStatus::Rejected,
        'rejection_reason' => 'Dokumen tidak valid.',
        'document_path' => 'organizer-documents/doc.pdf',
    ]);

    $this->actingAs($eo)
        ->post(route('eo.organization.verify'))
        ->assertRedirect(route('eo.organization'));

    $profile = $eo->fresh()->organizerProfile;
    expect($profile->verification_status)->toBe(VerificationStatus::Pending);
    expect($profile->rejection_reason)->toBeNull();
});

test('eo verified tetap bisa melihat profilnya', function () {
    $eo = makeEoWithProfile(['verification_status' => VerificationStatus::Verified]);

    $this->actingAs($eo)
        ->get(route('eo.organization'))
        ->assertOk()
        ->assertSee('Terverifikasi');
});

// ===========================================================================
// INTEGRASI DENGAN EVENT SUBMIT (EnsureEoVerified)
// ===========================================================================

test('eo dengan status pending tidak bisa submit event', function () {
    $eo = makeEoWithProfile(['verification_status' => VerificationStatus::Pending]);

    $event = Event::factory()
        ->for($eo->organizerProfile, 'organizer')
        ->create(['status' => EventStatus::Draft]);

    EventPosition::factory()->create(['event_id' => $event->id]);

    $this->actingAs($eo)
        ->post(route('eo.events.submit', $event))
        ->assertRedirect(); // back() — controller belum verified check

    // Status harus tetap draft
    expect($event->fresh()->status)->toBe(EventStatus::Draft);
});

test('eo dengan status rejected tidak bisa submit event', function () {
    $eo = makeEoWithProfile(['verification_status' => VerificationStatus::Rejected]);

    $event = Event::factory()
        ->for($eo->organizerProfile, 'organizer')
        ->create(['status' => EventStatus::Draft]);

    EventPosition::factory()->create(['event_id' => $event->id]);

    $this->actingAs($eo)
        ->post(route('eo.events.submit', $event))
        ->assertRedirect(); // back() — controller belum verified check

    // Status harus tetap draft
    expect($event->fresh()->status)->toBe(EventStatus::Draft);
});

test('eo tanpa profil sama sekali tidak bisa submit event', function () {
    $eo = User::factory()->eo()->create(); // tanpa profil

    // Buat event milik profil lain — EventPolicy akan return 403
    $otherProfile = OrganizerProfile::factory()->create();

    $event = Event::factory()
        ->for($otherProfile, 'organizer')
        ->create(['status' => EventStatus::Draft]);

    // EO tanpa profil tidak punya relasi ke event ini → EventPolicy 403
    $this->actingAs($eo)
        ->post(route('eo.events.submit', $event))
        ->assertForbidden();
});
