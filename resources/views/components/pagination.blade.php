@props(['paginator'])

@if ($paginator->total() > 0)
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $blockSize = 5;

        // Tentukan blok saat ini (1-indexed: 1 untuk halaman 1–5, 2 untuk halaman 6–10, dst)
        $currentBlock = (int) ceil($current / $blockSize);
        $totalBlocks = (int) ceil($last / $blockSize);

        // Halaman awal dan akhir untuk blok 5 halaman yang sedang aktif
        $start = (($currentBlock - 1) * $blockSize) + 1;
        $end = min($last, $currentBlock * $blockSize);

        // Navigasi perpindahan blok (Chunk / Windowed Navigation):
        // Panah < melompat ke halaman akhir blok sebelumnya (contoh: dari blok 6-10 kembali ke halaman 5)
        $hasPrevBlock = $currentBlock > 1;
        $prevBlockUrl = $hasPrevBlock ? $paginator->url($start - 1) : null;

        // Panah > melompat ke halaman awal blok berikutnya (contoh: dari blok 1-5 melompat ke halaman 6)
        $hasNextBlock = $end < $last;
        $nextBlockUrl = $hasNextBlock ? $paginator->url($end + 1) : null;
    @endphp

    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Info Hasil -->
        <p class="text-xs sm:text-[13px] text-gray-500 font-medium whitespace-nowrap">
            @if ($paginator->firstItem() === $paginator->lastItem())
                Menampilkan <span class="font-bold text-gray-800">{{ $paginator->firstItem() }}</span> dari <span class="font-bold text-gray-800">{{ $paginator->total() }}</span> hasil
            @else
                Menampilkan <span class="font-bold text-gray-800">{{ $paginator->firstItem() }}</span>–<span class="font-bold text-gray-800">{{ $paginator->lastItem() }}</span> dari <span class="font-bold text-gray-800">{{ $paginator->total() }}</span> hasil
            @endif
        </p>

        <!-- Tombol Navigasi Ikon < > + Blok 5 Angka (Windowed Pagination) -->
        @if ($paginator->hasPages())
            <div x-data="{ sliding: false, dir: 'none' }" class="inline-flex items-center gap-1.5 overflow-hidden p-0.5">
                {{-- Panah Kiri (<): Melompat ke blok 5 halaman sebelumnya --}}
                @if ($hasPrevBlock)
                    <a href="{{ $prevBlockUrl }}" rel="prev" aria-label="Kelompok Halaman Sebelumnya"
                       @click="dir = 'prev'; sliding = true"
                       class="w-8 h-8 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-indigo-50 hover:border-indigo-200 hover:text-brand-700 text-gray-600 transition-colors shadow-2xs shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Kelompok Halaman Sebelumnya"
                          class="w-8 h-8 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @endif

                {{-- Nomor Halaman per Blok (Maksimal 5 Nomor Berurutan) dengan Animasi Geser Halus --}}
                <div class="inline-flex items-center gap-1.5 transition-all duration-200 ease-out transform"
                     :class="{
                         'translate-x-3 opacity-0': sliding && dir === 'prev',
                         '-translate-x-3 opacity-0': sliding && dir === 'next',
                         'translate-x-0 opacity-100': !sliding
                     }">
                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $current)
                            <span aria-current="page"
                                  class="min-w-[32px] h-8 px-2.5 inline-flex items-center justify-center rounded-xl bg-brand-600 text-white text-xs font-bold shadow-xs">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $paginator->url($page) }}"
                               class="min-w-[32px] h-8 px-2.5 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-indigo-50 hover:border-indigo-200 hover:text-brand-700 text-gray-700 text-xs font-semibold transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor
                </div>

                {{-- Panah Kanan (>): Melompat ke blok 5 halaman berikutnya --}}
                @if ($hasNextBlock)
                    <a href="{{ $nextBlockUrl }}" rel="next" aria-label="Kelompok Halaman Selanjutnya"
                       @click="dir = 'next'; sliding = true"
                       class="w-8 h-8 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-indigo-50 hover:border-indigo-200 hover:text-brand-700 text-gray-600 transition-colors shadow-2xs shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Kelompok Halaman Selanjutnya"
                          class="w-8 h-8 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        @endif
    </nav>
@endif