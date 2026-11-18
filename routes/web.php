<?php

use App\Enums\EventStatus;
use App\Http\Controllers\Admin\ActiveEventController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminNavbarController;
use App\Http\Controllers\Admin\EoVerificationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewEventController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Crew\ApplicationController;
use App\Http\Controllers\Crew\CrewDashboardController;
use App\Http\Controllers\Crew\EventDetailController;
use App\Http\Controllers\Crew\HistoryController;
use App\Http\Controllers\Crew\MyEventController;
use App\Http\Controllers\Crew\NotificationController;
use App\Http\Controllers\Crew\ProfileController as CrewProfileController;
use App\Http\Controllers\Crew\ReportController as CrewReportController;
use App\Http\Controllers\Crew\SearchController;
use App\Http\Controllers\Crew\SettingsController;
use App\Http\Controllers\Eo\ApplicantController;
use App\Http\Controllers\Eo\BillingController;
use App\Http\Controllers\Eo\CrewCoordinationController;
use App\Http\Controllers\Eo\EoDashboardController;
use App\Http\Controllers\Eo\EventController;
use App\Http\Controllers\Eo\EventPositionController;
use App\Http\Controllers\Eo\OrganizerProfileController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicEventController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Route Crew (Volunteer)
Route::middleware(['auth', 'verified', 'role:volunteer'])->group(function () {
    Route::get('/dashboard', [CrewDashboardController::class, 'index'])
        ->name('dashboard');

    // Route Profil Volunteer
    Route::get('/profile', [CrewProfileController::class, 'show'])
        ->name('profile.show');

    Route::get('/profile/edit', [CrewProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [CrewProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/skills', [CrewProfileController::class, 'updateSkills'])
        ->name('profile.update.skills');

    Route::post('/profile/documents/upload', [CrewProfileController::class, 'uploadDocument'])
        ->name('profile.documents.upload');

    Route::delete('/profile/documents/{type}', [CrewProfileController::class, 'deleteDocument'])
        ->name('profile.documents.delete');

    // Route Settings
    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index');

    Route::put('/settings/email', [SettingsController::class, 'updateEmail'])
        ->name('settings.update.email');

    Route::put('/settings/phone', [SettingsController::class, 'updatePhone'])
        ->name('settings.update.phone');

    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])
        ->name('settings.update.password');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/email', [SettingsController::class, 'updateEmail'])->name('settings.update.email');
    Route::put('/settings/phone', [SettingsController::class, 'updatePhone'])->name('settings.update.phone');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.update.password');
    // Route Pencarian dan Detail Event
    Route::get('/search', [SearchController::class, 'index'])
        ->name('search.index');

    Route::get('/events/{event}', [EventDetailController::class, 'show'])
        ->whereNumber('event')
        ->name('events.detail');

    // Route Notifikasi Volunteer
    Route::get('/api/notifications', [NotificationController::class, 'index'])
        ->name('api.notifications.index');
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('api.notifications.read');
    Route::post('/api/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('api.notifications.read-all');

    // Lamaran Saya
    Route::get('/lamaran-saya', [ApplicationController::class, 'index'])
        ->name('applications.index');

    Route::post('/lamaran/ajukan', [ApplicationController::class, 'store'])
        ->name('applications.store');

    Route::get('/lamaran-saya/{id}/download', [ApplicationController::class, 'downloadCertificate'])
        ->name('applications.download');

    Route::get('/lamaran-saya/{id}/bukti-penerimaan', [ApplicationController::class, 'downloadAcceptanceLetter'])
        ->name('applications.download-acceptance');
    // Event Saya
    Route::get('/event-saya', [MyEventController::class, 'index'])
        ->name('my-events.index');

    // Riwayat Pengalaman
    Route::get('/riwayat', [HistoryController::class, 'index'])
        ->name('history.index');

    Route::get('/riwayat/sertifikat/{id}', [HistoryController::class, 'downloadCertificate'])
        ->name('history.downloadCertificate');

    Route::get('/riwayat/download-pdf', [HistoryController::class, 'downloadPdf'])
        ->name('history.downloadPdf');

    // Laporkan EO / Event oleh Crew
    Route::post('/reports', [CrewReportController::class, 'store'])
        ->name('crew.reports.store');
});

// Public Event Detail
Route::get('/events/{slug}', [PublicEventController::class, 'show'])
    ->name('events.show');

// Route Event Organizer (EO)
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

        Route::get('/events/{event}', [EventController::class, 'show'])
            ->whereNumber('event')
            ->name('events.show');

        Route::get('/events/{event}/edit', [EventController::class, 'edit'])
            ->name('events.edit');

        Route::put('/events/{event}', [EventController::class, 'update'])
            ->name('events.update');

        Route::delete('/events/{event}', [EventController::class, 'destroy'])
            ->name('events.destroy');

        Route::post('/events/{event}/submit', [EventController::class, 'submit'])
            ->middleware('eo.verified')
            ->name('events.submit');

        Route::post('/events/{event}/close', [EventController::class, 'close'])
            ->name('events.close');

        Route::post('/events/{event}/finish', [EventController::class, 'finish'])
            ->name('events.finish');

        Route::post('/events/{event}/positions', [EventPositionController::class, 'store'])
            ->name('events.positions.store');

        Route::post('/events/{event}/positions/bulk', [EventPositionController::class, 'storeMany'])
            ->name('events.positions.store-many');

        Route::get('/position-recommendation', [EventPositionController::class, 'recommendation'])
            ->name('positions.recommendation');

        Route::put('/events/{event}/positions/{position}', [EventPositionController::class, 'update'])
            ->name('events.positions.update');

        Route::delete('/events/{event}/positions/{position}', [EventPositionController::class, 'destroy'])
            ->name('events.positions.destroy');

        // Pendaftar (Applicant Management)
        Route::get('/pendaftar', [ApplicantController::class, 'index'])
            ->name('applicants.index');

        Route::patch('/pendaftar/{application}/accept', [ApplicantController::class, 'accept'])
            ->name('applicants.accept');

        Route::patch('/pendaftar/{application}/reject', [ApplicantController::class, 'reject'])
            ->name('applicants.reject');

        // Koordinasi Crew
        Route::get('/crew', [CrewCoordinationController::class, 'index'])
            ->name('crew.index');

        Route::get('/crew/export', [CrewCoordinationController::class, 'export'])
            ->name('crew.export');

        // Pembayaran & Tagihan
        Route::get('/billing', [BillingController::class, 'index'])
            ->name('billing.index');

        Route::post('/billing/invoice', [BillingController::class, 'createInvoice'])
            ->name('billing.invoice.create');

        Route::get('/billing/{payment}', [BillingController::class, 'show'])
            ->name('billing.show');

        Route::get('/billing/{payment}/download', [BillingController::class, 'downloadInvoice'])
            ->name('billing.download');

        Route::get('/billing/{payment}/status', [BillingController::class, 'status'])
            ->name('billing.status');

        Route::get('/billing/{payment}/finish', [BillingController::class, 'finishPayment'])
            ->name('billing.finish');

        Route::post('/billing/{payment}/verify', [BillingController::class, 'verify'])
            ->name('billing.verify');
    });

// Route Admin
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Verifikasi EO
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

        // Alias route Review Events
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

        // API Navbar Admin (Global Search & Notifikasi Dinamis)
        Route::get('/api/global-search', [AdminNavbarController::class, 'globalSearch'])
            ->name('api.global-search');
        Route::get('/api/notifications', [AdminNavbarController::class, 'getNotifications'])
            ->name('api.notifications');
        Route::post('/api/notifications/mark-all-read', [AdminNavbarController::class, 'markAllAsRead'])
            ->name('api.notifications.mark-all-read');
        Route::post('/api/notifications/{id}/read', [AdminNavbarController::class, 'markAsRead'])
            ->name('api.notifications.read');
    });

// Midtrans Webhook — no auth, no CSRF (excluded via bootstrap/app.php or VerifyCsrfToken)
Route::post('/webhooks/midtrans', [MidtransWebhookController::class, 'handle'])
    ->name('webhooks.midtrans');

require __DIR__.'/auth.php';
