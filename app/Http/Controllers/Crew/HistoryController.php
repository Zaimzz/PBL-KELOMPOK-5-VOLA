<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HistoryController extends Controller
{
   
    private function getHistoriesData(): array
    {
        return [
            [
                'id' => 1,
                'event_title' => 'Java Jazz Festival 2024',
                'organizer' => 'PT Java Festival Production',
                'position' => 'Liaison Officer (LO) Artis Internasional',
                'date' => '24 – 26 Mei 2024',
                'location' => 'JIExpo Kemayoran, Jakarta',
                'category' => 'Musik',
                'status_label' => 'Selesai',
            ],
            [
                'id' => 2,
                'event_title' => 'Bandung Creative Tech Summit 2024',
                'organizer' => 'Bandung Digital Hub',
                'position' => 'Usher & FoH Coordinator',
                'date' => '12 – 13 Oktober 2024',
                'location' => 'Sasana Budaya Ganesa (Sabuga), Bandung',
                'category' => 'Seminar',
                'status_label' => 'Selesai',
            ],
            [
                'id' => 3,
                'event_title' => 'Jakarta Marathon 2024',
                'organizer' => 'Yayasan Bumi Lestari',
                'position' => 'Water Station & Medical Marshal',
                'date' => '18 Agustus 2024',
                'location' => 'GBK Senayan, Jakarta',
                'category' => 'Olahraga',
                'status_label' => 'Selesai',
            ],
            [
                'id' => 4,
                'event_title' => 'Indonesia Comic Con 2023',
                'organizer' => 'Panorama Media',
                'position' => 'Ticketing & Registration Crew',
                'date' => '4 – 5 November 2023',
                'location' => 'Jakarta Convention Center (JCC)',
                'category' => 'Festival',
                'status_label' => 'Selesai',
            ],
            [
                'id' => 5,
                'event_title' => 'Surabaya Music Fest 2023',
                'organizer' => 'East Java Entertainment',
                'position' => 'Stage Crew & Sound Assistant',
                'date' => '15 – 17 September 2023',
                'location' => 'Grand City Convention Hall, Surabaya',
                'category' => 'Musik',
                'status_label' => 'Selesai',
            ],
            [
                'id' => 6,
                'event_title' => 'AI & Future Tech Conference 2023',
                'organizer' => 'Nusantara Tech Community',
                'position' => 'Registration & Info Desk',
                'date' => '20 – 21 Juli 2023',
                'location' => 'Menara Mandiri, Jakarta',
                'category' => 'Seminar',
                'status_label' => 'Selesai',
            ],
            [
                'id' => 7,
                'event_title' => 'Green Earth Run 2023',
                'organizer' => 'Yayasan Hijau Indonesia',
                'position' => 'Route Marshal & First Aid',
                'date' => '5 Juni 2023',
                'location' => 'Taman Monas, Jakarta',
                'category' => 'Olahraga',
                'status_label' => 'Selesai',
            ],
            [
                'id' => 8,
                'event_title' => 'Startup Summit Indonesia 2023',
                'organizer' => 'Indonesia Startup Forum',
                'position' => 'VIP Handler & Documentation',
                'date' => '10 – 12 Maret 2023',
                'location' => 'ICE BSD, Tangerang',
                'category' => 'Seminar',
                'status_label' => 'Selesai',
            ],
        ];
    }

    public function index(): View
    {
        $allHistories = $this->getHistoriesData();
        $activeCategory = request()->get('category', 'all');

        if ($activeCategory !== 'all') {
            $histories = array_values(
                array_filter(
                    $allHistories,
                    fn ($h) => $h['category'] === ucfirst($activeCategory)
                )
            );
        } else {
            $histories = $allHistories;
        }

        $perPage = 4;
        $currentPage = (int) request()->get('page', 1);

        $totalItems = count($histories);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        $currentPage = max(1, min($currentPage, $totalPages));

        $offset = ($currentPage - 1) * $perPage;

        $paginatedHistories = array_slice(
            $histories,
            $offset,
            $perPage
        );

        $counts = [
            'all' => count($allHistories),
            'festival' => count(array_filter(
                $allHistories,
                fn ($h) => $h['category'] === 'Festival'
            )),
            'seminar' => count(array_filter(
                $allHistories,
                fn ($h) => $h['category'] === 'Seminar'
            )),
            'sosial' => count(array_filter(
                $allHistories,
                fn ($h) => $h['category'] === 'Sosial'
            )),
            'kampus' => count(array_filter(
                $allHistories,
                fn ($h) => $h['category'] === 'Kampus'
            )),
            'komunitas' => count(array_filter(
                $allHistories,
                fn ($h) => $h['category'] === 'Komunitas'
            )),
            'olahraga' => count(array_filter(
                $allHistories,
                fn ($h) => $h['category'] === 'Olahraga'
            )),
            'musik' => count(array_filter(
                $allHistories,
                fn ($h) => $h['category'] === 'Musik'
            )),
        ];

        $totalCertificates = count($allHistories);

        return view('crew.history', compact(
            'paginatedHistories',
            'counts',
            'totalCertificates',
            'currentPage',
            'totalPages',
            'perPage',
            'activeCategory'
        ));
    }

    public function downloadCertificate($id)
    {
        if (!Auth::check()) {
            abort(403, 'Anda harus login untuk mengunduh sertifikat.');
        }

        $user = Auth::user();

        if (!$user instanceof User) {
            abort(403, 'Data pengguna tidak valid.');
        }

        $displayName = $user->name ?? 'Crew';

        $allHistories = $this->getHistoriesData();

        $event = collect($allHistories)->firstWhere(
            'id',
            (int) $id
        );

        if (!$event) {
            abort(404, 'Sertifikat tidak ditemukan.');
        }

        $pdf = Pdf::loadView(
            'crew.certificate-pdf',
            compact('event', 'displayName')
        );

        $pdf->setPaper('A4', 'landscape');

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        $filename = 'Sertifikat-' .
            str_replace(' ', '-', $event['event_title']) .
            '-' .
            str_replace(' ', '-', $displayName) .
            '.pdf';

        return $pdf->download($filename);
    }

    public function downloadPdf()
    {
        if (!Auth::check()) {
            abort(403, 'Anda harus login untuk mengunduh riwayat.');
        }

        $user = Auth::user();

        if (!$user instanceof User) {
            abort(403, 'Data pengguna tidak valid.');
        }

        $displayName = $user->name ?? 'Crew';
        $displayEmail = $user->email ?? 'crew@vola.test';
        $displayCity = $user->volunteerProfile?->city ?? 'Jakarta';

        $allHistories = $this->getHistoriesData();
        $totalEvents = count($allHistories);
        $totalHours = $totalEvents * 16;
        $totalCategories = count(array_unique(array_column($allHistories, 'category')));

        $pdf = Pdf::loadView(
            'crew.history-pdf',
            compact(
                'displayName',
                'displayEmail',
                'displayCity',
                'totalEvents',
                'totalHours',
                'totalCategories',
                'allHistories'
            )
        );

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        $filename = 'Riwayat-Pengalaman-' . str_replace(' ', '-', $displayName) . '.pdf';

        return $pdf->download($filename);
    }
}