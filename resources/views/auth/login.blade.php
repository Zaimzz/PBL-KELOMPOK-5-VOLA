<x-guest-layout>
    <div class="w-full bg-white/90 backdrop-blur-xl rounded-[28px] shadow-[0_20px_50px_rgba(69,52,230,0.08)] hover:shadow-[0_25px_60px_rgba(69,52,230,0.12)] p-7 sm:p-10 z-10 border border-white/80 transition-all duration-300 animate-card-entry">

        <div class="flex justify-center items-center mb-5 animate-stagger-item delay-50">
            <div class="flex items-center space-x-1.5">
                <span class="text-xl font-black tracking-tight text-gray-900">VOLA<span class="text-indigo-600">.</span></span>
            </div>
        </div>

        <div class="text-center mb-7 animate-stagger-item delay-100">
            <h2 class="text-[26px] sm:text-[28px] font-bold text-gray-900 tracking-tight">Selamat Datang Kembali</h2>
            <p class="mt-1.5 text-sm text-gray-500 font-medium">Masuk untuk melanjutkan aktivitasmu di VOLA.</p>
        </div>

        <x-auth-session-status class="mb-4 animate-stagger-item delay-100" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{
            role: '{{ old('role', 'volunteer') }}',
            email: '{{ old('email') }}',
            password: '',
            remember: {{ old('remember') ? 'true' : 'false' }},
            showPassword: false,
            isSubmitting: false,
            errors: {
                role: '{{ $errors->first('role') }}',
                email: '{{ $errors->first('email') }}',
                password: '{{ $errors->first('password') }}'
            },
            init() {
                const storedRemember = localStorage.getItem('vola_remember') === 'true';
                if (!{{ old('remember') ? 'true' : 'false' }} && storedRemember) {
                    this.remember = true;
                    if (!this.email && localStorage.getItem('vola_remember_email')) {
                        this.email = localStorage.getItem('vola_remember_email');
                    }
                }
            },
            validate() {
                this.errors.email = '';
                this.errors.password = '';

                let isValid = true;

                if (!this.email) {
                    this.errors.email = 'Email wajib diisi.';
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
                    this.errors.email = 'Format email tidak valid.';
                    isValid = false;
                }

                if (!this.password) {
                    this.errors.password = 'Kata sandi wajib diisi.';
                    isValid = false;
                }

                return isValid;
            },
            handleLoginSubmit(event) {
                if (!this.validate()) {
                    event.preventDefault();
                    return;
                }

                this.isSubmitting = true;

                if (this.remember) {
                    localStorage.setItem('vola_remember', 'true');
                    if (this.email) {
                        localStorage.setItem('vola_remember_email', this.email);
                    }
                    sessionStorage.removeItem('vola_session_active');
                    document.cookie = 'vola_remember=1; path=/; max-age=157680000; SameSite=Lax';
                    document.cookie = 'vola_session_active=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax';
                } else {
                    localStorage.removeItem('vola_remember');
                    localStorage.removeItem('vola_remember_email');
                    sessionStorage.setItem('vola_session_active', 'true');
                    document.cookie = 'vola_session_active=1; path=/; SameSite=Lax';
                    document.cookie = 'vola_remember=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax';
                }
            }
        }" @submit="handleLoginSubmit($event)" novalidate>
            @csrf

            <!-- Role Selector with Sliding Indicator -->
            <div class="animate-stagger-item delay-150">
                <label class="block text-[11px] font-bold text-gray-400 tracking-widest uppercase mb-2">Masuk Sebagai</label>
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
            </div>

            <!-- Email Address -->
            <div class="animate-stagger-item delay-200">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
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
                           :placeholder="role === 'eo' ? 'Masukkan email organisasi' : 'Masukkan email kamu'"
                           placeholder="Masukkan email kamu">
                </div>
                <p x-show="errors.email" x-text="errors.email" class="mt-2 text-[13px] text-red-600 font-medium" x-cloak></p>
            </div>

            <!-- Password -->
            <div class="animate-stagger-item delay-250">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors duration-200"
                         :class="errors.password ? 'text-red-400' : 'text-gray-400 group-hover:text-gray-600'">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input id="password" 
                           name="password" 
                           x-model="password" 
                           @input="errors.password = ''" 
                           x-bind:type="showPassword ? 'text' : 'password'" 
                           autocomplete="current-password" 
                           class="block w-full pl-11 pr-12 py-3 bg-[#F8F9FA]/90 rounded-xl text-sm font-medium placeholder-gray-400 transition-all duration-200 ease-out border border-gray-200/80 hover:border-indigo-300 hover:bg-white focus:bg-white focus:outline-none" 
                           :class="errors.password ? 'border-red-500 ring-2 ring-red-100 shadow-[0_0_0_1px_rgba(239,68,68,0.2)]' : 'focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 focus:shadow-[0_0_20px_rgba(99,102,241,0.15)]'" 
                           placeholder="Masukkan kata sandi">
                    <button type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-indigo-600 transition-colors duration-200"
                            aria-label="Toggle password visibility">
                        <!-- Eye-off (Password Hidden) -->
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                            <line x1="2" x2="22" y1="2" y2="22"/>
                        </svg>
                        <!-- Eye (Password Visible) -->
                        <svg x-cloak x-show="showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <p x-show="errors.password" x-text="errors.password" class="mt-2 text-[13px] text-red-600 font-medium" x-cloak></p>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between pt-1 animate-stagger-item delay-300">
                <div class="flex items-center">
                    <input id="remember_me" 
                           name="remember" 
                           type="checkbox" 
                           value="1" 
                           x-model="remember" 
                           class="h-4 w-4 text-[#4534E6] focus:ring-[#4534E6] border-gray-300 rounded cursor-pointer transition-transform duration-150 active:scale-95">
                    <label for="remember_me" class="ml-2 block text-sm font-medium text-gray-600 cursor-pointer select-none hover:text-gray-900 transition-colors">Ingat saya</label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-semibold text-[#4534E6] hover:text-indigo-800 hover:underline transition-colors">Lupa kata sandi?</a>
                </div>
            </div>

            <!-- Submit Button with Loading State -->
            <div class="pt-1 animate-stagger-item delay-350">
                <button type="submit" 
                        :disabled="isSubmitting"
                        class="group relative w-full flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-[#4534E6] hover:bg-[#3b2bc7] hover:shadow-[0_10px_25px_rgba(69,52,230,0.35)] hover:-translate-y-0.5 active:translate-y-0 active:scale-95 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all duration-200 disabled:opacity-75 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">
                    <span x-show="!isSubmitting" class="flex items-center justify-center transition-opacity duration-150">
                        <span>Masuk ke Beranda</span>
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

            <!-- Divider -->
            <div class="mt-5 relative animate-stagger-item delay-400">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200/80"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-white/80 backdrop-blur-sm text-gray-400 font-semibold tracking-widest uppercase">Atau</span>
                </div>
            </div>

            <!-- Google Login & Footer -->
            <div class="space-y-5 animate-stagger-item delay-450">
                <div>
                    <a href="{{ route('auth.google.redirect') }}" class="w-full inline-flex justify-center items-center py-3.5 px-4 rounded-xl bg-[#F8F9FA]/90 hover:bg-white text-sm font-semibold text-gray-700 border border-gray-200/70 hover:border-gray-300 hover:shadow-sm active:scale-95 transition-all duration-200">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.16v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.16C1.43 8.55 1 10.22 1 12s.43 3.45 1.16 4.93l2.85-2.22.83-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.16 7.07l3.68 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Masuk dengan Akun Google
                    </a>
                </div>

                <div class="text-center text-sm text-gray-500 font-medium">
                    Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-[#4534E6] hover:text-indigo-800 hover:underline transition-colors">Daftar sekarang</a>
                </div>

                <div class="text-center text-[11px] text-gray-400 leading-relaxed px-4">
                    Dengan masuk, kamu menyetujui <a href="#" class="font-semibold text-gray-600 hover:text-gray-900 underline transition-colors">Ketentuan Penggunaan</a> dan <a href="#" class="font-semibold text-gray-600 hover:text-gray-900 underline transition-colors">Kebijakan Privasi</a> VOLA.
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>