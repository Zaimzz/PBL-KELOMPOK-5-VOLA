<x-guest-layout>
    <div class="w-full bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 sm:p-10 z-10 border border-gray-100">
        
        <div class="flex justify-center items-center mb-6">
            <div class="flex items-center space-x-1.5">
                <span class="text-xl font-black tracking-tight text-gray-900">VOLA<span class="text-indigo-600">.</span></span>
            </div>
        </div>

        <div class="text-center mb-8">
            <h2 class="text-[28px] font-bold text-gray-900 tracking-tight">Portal Administrator</h2>
            <p class="mt-2 text-sm text-gray-500 font-medium">Gunakan kredensial Anda untuk mengakses panel admin.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-5" x-data="{ 
            email: '{{ old('email') }}',
            password: '',
            showPassword: false,
            errors: {
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

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" :class="errors.email ? 'text-red-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" class="block w-full pl-11 pr-4 py-3 bg-[#F8F9FA] rounded-xl text-sm transition-all placeholder-gray-400 font-medium" :class="errors.email ? 'border-red-500 ring-1 ring-red-500 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200' : 'border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200'" x-model="email" @input="errors.email = ''" placeholder="Masukkan email administrator">
                </div>
                <p x-show="errors.email" x-text="errors.email" class="mt-2 text-[13px] text-red-600 font-medium" x-cloak></p>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" :class="errors.password ? 'text-red-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input id="password" name="password" x-model="password" @input="errors.password = ''" x-bind:type="showPassword ? 'text' : 'password'" autocomplete="current-password" class="block w-full pl-11 pr-12 py-3 bg-[#F8F9FA] rounded-xl text-sm transition-all placeholder-gray-400 font-medium" :class="errors.password ? 'border-red-500 ring-1 ring-red-500 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200' : 'border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200'" placeholder="Masukkan kata sandi">
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                        <!-- Eye-off -->
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                            <line x1="2" x2="22" y1="2" y2="22"/>
                        </svg>
                        <!-- Eye -->
                        <svg x-cloak x-show="showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <p x-show="errors.password" x-text="errors.password" class="mt-2 text-[13px] text-red-600 font-medium" x-cloak></p>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between pt-1">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-[#4534E6] focus:ring-[#4534E6] border-gray-300 rounded cursor-pointer">
                    <label for="remember_me" class="ml-2 block text-sm font-medium text-gray-600 cursor-pointer">Ingat saya</label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-semibold text-[#4534E6] hover:text-indigo-800 transition-colors">Lupa kata sandi?</a>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#4534E6] hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Masuk ke Dasbor 
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
            
            <div class="text-center mt-6 text-[11px] text-gray-400 leading-relaxed px-4">
                Area Terbatas. Hanya untuk administrator internal VOLA.
            </div>
        </form>
    </div>
</x-guest-layout>
