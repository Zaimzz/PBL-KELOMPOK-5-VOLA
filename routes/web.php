<?php

use App\Enums\EventStatus;
use App\Http\Controllers\Admin\ActiveEventController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EoVerificationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewEventController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Crew\CrewDashboardController;
use App\Http\Controllers\Crew\EventDetailController;
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

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    $events = Event::query()
        ->with(['category', 'organizer'])
        ->where('status', EventStatus::Active)
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
    Route::get('/dashboard', [CrewDashboardController::class, 'index'])->name('dashboard');

    // Route Profil
    Route::get('/profile', [App\Http\Controllers\Crew\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\Crew\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Crew\ProfileController::class, 'update'])->name('profile.update');

    // Route Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.update.profile');
    Route::put('/settings/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.update.preferences');

    // Route Pencarian
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::get('/events/{event}', [EventDetailController::class, 'show'])->name('events.detail');
});

// Route volunteer lainnya diletakkan di sini.
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

        // Review Events
        Route::get('/review-events', [ReviewEventController::class, 'index'])
            ->name('review-events.index');
        Route::get('/review-events/{event}', [ReviewEventController::class, 'show'])
            ->name('review-events.show');
        Route::post('/review-events/{event}/approve', [ReviewEventController::class, 'approve'])
            ->name('review-events.approve');
        Route::post('/review-events/{event}/reject', [ReviewEventController::class, 'reject'])
            ->name('review-events.reject');

        // Aliases for review event route naming
        Route::get('/review-event', [ReviewEventController::class, 'index'])
            ->name('review-event.index');
        Route::get('/review-events-list', [ReviewEventController::class, 'index'])
            ->name('review-events');

        // Active Events
        Route::get('/active-events', [ActiveEventController::class, 'index'])
            ->name('active-events.index');
        Route::get('/active-events/{event}', [ActiveEventController::class, 'show'])
            ->name('active-events.show');
        Route::get('/active-events-list', [ActiveEventController::class, 'index'])
            ->name('active-events');

        // User Management
        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])
            ->name('users.show');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');
        Route::get('/users-list', [UserController::class, 'index'])
            ->name('users');

        // Reports Moderation
        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');
        Route::get('/reports/{report}', [ReportController::class, 'show'])
            ->name('reports.show');
        Route::post('/reports/{report}/action', [ReportController::class, 'handleAction'])
            ->name('reports.action');
        Route::get('/reports/{report}/evidence', [ReportController::class, 'downloadEvidence'])
            ->name('reports.evidence');
        Route::get('/reports-list', [ReportController::class, 'index'])
            ->name('reports');

        // Transactions
        Route::get('/transactions', [TransactionController::class, 'index'])
            ->name('transactions.index');

        Route::get('/transactions/download-report', [TransactionController::class, 'downloadReport'])
            ->name('transactions.download-report');

        Route::get('/transactions/report/csv', [TransactionController::class, 'downloadCsv'])
            ->name('transactions.download-csv');

        Route::get('/transactions/report/excel', [TransactionController::class, 'downloadExcel'])
            ->name('transactions.download-excel');

        Route::get('/transactions/{payment}', [TransactionController::class, 'show'])
            ->name('transactions.show');

        Route::get('/transactions/{payment}/download', [TransactionController::class, 'downloadInvoice'])
            ->name('transactions.download-invoice');

        Route::get('/transactions-list', [TransactionController::class, 'index'])
            ->name('transactions');
    });

require __DIR__.'/auth.php';
