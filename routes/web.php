<?php

use App\Http\Controllers\Eo\EoDashboardController;
use App\Http\Controllers\Eo\EventController;
use App\Http\Controllers\Eo\EventPositionController;
use App\Http\Controllers\Eo\OrganizerProfileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Bisa diakses semua role yang sudah login (volunteer, eo, admin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:volunteer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:eo'])->prefix('eo')->name('eo.')->group(function () {
    // EO Dashboard
    Route::get('/dashboard', [EoDashboardController::class, 'index'])->name('dashboard');

    // EO Organization Profile
    Route::get('/organization', [OrganizerProfileController::class, 'show'])->name('organization');
    Route::get('/organization/edit', [OrganizerProfileController::class, 'edit'])->name('organization.edit');
    Route::put('/organization', [OrganizerProfileController::class, 'update'])->name('organization.update');
    Route::post('/organization/verify', [OrganizerProfileController::class, 'submitVerification'])->name('organization.verify');

    // Event CRUD
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // Submit event for review (requires EO to be verified)
    Route::post('/events/{event}/submit', [EventController::class, 'submit'])
        ->name('events.submit');

    // Event Position management
    Route::post('/events/{event}/positions', [EventPositionController::class, 'store'])
        ->name('events.positions.store');
    Route::put('/events/{event}/positions/{position}', [EventPositionController::class, 'update'])
        ->name('events.positions.update');
    Route::delete('/events/{event}/positions/{position}', [EventPositionController::class, 'destroy'])
        ->name('events.positions.destroy');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // Replace with specific admin dashboard later
    })->name('dashboard');
});

require __DIR__.'/auth.php';
