<x-guest-layout>
    <div class="w-full bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 sm:p-10 z-10 border border-gray-100">
        
        <div class="flex justify-center mb-6">
            <div class="flex items-center justify-center w-14 h-14 bg-indigo-50 rounded-full text-[#4534E6]">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <!-- Circular Arrow -->
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v5h5" />
                    <!-- Inner Lock -->
                    <rect x="8.5" y="12" width="7" height="5" rx="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.25 12V9.5a1.75 1.75 0 0 1 3.5 0V12" />
                </svg>
            </div>
        </div>

        <div class="text-center mb-8">
            <h2 class="text-[28px] font-bold text-gray-900 tracking-tight">Lupa Kata Sandi?</h2>
            <p class="mt-3 text-sm text-gray-500 font-medium px-4">Masukkan email terdaftar dan buat kata sandi baru untuk akunmu.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-xl text-sm text-center mb-6 font-medium" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6" x-data="{ 
            role: '{{ old('role', 'volunteer') }}',
            email: '{{ old('email') }}',
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
            }
        }" @submit="if(!validate()) $event.preventDefault()" novalidate>
            @csrf

            <!-- Role Selector -->
            <div>
                <label class="block text-[11px] font-bold text-gray-400 tracking-widest uppercase mb-3 text-center">Reset Sebagai</label>
                <div class="grid grid-cols-2 gap-3">
                    <!-- Crew -->
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="volunteer" x-model="role" class="peer sr-only">
                        <div class="flex items-center justify-center p-3 border-2 border-transparent rounded-xl transition-all duration-200 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-100 hover:bg-gray-50 text-gray-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="text-xs font-semibold">Crew</span>
                        </div>
                    </label>
                    <!-- EO -->
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="eo" x-model="role" class="peer sr-only">
                        <div class="flex items-center justify-center p-3 border-2 border-transparent rounded-xl transition-all duration-200 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 peer-checked:border-indigo-100 hover:bg-gray-50 text-gray-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            <span class="text-xs font-semibold">Event Organizer</span>
                        </div>
                    </label>
                </div>
                <p x-show="errors.role" x-text="errors.role" class="mt-2 text-sm text-red-600 font-medium" x-cloak></p>
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Terdaftar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" :class="errors.email ? 'text-red-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" class="block w-full pl-11 pr-4 py-3 bg-[#F8F9FA] rounded-xl text-sm transition-all placeholder-gray-400 font-medium" :class="errors.email ? 'border-red-500 ring-1 ring-red-500 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200' : 'border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200'" x-model="email" @input="errors.email = ''" placeholder="Masukkan email yang terdaftar">
                </div>
                <p class="mt-2 text-[11px] text-gray-500 font-medium" x-show="!errors.email">Email digunakan untuk menemukan akun yang terdaftar.</p>
                <p x-show="errors.email" x-text="errors.email" class="mt-2 text-[13px] text-red-600 font-medium" x-cloak></p>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#4534E6] hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Kirim Tautan Reset 
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
            
            <div class="text-center mt-6 text-sm text-gray-500 font-medium">
                Sudah ingat kata sandi? <a href="{{ route('login') }}" class="font-bold text-[#4534E6] hover:text-indigo-800 transition-colors">Kembali ke Masuk</a>
            </div>
        </form>
    </div>
</x-guest-layout>
