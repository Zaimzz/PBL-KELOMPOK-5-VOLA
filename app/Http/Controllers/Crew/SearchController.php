<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->input('q', '');

        // Data dummy event (nanti bisa diganti dengan query database)
        $allEvents = [
            [
                'id' => 1,
                'title' => 'Indonesia Comic & Pop Culture Con 2025',
                'category' => 'Festival & Pop Culture',
                'filter_category' => 'Festival & Seni',
                'organizer' => 'PT Pop Media Nusantara',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '14 – 16 Juni 2025',
                'location' => 'JCC Senayan, Jakarta Pusat',
                'positions' => 'Liaison Officer (LO), Stage Crew, Registrasi, Usher, Media',
                'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
            ],
            [
                'id' => 2,
                'title' => 'Jakarta International Java Jazz Festival 2025',
                'category' => 'Musik & Konser',
                'filter_category' => 'Musik & Konser',
                'organizer' => 'PT Java Festival Production',
                'is_verified' => true,
                'status_badge' => 'Sisa 12 Hari',
                'status_badge_type' => 'warning',
                'date' => '5 – 7 Maret 2025',
                'location' => 'JIExpo Kemayoran, Jakarta Pusat',
                'positions' => 'Water Marshal, Media, Route',
                'image' => 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=800',
            ],
            [
                'id' => 3,
                'title' => 'Bandung Creative Tech Summit 2025',
                'category' => 'Seminar & Teknologi',
                'filter_category' => 'Seminar & Teknologi',
                'organizer' => 'Bandung Digital Hub',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '10 – 11 April 2025',
                'location' => 'Bandung Digital Hub, Bandung',
                'positions' => 'Exhibition Crew, Guest Relation',
                'image' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=800',
            ],
            [
                'id' => 4,
                'title' => 'Yogyakarta Heritage Marathon 2025',
                'category' => 'Olahraga & Marathon',
                'filter_category' => 'Olahraga & Marathon',
                'organizer' => 'Yogya Runners Community',
                'is_verified' => false,
                'status_badge' => 'Sisa 20 Hari',
                'status_badge_type' => 'warning',
                'date' => '25 Mei 2025',
                'location' => 'Malioboro, Yogyakarta',
                'positions' => 'Route Marshal, Water Station, Medical',
                'image' => 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=800',
            ],
            [
                'id' => 5,
                'title' => 'Bali Beach Cleanup & Social Action',
                'category' => 'Kegiatan Sosial',
                'filter_category' => 'Kegiatan Sosial',
                'organizer' => 'Ocean Guardians Bali',
                'is_verified' => true,
                'status_badge' => 'Open Volunteer',
                'status_badge_type' => 'success',
                'date' => '15 Juni 2025',
                'location' => 'Kuta Beach, Bali',
                'positions' => 'Koordinator Lapangan, Dokumentasi, Logistik',
                'image' => 'https://images.unsplash.com/photo-1618477247222-acbdb0e159b3?w=800',
            ],
        ];

        // Filter event berdasarkan query pencarian
        $events = collect($allEvents)->filter(function ($event) use ($query) {
            if (empty($query)) {
                return true; // Tampilkan semua jika tidak ada query
            }

            $searchable = strtolower($query);

            return str_contains(strtolower($event['title']), $searchable)
                || str_contains(strtolower($event['category']), $searchable)
                || str_contains(strtolower($event['organizer']), $searchable)
                || str_contains(strtolower($event['location']), $searchable)
                || str_contains(strtolower($event['positions']), $searchable);
        })->values()->all();

        $categories = ['Semua', 'Festival & Seni', 'Seminar & Teknologi', 'Kepanitiaan Kampus', 'Kegiatan Sosial', 'Olahraga & Marathon', 'Musik & Konser'];

        return view('crew.search', compact('events', 'query', 'categories'));
    }
}
