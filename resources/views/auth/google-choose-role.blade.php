<x-guest-layout>
    <div class="w-full bg-white/90 backdrop-blur-xl rounded-[28px] shadow-[0_20px_50px_rgba(69,52,230,0.08)] hover:shadow-[0_25px_60px_rgba(69,52,230,0.12)] p-6 sm:p-10 z-10 border border-white/80 transition-all duration-300 animate-card-entry">

        <div class="flex justify-center items-center mb-5 animate-stagger-item delay-50">
            <div class="flex items-center space-x-1.5">
                <span class="text-xl font-black tracking-tight text-gray-900">VOLA<span class="text-indigo-600">.</span></span>
            </div>
        </div>

        <div class="text-center mb-6 animate-stagger-item delay-100">
            <h2 class="text-2xl sm:text-[28px] font-bold text-gray-900 tracking-tight">Satu Langkah Lagi</h2>
            <p class="mt-1.5 text-sm text-gray-500 font-medium">Pilih peran kamu untuk menyelesaikan pendaftaran dengan Google.</p>
        </div>

        @if(session('google_user'))
            <div class="mb-6 p-3.5 rounded-2xl bg-indigo-50/70 border border-indigo-100 flex items-center gap-3 animate-stagger-item delay-100">
                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                    {{ strtoupper(substr(session('google_user.name', 'G'), 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-gray-900 truncate">{{ session('google_user.name') }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ session('google_user.email') }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('auth.google.chooseRole') }}" class="space-y-6" x-data="{
            role: @js(old('role', 'volunteer')),
            isSubmitting: false,
            errors: {
                role: @js($errors->first('role'))
            },
            handleGoogleRoleSubmit(event) {
                if (this.isSubmitting) {
                    event.preventDefault();
                    return;
                }
                this.isSubmitting = true;
                sessionStorage.setItem('vola_session_active', 'true');
                document.cookie = 'vola_session_active=1; path=/; SameSite=Lax';
            }
        }" @submit="handleGoogleRoleSubmit($event)" novalidate>
            @csrf

            <!-- Role Selector with Sliding Indicator -->
            <div class="animate-stagger-item delay-150">
                <label class="block text-[11px] font-bold text-gray-400 tracking-widest uppercase mb-2">Daftar Sebagai</label>
                <div class="relative bg-gray-100/80 p-1.5 rounded-2xl flex items-center border border-gray-200/60 shadow-inner">
                    <!-- Sliding Background Indicator Pill -->
                    <div class="absolute top-1.5 bottom-1.5 rounded-xl bg-white shadow-sm border border-black/5 transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"
                         :style="role === 'eo' ? 'left: calc(50% + 2px); width: calc(50% - 8px);' : 'left: 6px; width: calc(50% - 8px);'"
                         aria-hidden="true">
                    </div>

                    <!-- Crew -->
                    <label class="relative z-10 flex-1 cursor-pointer select-none">
                        <input type="radio" name="role" value="volunteer" x-model="role" @change="errors.role = ''" class="sr-only" {{ old('role', 'volunteer') === 'volunteer' ? 'checked' : '' }}>
                        <div class="flex items-center justify-center py-2.5 px-2 sm:px-3 rounded-xl transition-all duration-200"
                             :class="role === 'volunteer' ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-800 font-medium'">
                            <svg class="w-4 h-4 mr-1.5 sm:mr-2 shrink-0 transition-transform duration-200" :class="role === 'volunteer' ? 'scale-110 text-indigo-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-xs">Crew</span>
                        </div>
                    </label>

                    <!-- EO -->
                    <label class="relative z-10 flex-1 cursor-pointer select-none">
                        <input type="radio" name="role" value="eo" x-model="role" @change="errors.role = ''" class="sr-only" {{ old('role') === 'eo' ? 'checked' : '' }}>
                        <div class="flex items-center justify-center py-2.5 px-2 sm:px-3 rounded-xl transition-all duration-200"
                             :class="role === 'eo' ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-800 font-medium'">
                            <svg class="w-4 h-4 mr-1.5 sm:mr-2 shrink-0 transition-transform duration-200" :class="role === 'eo' ? 'scale-110 text-indigo-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                            <span class="text-xs truncate">Event Organizer</span>
                        </div>
                    </label>
                </div>
                <p x-show="errors.role" x-text="errors.role" class="mt-2 text-[13px] text-red-600 font-medium" x-cloak></p>
            </div>

            <div class="pt-1 animate-stagger-item delay-200">
                <button type="submit"
                        :disabled="isSubmitting"
                        class="group relative w-full flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-[#4534E6] hover:bg-[#3b2bc7] hover:shadow-[0_10px_25px_rgba(69,52,230,0.35)] hover:-translate-y-0.5 active:translate-y-0 active:scale-95 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all duration-200 disabled:opacity-75 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">
                    <span x-show="!isSubmitting" class="flex items-center justify-center transition-opacity duration-150">
                        <span x-text="role === 'volunteer' ? 'Selesaikan sebagai Crew' : 'Selesaikan sebagai Event Organizer'"></span>
                        <svg class="ml-2 w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </span>
                    <span x-cloak x-show="isSubmitting" class="flex items-center justify-center transition-opacity duration-150">
                        <svg class="animate-spin -ml-1 mr-2.5 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Memproses...</span>
                    </span>
                </button>
            </div>

            <div class="text-center text-sm text-gray-500 font-medium animate-stagger-item delay-250">
                <a href="{{ route('login') }}" class="font-bold text-[#4534E6] hover:text-indigo-800 hover:underline transition-colors">Batal dan kembali ke Masuk</a>
            </div>
        </form>
    </div>
</x-guest-layout>
