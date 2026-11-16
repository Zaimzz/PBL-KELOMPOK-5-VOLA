<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyEventController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $profile = $user->volunteerProfile;

        $events = collect([]);

        if ($profile) {
            $applications = Application::where('volunteer_profile_id', $profile->id)
                ->where('status', 'accepted')
                ->with(['position.event.organizer', 'position.event.category'])
                ->get();

            $events = $applications->map(function ($app) {
                $event = $app->position->event;
               
                $now = now();
                $startDate = $event->start_date;
                $endDate = $event->end_date ?? $event->start_date;

                $status = 'upcoming';
                if ($now >= $startDate && $now <= $endDate) {
                    $status = 'ongoing';
                } elseif ($now > $endDate) {
                    $status = 'completed';
                }

                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'image' => $event->poster_path ? asset('storage/' . $event->poster_path) : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
                    'organizer' => $event->organizer->organization_name ?? 'Unknown Organizer',
                    'date' => $event->start_date->translatedFormat('d M Y') . ($event->end_date && $event->end_date != $event->start_date ? ' - ' . $event->end_date->translatedFormat('d M Y') : ''),
                    'location' => trim(($event->location ?? '') . ', ' . ($event->city ?? '')),
                    'position' => $app->position->name,
                    'category' => $event->category->name ?? 'Umum',
                    'status' => $status,
                    'application_id' => $app->id,
                    'crew_id' => 'CRW-' . str_pad($app->id, 5, '0', STR_PAD_LEFT), 
                ];
            });
        }

        return view('crew.my-events', compact('events'));
    }
}