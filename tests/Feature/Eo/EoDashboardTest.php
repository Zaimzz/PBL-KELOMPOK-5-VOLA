<?php

use App\Models\Event;
use App\Models\OrganizerProfile;
use App\Models\User;

test('guest tidak bisa mengakses EO dashboard', function () {
    $this->get(route('eo.dashboard'))
        ->assertRedirect(route('login'));
});

test('volunteer tidak bisa mengakses EO dashboard', function () {
    $volunteer = User::factory()->volunteer()->create();

    $this->actingAs($volunteer)
        ->get(route('eo.dashboard'))
        ->assertForbidden();
});

test('admin tidak bisa mengakses EO dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('eo.dashboard'))
        ->assertForbidden();
});

test('eo bisa mengakses EO dashboard', function () {
    $eo = User::factory()->eo()->create();

    $this->actingAs($eo)
        ->get(route('eo.dashboard'))
        ->assertOk()
        ->assertViewIs('eo.dashboard');
});

test('eo dashboard menampilkan data event miliknya', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);
    $event = Event::factory()->for($profile, 'organizer')->create(['title' => 'Event Saya Sendiri']);

    $this->actingAs($eo)
        ->get(route('eo.dashboard'))
        ->assertOk()
        ->assertSee('Event Saya Sendiri');
});

test('eo dashboard tidak menampilkan event milik EO lain', function () {
    $eo = User::factory()->eo()->create();
    OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);

    $otherEo = User::factory()->eo()->create();
    $otherProfile = OrganizerProfile::factory()->verified()->create(['user_id' => $otherEo->id]);
    Event::factory()->for($otherProfile, 'organizer')->create(['title' => 'Event Orang Lain']);

    $this->actingAs($eo)
        ->get(route('eo.dashboard'))
        ->assertOk()
        ->assertDontSee('Event Orang Lain');
});

test('eo dashboard menghitung status event dengan benar dari database', function () {
    $eo = User::factory()->eo()->create();
    $profile = OrganizerProfile::factory()->verified()->create(['user_id' => $eo->id]);

    Event::factory()->for($profile, 'organizer')->create(['status' => 'draft']);
    Event::factory()->for($profile, 'organizer')->create(['status' => 'draft']);
    Event::factory()->pendingReview()->for($profile, 'organizer')->create();
    Event::factory()->active()->for($profile, 'organizer')->create();
    Event::factory()->rejected()->for($profile, 'organizer')->create();

    // Events owned by another EO must not be counted
    $otherEo = User::factory()->eo()->create();
    $otherProfile = OrganizerProfile::factory()->verified()->create(['user_id' => $otherEo->id]);
    Event::factory()->for($otherProfile, 'organizer')->create(['status' => 'draft']);

    $this->actingAs($eo)
        ->get(route('eo.dashboard'))
        ->assertOk()
        ->assertViewHas('totalEvents', 5)
        ->assertViewHas('draftCount', 2)
        ->assertViewHas('pendingReviewCount', 1)
        ->assertViewHas('awaitingPaymentCount', 0)
        ->assertViewHas('rejectedCount', 1);
});

test('eo diarahkan ke eo.dashboard setelah login', function () {
    $eo = User::factory()->eo()->create(['password' => bcrypt('password')]);

    // Login form butuh field 'role' untuk non-admin
    $this->post(route('login'), [
        'email' => $eo->email,
        'password' => 'password',
        'role' => 'eo',
    ])->assertRedirect(route('eo.dashboard'));
});
