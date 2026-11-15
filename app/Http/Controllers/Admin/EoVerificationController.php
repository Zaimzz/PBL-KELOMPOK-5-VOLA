<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\OrganizerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class EoVerificationController extends Controller
{
    public function index(Request $request)
    {
        $query = OrganizerProfile::with('user')->whereNotNull('verification_status');

        // Search: organisasi, PIC, kota, tipe, user, dan NIB/legal_number
        if ($request->filled('search')) {
            $search = $request->search;
            $cleanSearch = ltrim($search, '#');
            $hasNib = Schema::hasColumn('organizer_profiles', 'nib');
            $hasLegalNum = Schema::hasColumn('organizer_profiles', 'legal_number');

            $query->where(function ($q) use ($search, $cleanSearch, $hasNib, $hasLegalNum) {
                $operator = $q->getConnection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
                $q->where('organization_name', $operator, '%'.$search.'%')
                    ->orWhere('pic_name', $operator, '%'.$search.'%')
                    ->orWhere('city', $operator, '%'.$search.'%')
                    ->orWhere('organization_type', $operator, '%'.$search.'%')
                    ->orWhere('pic_phone', $operator, '%'.$search.'%')
                    ->orWhereHas('user', function ($uq) use ($search, $operator) {
                        $uq->where('name', $operator, '%'.$search.'%')
                            ->orWhere('email', $operator, '%'.$search.'%');
                    });

                if ($hasNib) {
                    $q->orWhere('nib', $operator, '%'.$cleanSearch.'%');
                }
                if ($hasLegalNum) {
                    $q->orWhere('legal_number', $operator, '%'.$cleanSearch.'%');
                }
            });
        }

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('verification_status', $request->status);
        }

        $organizers = $query->latest()->paginate(5)->withQueryString();

        if ($request->filled('selected')) {
            $selectedId = (int) $request->selected;
            if (! $organizers->getCollection()->contains('id', $selectedId)) {
                $targetEo = OrganizerProfile::with('user')->find($selectedId);
                if ($targetEo) {
                    $organizers->getCollection()->prepend($targetEo);
                }
            }
        }

        $organizersPayload = $organizers->getCollection()->map(function ($eo) {
            $hasDoc = ! empty($eo->document_path);
            $docUrl = $hasDoc ? route('admin.eo-verifications.document', $eo) : null;

            return [
                'id' => $eo->id,
                'organization_name' => $eo->organization_name,
                'avatar_initials' => strtoupper(substr($eo->organization_name, 0, 2)),
                'organization_type' => $eo->organization_type ?? 'Organisasi',
                'pic_name' => $eo->pic_name ?? ($eo->user?->name ?? '-'),
                'pic_phone' => $eo->pic_phone ?? '-',
                'city' => $eo->city ?? '-',
                'nib' => $eo->nib ?? $eo->legal_number ?? null,
                'user_name' => $eo->user?->name ?? '-',
                'user_email' => $eo->user?->email ?? '-',
                'description' => $eo->description ?: 'Tidak ada deskripsi profil.',
                'social_link' => $eo->social_link,
                'status' => $eo->verification_status instanceof VerificationStatus ? $eo->verification_status->value : (string) $eo->verification_status,
                'rejection_reason' => $eo->rejection_reason,
                'created_at_formatted' => $eo->created_at ? $eo->created_at->translatedFormat('d M Y, H:i').' WIB' : '-',
                'has_document' => $hasDoc,
                'document_url' => $docUrl,
                'approve_url' => route('admin.eo-verifications.approve', $eo),
            ];
        })->values();

        $stats = [
            'pending' => OrganizerProfile::where('verification_status', VerificationStatus::Pending)->count(),
            'verified' => OrganizerProfile::where('verification_status', VerificationStatus::Verified)->count(),
            'rejected' => OrganizerProfile::where('verification_status', VerificationStatus::Rejected)->count(),
        ];

        return view('admin.eo-verifications.index', compact('organizers', 'stats', 'organizersPayload'));
    }

    public function approve(Request $request, OrganizerProfile $organizer)
    {
        if ($organizer->verification_status !== VerificationStatus::Pending) {
            return back()->with('error', 'Status EO tidak valid untuk diverifikasi.');
        }

        $organizer->update([
            'verification_status' => VerificationStatus::Verified,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Akun EO berhasil disetujui.');
    }

    public function reject(Request $request, OrganizerProfile $organizer)
    {
        if ($request->has('rejection_reason') && ! $request->has('alasan')) {
            $request->validate([
                'rejection_reason' => ['required', 'string', 'max:500'],
            ], [
                'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            ]);
            $reason = $request->rejection_reason;
        } else {
            $request->validate([
                'alasan' => 'required|string',
            ]);
            $reason = $request->alasan;
        }

        if ($organizer->verification_status !== VerificationStatus::Pending) {
            return back()->with('error', 'Status EO tidak valid untuk ditolak.');
        }

        $organizer->update([
            'verification_status' => VerificationStatus::Rejected,
            'rejection_reason' => $reason,
            'verified_by' => null,
            'verified_at' => null,
        ]);

        return back()->with('success', 'Akun EO berhasil ditolak.');
    }

    public function document(OrganizerProfile $organizer)
    {
        if (! $organizer->document_path) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        if (Storage::disk('local')->exists($organizer->document_path)) {
            return Storage::disk('local')->response($organizer->document_path);
        }

        if (Storage::disk('public')->exists($organizer->document_path)) {
            return Storage::disk('public')->response($organizer->document_path);
        }

        if (file_exists(public_path('storage/'.$organizer->document_path))) {
            return response()->file(public_path('storage/'.$organizer->document_path));
        }

        abort(404, 'Dokumen fisik tidak ditemukan.');
    }
}
