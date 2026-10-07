<?php

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// ============================================================
// Helpers
// ============================================================

function createVerifiedEo(): array
{
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);

    return [$eo, $profile];
}

function createUnverifiedEo(): array
{
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->create(['user_id' => $eo->id]); // pending

    return [$eo, $profile];
}

// ============================================================
// Event List (index)
// ============================================================

test('guest tidak bisa melihat daftar event EO', function () {
    $this->get(route('eo.events.index'))
        ->assertRedirect(route('login'));
});

test('volunteer tidak bisa melihat daftar event EO', function () {
    $volunteer = User::factory()->volunteer()->create();

    $this->actingAs($volunteer)
        ->get(route('eo.events.index'))
        ->assertForbidden();
});

test('eo bisa melihat daftar event miliknya', function () {
    [$eo, $profile] = createVerifiedEo();
    $event = Event::factory()->for($profile, 'organizer')->create(['title' => 'Event Milikku']);

    $this->actingAs($eo)
        ->get(route('eo.events.index'))
        ->assertOk()
        ->assertSee('Event Milikku');
});

test('eo tidak bisa melihat event milik EO lain', function () {
    [$eo] = createVerifiedEo();

    $otherEo = User::factory()->eo()->create();
    $otherProfile = OrganizerProfile::factory()->verified()->create(['user_id' => $otherEo->id]);
    Event::factory()->for($otherProfile, 'organizer')->create(['title' => 'Event Orang Lain']);

    $this->actingAs($eo)
        ->get(route('eo.events.index'))
        ->assertOk()
        ->assertDontSee('Event Orang Lain');
});

// ============================================================
// Create Event
// ============================================================

test('eo bisa membuka form buat event', function () {
    [$eo] = createVerifiedEo();

    $this->actingAs($eo)
        ->get(route('eo.events.create'))
        ->assertOk()
        ->assertViewIs('eo.events.create');
});

test('eo bisa membuat event baru sebagai draft', function () {
    [$eo, $profile] = createVerifiedEo();
    $category = EventCategory::factory()->create();

    $this->actingAs($eo)
        ->post(route('eo.events.store'), [
            'title' => 'Festival Musik Test',
            'category_id' => $category->id,
            'description' => 'Deskripsi event test ini.',
            'location' => 'Gedung Serbaguna',
            'city' => 'Bandung',
            'start_date' => now()->addDays(30)->format('Y-m-d'),
            'end_date' => now()->addDays(32)->format('Y-m-d'),
            'registration_deadline' => now()->addDays(20)->format('Y-m-d'),
            'contact_info' => '08123456789',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('events', [
        'title' => 'Festival Musik Test',
        'organizer_id' => $profile->id,
        'status' => EventStatus::Draft->value,
    ]);
});

test('gagal membuat event tanpa judul', function () {
    [$eo] = createVerifiedEo();
    $category = EventCategory::factory()->create();

    $this->actingAs($eo)
        ->post(route('eo.events.store'), [
            'title' => '',
            'category_id' => $category->id,
        ])
        ->assertSessionHasErrors('title');
});

test('gagal membuat event dengan kategori tidak valid', function () {
    [$eo] = createVerifiedEo();

    $this->actingAs($eo)
        ->post(route('eo.events.store'), [
            'title' => 'Test Event',
            'category_id' => 99999,
        ])
        ->assertSessionHasErrors('category_id');
});

// ============================================================
// Edit Event
// ============================================================

test('eo bisa membuka form edit event miliknya yang berstatus draft', function () {
    [$eo, $profile] = createVerifiedEo();
    $event = Event::factory()->for($profile, 'organizer')->create(); // draft

    $this->actingAs($eo)
        ->get(route('eo.events.edit', $event))
        ->assertOk()
        ->assertViewIs('eo.events.edit');
});

test('eo bisa mengedit event miliknya yang berstatus draft', function () {
    [$eo, $profile] = createVerifiedEo();
    $category = EventCategory::factory()->create();
    $event = Event::factory()->for($profile, 'organizer')->create([
        'category_id' => $category->id,
    ]);

    $this->actingAs($eo)
        ->put(route('eo.events.update', $event), [
            'title' => 'Judul Baru',
            'category_id' => $category->id,
        ])
        ->assertRedirect(route('eo.events.edit', $event));

    expect($event->fresh()->title)->toBe('Judul Baru');
});

test('eo bisa mengedit event yang berstatus rejected', function () {
    [$eo, $profile] = createVerifiedEo();
    $category = EventCategory::factory()->create();
    $event = Event::factory()->rejected()->for($profile, 'organizer')->create([
        'category_id' => $category->id,
    ]);

    $this->actingAs($eo)
        ->put(route('eo.events.update', $event), [
            'title' => 'Judul Setelah Ditolak',
            'category_id' => $category->id,
        ])
        ->assertRedirect();

    expect($event->fresh()->title)->toBe('Judul Setelah Ditolak');
});

test('eo tidak bisa mengedit event yang berstatus pending_review', function () {
    [$eo, $profile] = createVerifiedEo();
    $category = EventCategory::factory()->create();
    $event = Event::factory()->pendingReview()->for($profile, 'organizer')->create([
        'category_id' => $category->id,
    ]);

    $this->actingAs($eo)
        ->put(route('eo.events.update', $event), [
            'title' => 'Judul Yang Tidak Boleh Berubah',
            'category_id' => $category->id,
        ])
        ->assertForbidden();

    expect($event->fresh()->title)->not->toBe('Judul Yang Tidak Boleh Berubah');
});

test('eo tidak bisa mengedit event milik EO lain', function () {
    [$eo] = createVerifiedEo();
    $category = EventCategory::factory()->create();

    $otherEo = User::factory()->eo()->create();
    $otherProfile = OrganizerProfile::factory()->verified()->create(['user_id' => $otherEo->id]);
    $event = Event::factory()->for($otherProfile, 'organizer')->create([
        'category_id' => $category->id,
    ]);

    $this->actingAs($eo)
        ->put(route('eo.events.update', $event), [
            'title' => 'Coba Edit Event Orang Lain',
            'category_id' => $category->id,
        ])
        ->assertForbidden();
});

// ============================================================
// Delete Event
// ============================================================

test('eo bisa menghapus event draft miliknya', function () {
    [$eo, $profile] = createVerifiedEo();
    $event = Event::factory()->for($profile, 'organizer')->create(); // draft

    $this->actingAs($eo)
        ->delete(route('eo.events.destroy', $event))
        ->assertRedirect(route('eo.events.index'));

    $this->assertSoftDeleted('events', ['id' => $event->id]);
});

test('eo bisa menghapus event rejected miliknya', function () {
    [$eo, $profile] = createVerifiedEo();
    $event = Event::factory()->rejected()->for($profile, 'organizer')->create();

    $this->actingAs($eo)
        ->delete(route('eo.events.destroy', $event))
        ->assertRedirect(route('eo.events.index'));

    $this->assertSoftDeleted('events', ['id' => $event->id]);
});

test('eo tidak bisa menghapus event yang berstatus pending_review', function () {
    [$eo, $profile] = createVerifiedEo();
    $event = Event::factory()->pendingReview()->for($profile, 'organizer')->create();

    $this->actingAs($eo)
        ->delete(route('eo.events.destroy', $event))
        ->assertForbidden();

    $this->assertNotSoftDeleted('events', ['id' => $event->id]);
});

test('eo tidak bisa menghapus event milik EO lain', function () {
    [$eo] = createVerifiedEo();

    $otherEo = User::factory()->eo()->create();
    $otherProfile = OrganizerProfile::factory()->verified()->create(['user_id' => $otherEo->id]);
    $event = Event::factory()->for($otherProfile, 'organizer')->create();

    $this->actingAs($eo)
        ->delete(route('eo.events.destroy', $event))
        ->assertForbidden();
});

// ============================================================
// Poster Upload
// ============================================================

test('upload poster valid berhasil', function () {
    Storage::fake('public');
    [$eo, $profile] = createVerifiedEo();
    $category = EventCategory::factory()->create();
    $event = Event::factory()->for($profile, 'organizer')->create([
        'category_id' => $category->id,
    ]);

    // Gunakan create() bukan image() karena GD extension mungkin tidak tersedia
    $file = UploadedFile::fake()->create('poster.jpg', 500, 'image/jpeg');

    $this->actingAs($eo)
        ->put(route('eo.events.update', $event), [
            'title' => $event->title,
            'category_id' => $category->id,
            'poster' => $file,
        ])
        ->assertRedirect();

    $event->refresh();
    expect($event->poster_path)->not->toBeNull();
    Storage::disk('public')->assertExists($event->poster_path);
});

test('upload poster dengan tipe file tidak valid ditolak', function () {
    Storage::fake('public');
    [$eo, $profile] = createVerifiedEo();
    $event = Event::factory()->for($profile, 'organizer')->create();

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->actingAs($eo)
        ->put(route('eo.events.update', $event), [
            'title' => $event->title,
            'category_id' => $event->category_id,
            'poster' => $file,
        ])
        ->assertSessionHasErrors('poster');
});

test('upload poster lebih dari 2MB ditolak', function () {
    Storage::fake('public');
    [$eo, $profile] = createVerifiedEo();
    $event = Event::factory()->for($profile, 'organizer')->create();

    // Buat file lebih dari 2MB (2049 KB) — tanpa butuh GD
    $file = UploadedFile::fake()->create('poster.jpg', 2049, 'image/jpeg');

    $this->actingAs($eo)
        ->put(route('eo.events.update', $event), [
            'title' => $event->title,
            'category_id' => $event->category_id,
            'poster' => $file,
        ])
        ->assertSessionHasErrors('poster');
});
