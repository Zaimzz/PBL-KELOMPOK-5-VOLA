<?php

namespace App\Http\Controllers\Eo;

use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Eo\StoreEventRequest;
use App\Http\Requests\Eo\UpdateEventRequest;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of the EO's events.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $organizerProfile = $user->organizerProfile;

        if (! $organizerProfile) {
            return view('eo.events.index', [
                'events' => collect(),
                'organizerProfile' => null,
                'statusFilter' => null,
            ]);
        }

        $query = Event::where('organizer_id', $organizerProfile->id)
            ->with(['category', 'positions']);

        // Filter by status if provided
        $statusFilter = $request->query('status');
        if ($statusFilter && EventStatus::tryFrom($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $events = $query->orderByDesc('created_at')->paginate(12);

        return view('eo.events.index', compact('events', 'organizerProfile', 'statusFilter'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', Event::class);

        $categories = EventCategory::orderBy('name')->get();

        return view('eo.events.create', compact('categories'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {
        Gate::authorize('create', Event::class);

        $user = $request->user();
        $organizerProfile = $user->organizerProfile;

        if (! $organizerProfile) {
            return redirect()->route('eo.organization')
                ->with('error', 'Anda harus membuat profil organisasi terlebih dahulu.');
        }

        $data = $request->validated();

        // Handle poster upload
        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('posters', 'public');
        }

        // Determine action: save as draft or keep as draft by default
        $status = EventStatus::Draft;

        // Generate unique slug
        $slug = Str::slug($data['title']);
        $slugBase = $slug;
        $counter = 1;
        while (Event::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $slugBase.'-'.$counter++;
        }

        $event = Event::create([
            'organizer_id' => $organizerProfile->id,
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'poster_path' => $posterPath,
            'location' => $data['location'] ?? null,
            'city' => $data['city'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'registration_deadline' => $data['registration_deadline'] ?? null,
            'contact_info' => $data['contact_info'] ?? null,
            'coordination_link' => $data['coordination_link'] ?? null,
            'pic_name' => $data['pic_name'] ?? null,
            'pic_phone' => $data['pic_phone'] ?? null,
            'benefits' => $data['benefits'] ?? null,
            'status' => $status,
        ]);

        return redirect()->route('eo.events.edit', $event)
            ->with('success', 'Event berhasil disimpan sebagai draft. Tambahkan posisi untuk melengkapi event Anda.');
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Request $request, Event $event): View
    {
        Gate::authorize('update', $event);

        $categories = EventCategory::orderBy('name')->get();
        $event->load('positions');

        return view('eo.events.edit', compact('event', 'categories'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        Gate::authorize('update', $event);

        $data = $request->validated();

        // Handle poster upload
        if ($request->hasFile('poster')) {
            // Delete old poster if exists
            if ($event->poster_path) {
                Storage::disk('public')->delete($event->poster_path);
            }
            $data['poster_path'] = $request->file('poster')->store('posters', 'public');
        }

        // Remove poster key from data (not a DB column directly)
        unset($data['poster']);

        // Update slug if title changed
        if ($data['title'] !== $event->title) {
            $slug = Str::slug($data['title']);
            $slugBase = $slug;
            $counter = 1;
            while (Event::withTrashed()->where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                $slug = $slugBase.'-'.$counter++;
            }
            $data['slug'] = $slug;
        }

        $event->update($data);

        return redirect()->route('eo.events.edit', $event)
            ->with('success', 'Event berhasil diperbarui.');
    }

    /**
     * Remove the specified event from storage (soft delete).
     */
    public function destroy(Request $request, Event $event): RedirectResponse
    {
        Gate::authorize('delete', $event);

        // Delete poster if exists
        if ($event->poster_path) {
            Storage::disk('public')->delete($event->poster_path);
        }

        $event->delete();

        return redirect()->route('eo.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    /**
     * Submit event for admin review.
     * Transitions: draft → pending_review
     */
    public function submit(Request $request, Event $event): RedirectResponse
    {
        Gate::authorize('submit', $event);

        $user = $request->user();
        $organizerProfile = $user->organizerProfile;

        // Check EO verification status
        if (
            ! $organizerProfile ||
            $organizerProfile->verification_status !== VerificationStatus::Verified
        ) {
            return back()->with(
                'error',
                'Akun Anda belum terverifikasi oleh admin. Anda tidak dapat mengirim event untuk direview.'
            );
        }

        // Validate event status
        if (! in_array($event->status, [EventStatus::Draft, EventStatus::Rejected], true)) {
            return back()->with('error', 'Hanya event berstatus Draft atau Ditolak yang dapat diajukan untuk review.');
        }

        // Validate required fields
        $missingFields = [];
        if (! $event->title) {
            $missingFields[] = 'Nama Event';
        }
        if (! $event->category_id) {
            $missingFields[] = 'Kategori';
        }
        if (! $event->description) {
            $missingFields[] = 'Deskripsi';
        }
        if (! $event->location) {
            $missingFields[] = 'Lokasi';
        }
        if (! $event->start_date) {
            $missingFields[] = 'Tanggal Mulai';
        }
        if (! $event->end_date) {
            $missingFields[] = 'Tanggal Selesai';
        }
        if (! $event->registration_deadline) {
            $missingFields[] = 'Batas Pendaftaran';
        }
        if (! $event->contact_info) {
            $missingFields[] = 'Info Kontak';
        }

        if (! empty($missingFields)) {
            return back()->with(
                'error',
                'Informasi event belum lengkap. Field yang harus diisi: '.implode(', ', $missingFields).'.'
            );
        }

        // Validate at least 1 position
        if ($event->positions()->count() === 0) {
            return back()->with('error', 'Event harus memiliki minimal 1 posisi sebelum dapat diajukan untuk review.');
        }

        // Transition to pending_review
        $event->update(['status' => EventStatus::PendingReview]);

        return redirect()->route('eo.events.index')
            ->with('success', 'Event berhasil diajukan untuk review admin. Kami akan menghubungi Anda setelah proses review selesai.');
    }
}
