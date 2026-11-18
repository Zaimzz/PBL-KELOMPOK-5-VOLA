<x-layouts.crew title="Edit Profil - VOLA">
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ photoPreview: null }">
    
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Profil</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui informasi profil kamu.</p>
    </div>

    @if(session('status'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="text-sm font-medium text-emerald-800">{{ session('status') }}</p>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-900 mb-3">Foto Profil</label>
                <div class="flex items-center gap-6">
                    <div class="relative w-24 h-24 rounded-full overflow-hidden bg-gray-100 border-2 border-dashed border-gray-300 shrink-0">
                        <img x-show="photoPreview" :src="photoPreview" class="w-full h-full object-cover" style="display: none;">
                        <div x-show="!photoPreview" class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                            @if($profile?->photo_url)
                                <img src="{{ $profile->photo_url }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="text-[10px]">No Photo</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="photo" id="photo" accept="image/*" class="hidden" @change="photoPreview = URL.createObjectURL($event.target.files[0])">
                        <label for="photo" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Unggah Foto Baru
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Format JPG, PNG. Maksimal 2MB.</p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#4F46E5] text-sm">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Tentang Saya</label>
                <textarea name="bio" rows="4" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#4F46E5] text-sm">{{ old('bio', $profile?->bio) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Kota Domisili</label>
                <select name="city" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#4F46E5] text-sm bg-white">
                    <option value="">Pilih Kota</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ old('city', $profile?->city) == $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>
           
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
            <a href="{{ route('profile.show') }}" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-[#4F46E5] rounded-xl hover:bg-indigo-700 shadow-md">Simpan Perubahan</button>
        </div>
    </form>
</div>
</x-layouts.crew>