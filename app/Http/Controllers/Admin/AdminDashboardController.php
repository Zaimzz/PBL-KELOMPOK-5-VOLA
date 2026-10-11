<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\OrganizerProfile;
use App\Models\Payment;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $pendingEoCount = OrganizerProfile::where('verification_status', VerificationStatus::Pending)->count();
        $pendingReviewEventCount = Event::where('status', EventStatus::PendingReview)->count();
        $activeEventCount = Event::where('status', EventStatus::Active)->count();
        $transactionCount = Payment::count();

        $actionRequired = collect();

        // 1. Pending Event Reviews
        $pendingEvents = Event::with('organizer')
            ->where('status', EventStatus::PendingReview)
            ->latest()
            ->take(3)
            ->get();

        foreach ($pendingEvents as $event) {
            $actionRequired->push([
                'type' => 'event',
                'title' => $event->title,
                'subtitle' => 'Penyelenggara: '.($event->organizer->organization_name ?? '-'),
                'time' => $event->updated_at->diffForHumans(),
                'action_text' => 'Review Event',
                'action_link' => '#',
                'icon_color' => 'bg-indigo-100 text-indigo-600',
            ]);
        }

        // 2. Pending EO Verifications
        $pendingEos = OrganizerProfile::where('verification_status', VerificationStatus::Pending)
            ->latest()
            ->take(3)
            ->get();

        foreach ($pendingEos as $eo) {
            $actionRequired->push([
                'type' => 'eo',
                'title' => $eo->organization_name,
                'subtitle' => 'Dokumen terlampir',
                'time' => $eo->updated_at->diffForHumans(),
                'action_text' => 'Periksa Dokumen',
                'action_link' => route('admin.eo-verifications', ['search' => $eo->organization_name]),
                'icon_color' => 'bg-amber-100 text-amber-600',
            ]);
        }

        $actionRequired = $actionRequired->sortByDesc('time')->take(4);

        return view('admin.dashboard', compact(
            'pendingEoCount',
            'pendingReviewEventCount',
            'activeEventCount',
            'transactionCount',
            'actionRequired'
        ));
    }
}
