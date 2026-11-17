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
        $awaitingPaymentCount = 0;
        $rejectedCount = 0;

        if ($organizerProfile) {
            $events = Event::where('organizer_id', $organizerProfile->id)
                ->with(['category', 'positions'])
                ->orderByDesc('created_at')
                ->take(5)
                ->get();

            $statusCounts = Event::query()
                ->where('organizer_id', $organizerProfile->id)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $totalEvents = (int) $statusCounts->sum();
            $draftCount = (int) ($statusCounts[EventStatus::Draft->value] ?? 0);
            $pendingReviewCount = (int) ($statusCounts[EventStatus::PendingReview->value] ?? 0);
            $awaitingPaymentCount = (int) ($statusCounts[EventStatus::AwaitingPayment->value] ?? 0);
            $rejectedCount = (int) ($statusCounts[EventStatus::Rejected->value] ?? 0);
        }

        return view('eo.dashboard', compact(
            'organizerProfile',
            'events',
            'totalEvents',
            'draftCount',
            'pendingReviewCount',
            'awaitingPaymentCount',
            'rejectedCount',
        ));
    }
}
