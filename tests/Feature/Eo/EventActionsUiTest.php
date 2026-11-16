<?php

use App\Models\Event;
use App\Models\OrganizerProfile;
use App\Models\User;

// ============================================================
// Helpers
// ============================================================

function makeUiEo(): array
{
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);

    return [$eo, $profile];
}

// ============================================================
// 1. Tombol mengikuti Policy per status
// ============================================================

test('event draft menampilkan tombol edit submit dan hapus', function () {
    [$eo, $profile] = makeUiEo();
    $event = Event::factory()->for($profile, 'organizer')->create(['status' => 'draft']);

    $this->actingAs($eo)
        ->get(route('eo.events.index'))
        ->assertOk()
        ->assertSee('Edit Rincian')
        ->assertSee('Kirim untuk Review')
        ->assertSee('Hapus')
        ->assertDontSee('Tutup Rekrutmen')
        ->assertDontSee('Tandai Selesai');
});

test('event pending review hanya menampilkan lihat detail tanpa tombol aksi terlarang', function () {
    [$eo, $profile] = makeUiEo();
    $event = Event::factory()->pendingReview()->for($profile, 'organizer')->create();

    $this->actingAs($eo)
        ->get(route('eo.events.index'))
        ->assertOk()
        ->assertSee('Lihat Detail')
        ->assertSee('Terkunci selama review')
        ->assertDontSee('Edit Rincian')
        ->assertDontSee('Kirim untuk Review')
        ->assertDontSee('Tutup Rekrutmen');
});

test('tautan lihat detail mengarah ke halaman detail bukan halaman edit', function () {
    [$eo, $profile] = makeUiEo();
    $event = Event::factory()->pendingReview()->for($profile, 'organizer')->create();

    $this->actingAs($eo)
        ->get(route('eo.events.index'))
        ->assertOk()
        ->assertSee(route('eo.events.show', $event), false)
        ->assertDontSee(route('eo.events.edit', $event), false);
});

test('event aktif menampilkan tombol tutup rekrutmen dan tandai selesai', function () {
    [$eo, $profile] = makeUiEo();
    $event = Event::factory()->active()->for($profile, 'organizer')->create();

    $this->actingAs($eo)
        ->get(route('eo.events.index'))
        ->assertOk()
        ->assertSee('Tutup Rekrutmen')
        ->assertSee('Tandai Selesai')
        ->assertSee('Lihat Detail')
        ->assertDontSee('Edit Rincian');

    $this->actingAs($eo)
        ->get(route('eo.events.show', $event))
        ->assertOk()
        ->assertSee('Tutup Rekrutmen')
        ->assertSee('Tandai Selesai');
});

test('tombol submit disembunyikan untuk EO yang belum terverifikasi', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->create(['user_id' => $eo->id]);
    $event = Event::factory()->for($profile, 'organizer')->create(['status' => 'draft']);

    $this->actingAs($eo)
        ->get(route('eo.events.index'))
        ->assertOk()
        ->assertSee('Edit Rincian')
        ->assertDontSee('Kirim untuk Review');
});

// ============================================================
// 2. Halaman detail read-only
// ============================================================

test('eo pemilik dapat membuka halaman detail event yang terkunci', function () {
    [$eo, $profile] = makeUiEo();
    $event = Event::factory()->pendingReview()->for($profile, 'organizer')->create([
        'title' => 'Event Sedang Direview',
    ]);

    $this->actingAs($eo)
        ->get(route('eo.events.show', $event))
        ->assertOk()
        ->assertSee('Event Sedang Direview')
        ->assertSee('Menunggu Review')
        ->assertDontSee('Edit Rincian');
});

test('halaman detail event selesai tidak menampilkan aksi tutup atau selesai', function () {
    [$eo, $profile] = makeUiEo();
    $event = Event::factory()->finished()->for($profile, 'organizer')->create();

    $this->actingAs($eo)
        ->get(route('eo.events.show', $event))
        ->assertOk()
        ->assertSee('Selesai')
        ->assertDontSee('Tutup Rekrutmen')
        ->assertDontSee('Tandai Selesai');
});

test('eo tidak dapat membuka detail event milik EO lain', function () {
    [$eo] = makeUiEo();

    [$otherEo, $otherProfile] = makeUiEo();
    $otherEvent = Event::factory()->active()->for($otherProfile, 'organizer')->create();

    $this->actingAs($eo)
        ->get(route('eo.events.show', $otherEvent))
        ->assertForbidden();
});
