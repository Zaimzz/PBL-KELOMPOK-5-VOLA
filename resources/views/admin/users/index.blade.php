<x-layouts.admin title="Manajemen Pengguna">

    <!-- Header & Breadcrumbs Section -->
    <div class="mb-6">
        <div class="mb-4">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola data seluruh relawan dan penyelenggara event, pantau status akun, dan moderasi akses platform.</p>
        </div>

        <!-- Header Metrics: 4 Luxury Cards in Full-Width Grid (Wider 'Ditangguhkan' Card) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Pengguna -->
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-extrabold text-gray-900">{{ number_format($stats['total_users'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-500 font-medium">Total Pengguna</div>
                </div>
            </div>

            <!-- Volunteer Aktif -->
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-extrabold text-gray-900">{{ number_format($stats['active_volunteers'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-500 font-medium">Volunteer Aktif</div>
                </div>
            </div>

            <!-- Event Organizer -->
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-extrabold text-gray-900">{{ number_format($stats['total_eos'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-500 font-medium">Event Organizer</div>
                </div>
            </div>

            <!-- Akun Ditangguhkan -->
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-extrabold text-rose-600">{{ number_format($stats['suspended_users'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-500 font-medium">Ditangguhkan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container with Alpine.js State -->
    <div x-data="userManager()" class="space-y-6">

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
                           placeholder="Cari nama, email, kontak, kampus, atau organisasi..."
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                </div>

                <!-- Dropdown Peran -->
                <div class="w-full sm:w-44 shrink-0">
                    <select name="role" onchange="this.form.submit()" class="w-full py-2 px-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                        <option value="all">Semua Peran</option>
                        <option value="volunteer" {{ request('role') === 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                        <option value="eo" {{ request('role') === 'eo' ? 'selected' : '' }}>Event Organizer</option>
                    </select>
                </div>

                <!-- Dropdown Status -->
                <div class="w-full sm:w-44 shrink-0">
                    <select name="status" onchange="this.form.submit()" class="w-full py-2 px-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition-colors">
                        <option value="all">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                    </select>
                </div>

                @if(request('search') || (request('role') && request('role') !== 'all') || (request('status') && request('status') !== 'all'))
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-500 hover:text-gray-800 transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
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
        <x-card padding="p-0" class="overflow-hidden shadow-sm border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/75 border-b border-gray-100 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <th class="py-3.5 px-6">Nama / Pengguna</th>
                            <th class="py-3.5 px-4">Peran</th>
                            <th class="py-3.5 px-6">Email & Kontak</th>
                            <th class="py-3.5 px-6">Asal / Institusi</th>
                            <th class="py-3.5 px-4 text-center">Status Akun</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
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
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full {{ $isVolunteer ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' }} flex items-center justify-center font-bold text-sm shrink-0 overflow-hidden shadow-xs">
                                            @if($user->avatar_path)
                                                <img src="{{ Storage::url($user->avatar_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-sm leading-snug line-clamp-1">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">Gabung {{ $user->created_at->translatedFormat('d M Y') }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Peran -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if($isVolunteer)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                            Volunteer
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-brand-700 border border-indigo-200/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                                            Event Organizer
                                        </span>
                                    @endif
                                </td>

                                <!-- Email & Kontak -->
                                <td class="py-4 px-6">
                                    <div class="font-medium text-gray-900 text-xs">{{ $user->email }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        {{ $user->phone ?: '-' }}
                                    </div>
                                </td>

                                <!-- Asal / Institusi -->
                                <td class="py-4 px-6">
                                    <div class="font-semibold text-gray-800 text-xs line-clamp-1">{{ $institution }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $location }}</div>
                                </td>

                                <!-- Status Akun -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if($isActive)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Ditangguhkan
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <!-- Tombol Detail Akun -->
                                        <button type="button"
                                                @click="openDetail({{ $user->id }})"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-brand-700 text-xs font-semibold rounded-xl transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail
                                        </button>

                                        <!-- Tombol Suspend / Aktifkan -->
                                        @if($isActive)
                                            <button type="button"
                                                    @click="confirmStatusChange({{ $user->id }}, '{{ addslashes($user->name) }}', 'suspend')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-xl transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                Suspend
                                            </button>
                                        @else
                                            <button type="button"
                                                    @click="confirmStatusChange({{ $user->id }}, '{{ addslashes($user->name) }}', 'activate')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-xl transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            @if($users->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </x-card>

        <!-- Komponen Grafik: Distribusi Pertumbuhan Pengguna -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Distribusi Pertumbuhan Pengguna</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Statistik tren pendaftaran Volunteer dan Event Organizer dalam 6 bulan terakhir</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-md bg-blue-500"></span>
                        <span class="text-gray-600">Volunteer</span>
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
                                    <div class="bg-blue-500 transition-all duration-300 hover:brightness-110" style="height: {{ $volHeight }}%" title="Volunteer: {{ $volCount }}"></div>
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

        <!-- Modal Detail Akun Pengguna (Alpine.js) -->
        <div x-show="detailModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;"
             @keydown.escape.window="detailModalOpen = false">

            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl relative border border-gray-100"
                 @click.outside="detailModalOpen = false">
                
                <!-- Close Button -->
                <button type="button" @click="detailModalOpen = false" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Loading Overlay State (Identik dengan Event Aktif & Verifikasi EO) -->
                <template x-if="loadingUser">
                    <div class="py-12 text-center text-gray-400">
                        <svg class="w-8 h-8 animate-spin mx-auto text-brand-600 mb-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <p class="text-xs">Memuat detail profil pengguna...</p>
                    </div>
                </template>

                <!-- Loaded Detail Content -->
                <template x-if="!loadingUser && selectedUser">
                    <div>
                        <!-- Modal Header -->
                <div class="flex items-center gap-3.5 pb-4 border-b border-gray-100">
                    <div class="w-12 h-12 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center font-extrabold text-base overflow-hidden shrink-0 shadow-xs">
                        <template x-if="selectedUser && selectedUser.avatar_url">
                            <img :src="selectedUser.avatar_url" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!selectedUser || !selectedUser.avatar_url">
                            <span x-text="selectedUser ? selectedUser.name.substring(0,2).toUpperCase() : 'US'"></span>
                        </template>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900" x-text="selectedUser ? selectedUser.name : 'Detail Akun'"></h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                  :class="selectedUser && selectedUser.role === 'volunteer' ? 'bg-blue-50 text-blue-700' : 'bg-indigo-50 text-indigo-700'"
                                  x-text="selectedUser ? selectedUser.role_label : ''"></span>
                            <span class="text-xs text-gray-400" x-text="selectedUser ? selectedUser.joined_at : ''"></span>
                        </div>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="py-4 space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3.5 rounded-xl border border-gray-100">
                        <div>
                            <span class="text-gray-400 font-medium">Alamat Email</span>
                            <p class="font-bold text-gray-800 text-xs mt-0.5 truncate" x-text="selectedUser ? selectedUser.email : '-'"></p>
                        </div>
                        <div>
                            <span class="text-gray-400 font-medium">Nomor WhatsApp/Kontak</span>
                            <p class="font-bold text-gray-800 text-xs mt-0.5" x-text="selectedUser ? selectedUser.phone : '-'"></p>
                        </div>
                    </div>

                    <!-- Details for Volunteer -->
                    <template x-if="selectedUser && selectedUser.role === 'volunteer' && selectedUser.details">
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-gray-400 font-medium">Asal Institusi / Kampus</span>
                                    <p class="font-bold text-gray-800 mt-0.5" x-text="selectedUser.details.institution"></p>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium">Kota Domisili</span>
                                    <p class="font-bold text-gray-800 mt-0.5" x-text="selectedUser.details.city"></p>
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-400 font-medium">Bio / Pengantar Singkat</span>
                                <p class="text-gray-700 mt-0.5 leading-relaxed" x-text="selectedUser.details.bio"></p>
                            </div>
                            <div>
                                <span class="text-gray-400 font-medium">Keahlian & Minat</span>
                                <p class="font-semibold text-gray-800 mt-0.5" x-text="selectedUser.details.skills"></p>
                            </div>
                        </div>
                    </template>

                    <!-- Details for EO -->
                    <template x-if="selectedUser && selectedUser.role === 'eo' && selectedUser.details">
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-gray-400 font-medium">Nama Organisasi</span>
                                    <p class="font-bold text-gray-800 mt-0.5" x-text="selectedUser.details.organization_name"></p>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium">Jenis Organisasi</span>
                                    <p class="font-bold text-gray-800 mt-0.5" x-text="selectedUser.details.organization_type"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-gray-400 font-medium">Penanggung Jawab (PIC)</span>
                                    <p class="font-bold text-gray-800 mt-0.5" x-text="selectedUser.details.pic_name"></p>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-medium">Kontak PIC</span>
                                    <p class="font-bold text-gray-800 mt-0.5" x-text="selectedUser.details.pic_phone"></p>
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-400 font-medium">Deskripsi Organisasi</span>
                                <p class="text-gray-700 mt-0.5 leading-relaxed" x-text="selectedUser.details.description"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="button" @click="detailModalOpen = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors">
                        Tutup
                    </button>
                </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Modal Konfirmasi Status (Suspend/Aktifkan) Alpine.js -->
        <div x-show="confirmModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;"
             @keydown.escape.window="confirmModalOpen = false">

            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl relative border border-gray-100 text-center"
                 @click.outside="confirmModalOpen = false">

                <!-- Icon Warning / Question -->
                <div class="w-12 h-12 rounded-full mx-auto flex items-center justify-center mb-4"
                     :class="targetAction === 'suspend' ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600'">
                    <template x-if="targetAction === 'suspend'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </template>
                    <template x-if="targetAction === 'activate'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                </div>

                <h3 class="text-base font-extrabold text-gray-900" x-text="targetAction === 'suspend' ? 'Tangguhkan Akun Pengguna?' : 'Aktifkan Kembali Akun Pengguna?'"></h3>
                <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                    Apakah Anda yakin ingin <span class="font-semibold" x-text="targetAction === 'suspend' ? 'menangguhkan akses' : 'mengaktifkan kembali akses'"></span> untuk pengguna <strong class="text-gray-900" x-text="targetUserName"></strong>?
                </p>

                <!-- Action Buttons with Loading State -->
                <div class="mt-6 flex items-center justify-center gap-3">
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
                                class="inline-flex items-center justify-center gap-2 px-5 py-2 text-xs font-bold text-white rounded-xl transition-all shadow-xs"
                                :class="targetAction === 'suspend' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700'">
                            <svg x-show="isLoading" class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
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

    @push('scripts')
    <script>
        function userManager() {
            return {
                detailModalOpen: false,
                confirmModalOpen: false,
                isLoading: false,
                loadingUser: false,
                selectedUser: null,
                targetUserId: null,
                targetUserName: '',
                targetAction: 'suspend',

                openDetail(userId) {
                    this.loadingUser = true;
                    this.selectedUser = null;
                    this.detailModalOpen = true;

                    fetch('/admin/users/' + userId)
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                this.selectedUser = res.data;
                            }
                        })
                        .catch(err => {
                            console.error('Failed to load user details:', err);
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
