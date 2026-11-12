<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoryController extends Controller
{
    public function index(): View
    {
        $histories = [
            ['id' => 1, 'event_title' => 'Java Jazz Festival 2024', 'event_organizer' => 'PT Java Festival Production', 'event_category' => 'Musik', 'position' => 'Stage Crew & Sound Assistant', 'location' => 'JIExpo Kemayoran, Jakarta', 'date' => '24 – 26 Mei 2024', 'hours' => 30, 'status' => 'completed', 'status_label' => 'Selesai', 'certificate_available' => true],
            ['id' => 2, 'event_title' => 'Bandung Creative Tech Summit 2024', 'event_organizer' => 'Bandung Digital Hub', 'event_category' => 'Seminar', 'position' => 'Usher & FoH Coordinator', 'location' => 'Sasana Budaya Ganesha (Sabuga), Bandung', 'date' => '12 – 13 Oktober 2024', 'hours' => 18, 'status' => 'completed', 'status_label' => 'Selesai', 'certificate_available' => true],
            ['id' => 3, 'event_title' => 'Jakarta Marathon 2024', 'event_organizer' => 'Yayasan Bumi Lestari', 'event_category' => 'Olahraga', 'position' => 'Water Station & Medical Marshal', 'location' => 'GBK Senayan, Jakarta', 'date' => '18 Agustus 2024', 'hours' => 8, 'status' => 'completed', 'status_label' => 'Selesai', 'certificate_available' => true],
            ['id' => 4, 'event_title' => 'Indonesia Comic Con 2023', 'event_organizer' => 'Panorama Media', 'event_category' => 'Festival', 'position' => 'Ticketing & Registration Crew', 'location' => 'Jakarta Convention Center (JCC)', 'date' => '4 – 5 November 2023', 'hours' => 20, 'status' => 'completed', 'status_label' => 'Selesai', 'certificate_available' => true],
            ['id' => 5, 'event_title' => 'Surabaya Music Fest 2023', 'event_organizer' => 'East Java Entertainment', 'event_category' => 'Musik', 'position' => 'Stage Crew & Sound Assistant', 'location' => 'Grand City Convention Hall, Surabaya', 'date' => '15 – 17 September 2023', 'hours' => 24, 'status' => 'completed', 'status_label' => 'Selesai', 'certificate_available' => true],
            ['id' => 6, 'event_title' => 'AI & Future Tech Conference 2023', 'event_organizer' => 'Nusantara Tech Community', 'event_category' => 'Seminar', 'position' => 'Registration & Info Desk', 'location' => 'Menara Mandiri, Jakarta', 'date' => '20 – 21 Juli 2023', 'hours' => 14, 'status' => 'completed', 'status_label' => 'Selesai', 'certificate_available' => true],
            ['id' => 7, 'event_title' => 'Green Earth Run 2023', 'event_organizer' => 'Yayasan Hijau Indonesia', 'event_category' => 'Olahraga', 'position' => 'Route Marshal & First Aid', 'location' => 'Taman Monas, Jakarta', 'date' => '5 Juni 2023', 'hours' => 6, 'status' => 'completed', 'status_label' => 'Selesai', 'certificate_available' => true],
            ['id' => 8, 'event_title' => 'Startup Summit Indonesia 2023', 'event_organizer' => 'Indonesia Startup Forum', 'event_category' => 'Seminar', 'position' => 'VIP Handler & Documentation', 'location' => 'ICE BSD, Tangerang', 'date' => '10 – 12 Maret 2023', 'hours' => 22, 'status' => 'completed', 'status_label' => 'Selesai', 'certificate_available' => true],
        ];

        return view('crew.history', compact('histories'));
    }

    public function downloadCertificate(int $id)
    {
        $displayName = auth()->user()->name ?? 'Volunteer';

        $events = [
            1 => ['id' => 1, 'event_title' => 'Java Jazz Festival 2024', 'organizer' => 'PT Java Festival Production', 'position' => 'Stage Crew & Sound Assistant', 'location' => 'JIExpo Kemayoran, Jakarta', 'date' => '24 – 26 Mei 2024', 'category' => 'Musik'],
            2 => ['id' => 2, 'event_title' => 'Bandung Creative Tech Summit 2024', 'organizer' => 'Bandung Digital Hub', 'position' => 'Usher & FoH Coordinator', 'location' => 'Sasana Budaya Ganesha (Sabuga), Bandung', 'date' => '12 – 13 Oktober 2024', 'category' => 'Seminar'],
            3 => ['id' => 3, 'event_title' => 'Jakarta Marathon 2024', 'organizer' => 'Yayasan Bumi Lestari', 'position' => 'Water Station & Medical Marshal', 'location' => 'GBK Senayan, Jakarta', 'date' => '18 Agustus 2024', 'category' => 'Olahraga'],
            4 => ['id' => 4, 'event_title' => 'Indonesia Comic Con 2023', 'organizer' => 'Panorama Media', 'position' => 'Ticketing & Registration Crew', 'location' => 'Jakarta Convention Center (JCC)', 'date' => '4 – 5 November 2023', 'category' => 'Festival'],
            5 => ['id' => 5, 'event_title' => 'Surabaya Music Fest 2023', 'organizer' => 'East Java Entertainment', 'position' => 'Stage Crew & Sound Assistant', 'location' => 'Grand City Convention Hall, Surabaya', 'date' => '15 – 17 September 2023', 'category' => 'Musik'],
            6 => ['id' => 6, 'event_title' => 'AI & Future Tech Conference 2023', 'organizer' => 'Nusantara Tech Community', 'position' => 'Registration & Info Desk', 'location' => 'Menara Mandiri, Jakarta', 'date' => '20 – 21 Juli 2023', 'category' => 'Seminar'],
            7 => ['id' => 7, 'event_title' => 'Green Earth Run 2023', 'organizer' => 'Yayasan Hijau Indonesia', 'position' => 'Route Marshal & First Aid', 'location' => 'Taman Monas, Jakarta', 'date' => '5 Juni 2023', 'category' => 'Olahraga'],
            8 => ['id' => 8, 'event_title' => 'Startup Summit Indonesia 2023', 'organizer' => 'Indonesia Startup Forum', 'position' => 'VIP Handler & Documentation', 'location' => 'ICE BSD, Tangerang', 'date' => '10 – 12 Maret 2023', 'category' => 'Seminar'],
        ];

        $event = $events[$id] ?? null;

        if (!$event) {
            abort(404, 'Sertifikat tidak ditemukan untuk ID: ' . $id);
        }

        $pdf = Pdf::loadView('crew.certificate-pdf', compact('event', 'displayName'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'Sertifikat-' . str_replace(' ', '-', $event['event_title']) . '-' . str_replace(' ', '-', $displayName) . '.pdf';

        return $pdf->download($filename);
    }

    public function downloadPdf()
    {
        $histories = [
            ['id' => 1, 'event_title' => 'Java Jazz Festival 2024', 'event_organizer' => 'PT Java Festival Production', 'event_category' => 'Musik', 'position' => 'Stage Crew', 'date' => '24–26 Mei 2024', 'hours' => 30],
            ['id' => 2, 'event_title' => 'Bandung Creative Tech Summit 2024', 'event_organizer' => 'Bandung Digital Hub', 'event_category' => 'Seminar', 'position' => 'Usher', 'date' => '12–13 Okt 2024', 'hours' => 18],
        ];

        $displayName = auth()->user()->name ?? 'Volunteer';

        $pdf = Pdf::loadView('crew.history-pdf', compact('histories', 'displayName'));

        return $pdf->download('rekap-riwayat-volunteer.pdf');
    }
}