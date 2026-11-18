<x-layouts.admin title="Manajemen Pengguna">

    <!-- Header & Breadcrumbs Section -->
    <div class="mb-6 relative z-10 opacity-100 w-full max-w-full min-w-0">
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 w-full">
            <!-- Sisi Kiri: Badge, Judul & Deskripsi -->
            <div class="max-w-md">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border mb-2.5 bg-sky-50 text-sky-700 border-sky-200/70">
                    <span class="relative flex h-2 w-2 mr-0.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-sky-600"></span>
                    </span>
                    <span>DIREKTORI AKUN • MODERASI AKSES</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen <span class="text-amber-500">Pengguna</span></h1>
                <p class="text-slate-600 text-sm mt-1 leading-relaxed">Kelola data seluruh crew dan penyelenggara event, pantau status akun, dan moderasi akses platform.</p>
            </div>

            <!-- Sisi Kanan: 4 Luxury Cards Memanjang 1 Baris -->
            <div id="admin-metrics-container" class="flex flex-wrap items-center gap-3 w-full xl:w-auto shrink-0">
                <x-metric-card label="Total Pengguna" :value="number_format($stats['total_users'], 0, ',', '.')" bg="bg-indigo-50" text="text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </x-metric-card>

                <x-metric-card label="Crew Aktif" :value="number_format($stats['active_volunteers'], 0, ',', '.')" bg="bg-blue-50" text="text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </x-metric-card>

                <x-metric-card label="Event Organizer" :value="number_format($stats['total_eos'], 0, ',', '.')" bg="bg-emerald-50" text="text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </x-metric-card>

                <x-metric-card label="Ditangguhkan" :value="number_format($stats['suspended_users'], 0, ',', '.')" bg="bg-rose-50" text="text-rose-600" valueClass="text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </x-metric-card>
            </div>
        </div>
    </div>

    <!-- Main Container with Alpine.js State -->
    <div id="admin-content-container" x-data="userManager()" class="w-full max-w-full min-w-0 space-y-6">

        <!-- Search & Filter Card -->
        <x-card class="p-3">
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3">
                <!-- Search Box -->
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama pengguna, email, kota, atau institusi"
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                </div>

                <!-- Dropdown Peran -->
                <div x-data="{ 
                        open: false, 
                        selected: '{{ request('role', 'all') }}',
                        options: {
                            'all': 'Semua Peran',
                            'volunteer': 'Crew',
                            'eo': 'Event Organizer'
                        },
                        select(val) {
                            this.selected = val;
                            this.open = false;
                            $nextTick(() => { window.adminSubmitForm(this.$refs.hiddenRole.form); });
                        }
                    }" 
                    class="relative w-full sm:w-48 shrink-0"
                    @click.away="open = false">
                    
                    <input type="hidden" name="role" :value="selected" x-ref="hiddenRole">

                    <button type="button" 
                            @click="open = !open" 
                            class="w-full flex items-center justify-between gap-3 px-4 py-2 bg-gray-50 hover:bg-gray-100/80 border border-gray-200 rounded-xl text-sm text-gray-700 font-medium focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-left">
                        <span class="truncate" x-text="options[selected] || 'Semua Peran'"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" 
                             :class="{ 'rotate-180 text-brand-600': open }" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                         class="absolute left-0 right-0 mt-1.5 py-1.5 bg-white border border-gray-100 rounded-xl shadow-xl z-50 overflow-hidden" 
                         style="display: none;" 
                         x-cloak>
                        <template x-for="(label, key) in options" :key="key">
                            <button type="button" 
                                    @click="select(key)"
                                    class="w-full flex items-center justify-between px-3.5 py-2 text-xs sm:text-sm text-left transition-colors"
                                    :class="selected === key ? 'bg-brand-50 text-brand-700 font-bold' : 'text-gray-700 hover:bg-gray-50'">
                                <span x-text="label"></span>
                                <svg x-show="selected === key" class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Dropdown Status -->
                <div x-data="{ 
                        open: false, 
                        selected: '{{ request('status', 'all') }}',
                        options: {
                            'all': 'Semua Status',
                            'active': 'Aktif',
                            'suspended': 'Ditangguhkan'
                        },
                        select(val) {
                            this.selected = val;
                            this.open = false;
                            $nextTick(() => { window.adminSubmitForm(this.$refs.hiddenStatus.form); });
                        }
                    }" 
                    class="relative w-full sm:w-48 shrink-0"
                    @click.away="open = false">
                    
                    <input type="hidden" name="status" :value="selected" x-ref="hiddenStatus">

                    <button type="button" 
                            @click="open = !open" 
                            class="w-full flex items-center justify-between gap-3 px-4 py-2 bg-gray-50 hover:bg-gray-100/80 border border-gray-200 rounded-xl text-sm text-gray-700 font-medium focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-left">
                        <span class="truncate" x-text="options[selected] || 'Semua Status'"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" 
                             :class="{ 'rotate-180 text-brand-600': open }" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                         class="absolute left-0 right-0 mt-1.5 py-1.5 bg-white border border-gray-100 rounded-xl shadow-xl z-50 overflow-hidden" 
                         style="display: none;" 
                         x-cloak>
                        <template x-for="(label, key) in options" :key="key">
                            <button type="button" 
                                    @click="select(key)"
                                    class="w-full flex items-center justify-between px-3.5 py-2 text-xs sm:text-sm text-left transition-colors"
                                    :class="selected === key ? 'bg-brand-50 text-brand-700 font-bold' : 'text-gray-700 hover:bg-gray-50'">
                                <span x-text="label"></span>
                                <svg x-show="selected === key" class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Reset Button -->
                @php
                    $hasActiveFilter = (request('search') && trim(request('search')) !== '')
                        || (request('role') && request('role') !== 'all')
                        || (request('status') && request('status') !== 'all');
                @endphp

                @if($hasActiveFilter)
                    <a href="{{ route('admin.users.index') }}" 
                       data-reset="true"
                       x-data="{ resetting: false }"
                       @click="resetting = true"
                       class="group inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-500 hover:text-gray-800 active:scale-95 transition-all shrink-0">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:-rotate-45 shrink-0"
                             :class="{ 'animate-spin text-brand-600': resetting }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Reset</span>
                    </a>
                @endif
            </form>
        </x-card>

        <!-- Flash Success Notification -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">&times;</button>
            </div>
        @endif

        <!-- Data Table -->
        <x-card padding="p-0" class="overflow-hidden shadow-sm border border-slate-200/90 w-full max-w-full min-w-0">
            <div class="overflow-x-auto w-full">
                <table class="table-fixed w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/75 border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                            <th class="py-3.5 px-4 w-[19%]">Nama / Pengguna</th>
                            <th class="py-3.5 px-2 w-[14%] text-center">Peran</th>
                            <th class="py-3.5 px-3 w-[20%]">Email & Kontak</th>
                            <th class="py-3.5 px-3 w-[16%]">Asal / Institusi</th>
                            <th class="py-3.5 px-2 w-[12%] text-center">Status Akun</th>
                            <th class="py-3.5 px-3 w-[19%] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($users as $user)
                            @php
                                $isVolunteer = $user->role === \App\Enums\UserRole::Volunteer;
                                $institution = $isVolunteer 
                                    ? ($user->volunteerProfile?->institution ?? '-') 
                                    : ($user->organizerProfile?->organization_name ?? '-');
                                $location = $isVolunteer 
                                    ? ($user->volunteerProfile?->city ?? '-') 
                                    : ($user->organizerProfile?->city ?? '-');
                                $isActive = $user->status === \App\Enums\UserStatus::Active;
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <!-- Nama / Pengguna -->
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-full {{ $isVolunteer ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' }} flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden shadow-xs">
                                            @if($user->avatar_path)
                                                <img src="{{ Storage::url($user->avatar_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-bold text-gray-900 text-sm leading-snug truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                                            <div class="text-[11px] text-gray-400 mt-0.5 truncate">Gabung {{ $user->created_at->translatedFormat('d M Y') }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Peran -->
                                <td class="py-4 px-2 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        @if($isVolunteer)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/70">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 shrink-0"></span>
                                                Crew
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-brand-700 border border-indigo-200/70">
                                                <span class="w-1.5 h-1.5 rounded-full bg-brand-600 shrink-0"></span>
                                                Event Organizer
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Email & Kontak -->
                                <td class="py-4 px-3">
                                    <div class="font-medium text-gray-900 text-xs truncate" title="{{ $user->email }}">{{ $user->email }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1 truncate">
                                        <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span class="truncate">{{ $user->phone ?: '-' }}</span>
                                    </div>
                                </td>

                                <!-- Asal / Institusi -->
                                <td class="py-4 px-3">
                                    <div class="font-semibold text-gray-800 text-xs truncate" title="{{ $institution }}">{{ $institution }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5 truncate">{{ $location }}</div>
                                </td>

                                <!-- Status Akun -->
                                <td class="py-4 px-2 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        @if($isActive)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/70">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600 shrink-0"></span>
                                                Ditangguhkan
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-3 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <!-- Tombol Detail Akun -->
                                        <button type="button"
                                                @click="openDetail({{ $user->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-brand-700 text-xs font-semibold rounded-xl transition-colors">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail
                                        </button>

                                        <!-- Tombol Suspend / Aktifkan -->
                                        @if($isActive)
                                            <button type="button"
                                                    @click="confirmStatusChange({{ $user->id }}, '{{ addslashes($user->name) }}', 'suspend')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-xl transition-colors">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                Suspend
                                            </button>
                                        @else
                                            <button type="button"
                                                    @click="confirmStatusChange({{ $user->id }}, '{{ addslashes($user->name) }}', 'activate')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-xl transition-colors">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Aktifkan
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="font-semibold text-gray-700">Tidak ada data pengguna yang ditemukan</p>
                                    <p class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau reset filter peran dan status.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="p-4 border-t border-slate-100">
                {{ $users->withQueryString()->links('components.pagination') }}
            </div>
        </x-card>

        <!-- Komponen Grafik: Distribusi Pertumbuhan Pengguna -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Distribusi Pertumbuhan Pengguna</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Statistik tren pendaftaran Crew dan Event Organizer dalam 6 bulan terakhir</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-md bg-blue-500"></span>
                        <span class="text-gray-600">Crew</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-md bg-indigo-600"></span>
                        <span class="text-gray-600">Event Organizer</span>
                    </div>
                </div>
            </div>

            <!-- Visual Bar Chart -->
            <div class="space-y-4">
                <div class="grid grid-cols-6 gap-2 sm:gap-4 items-end h-44 pt-6 pb-2 border-b border-gray-100">
                    @foreach($growthChart['labels'] as $index => $label)
                        @php
                            $volCount = $growthChart['volunteers'][$index];
                            $eoCount = $growthChart['organizers'][$index];
                            $totalCount = $growthChart['totals'][$index];
                            $maxVal = max($growthChart['max_value'], 1);
                            $heightPercent = round(($totalCount / $maxVal) * 100);
                            $volHeight = $totalCount > 0 ? round(($volCount / $totalCount) * 100) : 0;
                            $eoHeight = $totalCount > 0 ? (100 - $volHeight) : 0;
                        @endphp
                        <div class="flex flex-col items-center h-full justify-end group">
                            <!-- Tooltip count -->
                            <div class="text-[10px] font-bold text-gray-700 mb-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                {{ $totalCount }}
                            </div>
                            
                            <!-- Stacked bar -->
                            <div class="w-full max-w-[42px] bg-gray-100 rounded-t-lg overflow-hidden flex flex-col justify-end transition-all duration-300"
                                 style="height: {{ max($heightPercent, 6) }}%">
                                @if($eoCount > 0)
                                    <div class="bg-indigo-600 transition-all duration-300 hover:brightness-110" style="height: {{ $eoHeight }}%" title="EO: {{ $eoCount }}"></div>
                                @endif
                                @if($volCount > 0)
                                    <div class="bg-blue-500 transition-all duration-300 hover:brightness-110" style="height: {{ $volHeight }}%" title="Crew: {{ $volCount }}"></div>
                                @endif
                                @if($totalCount === 0)
                                    <div class="bg-gray-200 h-full w-full"></div>
                                @endif
                            </div>

                            <!-- Label Month -->
                            <div class="text-[11px] font-semibold text-gray-500 mt-2 truncate w-full text-center">
                                {{ $label }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-between text-[11px] text-gray-400 px-1">
                    <span>* Data diperbarui secara real-time berdasarkan riwayat pendaftaran sistem</span>
                    <span>Skala Maksimum: {{ $growthChart['max_value'] }} Pendaftar</span>
                </div>
            </div>
        </div>

        <!-- Modal Detail Akun Pengguna (Alpine.js - Compact Anti-Scroll) -->
        <template x-teleport="body">
            <div x-show="detailModalOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[9999] overflow-y-auto overscroll-y-contain"
                 style="display: none;"
                 @keydown.escape.window="detailModalOpen = false">

                <!-- Backdrop Overlay: WAJIB menutupi 100% seluruh layar termasuk Top Navbar dan Sidebar kiri -->
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity z-[9999]"
                     @click="detailModalOpen = false"></div>

                <!-- Centering Wrapper (Safe padding p-6 sm:p-8 lg:pl-64) -->
                <div class="fixed inset-0 z-[9999] lg:pl-64 flex items-center justify-center p-6 sm:p-8"
                     @click.self="detailModalOpen = false">
                    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl relative border border-gray-100 my-auto max-h-[85vh] max-h-[85dvh] flex flex-col overflow-hidden"
                         @click.stop
                         @click.outside="detailModalOpen = false">
                    
                        <!-- Loading Overlay State -->
                        <template x-if="loadingUser">
                            <div class="p-8 py-12 text-center text-gray-400 my-auto">
                                <svg class="w-8 h-8 animate-spin mx-auto text-brand-600 mb-2.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <p class="text-xs font-medium text-gray-600">Memuat rincian profil pengguna...</p>
                            </div>
                        </template>

                        <!-- Error State with Retry Button -->
                        <template x-if="!loadingUser && userError">
                            <div class="p-8 py-10 text-center space-y-3.5 my-auto">
                                <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 mx-auto flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Gagal Memuat Detail Profil</h4>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="userError"></p>
                                </div>
                                <div>
                                    <button type="button" @click="openDetail(currentUserId)"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Coba Lagi
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Loaded Detail Content -->
                        <template x-if="!loadingUser && selectedUser">
                            <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
                                <!-- Modal Header: px-5 py-3.5 -->
                                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-white">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center font-extrabold text-xs overflow-hidden shrink-0 shadow-xs">
                                            <template x-if="selectedUser && selectedUser.avatar_url">
                                                <img :src="selectedUser.avatar_url" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!selectedUser || !selectedUser.avatar_url">
                                                <span x-text="selectedUser ? selectedUser.name.substring(0,2).toUpperCase() : 'US'"></span>
                                            </template>
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-extrabold text-gray-900 truncate" x-text="selectedUser ? selectedUser.name : 'Detail Akun'"></h3>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                                      :class="selectedUser && selectedUser.role === 'volunteer' ? 'bg-blue-50 text-blue-700' : 'bg-indigo-50 text-indigo-700'"
                                                      x-text="selectedUser ? selectedUser.role_label : ''"></span>
                                                <span class="text-[11px] text-gray-400" x-text="selectedUser ? selectedUser.joined_at : ''"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Body: p-5 with gap-3 or space-y-3 -->
                                <div class="p-5 space-y-3 text-xs flex-1 overflow-y-auto overscroll-y-contain min-h-0 pr-1">
                                    <div class="grid grid-cols-2 gap-2.5 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                        <div>
                                            <span class="text-gray-400 font-medium text-[11px]">Alamat Email</span>
                                            <p class="font-bold text-gray-800 text-xs mt-0.5 truncate" x-text="selectedUser ? selectedUser.email : '-'"></p>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 font-medium text-[11px]">Nomor WhatsApp/Kontak</span>
                                            <p class="font-bold text-gray-800 text-xs mt-0.5" x-text="selectedUser ? selectedUser.phone : '-'"></p>
                                        </div>
                                    </div>

                                    <!-- Details for Crew -->
                                    <template x-if="selectedUser && selectedUser.role === 'volunteer' && selectedUser.details">
                                        <div class="space-y-2.5">
                                            <div class="grid grid-cols-2 gap-2.5 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                                <div>
                                                    <span class="text-gray-400 font-medium text-[11px]">Asal Institusi / Kampus</span>
                                                    <p class="font-bold text-gray-800 mt-0.5" x-text="selectedUser.details.institution"></p>
                                                </div>
                                                <div>
                                                    <span class="text-gray-400 font-medium text-[11px]">Kota Domisili</span>
                                                    <p class="font-bold text-gray-800 mt-0.5" x-text="selectedUser.details.city"></p>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="text-gray-400 font-medium text-[11px]">Bio / Pengantar Singkat</span>
                                                <p class="text-gray-700 mt-0.5 leading-relaxed" x-text="selectedUser.details.bio"></p>
                                            </div>
                                            <div>
                                                <span class="text-gray-400 font-medium text-[11px]">Keahlian & Minat</span>
                                                <p class="font-semibold text-gray-800 mt-0.5" x-text="selectedUser.details.skills"></p>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Details for EO -->
                                    <template x-if="selectedUser && selectedUser.role === 'eo' && selectedUser.details">
                                        <div class="space-y-2.5">
                                            <div class="grid grid-cols-2 gap-2.5 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                                <div>
                                                    <span class="text-gray-400 font-medium text-[11px]">Nama Organisasi</span>
                                                    <p class="font-bold text-gray-800 mt-0.5" x-text="selectedUser.details.organization_name"></p>
                                                </div>
                                                <div>
                                                    <span class="text-gray-400 font-medium text-[11px]">Jenis Organisasi</span>
                                                    <p class="font-bold text-gray-800 mt-0.5" x-text="selectedUser.details.organization_type"></p>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2.5 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                                <div>
                                                    <span class="text-gray-400 font-medium text-[11px]">Penanggung Jawab (PIC)</span>
                                                    <p class="font-bold text-gray-800 mt-0.5" x-text="selectedUser.details.pic_name"></p>
                                                </div>
                                                <div>
                                                    <span class="text-gray-400 font-medium text-[11px]">Kontak PIC</span>
                                                    <p class="font-bold text-gray-800 mt-0.5" x-text="selectedUser.details.pic_phone"></p>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="text-gray-400 font-medium text-[11px]">Deskripsi Organisasi</span>
                                                <p class="text-gray-700 mt-0.5 leading-relaxed" x-text="selectedUser.details.description"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- Modal Konfirmasi Status (Suspend/Aktifkan) Alpine.js - Compact Anti-Scroll -->
        <template x-teleport="body">
            <div x-show="confirmModalOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[9999] overflow-y-auto overscroll-y-contain"
                 style="display: none;"
                 @keydown.escape.window="confirmModalOpen = false">

                <!-- Fullscreen Backdrop Overlay: WAJIB menutupi 100% seluruh layar termasuk Top Navbar dan Sidebar kiri -->
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity z-[9999]"
                     @click="confirmModalOpen = false"></div>

                <!-- Centering Wrapper (Safe padding p-6 sm:p-8 lg:pl-64) -->
                <div class="fixed inset-0 z-[9999] lg:pl-64 flex items-center justify-center p-6 sm:p-8"
                     @click.self="confirmModalOpen = false">
                    <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl relative border border-gray-100 text-center my-auto max-h-[85vh] max-h-[85dvh] overflow-y-auto overscroll-y-contain flex flex-col space-y-3"
                         @click.stop
                         @click.outside="confirmModalOpen = false">

                        <!-- Icon Warning / Question -->
                        <div class="w-10 h-10 rounded-xl mx-auto flex items-center justify-center"
                             :class="targetAction === 'suspend' ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600'">
                            <template x-if="targetAction === 'suspend'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </template>
                            <template x-if="targetAction === 'activate'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                        </div>

                        <div>
                            <h3 class="text-base font-extrabold text-gray-900" x-text="targetAction === 'suspend' ? 'Tangguhkan Akun Pengguna?' : 'Aktifkan Kembali Akun Pengguna?'"></h3>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Apakah Anda yakin ingin <span class="font-semibold" x-text="targetAction === 'suspend' ? 'menangguhkan akses' : 'mengaktifkan kembali akses'"></span> untuk pengguna <strong class="text-gray-900" x-text="targetUserName"></strong>?
                            </p>
                        </div>

                        <!-- Keterangan Dampak Penangguhan Akun (Compact Box) -->
                        <template x-if="targetAction === 'suspend'">
                            <div class="p-2.5 bg-rose-50/90 border border-rose-200/80 rounded-xl text-left text-xs text-rose-900 space-y-1 leading-snug shadow-2xs">
                                <p class="font-bold flex items-center gap-1.5 text-rose-950 text-[11px]">
                                    <svg class="w-3.5 h-3.5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Peringatan Dampak Penangguhan (Suspend):
                                </p>
                                <div class="space-y-0.5 text-[11px] text-rose-800 pl-4 list-disc">
                                    <p>&bull; <strong>Auto-logout</strong> dan pemblokiran sesi seketika.</p>
                                    <p>&bull; <strong>Crew:</strong> Pencabutan akses riwayat keikutsertaan event & sertifikat.</p>
                                    <p>&bull; <strong>EO:</strong> Pembekuan event operasional dan penguncian NIB.</p>
                                </div>
                            </div>
                        </template>

                        <!-- Action Buttons: pt-2 border-t -->
                        <div class="pt-2 flex items-center justify-center gap-2.5 border-t border-gray-100">
                            <button type="button"
                                    @click="confirmModalOpen = false"
                                    :disabled="isLoading"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors">
                                Batal
                            </button>

                            <form :action="'/admin/users/' + targetUserId + '/toggle-status'" method="POST" @submit="isLoading = true">
                                @csrf
                                <button type="submit"
                                        :disabled="isLoading"
                                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-bold text-white rounded-xl transition-all shadow-xs"
                                        :class="targetAction === 'suspend' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700'">
                                    <svg x-show="isLoading" class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    <span x-text="isLoading ? 'Memproses...' : (targetAction === 'suspend' ? 'Ya, Tangguhkan' : 'Ya, Aktifkan')"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </div>

    @push('scripts')
    <script>
        function userManager() {
            return {
                detailModalOpen: false,
                confirmModalOpen: false,
                isLoading: false,
                loadingUser: false,
                userError: null,
                currentUserId: null,
                selectedUser: null,
                targetUserId: null,
                targetUserName: '',
                targetAction: 'suspend',

                openDetail(userId) {
                    this.currentUserId = userId;
                    this.loadingUser = true;
                    this.userError = null;
                    this.selectedUser = null;
                    this.detailModalOpen = true;

                    fetch('/admin/users/' + userId, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Status HTTP ' + res.status);
                        return res.json();
                    })
                    .then(res => {
                        if (res.success && res.data) {
                            this.selectedUser = res.data;
                        } else {
                            throw new Error(res.message || 'Gagal memuat detail profil.');
                        }
                    })
                    .catch(err => {
                        console.error('Failed to load user details:', err);
                        this.userError = 'Gagal memuat rincian profil pengguna. Silakan coba lagi.';
                    })
                    .finally(() => {
                        this.loadingUser = false;
                    });
                },

                confirmStatusChange(userId, userName, action) {
                    this.targetUserId = userId;
                    this.targetUserName = userName;
                    this.targetAction = action;
                    this.confirmModalOpen = true;
                }
            };
        }
    </script>
    @endpush

</x-layouts.admin>