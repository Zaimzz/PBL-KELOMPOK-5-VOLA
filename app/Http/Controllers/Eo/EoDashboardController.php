<?php

namespace App\Http\Controllers\Eo;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            // Cache hanya menyimpan array/scalar agar tidak bergantung pada
            // unserialization objek (Collection/Model/Carbon) di store file.
            $payload = Cache::remember('eo_dashboard_'.$organizerProfile->id, 60, function () use ($organizerProfile): array {
                $dbEvents = Event::where('organizer_id', $organizerProfile->id)
                    ->with(['category', 'positions'])
                    ->orderByDesc('created_at')
                    ->take(5)
                    ->get();

                $eventRows = $dbEvents->map(fn (Event $event): array => [
                    'attributes' => $event->getRawOriginal(),
                    'category' => $event->category?->getRawOriginal(),
                    'positions' => $event->positions->map(fn (EventPosition $position): array => $position->getRawOriginal())->all(),
                ])->all();

                $statusCounts = Event::query()
                    ->where('organizer_id', $organizerProfile->id)
                    ->selectRaw('status, COUNT(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->all();

                return ['events' => $eventRows, 'statusCounts' => $statusCounts];
            });

            $events = collect($payload['events'])->map(function (array $row): Event {
                $event = (new Event)->setRawAttributes($row['attributes']);
                $event->exists = true;

                $category = $row['category'] !== null
                    ? (new EventCategory)->setRawAttributes($row['category'])
                    : null;

                $event->setRelation('category', $category);
                $event->setRelation('positions', EventPosition::hydrate($row['positions']));

                return $event;
            });

            $statusCounts = $payload['statusCounts'];

            $totalEvents = (int) array_sum($statusCounts);
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
