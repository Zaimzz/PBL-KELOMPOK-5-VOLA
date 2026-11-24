<?php

namespace App\Http\Controllers\Crew;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * Menyimpan laporan baru yang diajukan oleh Crew.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['nullable', 'exists:events,id'],
            'reported_id' => ['nullable', 'exists:users,id'],
            'reason' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'evidence' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
        ], [
            'reason.required' => 'Pilih alasan pelaporan terlebih dahulu.',
            'description.required' => 'Kronologi atau penjelasan kejadian wajib diisi.',
            'description.min' => 'Penjelasan minimal 20 karakter agar laporan jelas.',
            'evidence.mimes' => 'Bukti harus berupa file gambar (JPG, PNG) atau PDF.',
            'evidence.max' => 'Ukuran file bukti maksimal 5MB.',
        ]);

        $reporter = Auth::user();
        $reportedUserId = $validated['reported_id'] ?? null;
        $eventTitle = null;

        // Jika pelaporan berdasar event, ambil user_id penyelenggara event
        if (! empty($validated['event_id'])) {
            $event = Event::with('organizer.user')->find($validated['event_id']);
            if ($event) {
                $eventTitle = $event->title;
                if (! $reportedUserId && $event->organizer?->user_id) {
                    $reportedUserId = $event->organizer->user_id;
                }
            }
        }

        if (! $reportedUserId) {
            $errorMsg = 'Penyelenggara event yang dilaporkan tidak valid.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }

            return back()->withErrors(['reported_id' => $errorMsg]);
        }

        // Cegah pengguna melaporkan dirinya sendiri
        if ((int) $reportedUserId === (int) $reporter->id) {
            $errorMsg = 'Anda tidak dapat melaporkan akun Anda sendiri.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }

            return back()->withErrors(['reported_id' => $errorMsg]);
        }

        // Generate nomor tiket unik
        do {
            $ticketNumber = 'REP-'.date('Ymd').'-'.strtoupper(Str::random(5));
        } while (Report::where('ticket_number', $ticketNumber)->exists());

        // Simpan file bukti jika ada
        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('evidence', 'public');
        }

        $description = $validated['description'];
        if ($eventTitle) {
            $description = "[Terkait Event: {$eventTitle}]\n\n".$description;
        }

        $report = Report::create([
            'ticket_number' => $ticketNumber,
            'reporter_id' => $reporter->id,
            'reported_id' => $reportedUserId,
            'reason' => $validated['reason'],
            'description' => $description,
            'evidence_path' => $evidencePath,
            'status' => ReportStatus::Pending,
        ]);

        $message = "Laporan berhasil diajukan dengan nomor tiket #{$report->ticket_number}. Tim Admin akan segera meninjau laporan ini.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'ticket_number' => $report->ticket_number,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}
