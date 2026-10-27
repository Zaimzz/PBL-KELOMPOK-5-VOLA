<?php

namespace App\Http\Controllers\Eo;

use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Eo\UpdateOrganizerProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrganizerProfileController extends Controller
{
    /**
     * Tampilkan halaman profil organisasi EO yang sedang login.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $profile = $user->organizerProfile;
        $events = collect();

        if ($profile) {
            $events = $profile->events()
                ->with('category')
                ->latest('created_at')
                ->limit(4)
                ->get();
        }

        return view('eo.organization.index', compact('profile', 'events'));
    }

    /**
     * Tampilkan form edit profil organisasi.
     */
    public function edit(Request $request): View
    {
        $profile = $request->user()->organizerProfile;

        return view('eo.organization.edit', compact('profile'));
    }

    /**
     * Simpan perubahan profil (tanpa mengubah verification_status).
     */
    public function update(UpdateOrganizerProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Handle document upload
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('organizer-documents', 'local');
            $validated['document_path'] = $path;
        }

        // Hapus key 'document' dari validated agar tidak masuk fillable
        unset($validated['document']);

        $profile = $user->organizerProfile;

        if ($profile) {
            // Hapus dokumen lama jika ada dokumen baru
            if (isset($validated['document_path']) && $profile->document_path) {
                Storage::disk('local')->delete($profile->document_path);
            }

            // Jangan izinkan EO mengubah status verifikasi
            unset(
                $validated['verification_status'],
                $validated['verified_by'],
                $validated['verified_at'],
                $validated['rejection_reason'],
            );

            $profile->update($validated);
        } else {
            // Buat profile baru dengan status null (belum diajukan)
            unset(
                $validated['verification_status'],
                $validated['verified_by'],
                $validated['verified_at'],
                $validated['rejection_reason'],
            );

            $user->organizerProfile()->create(array_merge($validated, [
                'verification_status' => VerificationStatus::Pending,
            ]));
        }

        return redirect()
            ->route('eo.organization')
            ->with('success', 'Profil organisasi berhasil disimpan.');
    }

    /**
     * Ajukan verifikasi (pending) atau ajukan kembali setelah rejected.
     * Hanya diizinkan jika status = null/rejected.
     */
    public function submitVerification(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->organizerProfile;

        if (! $profile) {
            return redirect()
                ->route('eo.organization')
                ->with('error', 'Lengkapi profil organisasi terlebih dahulu sebelum mengajukan verifikasi.');
        }

        // Hanya bisa diajukan jika belum pending/verified
        if ($profile->verification_status === VerificationStatus::Pending) {
            return redirect()
                ->route('eo.organization')
                ->with('warning', 'Profil Anda sudah dalam antrian verifikasi. Mohon tunggu proses pemeriksaan oleh Admin.');
        }

        if ($profile->verification_status === VerificationStatus::Verified) {
            return redirect()
                ->route('eo.organization')
                ->with('warning', 'Akun Anda sudah terverifikasi.');
        }

        // Status rejected atau null → set ke pending, hapus rejection_reason
        $profile->update([
            'verification_status' => VerificationStatus::Pending,
            'rejection_reason' => null,
            'verified_by' => null,
            'verified_at' => null,
        ]);

        return redirect()
            ->route('eo.organization')
            ->with('success', 'Pengajuan verifikasi berhasil dikirim. Silakan tunggu proses pemeriksaan oleh Admin.');
    }
}
