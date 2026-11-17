<?php

namespace App\Http\Controllers\Crew;

use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventDetailController extends Controller
{
    public function show(int $event): View
    {
        /** @var Event $model */
        $model = Event::with(['category', 'organizer', 'positions'])
            ->where('status', EventStatus::Active)
            ->whereNotNull('published_at')
            ->findOrFail($event);

        $organizer = $model->organizer;
        $isVerified = $organizer?->verification_status === VerificationStatus::Verified;

        $startDate = $model->start_date?->translatedFormat('d M Y');
        $endDate = $model->end_date?->translatedFormat('d M Y');
        $dateFormatted = $endDate && $endDate !== $startDate
            ? $startDate.' – '.$endDate
            : ($startDate ?? '-');

        $totalDays = $model->start_date && $model->end_date
            ? $model->start_date->diffInDays($model->end_date) + 1
            : 1;

        $dateDuration = $totalDays.' '.($totalDays === 1 ? 'Hari Acara' : 'Hari Penuh Acara');

        $deadline = $model->registration_deadline
            ? $model->registration_deadline->translatedFormat('d M Y')
            : '-';

        $daysLeft = $model->registration_deadline
            ? max(0, (int) now()->startOfDay()->diffInDays($model->registration_deadline, false))
            : 0;

        $shiftTime = '-';
        if ($model->start_time && $model->end_time) {
            $shiftTime = substr((string) $model->start_time, 0, 5).' – '.substr((string) $model->end_time, 0, 5).' WIB';
        } elseif ($model->start_time) {
            $shiftTime = substr((string) $model->start_time, 0, 5).' WIB';
        }

        $location = trim(($model->location ?? '').($model->city ? ', '.$model->city : ''), ', ') ?: '-';
        $locationCity = $model->city ?? $model->location ?? '-';

        $imageUrl = $model->poster_path
            ? Storage::disk('public')->url($model->poster_path)
            : asset('images/event-placeholder.svg');

        $orgName = $organizer?->organization_name ?? '-';
        $orgAbbr = implode('', array_map(
            fn (string $w) => strtoupper($w[0]),
            array_slice(array_filter(explode(' ', $orgName)), 0, 3)
        ));

        $benefitsRaw = $model->benefits;
        $benefitsList = is_array($benefitsRaw) && count($benefitsRaw) > 0
            ? $benefitsRaw
            : [
                'E-Sertifikat resmi dari penyelenggara',
                'Pengalaman kerja lapangan nyata',
                'Networking dengan profesional di bidangnya',
            ];

        $eventData = [
            'id' => $model->id,
            'title' => $model->title,
            'category' => $model->category?->name ?? 'Umum',
            'filter_category' => $model->category?->name ?? 'Umum',
            'organizer' => $orgName,
            'organizer_abbr' => $orgAbbr ?: 'ORG',
            'organizer_type' => $organizer?->organization_type ?? 'Penyelenggara Event',
            'organizer_desc' => $organizer?->description ?? 'Penyelenggara event di Indonesia.',
            'organizer_verified' => $isVerified,
            'organizer_user_id' => $organizer?->user_id,
            'status' => 'Open Volunteer',
            'status_badge' => 'Open Volunteer',
            'status_badge_type' => 'success',
            'days_left' => $daysLeft,
            'date' => $dateFormatted,
            'date_duration' => $dateDuration,
            'shift_time' => $shiftTime,
            'shift_note' => 'Termasuk Rotasi Istirahat',
            'location' => $location,
            'location_city' => $locationCity,
            'location_short' => $model->location ?? $model->city ?? '-',
            'entrance' => $model->location ?? '-',
            'deadline' => $deadline,
            'deadline_time' => 'Pukul 23:59 WIB',
            'total_positions' => $model->positions->count(),
            'total_quota' => $model->positions->sum('quota'),
            'address' => $location,
            'meeting_point' => $model->coordination_link
                ? 'Lihat di: '.$model->coordination_link
                : ($location !== '-' ? $location : 'Informasi meeting point akan diberikan saat briefing.'),
            'image' => $imageUrl,
            'description' => $model->description ?? '',
        ];

        $user = auth()->user();
        $volunteerProfile = $user?->volunteerProfile;
        $appliedPositionIds = $volunteerProfile
            ? Application::where('volunteer_profile_id', $volunteerProfile->id)
                ->pluck('position_id')
                ->all()
            : [];

        $positions = $model->positions->map(function ($pos) use ($appliedPositionIds) {
            $quota = (int) ($pos->quota ?? 0);
            $filled = Application::where('position_id', $pos->id)
                ->whereIn('status', ['pending', 'processing', 'accepted'])
                ->count();
            $sisaKursi = max(0, $quota - $filled);
            $pct = $quota > 0 ? min(100, (int) round(($filled / $quota) * 100)) : 0;

            $requirements = [];
            if ($pos->requirements) {
                $requirements = array_values(array_filter(
                    array_map('trim', preg_split('/[\n,]+/', (string) $pos->requirements))
                ));
            }

            return [
                'id' => $pos->id,
                'title' => $pos->name,
                'badge' => null,
                'sisa_kursi' => $sisaKursi,
                'description' => $pos->description ?? 'Deskripsi posisi belum tersedia.',
                'quota' => $quota,
                'date' => null,
                'requirements' => $requirements,
                'is_selected' => false,
                'is_applied' => in_array($pos->id, $appliedPositionIds, true),
                'capacity' => $quota,
                'filled' => $filled,
                'percentage' => $pct,
            ];
        })->values()->all();

        $recruitmentFlow = [
            ['step' => 1, 'title' => 'Daftar Online', 'desc' => 'Isi formulir'],
            ['step' => 2, 'title' => 'Seleksi Berkas', 'desc' => 'Review CV & motivasi'],
            ['step' => 3, 'title' => 'Pengumuman', 'desc' => 'Via email & WhatsApp'],
            ['step' => 4, 'title' => 'Konfirmasi', 'desc' => 'Konfirmasi kehadiran'],
            ['step' => 5, 'title' => 'Briefing & Event', 'desc' => 'Onboarding H-1'],
        ];

        $benefits = $benefitsList;

        $requirements = [
            'Berusia minimal 18 tahun (mahasiswa atau fresh graduate dipersilakan)',
            'Berkomitmen hadir penuh selama acara berlangsung',
            'Bertanggung jawab, proaktif, dan dapat bekerja secara kolaboratif',
            'Wajib mengikuti technical meeting dan briefing H-1 di lokasi',
        ];

        return view('crew.event-detail', compact(
            'eventData',
            'positions',
            'recruitmentFlow',
            'benefits',
            'requirements',
        ));
    }
}
