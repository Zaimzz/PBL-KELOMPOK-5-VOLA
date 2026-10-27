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

class AdminDashboardController extends Controller
{
    public function index()
    {
        $pendingEoCount = OrganizerProfile::where('verification_status', VerificationStatus::Pending)->count();
        $pendingReviewEventCount = Event::where('status', EventStatus::PendingReview)->count();
        $activeEventCount = Event::where('status', EventStatus::Active)->count();
        $transactionCount = Payment::count();

        // 1. Overview Metrics (At a Glance)
        $monthlyRevenue = Payment::where('status', PaymentStatus::Paid)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $pendingReportsCount = class_exists(Report::class)
            ? Report::whereIn('status', [ReportStatus::Pending, ReportStatus::InReview])->count()
            : 0;

        $totalVolunteersCount = User::where('role', UserRole::Volunteer)->count();

        // 2. Dynamic Greeting
        $hour = (int) now()->format('H');
        if ($hour >= 5 && $hour < 12) {
            $greetingTime = 'Pagi';
        } elseif ($hour >= 12 && $hour < 15) {
            $greetingTime = 'Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greetingTime = 'Sore';
        } else {
            $greetingTime = 'Malam';
        }

        // 3. Action Required (Pending Tasks Widget)
        $actionRequired = collect();

        $pendingEvents = Event::with('organizer')
            ->where('status', EventStatus::PendingReview)
            ->latest()
            ->take(3)
            ->get();

        foreach ($pendingEvents as $event) {
            $actionRequired->push([
                'type' => 'event',
                'title' => $event->title,
                'subtitle' => 'Penyelenggara: '.($event->organizer->organization_name ?? '-'),
                'raw_time' => $event->created_at ?? $event->updated_at,
                'time' => $event->updated_at->diffForHumans(),
                'action_text' => 'Review Event',
                'action_link' => route('admin.review-events.index', ['selected' => $event->id]),
                'icon_color' => 'bg-indigo-50 text-brand-600',
            ]);
        }

        $pendingEos = OrganizerProfile::where('verification_status', VerificationStatus::Pending)
            ->latest()
            ->take(3)
            ->get();

        foreach ($pendingEos as $eo) {
            $actionRequired->push([
                'type' => 'eo',
                'title' => $eo->organization_name,
                'subtitle' => 'PIC: '.($eo->pic_name ?? '-').' (Dokumen Legalitas)',
                'raw_time' => $eo->created_at ?? $eo->updated_at,
                'time' => $eo->updated_at->diffForHumans(),
                'action_text' => 'Periksa Dokumen',
                'action_link' => route('admin.eo-verifications', ['search' => $eo->organization_name, 'selected' => $eo->id]),
                'icon_color' => 'bg-amber-50 text-amber-600',
            ]);
        }

        $actionRequired = $actionRequired->sortByDesc('raw_time')->values()->take(5);

        // 4. Recent Activity Feed (5 Aktivitas Terkini)
        $activities = collect();

        // Pendaftaran EO
        $recentEos = OrganizerProfile::latest('created_at')->take(3)->get();
        foreach ($recentEos as $reo) {
            $activities->push([
                'title' => 'Pendaftaran Penyelenggara Baru',
                'description' => "EO {$reo->organization_name} mendaftarkan profil organisasi.",
                'time' => $reo->created_at ? $reo->created_at->diffForHumans() : 'Baru saja',
                'raw_time' => $reo->created_at ?? now(),
                'badge' => 'Penyelenggara',
                'badge_color' => 'bg-indigo-50 text-brand-700',
            ]);
        }

        // Transaksi Terakhir
        $recentPayments = Payment::with('event')->latest('created_at')->take(3)->get();
        foreach ($recentPayments as $rpay) {
            $isPaid = $rpay->status === PaymentStatus::Paid;
            $activities->push([
                'title' => 'Transaksi Layanan #'.$rpay->invoice_number,
                'description' => 'Pembayaran Rp '.number_format($rpay->amount, 0, ',', '.').' ('.($isPaid ? 'LUNAS' : 'Menunggu').') untuk event '.($rpay->event?->title ?? '-'),
                'time' => $rpay->created_at ? $rpay->created_at->diffForHumans() : 'Baru saja',
                'raw_time' => $rpay->created_at ?? now(),
                'badge' => 'Transaksi',
                'badge_color' => $isPaid ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700',
            ]);
        }

        // Event Masuk
        $recentSubmittedEvents = Event::latest('created_at')->take(3)->get();
        foreach ($recentSubmittedEvents as $rse) {
            $activities->push([
                'title' => 'Pengajuan Event: '.$rse->title,
                'description' => 'Event baru diajukan ke platform dengan status: '.ucfirst($rse->status->value),
                'time' => $rse->created_at ? $rse->created_at->diffForHumans() : 'Baru saja',
                'raw_time' => $rse->created_at ?? now(),
                'badge' => 'Event',
                'badge_color' => 'bg-blue-50 text-blue-700',
            ]);
        }

        $recentActivities = $activities->sortByDesc('raw_time')->values()->take(5);

        return view('admin.dashboard', compact(
            'pendingEoCount',
            'pendingReviewEventCount',
            'activeEventCount',
            'transactionCount',
            'monthlyRevenue',
            'pendingReportsCount',
            'totalVolunteersCount',
            'greetingTime',
            'actionRequired',
            'recentActivities'
        ));
    }
}
