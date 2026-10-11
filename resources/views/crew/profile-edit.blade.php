<x-layouts.crew title="Edit Profil - VOLA">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Profil</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi profil kamu.</p>
        </div>

        @if(session('status'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                <p class="text-sm font-medium text-emerald-800">{{ session('status') }}</p>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Tentang Saya</label>
                    <textarea name="bio" rows="4" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent text-sm @error('bio') border-red-500 @enderror" placeholder="Ceritakan tentang diri Anda...">{{ old('bio', $profile?->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Institusi/Kampus</label>
                    <input type="text" name="institution" value="{{ old('institution', $profile?->institution) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent text-sm @error('institution') border-red-500 @enderror" placeholder="Contoh: Universitas Indonesia">
                    @error('institution')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Kota Domisili</label>
                    <input type="text" name="city" value="{{ old('city', $profile?->city) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent text-sm @error('city') border-red-500 @enderror" placeholder="Contoh: Jakarta">
                    @error('city')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('profile.show') }}" class="px-5 py-2.5 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#4F46E5] rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-layouts.crew>