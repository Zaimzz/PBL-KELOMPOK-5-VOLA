<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use App\Support\MediaStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::user();
        $profile = $user->volunteerProfile;
        $skills = is_array($profile?->skills) ? $profile->skills : [];

        $availableSkills = [
            'Liaison Officer (LO)', 'Dokumentasi & Media', 'Registrasi & Ticketing',
            'Stage Crew', 'Keamanan / Security', 'Konsumsi & Logistik',
            'MC / Host', 'Desain Grafis', 'Videografer', 'First Aid / Medis',
        ];

        return view('crew.profile', compact('user', 'profile', 'skills', 'availableSkills'));
    }

    public function edit(): View
    {
        $user = Auth::user();
        $profile = $user->volunteerProfile;

        $cities = [
            'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang',
            'Makassar', 'Palembang', 'Tangerang', 'Depok', 'Yogyakarta',
            'Malang', 'Bekasi', 'Batam', 'Pekanbaru', 'Denpasar',
            'Samarinda', 'Balikpapan', 'Pontianak', 'Manado', 'Padang',
            'Lampung', 'Jambi', 'Banjarmasin', 'Mataram', 'Kupang',
            'Jayapura', 'Ambon', 'Sorong', 'Ternate', 'Banda Aceh',
        ];

        return view('crew.profile-edit', compact('user', 'profile', 'cities'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->volunteerProfile;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validated['name'] !== $user->name) {
            $user->update(['name' => $validated['name']]);
        }

        if ($request->hasFile('photo')) {
            if ($profile && $profile->photo_path) {
                MediaStorage::deleteSafe($profile->photo_path);
            }
            $path = $request->file('photo')->store('profiles', 'public');
            $validated['photo_path'] = $path;
        }

        $dataToUpdate = array_merge(
            $request->only(['bio', 'city']),
            isset($validated['photo_path']) ? ['photo_path' => $validated['photo_path']] : []
        );

        $user->volunteerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $dataToUpdate
        );

        return redirect()->route('profile.show')->with('status', 'Profil berhasil diperbarui.');
    }

    public function updateSkills(Request $request)
    {
        $request->validate([
            'skills' => 'nullable|array',
            'custom_skill' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $profile = $user->volunteerProfile;

        $selectedSkills = $request->input('skills', []);
        $customSkill = trim($request->input('custom_skill'));

        if (! empty($customSkill)) {
            $selectedSkills[] = $customSkill;
        }

        $finalSkills = array_values(array_unique($selectedSkills));

        $user->volunteerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            ['skills' => $finalSkills]
        );

        return redirect()->route('profile.show')->with('status', 'Keahlian berhasil diperbarui.');
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'type' => 'required|in:cv_path,portfolio_path',
        ]);

        $user = Auth::user();
        $profile = $user->volunteerProfile;

        if ($profile && $profile->{$request->type}) {
            MediaStorage::deleteSafe($profile->{$request->type});
        }

        $originalName = $request->file('file')->getClientOriginalName();
        $path = $request->file('file')->storeAs('documents', $originalName, 'public');

        $user->volunteerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [$request->type => $path]
        );

        return response()->json([
            'success' => true,
            'url' => MediaStorage::documentUrl($path),
            'filename' => $originalName,
            'message' => 'File berhasil diunggah!',
        ]);
    }

    public function deleteDocument(string $type)
    {
        if (! in_array($type, ['cv_path', 'portfolio_path'])) {
            abort(400, 'Tipe dokumen tidak valid.');
        }

        $user = Auth::user();
        $profile = $user->volunteerProfile;

        if ($profile && $profile->{$type}) {
            MediaStorage::deleteSafe($profile->{$type});
            $profile->update([$type => null]);
        }

        return redirect()->route('profile.show')->with('status', 'File berhasil dihapus.');
    }
}
