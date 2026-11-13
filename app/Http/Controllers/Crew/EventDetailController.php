<?php

namespace App\Http\Controllers\Crew;

use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventDetailController extends Controller
{
    private const CATEGORY_IMAGES = [
        'festival'  => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800',
        'seminar'   => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
        'musik'     => 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=800',
        'olahraga'  => 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=800',
        'sosial'    => 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?w=800',
        'kampus'    => 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?w=800',
        'komunitas' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800',
        'umum'      => 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=800',
    ];

    private function getImageByCategory(?string $category): string
    {
        $key = strtolower(trim($category ?? 'umum'));
        return self::CATEGORY_IMAGES[$key] ?? self::CATEGORY_IMAGES['umum'];
    }

    public function show($event): View
    {
        $eventNum = (int) $event;

        $dbEvent = Event::with(['category', 'organizer', 'positions'])
            ->where('status', EventStatus::Active)
            ->find($eventNum);

        if (!$dbEvent) {
            abort(404, 'Event tidak ditemukan');
        }

        $categoryName = $dbEvent->category?->name ?? 'Umum';
        $organizer = $dbEvent->organizer;
        $isVerified = $organizer?->verification_status === VerificationStatus::Verified;

        $startDate = $dbEvent->start_date?->translatedFormat('d M Y');
        $endDate = $dbEvent->end_date?->translatedFormat('d M Y');
        $dateFormatted = $endDate && $endDate !== $startDate ? $startDate.' – '.$endDate : ($startDate ?? '-');

        $totalDays = $dbEvent->start_date && $dbEvent->end_date ? $dbEvent->start_date->diffInDays($dbEvent->end_date) + 1 : 1;
        $dateDuration = $totalDays.' '.($totalDays === 1 ? 'Hari Acara' : 'Hari Penuh Acara');

        $deadline = $dbEvent->registration_deadline ? $dbEvent->registration_deadline->translatedFormat('d F Y') : '-';
        $daysLeft = $dbEvent->registration_deadline ? max(0, (int) now()->startOfDay()->diffInDays($dbEvent->registration_deadline, false)) : 0;

        $shiftTime = '-';
        if ($dbEvent->start_time && $dbEvent->end_time) {
            $shiftTime = substr((string) $dbEvent->start_time, 0, 5).' – '.substr((string) $dbEvent->end_time, 0, 5).' WIB';
        } elseif ($dbEvent->start_time) {
            $shiftTime = substr((string) $dbEvent->start_time, 0, 5).' WIB';
        }

        $location = trim(($dbEvent->location ?? '').($dbEvent->city ? ', '.$dbEvent->city : ''), ', ') ?: '-';
        $locationCity = $dbEvent->city ?? $dbEvent->location ?? '-';

        $imageUrl = $dbEvent->poster_path ? Storage::url($dbEvent->poster_path) : $this->getImageByCategory($categoryName);

        $orgName = $organizer?->organization_name ?? '-';
        $orgAbbr = implode('', array_map(fn (string $w) => strtoupper($w[0]), array_slice(array_filter(explode(' ', $orgName)), 0, 3)));

        $benefitsRaw = $dbEvent->benefits;
        $benefits = is_array($benefitsRaw) && count($benefitsRaw) > 0 ? $benefitsRaw : ['E-Sertifikat resmi dari penyelenggara', 'Pengalaman kerja lapangan nyata', 'Networking dengan profesional di bidangnya'];

        $eventData = [
            'id' => $dbEvent->id, 'title' => $dbEvent->title, 'category' => $categoryName, 'filter_category' => $categoryName,
            'organizer' => $orgName, 'organizer_abbr' => $orgAbbr ?: 'ORG', 'organizer_type' => $organizer?->organization_type ?? 'Penyelenggara Event',
            'organizer_desc' => $organizer?->description ?? 'Penyelenggara event di Indonesia.', 'organizer_verified' => $isVerified,
            'status' => 'Open Volunteer', 'status_badge' => 'Open Volunteer', 'status_badge_type' => 'success',
            'days_left' => $daysLeft, 'date' => $dateFormatted, 'date_duration' => $dateDuration, 'shift_time' => $shiftTime,
            'shift_note' => 'Termasuk Rotasi Istirahat', 'location' => $location, 'location_city' => $locationCity,
            'location_short' => $dbEvent->location ?? $dbEvent->city ?? '-', 'entrance' => $dbEvent->location ?? '-',
            'deadline' => $deadline, 'deadline_time' => 'Pukul 23:59 WIB', 'total_positions' => $dbEvent->positions->count(),
            'total_quota' => (int) $dbEvent->positions->sum('quota'), 'address' => $location,
            'meeting_point' => $dbEvent->coordination_link ? 'Lihat di: '.$dbEvent->coordination_link : ($location !== '-' ? $location : 'Informasi meeting point akan diberikan saat briefing.'),
            'image' => $imageUrl, 'description' => $dbEvent->description ?? '',
        ];

        $positions = $dbEvent->positions->map(function ($pos, $index) {
            $quota = (int) ($pos->quota ?? 0);
            $filled = Application::where('position_id', $pos->id)
                ->whereIn('status', ['pending', 'processing', 'accepted'])
                ->count();

            $pct = $quota > 0 ? min(100, (int) round(($filled / $quota) * 100)) : 0;
            $sisaKursi = max(0, $quota - $filled);

            $requirements = [];
            if ($pos->requirements) {
                $requirements = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', (string) $pos->requirements))));
            }

            return [
                'id' => $pos->id, 'title' => $pos->name, 'badge' => $index === 0 ? 'Unggulan' : null,
                'sisa_kursi' => $sisaKursi, 'description' => $pos->description ?: 'Bertanggung jawab dalam kelancaran operasional divisi.',
                'quota' => $quota, 'date' => 'Briefing H-1 di Lokasi Acara', 'requirements' => $requirements,
                'is_selected' => false, 'capacity' => $filled, 'filled' => $filled, 'percentage' => $pct,
            ];
        })->toArray();

        $recruitmentFlow = [
            ['step' => 1, 'title' => 'Pilih Posisi', 'desc' => 'Sesuai minat divisi'],
            ['step' => 2, 'title' => 'Ajukan Berkas', 'desc' => 'Data diri & CV'],
            ['step' => 3, 'title' => 'Seleksi Review', 'desc' => 'Verifikasi tim EO'],
            ['step' => 4, 'title' => 'Pengumuman', 'desc' => 'Notifikasi email & app'],
            ['step' => 5, 'title' => 'Briefing & Event', 'desc' => 'Onboarding H-1'],
        ];

        $requirements = [
            'Berusia minimal 18 tahun (mahasiswa atau fresh graduate dipersilakan)',
            'Berkomitmen hadir penuh selama acara berlangsung',
            'Bertanggung jawab, proaktif, dan dapat bekerja secara kolaboratif',
            'Wajib mengikuti technical meeting dan briefing H-1 di lokasi',
        ];

        return view('crew.event-detail', compact('eventData', 'positions', 'recruitmentFlow', 'benefits', 'requirements'));
    }
}