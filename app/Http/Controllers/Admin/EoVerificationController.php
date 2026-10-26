<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\OrganizerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EoVerificationController extends Controller
{
    public function index(Request $request)
    {
        $query = OrganizerProfile::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $operator = $q->getConnection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
                $q->where('organization_name', $operator, '%'.$search.'%')
                    ->orWhere('pic_name', $operator, '%'.$search.'%');
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }

        $organizers = $query->latest()->paginate(5)->withQueryString();

        $stats = [
            'pending' => OrganizerProfile::where('verification_status', VerificationStatus::Pending)->count(),
            'verified' => OrganizerProfile::where('verification_status', VerificationStatus::Verified)->count(),
            'rejected' => OrganizerProfile::where('verification_status', VerificationStatus::Rejected)->count(),
        ];

        return view('admin.eo-verifications.index', compact('organizers', 'stats'));
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
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        if ($organizer->verification_status !== VerificationStatus::Pending) {
            return back()->with('error', 'Status EO tidak valid untuk ditolak.');
        }

        $organizer->update([
            'verification_status' => VerificationStatus::Rejected,
            'rejection_reason' => $request->rejection_reason,
            'verified_by' => null,
            'verified_at' => null,
        ]);

        return back()->with('success', 'Akun EO berhasil ditolak.');
    }

    public function document(OrganizerProfile $organizer)
    {
        if (! $organizer->document_path || ! Storage::disk('local')->exists($organizer->document_path)) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        return Storage::disk('local')->response($organizer->document_path);
    }
}
