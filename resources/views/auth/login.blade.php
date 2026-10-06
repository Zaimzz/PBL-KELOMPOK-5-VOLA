<x-guest-layout>
    <div class="w-full bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 sm:p-10 z-10 border border-gray-100">

        <div class="flex justify-center items-center mb-6">
            <div class="flex items-center space-x-1.5">
                <span class="text-xl font-black tracking-tight text-gray-900">VOLA<span class="text-indigo-600">.</span></span>
            </div>
        </div>

        <div class="text-center mb-8">
            <h2 class="text-[28px] font-bold text-gray-900 tracking-tight">Selamat Datang Kembali</h2>
            <p class="mt-2 text-sm text-gray-500 font-medium">Masuk untuk melanjutkan aktivitasmu di VOLA.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-7" x-data="{
            role: '{{ $errors->has('role') ? '' : old('role') }}',
            email: '{{ old('email') }}',
            password: '',
            showPassword: false,
            errors: {
                role: '{{ $errors->first('role') }}',
                email: '{{ $errors->first('email') }}',
                password: '{{ $errors->first('password') }}'
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
            }
        }" @submit="if(!validate()) $event.preventDefault()" novalidate>
            @csrf

            <!-- Role Selector -->
            <div>
                <label class="block text-[11px] font-bold text-gray-400 tracking-widest uppercase mb-4">Masuk Sebagai</label>
                <div class="grid grid-cols-2 gap-3">
                    <!-- Crew -->
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="volunteer" x-model="role" class="peer sr-only">
                        <div class="flex flex-col items-center justify-center p-3 border-2 border-transparent rounded-xl transition-all duration-200 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-100 hover:bg-gray-50 text-gray-500">
                            <svg class="w-5 h-5 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="text-xs font-semibold">Crew</span>
                        </div>
                    </label>
                    <!-- EO -->
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="eo" x-model="role" class="peer sr-only">
                        <div class="flex flex-col items-center justify-center p-3 border-2 border-transparent rounded-xl transition-all duration-200 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-100 hover:bg-gray-50 text-gray-500">
                            <svg class="w-5 h-5 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            <span class="text-xs font-semibold whitespace-nowrap">Event Organizer</span>
                        </div>
                    </label>
                </div>

            <!-- Email Address -->
            <div style="margin-top: 1rem;">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" :class="errors.email ? 'text-red-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" class="block w-full pl-11 pr-4 py-3 bg-[#F8F9FA] rounded-xl text-sm transition-all placeholder-gray-400 font-medium" :class="errors.email ? 'border-red-500 ring-1 ring-red-500 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200' : 'border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200'" x-model="email" @input="errors.email = ''" placeholder="Masukkan email kamu">
                </div>
                <p x-show="errors.email" x-text="errors.email" class="mt-2 text-[13px] text-red-600 font-medium" x-cloak></p>
            </div>

            <!-- Password -->
            <div style="margin-top: 1rem;">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" :class="errors.password ? 'text-red-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input id="password" name="password" x-model="password" @input="errors.password = ''" x-bind:type="showPassword ? 'text' : 'password'" autocomplete="current-password" class="block w-full pl-11 pr-12 py-3 bg-[#F8F9FA] rounded-xl text-sm transition-all placeholder-gray-400 font-medium" :class="errors.password ? 'border-red-500 ring-1 ring-red-500 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200' : 'border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200'" placeholder="Masukkan kata sandi">
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
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
            <div class="flex items-center justify-between pt-2">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-[#4534E6] focus:ring-[#4534E6] border-gray-300 rounded cursor-pointer">
                    <label for="remember_me" class="ml-2 block text-sm font-medium text-gray-600 cursor-pointer">Ingat saya</label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-semibold text-[#4534E6] hover:text-indigo-800 transition-colors">Lupa kata sandi?</a>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center py-4 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#4534E6] hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Masuk ke Beranda
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>

            <div class="mt-6 relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-white text-gray-400 font-semibold tracking-widest uppercase">Atau</span>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('auth.google.redirect') }}" class="w-full inline-flex justify-center items-center py-3.5 px-4 rounded-xl bg-[#F8F9FA] text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.16v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.16C1.43 8.55 1 10.22 1 12s.43 3.45 1.16 4.93l2.85-2.22.83-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.16 7.07l3.68 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Masuk dengan Akun Google
                </a>
            </div>

            <div class="text-center mt-6 text-sm text-gray-500 font-medium">
                Belum punya akun volunteer? <a href="{{ route('register') }}" class="font-bold text-[#4534E6] hover:text-indigo-800 transition-colors">Daftar sekarang</a>
            </div>

            <div class="text-center mt-6 text-[11px] text-gray-400 leading-relaxed px-4">
                Dengan mendaftar, kamu menyetujui <a href="#" class="font-semibold text-gray-600 hover:text-gray-900">Ketentuan Penggunaan</a> dan <a href="#" class="font-semibold text-gray-600 hover:text-gray-900">Kebijakan Privasi</a> VOLA.
            </div>
        </form>
    </div>
</x-guest-layout>