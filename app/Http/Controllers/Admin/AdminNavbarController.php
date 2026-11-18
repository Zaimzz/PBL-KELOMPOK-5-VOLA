<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\OrganizerProfile;
use App\Models\Payment;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminNavbarController extends Controller
{
    /**
     * Endpoint Live Global Search terpadu untuk 7 menu Admin:
     * 1. Beranda (Pintasan Modul / Metrik)
     * 2. Verifikasi EO (Nama Organisasi / NIB)
     * 3. Review Event (Judul Event / EO)
     * 4. Event Aktif (Nama Event / Lokasi)
     * 5. Pengguna (Crew / EO / Email / Asal)
     * 6. Laporan (Tiket #RPT-... / Pelapor / Terlapor)
     * 7. Transaksi (Invoice #INV-... / Nominal)
     */
    public function globalSearch(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('q', ''));
        $cleanQuery = ltrim($query, '#');

        if (mb_strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'query' => $query,
                'total_results' => 0,
                'categories' => [],
            ]);
        }

        // Operator dinamis: ILIKE untuk PostgreSQL (case-insensitive), LIKE untuk SQLite/MySQL
        $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

        $results = [];
        $totalCount = 0;

        // 1. Kategori: Beranda (Pintasan Modul, Log Aktivitas, Antrean Moderasi)
        $dashboardKeywords = [
            'beranda', 'dashboard', 'ringkasan', 'kontrol', 'statistik', 'metrik',
            'pusat', 'log', 'aktivitas', 'riwayat', 'antrean', 'tindakan', 'moderasi', 'cepat',
        ];
        $matchDashboard = false;
        $lowerQuery = mb_strtolower($query);
        foreach ($dashboardKeywords as $kw) {
            if (str_contains($kw, $lowerQuery) || str_contains($lowerQuery, $kw)) {
                $matchDashboard = true;
                break;
            }
        }
        if ($matchDashboard) {
            $dashItems = [
                [
                    'id' => 'dash_home',
                    'title' => 'Pusat Kontrol Utama (Dashboard)',
                    'subtitle' => 'Pantau ringkasan metrik real-time, antrean kurasi, dan log sistem',
                    'url' => route('admin.dashboard'),
                    'badge' => 'Beranda',
                    'badge_color' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'icon' => 'home',
                ],
                [
                    'id' => 'dash_logs',
                    'title' => 'Riwayat Log & Aktivitas Sistem Terkini',
                    'subtitle' => 'Audit log kronologis pembaruan status transaksi, verifikasi EO, dan approval',
                    'url' => route('admin.dashboard').'#riwayat-log',
                    'badge' => 'Log Aktivitas',
                    'badge_color' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'icon' => 'home',
                ],
            ];
            $results[] = [
                'name' => 'Beranda & Pusat Kontrol',
                'badge_color' => 'bg-indigo-50 text-indigo-700',
                'items' => $dashItems,
            ];
            $totalCount += count($dashItems);
        }

        // 2. Kategori: Verifikasi EO
        // Kolom diuji: organization_name, pic_name, city, organization_type, user.name, user.email, nib/legal_number
        [$hasNibColumn, $hasLegalNumColumn] = Cache::remember('admin_search_schema_columns', 3600, function () {
            return [
                Schema::hasColumn('organizer_profiles', 'nib'),
                Schema::hasColumn('organizer_profiles', 'legal_number'),
            ];
        });

        $eoProfiles = OrganizerProfile::query()
            ->with('user')
            ->where(function ($q) use ($query, $cleanQuery, $operator, $hasNibColumn, $hasLegalNumColumn) {
                $q->where('organization_name', $operator, "%{$query}%")
                    ->orWhere('pic_name', $operator, "%{$query}%")
                    ->orWhere('city', $operator, "%{$query}%")
                    ->orWhere('organization_type', $operator, "%{$query}%")
                    ->orWhere('pic_phone', $operator, "%{$query}%")
                    ->orWhereHas('user', function ($uq) use ($query, $operator) {
                        $uq->where('name', $operator, "%{$query}%")
                            ->orWhere('email', $operator, "%{$query}%");
                    });

                if ($hasNibColumn) {
                    $q->orWhere('nib', $operator, "%{$cleanQuery}%");
                }
                if ($hasLegalNumColumn) {
                    $q->orWhere('legal_number', $operator, "%{$cleanQuery}%");
                }
            })
            ->latest('updated_at')
            ->take(5)
            ->get();

        if ($eoProfiles->isNotEmpty()) {
            $eoItems = $eoProfiles->map(function ($eo) {
                $statusLabel = match ($eo->verification_status) {
                    VerificationStatus::Verified => 'Terverifikasi',
                    VerificationStatus::Rejected => 'Ditolak',
                    default => 'Menunggu Verifikasi',
                };

                $nibText = $eo->nib ?? $eo->legal_number ?? null;
                $nibSubtitle = $nibText ? " • NIB: {$nibText}" : '';

                return [
                    'id' => 'eo_'.$eo->id,
                    'title' => $eo->organization_name,
                    'subtitle' => 'PIC: '.($eo->pic_name ?? $eo->user?->name ?? '-').' • Kota: '.($eo->city ?? '-')." [{$statusLabel}]{$nibSubtitle}",
                    'url' => route('admin.eo-verifications', ['search' => $eo->organization_name, 'selected' => $eo->id]),
                    'badge' => 'Verifikasi EO',
                    'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'icon' => 'shield',
                ];
            })->all();

            $results[] = [
                'name' => 'Verifikasi EO & Legalitas',
                'badge_color' => 'bg-amber-50 text-amber-700',
                'items' => $eoItems,
            ];
            $totalCount += count($eoItems);
        }

        // 3. Kategori: Review Event (Antrean Pengajuan Menunggu Kurasi)
        // Kolom diuji: title, location, city, pic_name, organizer.organization_name, organizer.pic_name, organizer.city, category.name
        $reviewEvents = Event::with(['organizer', 'category'])
            ->where('status', EventStatus::PendingReview)
            ->where(function ($q) use ($query, $operator) {
                $q->where('title', $operator, "%{$query}%")
                    ->orWhere('city', $operator, "%{$query}%")
                    ->orWhere('location', $operator, "%{$query}%")
                    ->orWhere('pic_name', $operator, "%{$query}%")
                    ->orWhereHas('organizer', function ($oq) use ($query, $operator) {
                        $oq->where('organization_name', $operator, "%{$query}%")
                            ->orWhere('pic_name', $operator, "%{$query}%")
                            ->orWhere('city', $operator, "%{$query}%");
                    })
                    ->orWhereHas('category', function ($cq) use ($query, $operator) {
                        $cq->where('name', $operator, "%{$query}%");
                    });
            })
            ->latest('updated_at')
            ->take(5)
            ->get();

        if ($reviewEvents->isNotEmpty()) {
            $reviewItems = $reviewEvents->map(function ($event) {
                $categoryName = $event->category?->name ?? 'Event';
                $orgName = $event->organizer?->organization_name ?? '-';

                return [
                    'id' => 'review_'.$event->id,
                    'title' => $event->title,
                    'subtitle' => "Penyelenggara: {$orgName} • Kategori: {$categoryName} • Lokasi: ".($event->city ?? $event->location ?? '-'),
                    'url' => route('admin.review-events.index', ['search' => $event->title]),
                    'badge' => 'Review Event',
                    'badge_color' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'icon' => 'clipboard',
                ];
            })->all();

            $results[] = [
                'name' => 'Review Event (Antrean Kurasi)',
                'badge_color' => 'bg-rose-50 text-rose-700',
                'items' => $reviewItems,
            ];
            $totalCount += count($reviewItems);
        }

        // 4. Kategori: Event Aktif (Aktif, Ditutup, Selesai)
        // Kolom diuji: title, city, location, pic_name, organizer.organization_name, organizer.city, category.name, positions.name
        $activeEvents = Event::with(['organizer', 'category', 'positions'])
            ->whereIn('status', [EventStatus::Active, EventStatus::Closed, EventStatus::Finished])
            ->where(function ($q) use ($query, $operator) {
                $q->where('title', $operator, "%{$query}%")
                    ->orWhere('city', $operator, "%{$query}%")
                    ->orWhere('location', $operator, "%{$query}%")
                    ->orWhere('pic_name', $operator, "%{$query}%")
                    ->orWhereHas('organizer', function ($oq) use ($query, $operator) {
                        $oq->where('organization_name', $operator, "%{$query}%")
                            ->orWhere('city', $operator, "%{$query}%");
                    })
                    ->orWhereHas('category', function ($cq) use ($query, $operator) {
                        $cq->where('name', $operator, "%{$query}%");
                    })
                    ->orWhereHas('positions', function ($pq) use ($query, $operator) {
                        $pq->where('name', $operator, "%{$query}%");
                    });
            })
            ->latest('updated_at')
            ->take(5)
            ->get();

        if ($activeEvents->isNotEmpty()) {
            $activeItems = $activeEvents->map(function ($event) {
                $statusText = match ($event->status) {
                    EventStatus::Active => 'Aktif Tayang',
                    EventStatus::Closed => 'Pendaftaran Ditutup',
                    EventStatus::Finished => 'Event Selesai',
                    default => ucfirst($event->status->value),
                };

                $positionsSummary = $event->positions->pluck('name')->take(2)->implode(', ');
                $posSuffix = $positionsSummary ? " • Posisi: {$positionsSummary}" : '';

                return [
                    'id' => 'active_'.$event->id,
                    'title' => $event->title,
                    'subtitle' => 'Lokasi: '.($event->city ?? $event->location ?? '-')." • {$statusText}{$posSuffix}",
                    'url' => route('admin.active-events.index', ['search' => $event->title]),
                    'badge' => 'Event Aktif',
                    'badge_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'icon' => 'calendar',
                ];
            })->all();

            $results[] = [
                'name' => 'Event Aktif & Berjalan',
                'badge_color' => 'bg-emerald-50 text-emerald-700',
                'items' => $activeItems,
            ];
            $totalCount += count($activeItems);
        }

        // 5. Kategori: Pengguna (Crew / EO)
        // Kolom diuji: name, email, phone, volunteerProfile.institution, volunteerProfile.city, organizerProfile.organization_name, organizerProfile.pic_name, organizerProfile.city, nib
        $users = User::with(['volunteerProfile', 'organizerProfile'])
            ->whereIn('role', [UserRole::Volunteer, UserRole::Eo])
            ->where(function ($q) use ($query, $cleanQuery, $operator, $hasNibColumn, $hasLegalNumColumn) {
                $q->where('name', $operator, "%{$query}%")
                    ->orWhere('email', $operator, "%{$query}%")
                    ->orWhere('phone', $operator, "%{$query}%")
                    ->orWhereHas('volunteerProfile', function ($vq) use ($query, $operator) {
                        $vq->where('institution', $operator, "%{$query}%")
                            ->orWhere('city', $operator, "%{$query}%");
                    })
                    ->orWhereHas('organizerProfile', function ($oq) use ($query, $cleanQuery, $operator, $hasNibColumn, $hasLegalNumColumn) {
                        $oq->where('organization_name', $operator, "%{$query}%")
                            ->orWhere('pic_name', $operator, "%{$query}%")
                            ->orWhere('city', $operator, "%{$query}%");

                        if ($hasNibColumn) {
                            $oq->orWhere('nib', $operator, "%{$cleanQuery}%");
                        }
                        if ($hasLegalNumColumn) {
                            $oq->orWhere('legal_number', $operator, "%{$cleanQuery}%");
                        }
                    });
            })
            ->latest('created_at')
            ->take(5)
            ->get();

        if ($users->isNotEmpty()) {
            $userItems = $users->map(function ($user) {
                $roleLabel = $user->role === UserRole::Volunteer ? 'Crew' : 'EO';
                $origin = $user->role === UserRole::Volunteer
                    ? ($user->volunteerProfile?->institution ?? $user->volunteerProfile?->city ?? '-')
                    : ($user->organizerProfile?->organization_name ?? $user->organizerProfile?->city ?? '-');
                $statusLabel = $user->status->value === 'active' ? 'Aktif' : 'Ditangguhkan';

                return [
                    'id' => 'user_'.$user->id,
                    'title' => $user->name,
                    'subtitle' => "[{$roleLabel}] {$user->email} • Asal: {$origin} • Status: {$statusLabel}",
                    'url' => route('admin.users.index', ['search' => $user->name]),
                    'badge' => 'Pengguna',
                    'badge_color' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'icon' => 'user',
                ];
            })->all();

            $results[] = [
                'name' => 'Pengguna Terdaftar',
                'badge_color' => 'bg-blue-50 text-blue-700',
                'items' => $userItems,
            ];
            $totalCount += count($userItems);
        }

        // 6. Kategori: Laporan & Moderasi
        // Kolom diuji: ticket_number, reason, description, status, reporter.name, reporter.email, reported.name, reported.email
        $reports = Report::with(['reporter', 'reported'])
            ->where(function ($q) use ($query, $cleanQuery, $operator) {
                $q->where('ticket_number', $operator, "%{$cleanQuery}%")
                    ->orWhere('ticket_number', $operator, "%{$query}%")
                    ->orWhere('reason', $operator, "%{$query}%")
                    ->orWhere('description', $operator, "%{$query}%")
                    ->orWhereHas('reporter', function ($rq) use ($query, $operator) {
                        $rq->where('name', $operator, "%{$query}%")
                            ->orWhere('email', $operator, "%{$query}%");
                    })
                    ->orWhereHas('reported', function ($tq) use ($query, $operator) {
                        $tq->where('name', $operator, "%{$query}%")
                            ->orWhere('email', $operator, "%{$query}%");
                    });
            })
            ->latest('created_at')
            ->take(5)
            ->get();

        if ($reports->isNotEmpty()) {
            $reportItems = $reports->map(function ($rpt) {
                $statusLabel = match ($rpt->status) {
                    ReportStatus::Pending => 'Menunggu Tindakan',
                    ReportStatus::InReview => 'Sedang Ditinjau',
                    ReportStatus::Resolved => 'Selesai',
                    ReportStatus::Rejected => 'Ditolak',
                    default => ucfirst((string) $rpt->status->value),
                };

                $reporterName = $rpt->reporter?->name ?? 'Pelapor';
                $reportedName = $rpt->reported?->name ?? 'Terlapor';

                return [
                    'id' => 'report_'.$rpt->id,
                    'title' => 'Tiket #'.$rpt->ticket_number,
                    'subtitle' => "{$rpt->reason} • Pelapor: {$reporterName} • Terlapor: {$reportedName} [{$statusLabel}]",
                    'url' => route('admin.reports.index', ['search' => $rpt->ticket_number]),
                    'badge' => 'Laporan',
                    'badge_color' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'icon' => 'alert',
                ];
            })->all();

            $results[] = [
                'name' => 'Laporan & Moderasi',
                'badge_color' => 'bg-purple-50 text-purple-700',
                'items' => $reportItems,
            ];
            $totalCount += count($reportItems);
        }

        // 7. Kategori: Transaksi & Pembayaran
        // Batasi pencocokan presisi HANYA pada kolom bisnis utama:
        // 1. Nomor Invoice (invoice_number, misal #INV-...)
        // 2. Judul Event terkait (relasi event.title)
        // 3. Nama Organisasi / EO penyelenggara (relasi event.organizer.organization_name)
        $payments = Payment::with(['event.organizer'])
            ->where(function ($q) use ($query, $cleanQuery, $operator) {
                $q->where('invoice_number', $operator, "%{$cleanQuery}%")
                    ->orWhere('invoice_number', $operator, "%{$query}%")
                    ->orWhereHas('event', function ($eq) use ($query, $operator) {
                        $eq->where('title', $operator, "%{$query}%")
                            ->orWhereHas('organizer', function ($oq) use ($query, $operator) {
                                $oq->where('organization_name', $operator, "%{$query}%");
                            });
                    });
            })
            ->latest('created_at')
            ->take(5)
            ->get();

        if ($payments->isNotEmpty()) {
            $paymentItems = $payments->map(function ($pay) {
                $statusLabel = match ($pay->status) {
                    PaymentStatus::Paid => 'Lunas',
                    PaymentStatus::Pending => 'Menunggu Pembayaran',
                    PaymentStatus::Expired => 'Kedaluwarsa',
                    PaymentStatus::Failed => 'Gagal',
                    default => ucfirst((string) $pay->status->value),
                };

                $eventTitle = $pay->event?->title ?? 'Event';
                $orgName = $pay->event?->organizer?->organization_name;
                $orgSuffix = $orgName ? " ({$orgName})" : '';

                return [
                    'id' => 'trans_'.$pay->id,
                    'title' => 'Tagihan #'.$pay->invoice_number,
                    'subtitle' => 'Rp '.number_format($pay->amount, 0, ',', '.')." • {$statusLabel} • {$eventTitle}{$orgSuffix}",
                    'url' => route('admin.transactions.index', ['search' => $pay->invoice_number]),
                    'badge' => 'Transaksi',
                    'badge_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'icon' => 'credit-card',
                ];
            })->all();

            $results[] = [
                'name' => 'Transaksi & Pembayaran',
                'badge_color' => 'bg-emerald-50 text-emerald-700',
                'items' => $paymentItems,
            ];
            $totalCount += count($paymentItems);
        }

        return response()->json([
            'success' => true,
            'query' => $query,
            'total_results' => $totalCount,
            'categories' => $results,
        ]);
    }

    /**
     * Mengambil daftar notifikasi dinamis Admin (sesuai desain referensi UI/UX).
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $admin = auth()->user();

        $payload = Cache::remember('admin_nav_notifications_'.$admin->id, 30, function () use ($admin) {
            // Pastikan ada notifikasi aktif di database, jika belum ada, sinkronkan dari aktivitas sistem nyata
            $this->ensureAdminNotifications($admin);

            // Ambil semua notifikasi milik admin
            $dbNotifications = DB::table('notifications')
                ->where('notifiable_type', User::class)
                ->where('notifiable_id', $admin->id)
                ->orderByDesc('created_at')
                ->take(15)
                ->get();

            $notifications = $dbNotifications->map(function ($item) {
                $data = json_decode($item->data, true) ?: [];
                $createdAt = Carbon::parse($item->created_at);

                return [
                    'id' => $item->id,
                    'category' => $data['category'] ?? 'system',
                    'title' => $data['title'] ?? 'Pemberitahuan Sistem',
                    'description' => $data['description'] ?? '',
                    'url' => $data['url'] ?? route('admin.dashboard'),
                    'icon' => $data['icon'] ?? 'bell',
                    'icon_bg' => $data['icon_bg'] ?? 'bg-indigo-100 text-indigo-600',
                    'is_read' => $item->read_at !== null,
                    'created_at_human' => $createdAt->diffForHumans(),
                    'created_at_raw' => $item->created_at,
                ];
            });

            $unreadCount = $notifications->where('is_read', false)->count();
            $totalCount = $notifications->count();

            return [
                'success' => true,
                'unread_count' => $unreadCount,
                'total_count' => $totalCount,
                'notifications' => $notifications->values()->all(),
            ];
        });

        return response()->json($payload);
    }

    /**
     * Menandai satu notifikasi sebagai telah dibaca (read_at = now()).
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $admin = auth()->user();

        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $admin->id)
            ->first();

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.',
            ], 404);
        }

        DB::table('notifications')
            ->where('id', $id)
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        Cache::forget('admin_nav_notifications_'.$admin->id);
        Cache::forget('admin_nav_unread_'.$admin->id);

        $data = json_decode($notification->data, true) ?: [];

        return response()->json([
            'success' => true,
            'redirect_url' => $data['url'] ?? route('admin.dashboard'),
        ]);
    }

    /**
     * Menandai seluruh notifikasi milik Admin sebagai telah dibaca.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $admin = auth()->user();

        DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $admin->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        Cache::forget('admin_nav_notifications_'.$admin->id);
        Cache::forget('admin_nav_unread_'.$admin->id);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi berhasil ditandai sebagai telah dibaca.',
        ]);
    }

    /**
     * Sinkronisasi aktivitas nyata dari database ke tabel notifications untuk Admin
     * jika belum ada notifikasi atau ada event baru.
     */
    private function ensureAdminNotifications(User $admin): void
    {
        $existingCount = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $admin->id)
            ->count();

        if ($existingCount >= 8) {
            return;
        }

        $now = now();
        $itemsToInsert = [];

        // 1. Pembayaran Berhasil Terakhir
        $recentPayments = Payment::with('event')
            ->where('status', PaymentStatus::Paid)
            ->latest('updated_at')
            ->take(2)
            ->get();

        foreach ($recentPayments as $idx => $pay) {
            $eventTitle = $pay->event?->title ?? 'Event VOLA';
            $itemsToInsert[] = [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\AdminActivityNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'category' => 'payment',
                    'title' => 'Pembayaran Berhasil Diverifikasi',
                    'description' => "Tagihan #{$pay->invoice_number} untuk {$eventTitle} telah lunas diverifikasi otomatis via QRIS/Midtrans.",
                    'url' => route('admin.transactions.index', ['search' => $pay->invoice_number]),
                    'icon' => 'check',
                    'icon_bg' => 'bg-emerald-100 text-emerald-600',
                ]),
                'read_at' => $idx === 0 ? null : $now->copy()->subMinutes(15),
                'created_at' => $pay->updated_at ?? $now->copy()->subMinutes(15 * ($idx + 1)),
                'updated_at' => $now,
            ];
        }

        // 2. Pengajuan Event Baru Butuh Review
        $pendingEvents = Event::with('organizer')
            ->where('status', EventStatus::PendingReview)
            ->latest('created_at')
            ->take(2)
            ->get();

        foreach ($pendingEvents as $idx => $ev) {
            $orgName = $ev->organizer?->organization_name ?? 'Penyelenggara';
            $itemsToInsert[] = [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\AdminActivityNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'category' => 'review',
                    'title' => 'Event Disetujui Kurator VOLA',
                    'description' => "Pengajuan event {$ev->title} oleh {$orgName} telah masuk antrean kurasi dan siap ditinjau.",
                    'url' => route('admin.review-events.index', ['search' => $ev->title]),
                    'icon' => 'sparkles',
                    'icon_bg' => 'bg-purple-100 text-purple-600',
                ]),
                'read_at' => null,
                'created_at' => $ev->created_at ?? $now->copy()->subHours(2 * ($idx + 1)),
                'updated_at' => $now,
            ];
        }

        // 3. Verifikasi Legalitas EO Masuk
        $pendingEos = OrganizerProfile::where('verification_status', VerificationStatus::Pending)
            ->latest('created_at')
            ->take(2)
            ->get();

        foreach ($pendingEos as $idx => $peo) {
            $itemsToInsert[] = [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\AdminActivityNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'category' => 'eo',
                    'title' => 'Verifikasi Legalitas EO Baru',
                    'description' => "Penyelenggara {$peo->organization_name} mengunggah berkas legalitas NIB/SK untuk diverifikasi.",
                    'url' => route('admin.eo-verifications', ['search' => $peo->organization_name]),
                    'icon' => 'shield',
                    'icon_bg' => 'bg-amber-100 text-amber-600',
                ]),
                'read_at' => $idx === 0 ? null : $now->copy()->subHours(5),
                'created_at' => $peo->created_at ?? $now->copy()->subHours(3 * ($idx + 1)),
                'updated_at' => $now,
            ];
        }

        // 4. Laporan Aduan Masuk
        $pendingReports = Report::whereIn('status', [ReportStatus::Pending, ReportStatus::InReview])
            ->latest('created_at')
            ->take(2)
            ->get();

        foreach ($pendingReports as $idx => $rep) {
            $itemsToInsert[] = [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\AdminActivityNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'category' => 'report',
                    'title' => 'Laporan Crew Masuk (Mendesak)',
                    'description' => "Aduan tiket #{$rep->ticket_number} terkait {$rep->reason} butuh tindakan segera moderasi.",
                    'url' => route('admin.reports.index', ['search' => $rep->ticket_number]),
                    'icon' => 'alert',
                    'icon_bg' => 'bg-rose-100 text-rose-600',
                ]),
                'read_at' => null,
                'created_at' => $rep->created_at ?? $now->copy()->subMinutes(45 * ($idx + 1)),
                'updated_at' => $now,
            ];
        }

        if (! empty($itemsToInsert)) {
            DB::table('notifications')->insert($itemsToInsert);
        }
    }
}
