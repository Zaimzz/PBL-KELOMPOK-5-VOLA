<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * Menampilkan daftar laporan pelanggaran dan metrik utama.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:all,pending,in_review,resolved,rejected'],
        ]);

        // 1. Metrik Header
        $stats = [
            'pending' => Report::where('status', ReportStatus::Pending)->count(),
            'in_review' => Report::where('status', ReportStatus::InReview)->count(),
            'resolved' => Report::where('status', ReportStatus::Resolved)->count(),
        ];

        // 2. Query Laporan
        $query = Report::with(['reporter', 'reported', 'resolvedBy']);

        // Filter Pencarian
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('reporter', function ($rq) use ($search) {
                        $rq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('reported', function ($tq) use ($search) {
                        $tq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Status Pills
        $statusFilter = $request->input('status', 'all');
        if ($statusFilter && $statusFilter !== 'all') {
            $enumVal = match ($statusFilter) {
                'pending' => ReportStatus::Pending,
                'in_review' => ReportStatus::InReview,
                'resolved' => ReportStatus::Resolved,
                'rejected' => ReportStatus::Rejected,
                default => null,
            };

            if ($enumVal) {
                $query->where('status', $enumVal);
            }
        }

        $reports = $query->latest('created_at')->paginate(10)->withQueryString();

        return view('admin.reports.index', compact('reports', 'stats', 'statusFilter'));
    }

    /**
     * Mengambil detail laporan untuk modal Alpine.js.
     */
    public function show(Report $report): JsonResponse
    {
        $report->load(['reporter', 'reported', 'resolvedBy']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $report->id,
                'ticket_number' => $report->ticket_number,
                'reason' => $report->reason,
                'description' => $report->description,
                'status' => $report->status->value,
                'status_label' => match ($report->status) {
                    ReportStatus::Pending => 'Menunggu Tindakan',
                    ReportStatus::InReview => 'Sedang Ditinjau',
                    ReportStatus::Resolved => 'Laporan Selesai',
                    ReportStatus::Rejected => 'Laporan Ditolak',
                },
                'action_taken' => $report->action_taken,
                'admin_notes' => $report->admin_notes,
                'evidence_url' => $report->evidence_path ? Storage::url($report->evidence_path) : null,
                'created_at' => $report->created_at->translatedFormat('d F Y, H:i'),
                'resolved_at' => $report->resolved_at?->translatedFormat('d F Y, H:i'),
                'resolved_by' => $report->resolvedBy?->name,
                'reporter' => [
                    'id' => $report->reporter->id,
                    'name' => $report->reporter->name,
                    'email' => $report->reporter->email,
                    'role' => $report->reporter->role->value,
                ],
                'reported' => [
                    'id' => $report->reported->id,
                    'name' => $report->reported->name,
                    'email' => $report->reported->email,
                    'role' => $report->reported->role->value,
                    'status' => $report->reported->status->value,
                ],
            ],
        ]);
    }

    /**
     * Menangani tindakan resolusi laporan dari Admin.
     */
    public function handleAction(Request $request, Report $report)
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'in:in_review,warning,suspend,resolve,reject'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $action = $validated['action'];
        $notes = $validated['admin_notes'] ?? null;
        $admin = auth()->user();

        DB::transaction(function () use ($report, $action, $notes, $admin) {
            $reportedUser = $report->reported;

            switch ($action) {
                case 'in_review':
                    $report->status = ReportStatus::InReview;
                    $report->action_taken = 'Sedang Ditinjau';
                    break;

                case 'warning':
                    $report->status = ReportStatus::Resolved;
                    $report->action_taken = 'Diberikan Peringatan Resmi';
                    $report->resolved_by = $admin->id;
                    $report->resolved_at = now();

                    // Buat notifikasi peringatan jika ada
                    $this->createNotification($reportedUser, [
                        'title' => 'Peringatan Resmi dari Tim Moderasi VOLA',
                        'message' => 'Akun Anda menerima laporan pelanggaran kode etik komunitas terkait tiket #'.$report->ticket_number.'. Catatan moderasi: '.($notes ?: 'Harap patuhi panduan komunitas.'),
                        'type' => 'moderation_warning',
                    ]);
                    break;

                case 'suspend':
                    $report->status = ReportStatus::Resolved;
                    $report->action_taken = 'Akun Ditangguhkan';
                    $report->resolved_by = $admin->id;
                    $report->resolved_at = now();

                    // Suspend akun terlapor jika bukan admin
                    if ($reportedUser && $reportedUser->role !== UserRole::Admin) {
                        $reportedUser->status = UserStatus::Suspended;
                        $reportedUser->save();
                    }

                    $this->createNotification($reportedUser, [
                        'title' => 'Pemberitahuan Penangguhan Akun',
                        'message' => 'Akun Anda telah ditangguhkan terkait pelanggaran tiket #'.$report->ticket_number.'. Hubungi dukungan jika ini adalah kekeliruan.',
                        'type' => 'account_suspended',
                    ]);
                    break;

                case 'resolve':
                    $report->status = ReportStatus::Resolved;
                    $report->action_taken = 'Diselesaikan';
                    $report->resolved_by = $admin->id;
                    $report->resolved_at = now();
                    break;

                case 'reject':
                    $report->status = ReportStatus::Rejected;
                    $report->action_taken = 'Laporan Ditolak (Tidak Terbukti)';
                    $report->resolved_by = $admin->id;
                    $report->resolved_at = now();
                    break;
            }

            if ($notes) {
                $report->admin_notes = $notes;
            }

            $report->save();
        });

        $message = "Tindakan pada laporan #{$report->ticket_number} berhasil diproses.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $report->status->value,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Mengunduh file bukti pendukung laporan jika ada.
     */
    public function downloadEvidence(Report $report)
    {
        if (! $report->evidence_path || ! Storage::exists($report->evidence_path)) {
            abort(404, 'Bukti pendukung tidak ditemukan.');
        }

        return Storage::download($report->evidence_path);
    }

    /**
     * Helper untuk membuat notifikasi database jika notifiable user valid.
     */
    private function createNotification(?User $user, array $data): void
    {
        if (! $user) {
            return;
        }

        try {
            DB::table('notifications')->insert([
                'id' => Str::uuid()->toString(),
                'type' => 'App\Notifications\ModerationNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode($data),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Fail silently if notifications table format differs
        }
    }
}
