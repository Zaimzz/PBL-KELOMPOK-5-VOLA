<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::user();
        $profile = $user->volunteerProfile;

        // Data dummy untuk skills (nanti bisa diambil dari database)
        $skills = $profile?->skills ?? [];

        // Data dummy untuk event history (nanti diambil dari relasi applications)
        $recentEvents = [
            [
                'name' => 'Tech Summit Malang 2024',
                'role' => 'Liaison Officer (LO VIP)',
                'status' => 'Selesai',
                'date' => '2024',
            ],
            [
                'name' => 'Festival Budaya Polinema',
                'role' => 'Divisi Acara & Registrasi',
                'status' => 'Selesai',
                'date' => '2024',
            ],
        ];

        return view('crew.profile', compact('user', 'profile', 'skills', 'recentEvents'));
    }

    public function edit(): View
    {
        $user = Auth::user();
        $profile = $user->volunteerProfile;

        return view('crew.profile-edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string|max:1000',
            'institution' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->volunteerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['bio', 'institution', 'city'])
        );

        return redirect()->route('profile.show')->with('status', 'Profil berhasil diperbarui.');
    }
}
