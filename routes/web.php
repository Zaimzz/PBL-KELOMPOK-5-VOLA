
<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EoVerificationController;
use App\Http\Controllers\Crew\CrewDashboardController;
use App\Http\Controllers\Crew\EventDetailController;
use App\Http\Controllers\Crew\NotificationController;
use App\Http\Controllers\Crew\ProfileController as CrewProfileController;
use App\Http\Controllers\Crew\SearchController;
use App\Http\Controllers\Crew\SettingsController;
use App\Http\Controllers\Eo\EoDashboardController;
use App\Http\Controllers\Eo\EventController;
use App\Http\Controllers\Eo\EventPositionController;
use App\Http\Controllers\Eo\OrganizerProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicEventController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

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

Route::get('/events/{slug}', [PublicEventController::class, 'show'])
    ->name('events.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:volunteer'])->group(function () {
    Route::get('/dashboard', [CrewDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [CrewProfileController::class, 'show'])
        ->name('profile.show');

    Route::get('/profile/edit', [CrewProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [CrewProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index');

    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])
        ->name('settings.update.profile');

    Route::put('/settings/preferences', [SettingsController::class, 'updatePreferences'])
        ->name('settings.update.preferences');

    Route::get('/search', [SearchController::class, 'index'])
        ->name('search.index');

    Route::get('/events/{event}', [EventDetailController::class, 'show'])
        ->name('events.detail');

    Route::get('/api/notifications', [NotificationController::class, 'index']);
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/api/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});

Route::middleware(['auth', 'verified', 'role:eo'])
    ->prefix('eo')
    ->name('eo.')
    ->group(function () {
        Route::get('/dashboard', [EoDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/organization', [OrganizerProfileController::class, 'show'])
            ->name('organization');

        Route::get('/organization/edit', [OrganizerProfileController::class, 'edit'])
            ->name('organization.edit');

        Route::put('/organization', [OrganizerProfileController::class, 'update'])
            ->name('organization.update');

        Route::post('/organization/verify', [OrganizerProfileController::class, 'submitVerification'])
            ->name('organization.verify');

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

        Route::post('/events/{event}/submit', [EventController::class, 'submit'])
            ->name('events.submit');

        Route::post('/events/{event}/positions', [EventPositionController::class, 'store'])
            ->name('events.positions.store');

        Route::put('/events/{event}/positions/{position}', [EventPositionController::class, 'update'])
            ->name('events.positions.update');

        Route::delete('/events/{event}/positions/{position}', [EventPositionController::class, 'destroy'])
            ->name('events.positions.destroy');
    });

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

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