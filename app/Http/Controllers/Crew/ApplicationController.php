<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Application;

class ApplicationController extends Controller
{
    private function getDummyApplications(): array
    {
        return [
            [
                'id' => 1, 'event_id' => 1, 'event_title' => 'Indonesia Comic & Pop Culture Con 2025',
                'event_organizer' => 'PT Pop Media Nusantara', 'event_category' => 'Festival',
                'position' => 'Liaison Officer (LO) Bintang Tamu', 'location' => 'JCC Senayan, Jakarta',
                'status' => 'accepted', 'status_label' => 'Diterima', 'applied_at' => '24 April 2025',
                'message' => 'Selamat! Kamu lolos seleksi berkas & wawancara.', 'message_type' => 'success', 'can_cancel' => false,
            ],
            [
                'id' => 2, 'event_id' => 16, 'event_title' => 'Bandung Creative Tech Summit 2025',
                'event_organizer' => 'Bandung Digital Hub', 'event_category' => 'Seminar',
                'position' => 'Usher & Registrasi Tamu VIP', 'location' => 'Sabuga ITB, Bandung',
                'status' => 'pending', 'status_label' => 'Menunggu Seleksi', 'applied_at' => '26 April 2025',
                'message' => 'Berkas telah terkirim dan sedang menunggu kurasi awal panitia.', 'message_type' => 'warning', 'can_cancel' => true,
            ],
        ];
    }

    public function index(): View
    {
        $user = auth()->user();
        $profile = $user?->volunteerProfile;

        if ($profile) {
            $dbApplications = Application::with(['position.event.category', 'position.event.organizer'])
                ->where('volunteer_profile_id', $profile->id)
                ->orderByDesc('applied_at')
                ->get();

            if ($dbApplications->isNotEmpty()) {
                $applications = $dbApplications->map(function ($app) {
                    $event = $app->position?->event;
                    $statusLabels = [
                        'pending' => ['label' => 'Menunggu Seleksi', 'type' => 'warning', 'msg' => 'Berkas sedang ditinjau oleh panitia.'],
                        'processing' => ['label' => 'Sedang Diproses', 'type' => 'info', 'msg' => 'Berkas sedang dalam tahap wawancara/verifikasi.'],
                        'accepted' => ['label' => 'Diterima', 'type' => 'success', 'msg' => 'Selamat! Kamu lolos seleksi. Silakan cek detail di menu Event Saya.'],
                        'rejected' => ['label' => 'Tidak Lolos', 'type' => 'error', 'msg' => 'Maaf, kamu belum lolos seleksi untuk posisi ini.'],
                        'cancelled' => ['label' => 'Dibatalkan', 'type' => 'error', 'msg' => 'Lamaran telah dibatalkan.'],
                    ];
                    $st = $statusLabels[$app->status->value] ?? $statusLabels['pending'];

                    return [
                        'id' => $app->id,
                        'event_id' => $event?->id,
                        'event_title' => $event?->title ?? 'Event Tidak Ditemukan',
                        'event_organizer' => $event?->organizer?->organization_name ?? '-',
                        'event_category' => $event?->category?->name ?? 'Umum',
                        'position' => $app->position?->name ?? '-',
                        'location' => $event?->location ?? '-',
                        'status' => $app->status->value,
                        'status_label' => $st['label'],
                        'applied_at' => $app->applied_at?->translatedFormat('d F Y') ?? '-',
                        'message' => $st['msg'],
                        'message_type' => $st['type'],
                        'can_cancel' => in_array($app->status->value, ['pending', 'processing']),
                    ];
                })->toArray();
            } else {
                $applications = $this->getDummyApplications();
            }
        } else {
            $applications = $this->getDummyApplications();
        }

        return view('crew.applications', compact('applications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'position_id' => 'required|integer|exists:event_positions,id',
            'motivation' => 'required|string|min:10',
            'experience' => 'nullable|string',
        ]);

        $user = auth()->user();
        $profile = $user?->volunteerProfile;

        if (!$profile) {
            return response()->json(['success' => false, 'message' => 'Profil volunteer belum lengkap.'], 400);
        }

        $exists = Application::where('volunteer_profile_id', $profile->id)
            ->where('position_id', $validated['position_id'])
            ->whereIn('status', ['pending', 'processing', 'accepted'])
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melamar untuk posisi ini.'], 409);
        }

        $application = Application::create([
            'volunteer_profile_id' => $profile->id,
            'position_id' => $validated['position_id'],
            'cover_letter' => $validated['motivation'],
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lamaran berhasil diajukan! Silakan pantau status di menu "Lamaran Saya".',
            'application_id' => $application->id,
        ]);
    }

    public function downloadAcceptanceLetter($id)
    {
        abort(404, 'Fitur dalam pengembangan');
    }

    public function downloadCertificate($id)
    {
        abort(404, 'Fitur dalam pengembangan');
    }
}