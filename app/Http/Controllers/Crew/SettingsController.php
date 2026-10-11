<?php

namespace App\Http\Controllers\Crew;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $profile = $user->volunteerProfile;

        $availableCategories = [
            ['id' => 'festival', 'name' => 'Festival & Seni'],
            ['id' => 'seminar', 'name' => 'Seminar & Teknologi'],
            ['id' => 'kepanitiaan', 'name' => 'Kepanitiaan Kampus'],
            ['id' => 'sosial', 'name' => 'Kegiatan Sosial'],
            ['id' => 'olahraga', 'name' => 'Olahraga & Marathon'],
            ['id' => 'musik', 'name' => 'Musik & Konser'],
        ];

        // Ambil skills dengan aman
        $rawSkills = $profile?->skills;
        if (is_array($rawSkills)) {
            $selectedSkills = $rawSkills;
        } elseif (is_string($rawSkills)) {
            $decoded = json_decode($rawSkills, true);
            $selectedSkills = is_array($decoded) ? $decoded : [];
        } else {
            $selectedSkills = [];
        }
        
        $preferredCity = $profile?->city ?? '';

        return view('crew.settings', compact(
            'user', 
            'availableCategories', 
            'selectedSkills', 
            'preferredCity'
        ));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $user->update($request->only('email', 'phone'));

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    public function updatePreferences(Request $request)
    {
        $request->validate([
            'skills' => 'nullable|array',
            'skills.*' => 'string',
            'city' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $skillsData = $request->input('skills', []);
        $cityData = $request->input('city');

        $profile = $user->volunteerProfile;
        
        if ($profile) {
            $profile->update([
                'skills' => $skillsData,
                'city' => $cityData,
            ]);
        } else {
            $user->volunteerProfile()->create([
                'skills' => $skillsData,
                'city' => $cityData,
            ]);
        }

        return back()->with('preferences_saved', true);
    }
}