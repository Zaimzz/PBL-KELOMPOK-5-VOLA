<x-guest-layout>
    <div class="max-w-md w-full mx-auto space-y-8 p-10 bg-white rounded-xl shadow-lg z-10">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Satu Langkah Lagi</h2>
            <p class="mt-2 text-sm text-gray-600">Pilih peran kamu untuk melanjutkan pendaftaran dengan Google.</p>
        </div>

        <form method="POST" action="{{ route('auth.google.chooseRole') }}" class="space-y-6" x-data="{ role: 'volunteer' }">
            @csrf

            <!-- Role Selector -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Daftar Sebagai</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="volunteer" x-model="role" class="peer sr-only">
                        <div class="text-center px-4 py-2 border rounded-lg text-sm font-medium transition-colors peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 hover:bg-gray-50 text-gray-700">
                            Crew
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="eo" x-model="role" class="peer sr-only">
                        <div class="text-center px-4 py-2 border rounded-lg text-sm font-medium transition-colors peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 hover:bg-gray-50 text-gray-700">
                            Event Organizer
                        </div>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span x-text="role === 'volunteer' ? 'Selesaikan sebagai Crew &rarr;' : 'Selesaikan sebagai Event Organizer &rarr;'"></span>
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
