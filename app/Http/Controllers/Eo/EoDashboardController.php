<?php

namespace App\Http\Controllers\Eo;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EoDashboardController extends Controller
{
    /**
     * Show the EO dashboard / beranda.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $organizerProfile = $user->organizerProfile;

        $events = collect();
        $totalEvents = 0;
        $draftCount = 0;
        $pendingReviewCount = 0;
        $rejectedCount = 0;

        if ($organizerProfile) {
            $events = Event::where('organizer_id', $organizerProfile->id)
                ->with(['category', 'positions'])
                ->orderByDesc('created_at')
                ->take(5)
                ->get();

            $allEvents = Event::where('organizer_id', $organizerProfile->id)->get();

            $totalEvents = $allEvents->count();
            $draftCount = $allEvents->where('status', EventStatus::Draft)->count();
            $pendingReviewCount = $allEvents->where('status', EventStatus::PendingReview)->count();
            $rejectedCount = $allEvents->where('status', EventStatus::Rejected)->count();
        }

        return view('eo.dashboard', compact(
            'organizerProfile',
            'events',
            'totalEvents',
            'draftCount',
            'pendingReviewCount',
            'rejectedCount',
        ));
    }
}
