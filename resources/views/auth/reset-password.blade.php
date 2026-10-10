<x-guest-layout>
    <div class="w-full bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 sm:p-10 z-10 border border-gray-100">
        <div class="text-center mb-8">
            <h2 class="text-[28px] font-bold text-gray-900 tracking-tight">Atur Ulang Kata Sandi</h2>
            <p class="mt-3 text-sm text-gray-500 font-medium px-4">Masukkan email dan kata sandi baru Anda.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-6" x-data="{
            email: '{{ old('email', $request->email) }}',
            password: '',
            password_confirmation: '',
            showPassword: false, 
            showConfirmPassword: false,
            errors: {
                email: '{{ $errors->first('email') }}',
                password: '{{ $errors->first('password') }}',
                password_confirmation: '{{ $errors->first('password_confirmation') }}'
            },
            get passwordValidLength() { return this.password.length >= 8; },
            get passwordValidCombination() { return /^(?=.*[A-Za-z])(?=.*\d).+$/.test(this.password); },
            validate() {
                this.errors.email = '';
                this.errors.password = '';
                this.errors.password_confirmation = '';
                
                let isValid = true;
                
                if (!this.email) {
                    this.errors.email = 'Email wajib diisi.';
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
                    this.errors.email = 'Format email tidak valid.';
                    isValid = false;
                }
                
                if (!this.password) {
                    this.errors.password = 'Kata sandi baru wajib diisi.';
                    isValid = false;
                } else if (!this.passwordValidLength || !this.passwordValidCombination) {
                    this.errors.password = 'Kata sandi harus minimal 8 karakter dan mengandung huruf serta angka.';
                    isValid = false;
                }
                
                if (!this.password_confirmation) {
                    this.errors.password_confirmation = 'Konfirmasi kata sandi wajib diisi.';
                    isValid = false;
                } else if (this.password !== this.password_confirmation) {
                    this.errors.password_confirmation = 'Konfirmasi kata sandi tidak sesuai.';
                    isValid = false;
                }
                
                return isValid;
            }
        }" @submit="if(!validate()) $event.preventDefault()" novalidate>
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Terdaftar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" :class="errors.email ? 'text-red-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" class="block w-full pl-11 pr-4 py-3 bg-[#F8F9FA] rounded-xl text-sm transition-all placeholder-gray-400 font-medium" :class="errors.email ? 'border-red-500 ring-1 ring-red-500 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200' : 'border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200'" x-model="email" @input="errors.email = ''" placeholder="Masukkan email yang terdaftar" readonly>
                </div>
                <p x-show="errors.email" x-text="errors.email" class="mt-2 text-[13px] text-red-600 font-medium" x-cloak></p>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi Baru</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" :class="errors.password ? 'text-red-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input id="password" name="password" x-model="password" @input="errors.password = ''" x-bind:type="showPassword ? 'text' : 'password'" class="block w-full pl-11 pr-12 py-3 bg-[#F8F9FA] rounded-xl text-sm transition-all placeholder-gray-400 font-medium" :class="errors.password ? 'border-red-500 ring-1 ring-red-500 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200' : 'border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200'" placeholder="Masukkan kata sandi baru">
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
                
                <!-- Password Validation Indicators -->
                <div class="mt-2 text-[11px] font-medium space-y-1.5" x-cloak x-show="!errors.password">
                    <p class="flex items-center" :class="passwordValidLength ? 'text-green-600' : 'text-gray-500'">
                        <!-- Check icon -->
                        <svg x-show="passwordValidLength" class="h-4 w-4 mr-1.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <!-- Cross icon -->
                        <svg x-show="!passwordValidLength" class="h-4 w-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        Minimal 8 karakter
                    </p>
                    <p class="flex items-center" :class="passwordValidCombination ? 'text-green-600' : 'text-gray-500'">
                        <!-- Check icon -->
                        <svg x-show="passwordValidCombination" class="h-4 w-4 mr-1.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <!-- Cross icon -->
                        <svg x-show="!passwordValidCombination" class="h-4 w-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        Mengandung huruf dan angka
                    </p>
                </div>
                <p x-show="errors.password" x-text="errors.password" class="mt-2 text-[13px] text-red-600 font-medium" x-cloak></p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" :class="errors.password_confirmation ? 'text-red-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <input id="password_confirmation" name="password_confirmation" x-model="password_confirmation" @input="errors.password_confirmation = ''" x-bind:type="showConfirmPassword ? 'text' : 'password'" class="block w-full pl-11 pr-12 py-3 bg-[#F8F9FA] rounded-xl text-sm transition-all placeholder-gray-400 font-medium" :class="errors.password_confirmation ? 'border-red-500 ring-1 ring-red-500 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200' : 'border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200'" placeholder="Masukkan kembali kata sandi baru">
                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                        <!-- Eye-off (Password Hidden) -->
                        <svg x-show="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                            <line x1="2" x2="22" y1="2" y2="22"/>
                        </svg>
                        <!-- Eye (Password Visible) -->
                        <svg x-cloak x-show="showConfirmPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <p x-show="errors.password_confirmation" x-text="errors.password_confirmation" class="mt-2 text-[13px] text-red-600 font-medium" x-cloak></p>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#4534E6] hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Ubah Kata Sandi 
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
