<x-layouts.crew title="Pengaturan Akun - VOLA">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ openModal: null }">
    
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Pengaturan Akun</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola Keamanan Akun Anda.</p>
    </div>

    @if(session('status'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="text-sm font-medium text-emerald-800">{{ session('status') }}</p>
        </div>
    @endif

    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm mb-6">

        <div class="space-y-6">
            <div class="flex items-center justify-between pb-6 border-b border-gray-100">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Alamat Email</p>
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-bold text-gray-900">{{ $user->email }}</p>
                        @if($user->hasVerifiedEmail())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Terverifikasi
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Email aktif digunakan untuk verifikasi akun crew.
                    </p>
                </div>
                <button @click="openModal = 'email'" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-50 transition">
                    Ubah Email
                </button>
            </div>

            <div class="flex items-center justify-between pb-6 border-b border-gray-100">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Nomor WhatsApp Aktif</p>
                    <p class="text-sm font-bold text-gray-900">{{ $user->phone ?? '+62 8xx-xxxx-xxxx' }}</p>
                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Nomor WhatsApp digunakan oleh EO untuk menghubungi Anda jika pendaftaran diterima.
                    </p>
                </div>
                <button @click="openModal = 'phone'" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-50 transition">
                    Ubah Nomor
                </button>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Kata Sandi</p>
                    <p class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        ••••••••••••
                        <span class="text-xs font-normal text-gray-500">
                            @php
                                $lastUpdated = \Carbon\Carbon::parse($user->updated_at)->locale('id')->diffForHumans();
                            @endphp
                            (Terakhir diubah {{ $lastUpdated }})
                        </span>                       
                    </p>
                </div>
                <button @click="openModal = 'password'" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-50 transition">
                    Ubah Kata Sandi
                </button>
            </div>
        </div>
    </div>

    <div x-show="openModal === 'email'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: none;">
        <div @click.outside="openModal = null" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 relative">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Ubah Alamat Email</h3>
                <button @click="openModal = null" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
         
            <form action="{{ route('settings.update.email') }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Baru</label>
                    <input type="email" name="email" value="{{ $user->email }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent text-sm">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="openModal = null" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#4F46E5] rounded-lg hover:bg-indigo-700">Simpan Email</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openModal === 'phone'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: none;">
        <div @click.outside="openModal = null" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 relative">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Ubah Nomor WhatsApp</h3>
                <button @click="openModal = null" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
          
            <form action="{{ route('settings.update.phone') }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <input type="text" name="phone" value="{{ $user->phone }}" required placeholder="+62 812..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent text-sm">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="openModal = null" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#4F46E5] rounded-lg hover:bg-indigo-700">Simpan Nomor</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openModal === 'password'" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: none;">
        <div @click.outside="openModal = null" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 relative">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Ubah Kata Sandi</h3>
                <button @click="openModal = null" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
           
            <form action="{{ route('settings.update.password') }}" method="POST">
                @csrf @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent text-sm">
                        @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent text-sm">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent text-sm">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="openModal = null" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#4F46E5] rounded-lg hover:bg-indigo-700">Perbarui Sandi</button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.crew>