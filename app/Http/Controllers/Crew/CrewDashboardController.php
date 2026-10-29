<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Models\Application;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CrewDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $profile = $user->volunteerProfile;

        $userName = $user?->name ?? 'Crew';
        $firstName = explode(' ', trim($userName))[0];

        $applications = Application::query()
            ->when(
                $profile,
                fn ($query) => $query->where(
                    'volunteer_profile_id',
                    $profile->id
                ),
                fn ($query) => $query->whereRaw('1 = 0')
            );

        $activeApplicationsCount = (clone $applications)->count();

        $pendingReviewCount = (clone $applications)
            ->where('status', 'pending')
            ->count();

        $acceptedCount = (clone $applications)
            ->where('status', 'accepted')
            ->count();

        $categories = [
            'Semua',
            'Festival',
            'Seminar',
            'Sosial',
            'Kampus',
            'Komunitas',
            'Olahraga',
        ];

        $eventsQuery = Event::query()
            ->with(['category', 'organizer', 'positions'])
            ->where('status', EventStatus::Active)
            ->whereDate('registration_deadline', '>=', today())
            ->whereHas('organizer', function ($query) {
                $query->where(
                    'verification_status',
                    VerificationStatus::Verified
                );
            });

        if ($request->filled('category')) {
            $eventsQuery->whereHas('category', function ($query) use ($request) {
                $query->where('slug', $request->input('category'));
            });
        }

        $events = $eventsQuery
            ->orderBy('start_date')
            ->get();

        $recommendedEvents = $events->map(function ($event) {
            $categoryName = $event->category?->name ?? 'Lainnya';

            return [
                'id' => $event->id,
                'title' => $event->title,
                'category' => $categoryName,
                'filter_category' => $categoryName,
                'organizer' => $event->organizer?->organization_name
                    ?? 'Penyelenggara',
                'is_verified' => true,
                'status_badge' => 'Pendaftaran Buka',
                'status_badge_type' => 'success',
                'date' => $event->start_date
                    ? $event->start_date->translatedFormat('d M Y')
                    : '-',
                'location' => $event->location ?: ($event->city ?? '-'),
                'positions' => $event->positions
                    ->pluck('name')
                    ->filter()
                    ->implode(', '),
                'image' => $event->poster_path
                    ? asset('storage/' . $event->poster_path)
                    : asset('images/dashboard-crew/tech-summit.jpg'),
                'is_active' => true,
                'deadline' => $event->registration_deadline?->format('Y-m-d'),
            ];
        })->values()->all();

        return view('crew.dashboard', compact(
            'user',
            'profile',
            'firstName',
            'activeApplicationsCount',
            'pendingReviewCount',
            'acceptedCount',
            'categories',
            'recommendedEvents'
        ));
    }
}
