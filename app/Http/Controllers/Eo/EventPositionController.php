<?php

namespace App\Http\Controllers\Eo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Eo\StoreEventPositionRequest;
use App\Models\Event;
use App\Models\EventPosition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class EventPositionController extends Controller
{
    /**
     * Store a newly created event position.
     */
    public function store(StoreEventPositionRequest $request, Event $event): RedirectResponse
    {
        Gate::authorize('managePositions', $event);

        $event->positions()->create($request->validated());

        return redirect()->route('eo.events.edit', $event)
            ->with('success', 'Posisi berhasil ditambahkan.');
    }

    /**
     * Update the specified event position.
     */
    public function update(StoreEventPositionRequest $request, Event $event, EventPosition $position): RedirectResponse
    {
        Gate::authorize('managePositions', $event);

        // Ensure position belongs to this event
        if ($position->event_id !== $event->id) {
            abort(403, 'Posisi ini bukan milik event tersebut.');
        }

        $position->update($request->validated());

        return redirect()->route('eo.events.edit', $event)
            ->with('success', 'Posisi berhasil diperbarui.');
    }

    /**
     * Remove the specified event position.
     */
    public function destroy(Event $event, EventPosition $position): RedirectResponse
    {
        Gate::authorize('managePositions', $event);

        // Ensure position belongs to this event
        if ($position->event_id !== $event->id) {
            abort(403, 'Posisi ini bukan milik event tersebut.');
        }

        $position->delete();

        return redirect()->route('eo.events.edit', $event)
            ->with('success', 'Posisi berhasil dihapus.');
    }
}
