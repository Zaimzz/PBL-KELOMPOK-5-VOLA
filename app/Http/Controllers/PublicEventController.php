<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\View\View;

class PublicEventController extends Controller
{
    public function show(string $slug): View
    {
        $event = Event::query()
            ->with(['category', 'organizer', 'positions'])
            ->where('slug', $slug)
            ->where('status', EventStatus::Active)
            ->whereNotNull('published_at')
            ->whereDate(
                'registration_deadline',
                '>=',
                now()->toDateString()
            )
            ->firstOrFail();

        return view('events.show', compact('event'));
    }
}