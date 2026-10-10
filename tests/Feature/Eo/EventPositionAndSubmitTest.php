<?php

use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPosition;
use App\Models\OrganizerProfile;
use App\Models\User;

// ============================================================
// Event Positions CRUD
// ============================================================

test('eo bisa menambah posisi ke event draft miliknya', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->for($profile, 'organizer')->create(); // draft

    $this->actingAs($eo)
        ->post(route('eo.events.positions.store', $event), [
            'name' => 'Liaison Officer',
            'description' => 'Mengelola koordinasi tamu',
            'requirements' => 'Komunikatif, ramah',
            'quota' => 5,
        ])
        ->assertRedirect(route('eo.events.edit', $event));

    $this->assertDatabaseHas('event_positions', [
        'event_id' => $event->id,
        'name' => 'Liaison Officer',
        'quota' => 5,
    ]);
});

test('gagal menambah posisi tanpa nama', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->for($profile, 'organizer')->create();

    $this->actingAs($eo)
        ->post(route('eo.events.positions.store', $event), [
            'name' => '',
            'quota' => 5,
        ])
        ->assertSessionHasErrors('name');
});

test('gagal menambah posisi dengan kuota kurang dari 1', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->for($profile, 'organizer')->create();

    $this->actingAs($eo)
        ->post(route('eo.events.positions.store', $event), [
            'name' => 'Usher',
            'quota' => 0,
        ])
        ->assertSessionHasErrors('quota');
});

test('eo bisa mengubah posisi event draft miliknya', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->for($profile, 'organizer')->create();
    $position = EventPosition::factory()->for($event)->create(['name' => 'Nama Lama', 'quota' => 3]);

    $this->actingAs($eo)
        ->put(route('eo.events.positions.update', [$event, $position]), [
            'name' => 'Nama Baru',
            'quota' => 10,
        ])
        ->assertRedirect(route('eo.events.edit', $event));

    expect($position->fresh()->name)->toBe('Nama Baru');
    expect($position->fresh()->quota)->toBe(10);
});

test('eo bisa menghapus posisi event draft miliknya', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->for($profile, 'organizer')->create();
    $position = EventPosition::factory()->for($event)->create();

    $this->actingAs($eo)
        ->delete(route('eo.events.positions.destroy', [$event, $position]))
        ->assertRedirect(route('eo.events.edit', $event));

    $this->assertModelMissing($position);
});

test('eo tidak bisa menambah posisi ke event pending_review miliknya', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->pendingReview()->for($profile, 'organizer')->create();

    $this->actingAs($eo)
        ->post(route('eo.events.positions.store', $event), [
            'name' => 'Posisi Baru',
            'quota' => 3,
        ])
        ->assertForbidden();
});

test('eo tidak bisa mengelola posisi event milik EO lain', function () {
    $eo = User::factory()->eo()->create();
    OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);

    $otherEo = User::factory()->eo()->create();
    $otherProfile = OrganizerProfile::factory()->verified()->create(['user_id' => $otherEo->id]);
    $otherEvent = Event::factory()->for($otherProfile, 'organizer')->create();

    $this->actingAs($eo)
        ->post(route('eo.events.positions.store', $otherEvent), [
            'name' => 'Posisi Coba Akses',
            'quota' => 3,
        ])
        ->assertForbidden();
});

// ============================================================
// Submit Event
// ============================================================

test('eo verified bisa submit event draft dengan minimal 1 posisi dan field lengkap', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $category = EventCategory::factory()->create();
    $event = Event::factory()->for($profile, 'organizer')->create([
        'category_id' => $category->id,
        'status' => EventStatus::Draft,
        'title' => 'Event Lengkap',
        'description' => 'Deskripsi yang cukup panjang.',
        'location' => 'Gedung Test',
        'start_date' => now()->addDays(30),
        'end_date' => now()->addDays(32),
        'registration_deadline' => now()->addDays(20),
        'contact_info' => '08123456789',
    ]);
    EventPosition::factory()->for($event)->create();

    $this->actingAs($eo)
        ->post(route('eo.events.submit', $event))
        ->assertRedirect(route('eo.events.index'));

    expect($event->fresh()->status)->toBe(EventStatus::PendingReview);
});

test('eo belum verified tidak bisa submit event', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->create([
        'user_id' => $eo->id,
        'verification_status' => VerificationStatus::Pending,
    ]);
    $event = Event::factory()->for($profile, 'organizer')->create([
        'status' => EventStatus::Draft,
    ]);
    EventPosition::factory()->for($event)->create();

    $this->actingAs($eo)
        ->post(route('eo.events.submit', $event))
        ->assertRedirect();

    // Status harus tetap draft
    expect($event->fresh()->status)->toBe(EventStatus::Draft);
});

test('tidak bisa submit event tanpa posisi', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $category = EventCategory::factory()->create();
    $event = Event::factory()->for($profile, 'organizer')->create([
        'category_id' => $category->id,
        'status' => EventStatus::Draft,
        'title' => 'Event Tanpa Posisi',
        'description' => 'Deskripsi.',
        'location' => 'Gedung Test',
        'start_date' => now()->addDays(30),
        'end_date' => now()->addDays(32),
        'registration_deadline' => now()->addDays(20),
        'contact_info' => '08123456789',
    ]);
    // No positions added

    $this->actingAs($eo)
        ->post(route('eo.events.submit', $event))
        ->assertRedirect();

    expect($event->fresh()->status)->toBe(EventStatus::Draft);
});

test('tidak bisa submit event dengan field wajib kosong', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->for($profile, 'organizer')->create([
        'status' => EventStatus::Draft,
        'description' => null, // missing required field
        'location' => null,
        'start_date' => null,
        'end_date' => null,
        'registration_deadline' => null,
        'contact_info' => null,
    ]);
    EventPosition::factory()->for($event)->create();

    $this->actingAs($eo)
        ->post(route('eo.events.submit', $event))
        ->assertRedirect();

    expect($event->fresh()->status)->toBe(EventStatus::Draft);
});

test('tidak bisa submit event yang sudah berstatus pending_review', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->pendingReview()->for($profile, 'organizer')->create();
    EventPosition::factory()->for($event)->create();

    $this->actingAs($eo)
        ->post(route('eo.events.submit', $event))
        ->assertForbidden();

    expect($event->fresh()->status)->toBe(EventStatus::PendingReview);
});

test('eo tidak bisa submit event milik EO lain', function () {
    $eo = User::factory()->eo()->create();
    OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);

    $otherEo = User::factory()->eo()->create();
    $otherProfile = OrganizerProfile::factory()->verified()->create(['user_id' => $otherEo->id]);
    $otherEvent = Event::factory()->for($otherProfile, 'organizer')->create([
        'status' => EventStatus::Draft,
    ]);
    EventPosition::factory()->for($otherEvent)->create();

    $this->actingAs($eo)
        ->post(route('eo.events.submit', $otherEvent))
        ->assertForbidden();

    expect($otherEvent->fresh()->status)->toBe(EventStatus::Draft);
});

test('event rejected bisa disubmit ulang setelah diedit', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $category = EventCategory::factory()->create();
    // Create event as rejected with complete fields
    $event = Event::factory()->for($profile, 'organizer')->create([
        'category_id' => $category->id,
        'status' => EventStatus::Rejected,
        'rejection_reason' => 'Informasi kurang lengkap.',
        'description' => 'Deskripsi setelah perbaikan.',
        'location' => 'Gedung Baru',
        'start_date' => now()->addDays(30),
        'end_date' => now()->addDays(32),
        'registration_deadline' => now()->addDays(20),
        'contact_info' => '08123456789',
    ]);
    EventPosition::factory()->for($event)->create();

    $this->actingAs($eo)
        ->post(route('eo.events.submit', $event))
        ->assertRedirect(route('eo.events.index'));

    expect($event->fresh()->status)->toBe(EventStatus::PendingReview);
});
