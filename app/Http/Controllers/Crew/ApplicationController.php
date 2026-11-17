<?php

namespace App\Http\Controllers\Crew;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\EventPosition;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $profile = $user?->volunteerProfile;

        $applications = [];

        if ($profile) {
            $rawApplications = Application::query()
                ->where('volunteer_profile_id', $profile->id)
                ->with(['position.event.organizer', 'position.event.category', 'event.organizer', 'event.category'])
                ->latest('applied_at')
                ->latest('id')
                ->get();

            $applications = $rawApplications->map(function (Application $app) {
                $position = $app->position;
                $event = $app->event ?? $position?->event;
                $organizer = $event?->organizer;
                $category = $event?->category;

                $statusVal = $app->status instanceof ApplicationStatus
                    ? $app->status->value
                    : (string) $app->status;

                $statusLabel = match ($statusVal) {
                    'accepted' => 'Diterima',
                    'rejected' => 'Tidak Lolos',
                    'processing' => 'Sedang Diproses',
                    'withdrawn' => 'Dibatalkan',
                    default => 'Menunggu Seleksi',
                };

                $messageType = match ($statusVal) {
                    'accepted' => 'success',
                    'rejected', 'withdrawn' => 'error',
                    'processing' => 'info',
                    default => 'warning',
                };

                if (! empty($app->decision_note)) {
                    $message = $app->decision_note;
                } else {
                    $message = match ($statusVal) {
                        'accepted' => 'Selamat! Kamu lolos seleksi berkas & pendaftaran volunteer. Silakan cek detail di menu Event Saya.',
                        'rejected' => 'Kuota telah terpenuhi atau kriteria belum sesuai. Terima kasih atas partisipasi dan antusiasme kamu.',
                        'processing' => 'Lamaran kamu sedang dalam tahap kurasi/seleksi lanjutan panitia.',
                        'withdrawn' => 'Lamaran telah dibatalkan.',
                        default => 'Berkas telah terkirim dan sedang menunggu kurasi awal panitia.',
                    };
                }

                $appliedAt = $app->applied_at ?? $app->created_at;
                $appliedAtFormatted = $appliedAt ? $appliedAt->translatedFormat('d F Y') : '-';

                $locationParts = array_filter([$event?->location, $event?->city]);
                $location = ! empty($locationParts) ? implode(', ', $locationParts) : 'Lokasi belum ditentukan';

                return [
                    'id' => $app->id,
                    'event_id' => $event?->id,
                    'event_title' => $event?->title ?? 'Event Tidak Ditemukan',
                    'event_organizer' => $organizer?->organization_name ?? 'Penyelenggara Event',
                    'event_category' => $category?->name ?? 'Kategori Umum',
                    'position' => $position?->name ?? 'Posisi Relawan',
                    'location' => $location,
                    'status' => $statusVal,
                    'status_label' => $statusLabel,
                    'applied_at' => $appliedAtFormatted,
                    'message' => $message,
                    'message_type' => $messageType,
                ];
            })->toArray();
        }

        return view('crew.applications', compact('applications'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $profile = $user?->volunteerProfile;

        if (! $profile) {
            $msg = 'Anda harus melengkapi profil volunteer terlebih dahulu.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }

            return back()->withErrors(['profile' => $msg]);
        }

        $request->validate([
            'position_id' => 'required|exists:event_positions,id',
            'motivation' => 'required|string|min:20',
            'experience' => 'nullable|string',
            'availability' => 'nullable|string',
        ]);

        $position = EventPosition::with('event')->findOrFail($request->position_id);

        $existing = Application::where('volunteer_profile_id', $profile->id)
            ->where('position_id', $position->id)
            ->first();

        if ($existing) {
            $msg = 'Anda sudah melamar untuk posisi ini.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 409);
            }

            return back()->withErrors(['position_id' => $msg]);
        }

        Application::create([
            'volunteer_profile_id' => $profile->id,
            'event_id' => $position->event_id,
            'position_id' => $position->id,
            'status' => ApplicationStatus::Pending,
            'motivation' => $request->motivation,
            'experience' => $request->experience,
            'availability' => $request->availability,
            'cover_letter' => $request->motivation,
            'applied_at' => now(),
        ]);

        $successMsg = 'Lamaran berhasil diajukan! Silakan pantau status di menu "Lamaran Saya".';
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
            ]);
        }

        return redirect()->route('applications.index')->with('success', $successMsg);
    }

    public function downloadAcceptanceLetter(int $id)
    {
        $user = Auth::user();
        $profile = $user?->volunteerProfile;

        if (! $profile) {
            abort(403);
        }

        $application = Application::where('volunteer_profile_id', $profile->id)
            ->where('id', $id)
            ->with(['position.event.organizer', 'position.event.category', 'event.organizer', 'event.category'])
            ->firstOrFail();

        $statusVal = $application->status instanceof ApplicationStatus
            ? $application->status->value
            : (string) $application->status;

        if ($statusVal !== 'accepted') {
            return back()->withErrors(['error' => 'Bukti penerimaan hanya tersedia untuk lamaran yang diterima.']);
        }

        $position = $application->position;
        $event = $application->event ?? $position?->event;
        $organizer = $event?->organizer;
        $category = $event?->category;

        $appliedAt = $application->applied_at ?? $application->created_at;
        $appliedAtFormatted = $appliedAt ? $appliedAt->translatedFormat('d F Y') : '-';

        $locationParts = array_filter([$event?->location, $event?->city]);
        $location = ! empty($locationParts) ? implode(', ', $locationParts) : 'Lokasi belum ditentukan';

        $applicationData = [
            'id' => $application->id,
            'event_title' => $event?->title ?? 'Event',
            'event_organizer' => $organizer?->organization_name ?? 'Panitia Event',
            'position' => $position?->name ?? 'Volunteer',
            'event_category' => $category?->name ?? 'Umum',
            'location' => $location,
            'applied_at' => $appliedAtFormatted,
        ];

        $displayName = $user->name ?? 'Volunteer';

        $pdf = Pdf::loadView('crew.bukti-penerimaan', [
            'application' => $applicationData,
            'displayName' => $displayName,
        ]);
        $pdf->setPaper('A4', 'landscape');

        $filename = 'Bukti-Penerimaan-'.str_replace(' ', '-', $event?->title ?? 'Event').'-'.str_replace(' ', '-', $displayName).'.pdf';

        return $pdf->download($filename);
    }

    public function downloadCertificate(int $id): RedirectResponse
    {
        return redirect()->route('history.downloadCertificate', ['id' => $id]);
    }
}
