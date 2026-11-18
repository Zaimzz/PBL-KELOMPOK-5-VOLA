@props(['paginator'])

@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $maxVisible = 5;

        if ($last <= $maxVisible) {
            $start = 1;
            $end = $last;
        } else {
            $half = floor($maxVisible / 2);
            $start = max(1, $current - $half);
            $end = min($last, $start + $maxVisible - 1);

            if ($end - $start + 1 < $maxVisible) {
                $start = max(1, $end - $maxVisible + 1);
            }
        }
    @endphp

    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Info Hasil -->
        <p class="text-xs sm:text-[13px] text-gray-500 font-medium whitespace-nowrap">
            Menampilkan <span class="font-bold text-gray-800">{{ $paginator->firstItem() }}</span>–<span class="font-bold text-gray-800">{{ $paginator->lastItem() }}</span> dari <span class="font-bold text-gray-800">{{ $paginator->total() }}</span> hasil
        </p>

        <!-- Tombol Navigasi Ikon < > + Sliding 5 Angka -->
        <div class="inline-flex items-center gap-1.5">
            {{-- Panah Kiri (<) --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Sebelumnya"
                      class="w-8 h-8 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya"
                   class="w-8 h-8 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-indigo-50 hover:border-indigo-200 hover:text-brand-700 text-gray-600 transition-colors shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endif

            {{-- Maksimal 5 Nomor Halaman yang Bergeser --}}
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

            {{-- Panah Kanan (>) --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Selanjutnya"
                   class="w-8 h-8 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-indigo-50 hover:border-indigo-200 hover:text-brand-700 text-gray-600 transition-colors shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="Selanjutnya"
                      class="w-8 h-8 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>
    </nav>
@endif