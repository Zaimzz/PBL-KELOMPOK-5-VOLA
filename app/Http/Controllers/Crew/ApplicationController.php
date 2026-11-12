<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ApplicationController extends Controller
{
    public static function getApplications(): array
    {
        return [
            [
                'id' => 1,
                'event_id' => 1,
                'event_title' => 'Indonesia Comic & Pop Culture Con 2025',
                'event_organizer' => 'PT Pop Media Nusantara',
                'event_category' => 'Festival',
                'position' => 'Liaison Officer (LO) Bintang Tamu',
                'location' => 'JCC Senayan, Jakarta',
                'status' => 'accepted',
                'status_label' => 'Diterima',
                'applied_at' => '24 April 2025',
                'message' => 'Selamat! Kamu lolos seleksi berkas & wawancara. Silakan cek detail di menu Event Saya.',
                'message_type' => 'success',
                'can_cancel' => false,
            ],
            [
                'id' => 2,
                'event_id' => 16,
                'event_title' => 'Bandung Creative Tech Summit 2025',
                'event_organizer' => 'Bandung Digital Hub',
                'event_category' => 'Seminar',
                'position' => 'Usher & Registrasi Tamu VIP',
                'location' => 'Sabuga ITB, Bandung',
                'status' => 'pending',
                'status_label' => 'Menunggu Seleksi',
                'applied_at' => '26 April 2025',
                'message' => 'Berkas telah terkirim dan sedang menunggu kurasi awal panitia.',
                'message_type' => 'warning',
                'can_cancel' => true,
            ],
            [
                'id' => 3,
                'event_id' => 12,
                'event_title' => 'Run For Earth Green Marathon 2025',
                'event_organizer' => 'Yayasan Bumi Lestari',
                'event_category' => 'Olahraga',
                'position' => 'Water Station Marshal',
                'location' => 'GBK Senayan, Jakarta',
                'status' => 'processing',
                'status_label' => 'Sedang Diproses',
                'applied_at' => '20 April 2025',
                'message' => 'Tahap 2: Wawancara online. Jadwal screening akan dikonfirmasi panitia.',
                'message_type' => 'info',
                'can_cancel' => false,
            ],
            [
                'id' => 4,
                'event_id' => 6,
                'event_title' => 'Pesta Rakyat UMKM & Kuliner Nusantara',
                'event_organizer' => 'Asosiasi Kreatif Jabar',
                'event_category' => 'Komunitas',
                'position' => 'Booth Coordinator',
                'location' => 'Gasibu, Bandung',
                'status' => 'rejected',
                'status_label' => 'Tidak Lolos',
                'applied_at' => '18 April 2025',
                'message' => 'Kuota telah terpenuhi. Terima kasih atas partisipasi dan antusiasme.',
                'message_type' => 'error',
                'can_cancel' => false,
            ],
        ];
    }

    public function index(): View
    {
        $applications = self::getApplications();

        return view('crew.applications', compact('applications'));
    }

    public function downloadAcceptanceLetter($id)
    {
        $applications = [
            1 => [
                'id' => 1,
                'event_title' => 'Indonesia Comic & Pop Culture Con 2025',
                'event_organizer' => 'PT Pop Media Nusantara',
                'event_category' => 'Festival',
                'position' => 'Liaison Officer (LO) Bintang Tamu',
                'location' => 'JCC Senayan, Jakarta',
                'applied_at' => '24 April 2025',
            ],
        ];

        $application = $applications[$id] ?? null;

        if (!$application) {
            abort(404, 'Bukti penerimaan tidak ditemukan');
        }

        $displayName = auth()->user()->name ?? 'Volunteer';

        $pdf = Pdf::loadView('crew.bukti-penerimaan', compact('application', 'displayName'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('bukti-penerimaan-' . $id . '.pdf');
    }

    public function downloadCertificate($id)
    {
        $events = [
            1 => [
                'id' => 1,
                'event_title' => 'Indonesia Comic & Pop Culture Con 2025',
                'organizer' => 'PT Pop Media Nusantara',
                'position' => 'Liaison Officer (LO) Bintang Tamu',
                'location' => 'JCC Senayan, Jakarta',
                'date' => '14 – 16 Juni 2025',
                'category' => 'Festival',
            ],
        ];

        $event = $events[$id] ?? null;

        if (!$event) {
            abort(404, 'Sertifikat tidak ditemukan');
        }

        $displayName = auth()->user()->name ?? 'Volunteer';

        $pdf = Pdf::loadView('crew.certificate-pdf', compact('event', 'displayName'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('sertifikat-volunteer-' . $id . '.pdf');
    }
}