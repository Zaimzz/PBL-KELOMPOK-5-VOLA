<?php

namespace App\Http\Controllers\Eo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Eo\StoreEventPositionRequest;
use App\Http\Requests\Eo\StoreEventPositionsRequest;
use App\Models\Event;
use App\Models\EventPosition;
use App\Models\PositionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class EventPositionController extends Controller
{
    /**
     * Store several positions (from the catalog and/or manual) at once.
     */
    public function storeMany(StoreEventPositionsRequest $request, Event $event): RedirectResponse
    {
        Gate::authorize('managePositions', $event);

        $positions = $request->validated('positions');

        DB::transaction(function () use ($event, $positions): void {
            foreach ($positions as $position) {
                $event->positions()->create([
                    'position_type_id' => $position['position_type_id'] ?? null,
                    'name' => $position['name'],
                    'category' => $position['category'] ?? null,
                    'quota' => $position['quota'],
                    'description' => $position['description'] ?? null,
                    'requirements' => $position['requirements'] ?? null,
                ]);
            }
        });

        return redirect()->route('eo.events.edit', $event)
            ->with('success', count($positions).' posisi berhasil ditambahkan.');
    }

    /**
     * Suggest a description and requirements for a manually typed position name.
     */
    public function recommendation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $recommendation = PositionType::recommendationFor((string) ($validated['name'] ?? ''));

        return response()->json([
            'found' => $recommendation !== null,
            'description' => $recommendation['description'] ?? '',
            'requirements' => $recommendation['requirements'] ?? '',
        ]);
    }

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
