<?php

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\OrganizerProfile;
use App\Models\User;

// ============================================================
// Helpers
// ============================================================

function makeLifecycleEoEvent(): array
{
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->active()->for($profile, 'organizer')->create();

    return [$eo, $profile, $event];
}

// ============================================================
// 1. Aksi tutup & selesai oleh EO
// ============================================================

test('eo dapat menutup rekrutmen event aktif', function () {
    [$eo, , $event] = makeLifecycleEoEvent();

    $this->actingAs($eo)
        ->post(route('eo.events.close', $event))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($event->fresh()->status)->toBe(EventStatus::Closed);
});

test('eo dapat menandai event aktif sebagai selesai', function () {
    [$eo, , $event] = makeLifecycleEoEvent();

    $this->actingAs($eo)
        ->post(route('eo.events.finish', $event))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($event->fresh()->status)->toBe(EventStatus::Finished);
});

test('eo dapat menandai event tertutup sebagai selesai', function () {
    [$eo, , $event] = makeLifecycleEoEvent();
    $event->update(['status' => EventStatus::Closed]);

    $this->actingAs($eo)
        ->post(route('eo.events.finish', $event))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($event->fresh()->status)->toBe(EventStatus::Finished);
});

// ============================================================
// 2. Transisi ilegal ditolak
// ============================================================

test('event draft tidak dapat ditutup', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->for($profile, 'organizer')->create(['status' => EventStatus::Draft]);

    $this->actingAs($eo)
        ->post(route('eo.events.close', $event))
        ->assertForbidden();

    expect($event->fresh()->status)->toBe(EventStatus::Draft);
});

test('event pending review tidak dapat ditandai selesai', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->pendingReview()->for($profile, 'organizer')->create();

    $this->actingAs($eo)
        ->post(route('eo.events.finish', $event))
        ->assertForbidden();

    expect($event->fresh()->status)->toBe(EventStatus::PendingReview);
});

test('event yang sudah selesai tidak dapat ditutup atau diselesaikan lagi', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->finished()->for($profile, 'organizer')->create();

    $this->actingAs($eo)
        ->post(route('eo.events.close', $event))
        ->assertForbidden();

    $this->actingAs($eo)
        ->post(route('eo.events.finish', $event))
        ->assertForbidden();

    expect($event->fresh()->status)->toBe(EventStatus::Finished);
});

test('eo tidak dapat menutup atau menyelesaikan event milik EO lain', function () {
    [$eo] = makeLifecycleEoEvent();

    $otherEo = User::factory()->eo()->create();
    $otherProfile = OrganizerProfile::factory()->verified()->create(['user_id' => $otherEo->id]);
    $otherEvent = Event::factory()->active()->for($otherProfile, 'organizer')->create();

    $this->actingAs($eo)
        ->post(route('eo.events.close', $otherEvent))
        ->assertForbidden();

    $this->actingAs($eo)
        ->post(route('eo.events.finish', $otherEvent))
        ->assertForbidden();

    expect($otherEvent->fresh()->status)->toBe(EventStatus::Active);
});

// ============================================================
// 3. Sinkronisasi status terjadwal
// ============================================================

test('events sync status menutup event aktif yang deadline pendaftarannya lewat', function () {
    [$eo, , $event] = makeLifecycleEoEvent();
    $event->update([
        'registration_deadline' => now()->subDay()->toDateString(),
        'end_date' => now()->addWeek()->toDateString(),
    ]);

    $this->artisan('events:sync-status')->assertSuccessful();

    expect($event->fresh()->status)->toBe(EventStatus::Closed);
});

test('events sync status menyelesaikan event yang tanggal selesainya lewat', function () {
    [$eo, $profile, $activeEvent] = makeLifecycleEoEvent();
    $activeEvent->update([
        'start_date' => now()->subWeek()->toDateString(),
        'end_date' => now()->subDay()->toDateString(),
        'registration_deadline' => now()->subWeeks(2)->toDateString(),
    ]);

    $closedEvent = Event::factory()->for($profile, 'organizer')->create([
        'status' => EventStatus::Closed,
        'start_date' => now()->subWeek()->toDateString(),
        'end_date' => now()->subDay()->toDateString(),
        'registration_deadline' => now()->subWeeks(2)->toDateString(),
    ]);

    $this->artisan('events:sync-status')->assertSuccessful();

    expect($activeEvent->fresh()->status)->toBe(EventStatus::Finished);
    expect($closedEvent->fresh()->status)->toBe(EventStatus::Finished);
});

test('events sync status tidak mengubah event yang masih berjalan', function () {
    [$eo, , $event] = makeLifecycleEoEvent();
    $event->update([
        'registration_deadline' => now()->addWeek()->toDateString(),
        'end_date' => now()->addWeeks(2)->toDateString(),
    ]);

    $this->artisan('events:sync-status')->assertSuccessful();

    expect($event->fresh()->status)->toBe(EventStatus::Active);
});

test('events sync status tidak menutup event yang deadline-nya hari ini', function () {
    [$eo, , $event] = makeLifecycleEoEvent();
    $event->update([
        'registration_deadline' => now()->toDateString(),
        'end_date' => now()->addWeek()->toDateString(),
    ]);

    $this->artisan('events:sync-status')->assertSuccessful();

    expect($event->fresh()->status)->toBe(EventStatus::Active);
});
