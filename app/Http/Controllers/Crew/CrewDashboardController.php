<?php

namespace App\Http\Controllers\Crew;

use App\Enums\ApplicationStatus;
use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CrewDashboardController extends Controller
{
    public function index(): View
    {
        $authUser = auth()->user();
        $firstName = explode(' ', $authUser->name)[0] ?? 'Crew';

        $volunteerProfile = $authUser->volunteerProfile;

        $activeApplicationsCount = 0;
        $pendingReviewCount = 0;
        $acceptedCount = 0;

        if ($volunteerProfile !== null) {
            $applications = $volunteerProfile->applications;
            $activeApplicationsCount = $applications
                ->whereNotIn('status', [ApplicationStatus::Withdrawn, ApplicationStatus::Rejected])
                ->count();
            $pendingReviewCount = $applications
                ->where('status', ApplicationStatus::Pending)
                ->count();
            $acceptedCount = $applications
                ->where('status', ApplicationStatus::Accepted)
                ->count();
        }

        $dbEvents = Event::query()
            ->with(['category', 'organizer', 'positions'])
            ->where('status', EventStatus::Active)
            ->whereNotNull('published_at')
            ->whereDate('registration_deadline', '>=', now()->toDateString())
            ->latest('published_at')
            ->take(4)
            ->get();

        $recommendedEvents = $dbEvents->map(function (Event $event): array {
            $positionNames = $event->positions->pluck('name');
            $positionsText = $positionNames->take(3)->implode(', ');
            if ($positionNames->count() > 3) {
                $positionsText .= '... ('.$positionNames->count().' posisi)';
            }

            $startDate = $event->start_date?->translatedFormat('d M Y');
            $endDate = $event->end_date?->translatedFormat('d M Y');
            $dateFormatted = $endDate && $endDate !== $startDate
                ? $startDate.' – '.$endDate
                : ($startDate ?? '-');

            $imageUrl = $event->poster_path
                ? Storage::url($event->poster_path)
                : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800';

            $isVerified = $event->organizer?->verification_status === VerificationStatus::Verified;

            return [
                'id' => $event->id,
                'title' => $event->title,
                'category' => $event->category?->name ?? 'Umum',
                'filter_category' => strtolower($event->category?->name ?? 'umum'),
                'organizer' => $event->organizer?->organization_name ?? '-',
                'organizer_verified' => $isVerified,
                'location' => trim(($event->location ?? '').($event->city ? ', '.$event->city : ''), ', '),
                'date' => $dateFormatted,
                'status' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'image' => $imageUrl,
                'positions_list' => $positionsText ?: 'Belum ada posisi',
            ];
        })->values()->all();

        $categories = [
            ['slug' => 'all', 'name' => 'Semua'],
            ['slug' => 'festival', 'name' => 'Festival'],
            ['slug' => 'seminar', 'name' => 'Seminar'],
            ['slug' => 'musik', 'name' => 'Musik'],
            ['slug' => 'olahraga', 'name' => 'Olahraga'],
            ['slug' => 'sosial', 'name' => 'Sosial'],
        ];

        return view('crew.dashboard', compact(
            'firstName',
            'recommendedEvents',
            'activeApplicationsCount',
            'acceptedCount',
            'pendingReviewCount',
            'categories'
        ));
    }
}
