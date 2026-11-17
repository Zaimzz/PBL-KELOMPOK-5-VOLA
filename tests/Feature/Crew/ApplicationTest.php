<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Event;
use App\Models\EventPosition;
use App\Models\OrganizerProfile;
use App\Models\User;
use App\Models\VolunteerProfile;

function setupVolunteerWithEvent(): array
{
    $eo = User::factory()->eo()->create();
    $eoProfile = OrganizerProfile::factory()->create(['user_id' => $eo->id]);
    $event = Event::factory()->active()->for($eoProfile, 'organizer')->create();
    $position = EventPosition::factory()->for($event)->create(['quota' => 5]);

    $volunteer = User::factory()->volunteer()->create();
    $volProfile = VolunteerProfile::factory()->create(['user_id' => $volunteer->id]);

    return [$volunteer, $volProfile, $position, $event];
}

test('volunteer dapat mengajukan lamaran baru', function () {
    [$volunteer, $volProfile, $position] = setupVolunteerWithEvent();

    $response = $this->actingAs($volunteer)->postJson(route('applications.store'), [
        'position_id' => $position->id,
        'motivation' => 'Saya sangat termotivasi untuk berkontribusi pada event ini dengan kemampuan komunikasi yang baik.',
        'experience' => 'Pernah menjadi LO di festival kampus 2025',
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Lamaran berhasil diajukan! Silakan pantau status di menu "Lamaran Saya".',
        ]);

    $this->assertDatabaseHas('applications', [
        'volunteer_profile_id' => $volProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending->value,
        'cover_letter' => 'Saya sangat termotivasi untuk berkontribusi pada event ini dengan kemampuan komunikasi yang baik.',
    ]);
});

test('penolakan lamaran duplikat saat status masih pending', function () {
    [$volunteer, $volProfile, $position] = setupVolunteerWithEvent();

    Application::factory()->create([
        'volunteer_profile_id' => $volProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending,
    ]);

    $response = $this->actingAs($volunteer)->postJson(route('applications.store'), [
        'position_id' => $position->id,
        'motivation' => 'Mencoba melamar lagi untuk posisi yang sama.',
    ]);

    $response->assertStatus(409)
        ->assertJson([
            'success' => false,
            'message' => 'Anda sudah melamar untuk posisi ini.',
        ]);

    expect(Application::where('volunteer_profile_id', $volProfile->id)->where('position_id', $position->id)->count())->toBe(1);
});

test('penolakan pengajuan ulang setelah lamaran berstatus rejected tanpa SQL unique constraint exception', function () {
    [$volunteer, $volProfile, $position] = setupVolunteerWithEvent();

    Application::factory()->rejected()->create([
        'volunteer_profile_id' => $volProfile->id,
        'position_id' => $position->id,
    ]);

    $response = $this->actingAs($volunteer)->postJson(route('applications.store'), [
        'position_id' => $position->id,
        'motivation' => 'Mencoba melamar kembali setelah sebelumnya ditolak.',
    ]);

    $response->assertStatus(409)
        ->assertJson([
            'success' => false,
            'message' => 'Anda sudah melamar untuk posisi ini.',
        ]);

    expect(Application::where('volunteer_profile_id', $volProfile->id)->where('position_id', $position->id)->count())->toBe(1);
});

test('penolakan pengajuan ulang setelah lamaran berstatus withdrawn tanpa SQL unique constraint exception', function () {
    [$volunteer, $volProfile, $position] = setupVolunteerWithEvent();

    Application::factory()->withdrawn()->create([
        'volunteer_profile_id' => $volProfile->id,
        'position_id' => $position->id,
    ]);

    $response = $this->actingAs($volunteer)->postJson(route('applications.store'), [
        'position_id' => $position->id,
        'motivation' => 'Mencoba melamar kembali setelah membatalkan lamaran.',
    ]);

    $response->assertStatus(409)
        ->assertJson([
            'success' => false,
            'message' => 'Anda sudah melamar untuk posisi ini.',
        ]);

    expect(Application::where('volunteer_profile_id', $volProfile->id)->where('position_id', $position->id)->count())->toBe(1);
});

test('volunteer dapat melihat halaman lamaran saya dengan status resmi', function () {
    [$volunteer, $volProfile, $position, $event] = setupVolunteerWithEvent();

    Application::factory()->create([
        'volunteer_profile_id' => $volProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending,
        'applied_at' => now(),
    ]);

    $response = $this->actingAs($volunteer)->get(route('applications.index'));

    $response->assertOk()
        ->assertSee('Lamaran Saya')
        ->assertSee('Menunggu Seleksi')
        ->assertDontSee('Sedang Diproses');
});

test('crew tidak melihat lamaran volunteer lain di halaman lamaran saya', function () {
    [$volunteer, $volProfile, $position] = setupVolunteerWithEvent();
    $otherVolunteer = User::factory()->volunteer()->create();
    $otherProfile = VolunteerProfile::factory()->create(['user_id' => $otherVolunteer->id]);

    Application::factory()->create([
        'volunteer_profile_id' => $otherProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending,
    ]);

    $response = $this->actingAs($volunteer)->get(route('applications.index'));

    $response->assertOk()
        ->assertSee('Belum Ada Lamaran');
});

test('volunteer dapat mengunduh bukti penerimaan saat lamaran diterima', function () {
    [$volunteer, $volProfile, $position] = setupVolunteerWithEvent();

    $application = Application::factory()->create([
        'volunteer_profile_id' => $volProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Accepted,
        'applied_at' => now(),
    ]);

    $response = $this->actingAs($volunteer)->get(route('applications.download-acceptance', $application->id));

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('volunteer tidak dapat mengunduh bukti penerimaan saat lamaran masih pending', function () {
    [$volunteer, $volProfile, $position] = setupVolunteerWithEvent();

    $application = Application::factory()->create([
        'volunteer_profile_id' => $volProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Pending,
        'applied_at' => now(),
    ]);

    $response = $this->actingAs($volunteer)->get(route('applications.download-acceptance', $application->id));

    $response->assertSessionHasErrors('error');
});

test('halaman lamaran saya menampilkan lihat detail event dan unduh bukti penerimaan saat diterima', function () {
    [$volunteer, $volProfile, $position] = setupVolunteerWithEvent();

    Application::factory()->create([
        'volunteer_profile_id' => $volProfile->id,
        'position_id' => $position->id,
        'status' => ApplicationStatus::Accepted,
        'applied_at' => now(),
    ]);

    $response = $this->actingAs($volunteer)->get(route('applications.index'));

    $response->assertOk()
        ->assertSee('Lihat Detail Event')
        ->assertSee('Unduh Bukti Penerimaan')
        ->assertDontSee('Batalkan Lamaran');
});
