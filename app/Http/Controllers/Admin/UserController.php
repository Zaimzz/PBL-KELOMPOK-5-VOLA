<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Menampilkan daftar manajemen pengguna dan metrik utama.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'string', 'in:all,volunteer,eo'],
            'status' => ['nullable', 'string', 'in:all,active,suspended'],
        ]);

        // 1. Metrik Ringkasan Header
        $stats = [
            'total_users' => User::whereIn('role', [UserRole::Volunteer, UserRole::Eo])->count(),
            'active_volunteers' => User::where('role', UserRole::Volunteer)->where('status', UserStatus::Active)->count(),
            'total_eos' => User::where('role', UserRole::Eo)->count(),
            'suspended_users' => User::where('status', UserStatus::Suspended)->count(),
        ];

        // 2. Query Utama Pengguna (Kecualikan Admin)
        $query = User::with(['volunteerProfile', 'organizerProfile'])
            ->whereIn('role', [UserRole::Volunteer, UserRole::Eo]);

        // Filter Pencarian
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('volunteerProfile', function ($vq) use ($search) {
                        $vq->where('institution', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%");
                    })
                    ->orWhereHas('organizerProfile', function ($oq) use ($search) {
                        $oq->where('organization_name', 'like', "%{$search}%")
                            ->orWhere('pic_name', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Peran
        if ($role = $request->input('role')) {
            if ($role === 'volunteer') {
                $query->where('role', UserRole::Volunteer);
            } elseif ($role === 'eo') {
                $query->where('role', UserRole::Eo);
            }
        }

        // Filter Status
        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->where('status', UserStatus::Active);
            } elseif ($status === 'suspended') {
                $query->where('status', UserStatus::Suspended);
            }
        }

        $users = $query->latest('created_at')->paginate(10)->withQueryString();

        // 3. Distribusi Pertumbuhan Pengguna (6 Bulan Terakhir untuk Komponen Grafik)
        $growthChart = $this->calculateUserGrowth();

        return view('admin.users.index', compact('users', 'stats', 'growthChart'));
    }

    /**
     * Mengambil detail profil akun pengguna untuk Modal Alpine.js.
     */
    public function show(User $user): JsonResponse
    {
        $user->load([
            'volunteerProfile.applications',
            'organizerProfile.events',
        ]);

        $profileData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '-',
            'role' => $user->role->value,
            'role_label' => $user->role === UserRole::Volunteer ? 'Volunteer' : 'Event Organizer',
            'status' => $user->status->value,
            'status_label' => $user->status === UserStatus::Active ? 'Aktif' : 'Ditangguhkan',
            'avatar_url' => $user->avatar_path ? Storage::url($user->avatar_path) : null,
            'joined_at' => $user->created_at->translatedFormat('d F Y, H:i'),
        ];

        if ($user->role === UserRole::Volunteer && $user->volunteerProfile) {
            $vp = $user->volunteerProfile;
            $profileData['details'] = [
                'type' => 'volunteer',
                'institution' => $vp->institution ?? '-',
                'city' => $vp->city ?? '-',
                'bio' => $vp->bio ?? '-',
                'skills' => is_array($vp->skills) ? implode(', ', $vp->skills) : ($vp->skills ?? '-'),
                'total_applications' => $vp->applications->count(),
                'cv_url' => $vp->cv_path ? Storage::url($vp->cv_path) : null,
            ];
        } elseif ($user->role === UserRole::Eo && $user->organizerProfile) {
            $op = $user->organizerProfile;
            $profileData['details'] = [
                'type' => 'eo',
                'organization_name' => $op->organization_name,
                'organization_type' => $op->organization_type ?? 'Organisasi',
                'city' => $op->city ?? '-',
                'pic_name' => $op->pic_name ?? '-',
                'pic_phone' => $op->pic_phone ?? '-',
                'description' => $op->description ?? '-',
                'verification_status' => $op->verification_status?->value ?? 'pending',
                'total_events' => $op->events->count(),
                'document_url' => $op->document_path ? Storage::url($op->document_path) : null,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $profileData,
        ]);
    }

    /**
     * Mengubah status pengguna (Suspend / Aktifkan kembali).
     */
    public function toggleStatus(Request $request, User $user)
    {
        if ($user->role === UserRole::Admin) {
            abort(403, 'Tidak diizinkan mengubah status akun Administrator.');
        }

        $newStatus = $user->status === UserStatus::Active ? UserStatus::Suspended : UserStatus::Active;
        $user->status = $newStatus;
        $user->save();

        $actionText = $newStatus === UserStatus::Suspended ? 'ditangguhkan' : 'diaktifkan kembali';
        $message = "Akun pengguna {$user->name} berhasil {$actionText}.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'new_status' => $newStatus->value,
                'status_label' => $newStatus === UserStatus::Active ? 'Aktif' : 'Ditangguhkan',
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Menghitung statistik pendaftaran pengguna untuk 6 bulan terakhir.
     */
    private function calculateUserGrowth(): array
    {
        $months = [];
        $volunteers = [];
        $organizers = [];
        $totals = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthYear = $date->format('Y-m');
            $label = $date->translatedFormat('M Y');

            $volCount = User::where('role', UserRole::Volunteer)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $eoCount = User::where('role', UserRole::Eo)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $months[] = $label;
            $volunteers[] = $volCount;
            $organizers[] = $eoCount;
            $totals[] = $volCount + $eoCount;
        }

        $maxVal = max(array_merge($totals, [1]));

        return [
            'labels' => $months,
            'volunteers' => $volunteers,
            'organizers' => $organizers,
            'totals' => $totals,
            'max_value' => $maxVal,
        ];
    }
}
