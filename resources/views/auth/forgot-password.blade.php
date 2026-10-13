<x-guest-layout>
    <div class="w-full bg-white/90 backdrop-blur-xl rounded-[28px] shadow-[0_20px_50px_rgba(69,52,230,0.08)] hover:shadow-[0_25px_60px_rgba(69,52,230,0.12)] p-7 sm:p-10 z-10 border border-white/80 transition-all duration-300 animate-card-entry">
        
        <div class="flex justify-center mb-5 animate-stagger-item delay-50">
            <div class="flex items-center justify-center w-14 h-14 bg-indigo-50/90 rounded-2xl text-[#4534E6] shadow-sm border border-indigo-100/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v5h5" />
                    <rect x="8.5" y="12" width="7" height="5" rx="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.25 12V9.5a1.75 1.75 0 0 1 3.5 0V12" />
                </svg>
            </div>
        </div>

        <div class="text-center mb-7 animate-stagger-item delay-100">
            <h2 class="text-[26px] sm:text-[28px] font-bold text-gray-900 tracking-tight">Lupa Kata Sandi?</h2>
            <p class="mt-1.5 text-sm text-gray-500 font-medium px-4">Masukkan email terdaftar dan buat kata sandi baru untuk akunmu.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-xl text-sm text-center mb-6 font-medium animate-stagger-item delay-100" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5" x-data="{ 
            role: '{{ old('role', 'volunteer') }}',
            email: '{{ old('email') }}',
            isSubmitting: false,
            errors: {
                role: '{{ $errors->first('role') }}',
                email: '{{ $errors->first('email') }}'
            },
            validate() {
                this.errors.email = '';
                
                let isValid = true;
                
                if (!this.email) {
                    this.errors.email = 'Email wajib diisi.';
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
                    this.errors.email = 'Format email tidak valid.';
                    isValid = false;
                }
                
                return isValid;
            },
            handleForgotSubmit(event) {
                if (!this.validate()) {
                    event.preventDefault();
                    return;
                }
                this.isSubmitting = true;
            }
        }" @submit="handleForgotSubmit($event)" novalidate>
            @csrf

            <!-- Role Selector with Sliding Indicator -->
            <div class="animate-stagger-item delay-150">
                <label class="block text-[11px] font-bold text-gray-400 tracking-widest uppercase mb-2 text-center">Reset Sebagai</label>
                <div class="relative bg-gray-100/80 p-1.5 rounded-2xl flex items-center border border-gray-200/60 shadow-inner">
                    <!-- Sliding Background Indicator Pill -->
                    <div class="absolute top-1.5 bottom-1.5 rounded-xl bg-white shadow-sm border border-black/5 transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"
                         :style="role === 'eo' ? 'left: calc(50% + 2px); width: calc(50% - 8px);' : 'left: 6px; width: calc(50% - 8px);'"
                         aria-hidden="true">
                    </div>

                    <!-- Crew -->
                    <label class="relative z-10 flex-1 cursor-pointer select-none">
                        <input type="radio" name="role" value="volunteer" x-model="role" class="sr-only" {{ old('role', 'volunteer') === 'volunteer' ? 'checked' : '' }}>
                        <div class="flex items-center justify-center py-2.5 px-3 rounded-xl transition-all duration-200"
                             :class="role === 'volunteer' ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-800 font-medium'">
                            <svg class="w-4 h-4 mr-2 transition-transform duration-200" :class="role === 'volunteer' ? 'scale-110 text-indigo-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-xs">Crew</span>
                        </div>
                    </label>

                    <!-- EO -->
                    <label class="relative z-10 flex-1 cursor-pointer select-none">
                        <input type="radio" name="role" value="eo" x-model="role" class="sr-only" {{ old('role') === 'eo' ? 'checked' : '' }}>
                        <div class="flex items-center justify-center py-2.5 px-3 rounded-xl transition-all duration-200"
                             :class="role === 'eo' ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-800 font-medium'">
                            <svg class="w-4 h-4 mr-2 transition-transform duration-200" :class="role === 'eo' ? 'scale-110 text-indigo-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                            <span class="text-xs whitespace-nowrap">Event Organizer</span>
                        </div>
                    </label>
                </div>
                <p x-show="errors.role" x-text="errors.role" class="mt-2 text-sm text-red-600 font-medium" x-cloak></p>
            </div>

            <!-- Email Address -->
            <div class="animate-stagger-item delay-200">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Terdaftar</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors duration-200"
                         :class="errors.email ? 'text-red-400' : 'text-gray-400 group-hover:text-gray-600'">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email" 
                           class="block w-full pl-11 pr-4 py-3 bg-[#F8F9FA]/90 rounded-xl text-sm font-medium placeholder-gray-400 transition-all duration-200 ease-out border border-gray-200/80 hover:border-indigo-300 hover:bg-white focus:bg-white focus:outline-none" 
                           :class="errors.email ? 'border-red-500 ring-2 ring-red-100 shadow-[0_0_0_1px_rgba(239,68,68,0.2)]' : 'focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 focus:shadow-[0_0_20px_rgba(99,102,241,0.15)]'" 
                           x-model="email" 
                           @input="errors.email = ''" 
                           :placeholder="role === 'eo' ? 'Masukkan email organisasi' : 'Masukkan email terdaftar'"
                           placeholder="Masukkan email yang terdaftar">
                </div>
                <p class="mt-2 text-[11px] text-gray-500 font-medium" x-show="!errors.email">Email digunakan untuk menemukan akun yang terdaftar.</p>
                <p x-show="errors.email" x-text="errors.email" class="mt-2 text-[13px] text-red-600 font-medium" x-cloak></p>
            </div>

            <!-- Submit Button with Loading State -->
            <div class="pt-1 animate-stagger-item delay-250">
                <button type="submit" 
                        :disabled="isSubmitting"
                        class="group relative w-full flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-[#4534E6] hover:bg-[#3b2bc7] hover:shadow-[0_10px_25px_rgba(69,52,230,0.35)] hover:-translate-y-0.5 active:translate-y-0 active:scale-95 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all duration-200 disabled:opacity-75 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">
                    <span x-show="!isSubmitting" class="flex items-center justify-center transition-opacity duration-150">
                        <span>Kirim Tautan Reset</span>
                        <svg class="ml-2 w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </span>
                    <span x-cloak x-show="isSubmitting" class="flex items-center justify-center transition-opacity duration-150">
                        <svg class="animate-spin -ml-1 mr-2.5 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mengirimkan...</span>
                    </span>
                </button>
            </div>
            
            <div class="text-center mt-5 text-sm text-gray-500 font-medium animate-stagger-item delay-300">
                Sudah ingat kata sandi? <a href="{{ route('login') }}" class="font-bold text-[#4534E6] hover:text-indigo-800 hover:underline transition-colors">Kembali ke Masuk</a>
            </div>
        </form>
    </div>
</x-guest-layout>
