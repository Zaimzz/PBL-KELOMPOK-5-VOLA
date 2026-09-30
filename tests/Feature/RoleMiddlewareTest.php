<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['auth', 'role:eo'])->get('/test-eo-only', fn () => 'OK EO');
});

test('volunteer tidak bisa akses route khusus eo', function () {
    $volunteer = User::factory()->volunteer()->create();

    $this->actingAs($volunteer)
        ->get('/test-eo-only')
        ->assertForbidden();
});

test('eo bisa akses route khusus eo', function () {
    $eo = User::factory()->eo()->create();

    $this->actingAs($eo)
        ->get('/test-eo-only')
        ->assertOk();
});

test('guest tidak bisa akses route yang butuh login', function () {
    $this->get('/test-eo-only')
        ->assertRedirect(); // redirect ke halaman login
});
