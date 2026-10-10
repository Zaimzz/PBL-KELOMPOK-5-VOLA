<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EoVerificationController;
use App\Http\Controllers\Eo\EoDashboardController;
use App\Http\Controllers\Eo\EventController;
use App\Http\Controllers\Eo\EventPositionController;
use App\Http\Controllers\Eo\OrganizerProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicEventController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    $events = Event::query()
        ->with(['category', 'organizer'])
        ->where('status', \App\Enums\EventStatus::Active)
        ->whereNotNull('published_at')
        ->whereDate('registration_deadline', '>=', now()->toDateString())
        ->latest('published_at')
        ->take(3)
        ->get();

    return view('welcome', compact('events'));
})->name('home');

// Public event detail
Route::get('/events/{slug}', [PublicEventController::class, 'show'])
    ->name('events.show');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

// Routes accessible to all authenticated, verified users
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Volunteer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:volunteer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

/*
|--------------------------------------------------------------------------
| Event Organizer (EO) Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:eo'])
    ->prefix('eo')
    ->name('eo.')
    ->group(function () {
        // EO Dashboard
        Route::get('/dashboard', [EoDashboardController::class, 'index'])
            ->name('dashboard');

        // Organization Profile
        Route::get('/organization', [OrganizerProfileController::class, 'show'])
            ->name('organization');

        Route::get('/organization/edit', [OrganizerProfileController::class, 'edit'])
            ->name('organization.edit');

        Route::put('/organization', [OrganizerProfileController::class, 'update'])
            ->name('organization.update');

        Route::post('/organization/verify', [OrganizerProfileController::class, 'submitVerification'])
            ->name('organization.verify');

        // Event CRUD
        Route::get('/events', [EventController::class, 'index'])
            ->name('events.index');

        Route::get('/events/create', [EventController::class, 'create'])
            ->name('events.create');

        Route::post('/events', [EventController::class, 'store'])
            ->name('events.store');

        Route::get('/events/{event}/edit', [EventController::class, 'edit'])
            ->name('events.edit');

        Route::put('/events/{event}', [EventController::class, 'update'])
            ->name('events.update');

        Route::delete('/events/{event}', [EventController::class, 'destroy'])
            ->name('events.destroy');

        // Submit event for review
        Route::post('/events/{event}/submit', [EventController::class, 'submit'])
            ->name('events.submit');

        // Event Position Management
        Route::post('/events/{event}/positions', [EventPositionController::class, 'store'])
            ->name('events.positions.store');

        Route::put('/events/{event}/positions/{position}', [EventPositionController::class, 'update'])
            ->name('events.positions.update');

        Route::delete('/events/{event}/positions/{position}', [EventPositionController::class, 'destroy'])
            ->name('events.positions.destroy');
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // EO Verification
        Route::get('/eo-verifications', [EoVerificationController::class, 'index'])
            ->name('eo-verifications');

        Route::get('/eo-verifications/{organizer}/document', [EoVerificationController::class, 'document'])
            ->name('eo-verifications.document');

        Route::post('/eo-verifications/{organizer}/approve', [EoVerificationController::class, 'approve'])
            ->name('eo-verifications.approve');

        Route::post('/eo-verifications/{organizer}/reject', [EoVerificationController::class, 'reject'])
            ->name('eo-verifications.reject');
    });

require __DIR__.'/auth.php';