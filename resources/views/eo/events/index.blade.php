<x-layouts.eo title="Event Saya — Vola">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Event Saya</h1>
            <p class="text-gray-500 mt-1">Kelola semua event yang sedang dan akan berjalan.</p>
        </div>
        <a href="{{ route('eo.events.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Event
        </a>
    </div>

    @if (! $organizerProfile)
        <x-alert type="warning" class="mb-6">
            Profil organisasi Anda belum dibuat. Silakan lengkapi
            <a href="{{ route('eo.organization') }}" class="font-semibold underline">Profil Organisasi</a>
            terlebih dahulu.
        </x-alert>
    @endif

    {{-- Status Filter Tabs --}}
    @php
        $statusTabs = [
            null => 'Semua',
            'draft' => 'Draft',
            'pending_review' => 'Menunggu Review',
            'rejected' => 'Ditolak',
        ];
    @endphp

    <div class="flex items-center gap-1 border-b border-gray-200 mb-6 overflow-x-auto">
        @foreach ($statusTabs as $value => $label)
            <a href="{{ route('eo.events.index', $value ? ['status' => $value] : []) }}"
               class="shrink-0 px-4 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                      {{ ($statusFilter === $value || ($value === null && $statusFilter === null))
                          ? 'border-brand-600 text-brand-600'
                          : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Events List --}}
    @if ($events->isEmpty())
        <x-card>
            <x-empty-state
                title="Belum ada event"
                description="{{ $statusFilter ? 'Tidak ada event dengan status ini.' : 'Mulai dengan membuat event pertama Anda.' }}"
            >
                <x-slot name="action">
                    <a href="{{ route('eo.events.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Event Baru
                    </a>
                </x-slot>
            </x-empty-state>
        </x-card>
    @else
        <div class="space-y-4">
            @foreach ($events as $event)
                <x-card padding="p-0">
                    <div class="flex items-stretch">
                        {{-- Poster --}}
                        <div class="w-32 sm:w-48 shrink-0 bg-gray-100 rounded-l-xl overflow-hidden min-h-[120px]">
                            @if ($event->poster_path)
                                <img src="{{ Storage::url($event->poster_path) }}"
                                     alt="Poster {{ $event->title }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center min-h-[120px] gap-2">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs text-gray-400">Tanpa Poster</span>
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 p-4 sm:p-6 flex flex-col justify-between min-w-0">
                            <div>
                                {{-- Status & Category --}}
                                <div class="flex items-center gap-2 flex-wrap mb-3">
                                    <x-badge-status :status="$event->status->value" />
                                    @if ($event->category)
                                        <span class="text-xs text-gray-500">{{ $event->category->name }}</span>
                                    @endif
                                </div>

                                {{-- Title --}}
                                <h3 class="font-semibold text-gray-900 text-base sm:text-lg truncate">{{ $event->title }}</h3>

                                {{-- Meta info --}}
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-gray-500">
                                    @if ($event->start_date)
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $event->start_date->translatedFormat('d M Y') }}
                                            @if ($event->end_date && $event->end_date->ne($event->start_date))
                                                – {{ $event->end_date->translatedFormat('d M Y') }}
                                            @endif
                                        </span>
                                    @endif
                                    @if ($event->location)
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            </svg>
                                            {{ $event->location }}
                                        </span>
                                    @endif
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $event->positions->count() }} Posisi
                                    </span>
                                </div>

                                {{-- Rejection reason --}}
                                @if ($event->status->value === 'rejected' && $event->rejection_reason)
                                    <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                        <p class="text-xs text-red-700"><strong>Alasan Penolakan:</strong> {{ $event->rejection_reason }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Actions based on status --}}
                            <div class="flex items-center gap-2 flex-wrap mt-4">
                                @php $status = $event->status->value; @endphp

                                @if (in_array($status, ['draft', 'rejected']))
                                    {{-- Edit --}}
                                    <a href="{{ route('eo.events.edit', $event) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Rincian
                                    </a>

                                    {{-- Submit --}}
                                    <form action="{{ route('eo.events.submit', $event) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Kirim event untuk review admin?')"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $status === 'rejected' ? 'Submit Ulang' : 'Kirim untuk Review' }}
                                        </button>
                                    </form>

                                    {{-- Delete --}}
                                    <form action="{{ route('eo.events.destroy', $event) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Hapus event ini secara permanen?')"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors border border-red-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>

                                @elseif ($status === 'pending_review')
                                    {{-- View only --}}
                                    <a href="{{ route('eo.events.edit', $event) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat Detail
                                    </a>
                                    <span class="text-xs text-yellow-600 italic">Terkunci selama review</span>

                                @else
                                    <a href="{{ route('eo.events.edit', $event) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat Detail
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif

</x-layouts.eo>
