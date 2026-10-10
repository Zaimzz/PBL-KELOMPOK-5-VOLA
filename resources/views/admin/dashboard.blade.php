<x-layouts.admin title="Beranda Admin">
    
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Pusat Kontrol VOLA</h1>
        <p class="text-gray-500">Kelola verifikasi penyelenggara event, moderasi pengajuan, laporan komunitas, dan transaksi platform.</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-card class="relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                    Tindakan Segera
                </span>
            </div>
            <div class="p-5">
                <p class="text-sm font-medium text-gray-500">EO Verifikasi</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <p class="text-3xl font-bold text-gray-900">{{ $pendingEoCount }}</p>
                    <p class="text-sm text-gray-500">Organisasi</p>
                </div>
                <p class="mt-1 text-xs text-gray-500">Legalitas & NIB menanti review</p>
            </div>
        </x-card>

        <x-card class="relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                    SLA &lt; 4 Jam
                </span>
            </div>
            <div class="p-5">
                <p class="text-sm font-medium text-gray-500">Review Event</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <p class="text-3xl font-bold text-gray-900">{{ $pendingReviewEventCount }}</p>
                    <p class="text-sm text-gray-500">Pengajuan</p>
                </div>
                <p class="mt-1 text-xs text-gray-500">Kurasi kelayakan konten kru</p>
            </div>
        </x-card>

        <x-card class="relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                    Tayang Publik
                </span>
            </div>
            <div class="p-5">
                <p class="text-sm font-medium text-gray-500">Event Aktif</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <p class="text-3xl font-bold text-gray-900">{{ $activeEventCount }}</p>
                    <p class="text-sm text-gray-500">Event</p>
                </div>
                <p class="mt-1 text-xs text-gray-500">Menerima pelamar relawan</p>
            </div>
        </x-card>

        <x-card class="relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                    Bank / QRIS
                </span>
            </div>
            <div class="p-5">
                <p class="text-sm font-medium text-gray-500">Pembayaran</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <p class="text-3xl font-bold text-gray-900">{{ $transactionCount }}</p>
                    <p class="text-sm text-gray-500">Transaksi</p>
                </div>
                <p class="mt-1 text-xs text-gray-500">Total riwayat pembayaran</p>
            </div>
        </x-card>
    </div>

    <!-- Quick Actions and Recent Activity -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- Perlu Tindakan Cepat -->
        <div class="xl:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Perlu Tindakan Cepat</h2>
                    <p class="text-sm text-gray-500">Antrean prioritas moderasi yang membutuhkan tindakan admin</p>
                </div>
                <span class="text-sm text-gray-500">{{ $actionRequired->count() }} antrean aktif</span>
            </div>

            @if($actionRequired->isEmpty())
                <x-card class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-600 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-medium mb-1">Semua antrean bersih!</h3>
                    <p class="text-sm text-gray-500">Tidak ada tindakan mendesak yang perlu diselesaikan saat ini.</p>
                </x-card>
            @else
                <div class="space-y-4">
                    @foreach($actionRequired as $action)
                        <x-card class="p-4 flex items-start gap-4">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $action['icon_color'] }}">
                                @if($action['type'] == 'event')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-medium {{ $action['type'] == 'event' ? 'text-indigo-600 bg-indigo-50' : 'text-amber-600 bg-amber-50' }} px-2 py-0.5 rounded">
                                        {{ $action['type'] == 'event' ? 'Pengajuan Event Baru' : 'Verifikasi Legalitas EO' }}
                                    </span>
                                    <span class="text-xs text-gray-400">&bull; Diajukan {{ $action['time'] }}</span>
                                </div>
                                <h3 class="font-semibold text-gray-900 truncate">{{ $action['title'] }}</h3>
                                <p class="text-sm text-gray-500 truncate">{{ $action['subtitle'] }}</p>
                            </div>
                            <div class="shrink-0">
                                <a href="{{ $action['action_link'] }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg {{ $action['type'] == 'event' ? 'bg-brand-600 text-white hover:bg-brand-700' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }} transition-colors">
                                    {{ $action['action_text'] }}
                                </a>
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Aktivitas Terbaru -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Aktivitas Terbaru</h2>
            </div>
            
            <x-card class="p-0">
                <div class="p-6 text-center text-gray-500">
                    <p class="text-sm">Belum ada riwayat aktivitas terbaru yang didukung oleh data sistem.</p>
                </div>
            </x-card>
        </div>

    </div>

</x-layouts.admin>
