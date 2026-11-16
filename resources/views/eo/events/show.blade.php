<x-layouts.eo :title="$event->title.' — Vola'">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('eo.events.index') }}" class="hover:text-gray-700">Event Saya</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900 font-medium truncate max-w-xs">{{ $event->title }}</span>
    </nav>

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-2 flex-wrap">
                <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
                <x-badge-status :status="$event->status->value" />
            </div>
            <p class="text-gray-500">
                {{ $event->category?->name ?? 'Umum' }}
                @if ($event->city)
                    · {{ $event->city }}
                @endif
            </p>
            @if ($event->status->value === 'rejected' && $event->rejection_reason)
                <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg max-w-xl">
                    <p class="text-sm text-red-700"><strong>Alasan Penolakan Admin:</strong> {{ $event->rejection_reason }}</p>
                </div>
            @endif
            @if ($event->status->value === 'pending_review')
                <x-alert type="warning" class="mt-3 max-w-xl">
                    Event ini sedang dalam proses review admin dan tidak dapat diedit.
                </x-alert>
            @endif
        </div>

        <div class="flex items-center gap-2 flex-wrap shrink-0">
            @if ($event->status->value === 'awaiting_payment')
                <a href="{{ route('eo.billing.index', ['event_id' => $event->id]) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors">
                    Bayar Sekarang
                </a>
            @endif

            @can('update', $event)
                <a href="{{ route('eo.events.edit', $event) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                    Edit Rincian
                </a>
            @endcan

            @can('submit', $event)
                <form action="{{ route('eo.events.submit', $event) }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Kirim event ini untuk review admin?')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        {{ $event->status->value === 'rejected' ? 'Submit Ulang untuk Review' : 'Kirim untuk Review' }}
                    </button>
                </form>
            @endcan

            @can('close', $event)
                <form action="{{ route('eo.events.close', $event) }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Tutup pendaftaran event ini? Pendaftar baru tidak akan bisa melamar.')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition-colors">
                        Tutup Rekrutmen
                    </button>
                </form>
            @endcan

            @can('finish', $event)
                <form action="{{ route('eo.events.finish', $event) }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Tandai event ini sebagai selesai?')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                        Tandai Selesai
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- LEFT --}}
        <div class="xl:col-span-2 space-y-6">
            <x-card>
                <h2 class="text-base font-semibold text-gray-900 mb-3">Deskripsi Event</h2>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $event->description ?: 'Belum ada deskripsi.' }}</p>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h2 class="text-base font-semibold text-gray-900">Posisi Relawan</h2>
                    <span class="text-xs text-gray-500">{{ $event->positions->count() }} posisi</span>
                </div>

                @if ($event->positions->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada posisi untuk event ini.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($event->positions as $position)
                            <div class="py-3 flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $position->name }}</p>
                                    @if ($position->category)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $position->category }}</p>
                                    @endif
                                    @if ($position->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ $position->description }}</p>
                                    @endif
                                </div>
                                <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-brand-50 text-brand-700">
                                    Kuota {{ $position->quota }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        {{-- RIGHT --}}
        <div class="space-y-6">
            <x-card>
                <h2 class="text-base font-semibold text-gray-900 mb-4">Detail Event</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Tanggal</dt>
                        <dd class="text-gray-900 font-medium">
                            @if ($event->start_date)
                                {{ $event->start_date->translatedFormat('d F Y') }}
                                @if ($event->end_date && $event->end_date->ne($event->start_date))
                                    – {{ $event->end_date->translatedFormat('d F Y') }}
                                @endif
                            @else
                                Belum ditentukan
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Waktu</dt>
                        <dd class="text-gray-900 font-medium">
                            {{ $event->start_time ?: '—' }}{{ $event->end_time ? ' – '.$event->end_time : '' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Batas Pendaftaran</dt>
                        <dd class="text-gray-900 font-medium">
                            {{ $event->registration_deadline?->translatedFormat('d F Y') ?? 'Belum ditentukan' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Lokasi</dt>
                        <dd class="text-gray-900 font-medium">{{ $event->location ?: 'Belum ditentukan' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Info Kontak</dt>
                        <dd class="text-gray-900 font-medium">{{ $event->contact_info ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">PIC</dt>
                        <dd class="text-gray-900 font-medium">
                            {{ $event->pic_name ?: '—' }}
                            @if ($event->pic_phone)
                                <span class="text-gray-500 font-normal">({{ $event->pic_phone }})</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </x-card>

            @if ($event->benefits)
                <x-card>
                    <h2 class="text-base font-semibold text-gray-900 mb-3">Benefit Relawan</h2>
                    <ul class="space-y-1.5 text-sm text-gray-700">
                        @foreach ($event->benefits as $benefit)
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $benefit }}
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif

            @can('delete', $event)
                <x-card>
                    <h3 class="text-sm font-semibold text-red-700 mb-2">Zona Berbahaya</h3>
                    <p class="text-xs text-gray-500 mb-3">Menghapus event ini bersifat permanen dan tidak dapat dibatalkan.</p>
                    <form action="{{ route('eo.events.destroy', $event) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Yakin ingin menghapus event ini? Tindakan ini tidak dapat dibatalkan.')"
                                class="w-full py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                            Hapus Event Ini
                        </button>
                    </form>
                </x-card>
            @endcan
        </div>
    </div>

</x-layouts.eo>
