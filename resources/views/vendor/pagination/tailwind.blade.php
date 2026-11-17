<nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row items-center justify-between gap-3 w-full">
    {{-- Informasi Jumlah Hasil Pencarian / Filter (Selalu Tampil) --}}
    <div>
        <p class="text-xs sm:text-sm text-gray-500">
            @if ($paginator->total() > 0)
                Menampilkan
                <span class="font-bold text-gray-900">{{ $paginator->firstItem() }}</span>
                sampai
                <span class="font-bold text-gray-900">{{ $paginator->lastItem() }}</span>
                dari
                <span class="font-bold text-gray-900">{{ $paginator->total() }}</span>
                hasil
            @else
                Menampilkan <span class="font-bold text-gray-900">0</span> hasil
            @endif
        </p>
    </div>

    {{-- Tombol Navigasi Halaman (Hanya Tampil Jika Total Halaman Lebih Dari Satu) --}}
    @if ($paginator->hasPages())
        <div class="flex items-center gap-1 sm:gap-1.5">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-50 border border-gray-200 rounded-xl cursor-not-allowed select-none">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-brand-600 hover:border-brand-200 transition-colors shadow-2xs">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Sebelumnya
                </a>
            @endif

            {{-- Nomor Halaman (Tampil di sm+) --}}
            <div class="hidden sm:flex items-center gap-1">
                @foreach ($elements as $element)
                    {{-- Separator Titik Tiga (...) --}}
                    @if (is_string($element))
                        <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-gray-400 select-none">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Daftar Link Halaman --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-brand-600 rounded-xl shadow-xs select-none" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-brand-600 hover:border-brand-200 transition-colors shadow-2xs">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Tombol Selanjutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-brand-600 hover:border-brand-200 transition-colors shadow-2xs">
                    Selanjutnya
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-50 border border-gray-200 rounded-xl cursor-not-allowed select-none">
                    Selanjutnya
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif
        </div>
    @endif
</nav>

