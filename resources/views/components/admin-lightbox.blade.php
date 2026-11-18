<!-- Global Admin Image & Document Lightbox Component -->
<div x-data="adminLightbox()"
     x-cloak
     x-show="open"
     x-on:open-admin-lightbox.window="openModal($event.detail)"
     x-on:keydown.escape.window="if(open) close()"
     style="z-index: 99999;"
     class="relative"
     aria-labelledby="lightbox-title"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop with blur (Lighter & softer overlay) -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-on:click="close()"
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Layout Container -->
    <div class="fixed inset-0 z-10 flex flex-col justify-between p-3 sm:p-5 pointer-events-none">
        
        <!-- Lightbox Header / Controls Bar -->
        <div class="w-full flex items-center justify-between gap-3 bg-white/95 border border-slate-200/80 text-slate-800 px-4 py-2.5 rounded-2xl shadow-xl backdrop-blur-md pointer-events-auto shrink-0 z-20">
            <!-- Left: Title & Caption -->
            <div class="min-w-0 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 border border-brand-200/70 flex items-center justify-center shrink-0">
                    <template x-if="type === 'pdf'">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </template>
                    <template x-if="type !== 'pdf'">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </template>
                </div>
                <div class="min-w-0">
                    <h3 id="lightbox-title" class="font-bold text-xs sm:text-sm text-slate-900 truncate" x-text="title"></h3>
                    <p class="text-[11px] text-slate-500 truncate" x-text="caption || (type === 'pdf' ? 'Dokumen PDF' : 'Pratinjau Foto / Berkas')"></p>
                </div>
            </div>

            <!-- Right: Actions & Zoom Controls -->
            <div class="flex items-center gap-1.5 shrink-0">
                <!-- Zoom Controls for Images -->
                <template x-if="type !== 'pdf'">
                    <div class="hidden sm:flex items-center gap-1 bg-slate-100 rounded-xl p-1 border border-slate-200/80 mr-2">
                        <button type="button"
                                x-on:click="zoomOut()"
                                title="Perkecil (-)"
                                class="w-7 h-7 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-white flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <span class="text-[11px] font-semibold text-slate-700 px-1 min-w-[3rem] text-center" x-text="Math.round(zoom * 100) + '%'"></span>
                        <button type="button"
                                x-on:click="zoomIn()"
                                title="Perbesar (+)"
                                class="w-7 h-7 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-white flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                        <button type="button"
                                x-on:click="resetZoom()"
                                title="Reset Ukuran"
                                class="px-2 py-1 rounded-lg text-[10px] font-bold text-slate-500 hover:text-slate-900 hover:bg-white transition-colors">
                            Reset
                        </button>
                    </div>
                </template>

                <!-- Buka di Tab Baru -->
                <a :href="src"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200/80 text-slate-700 hover:text-slate-900 rounded-xl text-xs font-semibold border border-slate-200 transition-colors"
                   title="Buka berkas langsung di tab baru">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span class="hidden sm:inline">Buka Asli</span>
                </a>

                <!-- Close Button -->
                <button type="button"
                        x-on:click="close()"
                        class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-rose-500 text-slate-600 hover:text-white flex items-center justify-center border border-slate-200 hover:border-rose-500 transition-colors"
                        title="Tutup (Esc)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Lightbox Content Viewer (Full center viewport) -->
        <div class="relative flex-1 flex items-center justify-center p-2 sm:p-4 min-h-0 overflow-auto pointer-events-auto cursor-pointer"
             x-on:click.self="close()">
            <!-- Loading Indicator Overlay -->
            <div x-show="loading"
                 class="absolute inset-0 z-10 flex flex-col items-center justify-center text-center pointer-events-none">
                <div class="bg-white/95 border border-slate-200/80 rounded-2xl px-6 py-5 shadow-xl backdrop-blur-md cursor-default pointer-events-auto"
                     x-on:click.stop>
                    <svg class="w-9 h-9 animate-spin mx-auto text-brand-600 mb-2.5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <p class="text-xs font-semibold text-slate-700">Memuat berkas pratinjau...</p>
                </div>
            </div>

            <!-- Error State -->
            <div x-show="hasError && !loading"
                 x-on:click.stop
                 class="bg-white border border-rose-200 rounded-2xl p-6 text-center max-w-md shadow-2xl cursor-default">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">Gagal Menampilkan Berkas</h4>
                <p class="text-xs text-slate-500 mb-4">Berkas gambar atau dokumen tidak dapat dimuat atau path penyimpanan tidak valid.</p>
                <a :href="src" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-xl transition-colors">
                    Coba Buka Langsung di Browser
                </a>
            </div>

            <!-- Image Viewer -->
            <template x-if="type !== 'pdf' && !hasError && src">
                <div class="relative max-h-[82vh] max-w-full flex items-center justify-center overflow-hidden cursor-pointer"
                     x-on:click.self="close()">
                    <img :src="src"
                         :alt="title"
                         x-on:load="loading = false; hasError = false"
                         x-on:error="if(open && src) { loading = false; hasError = true; }"
                         x-on:click.stop
                         :style="'transform: scale(' + zoom + ');'"
                         class="max-h-[80vh] max-w-[92vw] w-auto h-auto object-contain rounded-2xl shadow-2xl transition-transform duration-200 select-none cursor-default">
                </div>
            </template>

            <!-- PDF Document Viewer (Wide container + FitH) -->
            <template x-if="type === 'pdf' && !hasError && src">
                <div class="w-full max-w-6xl h-[82vh] bg-white rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 flex flex-col cursor-default"
                     x-on:click.stop>
                    <iframe :src="pdfViewerSrc"
                            class="w-full flex-1 border-0 bg-white"
                            x-on:load="loading = false"
                            title="PDF Viewer"></iframe>
                </div>
            </template>
        </div>

        <!-- Bottom Controls / Hint Bar -->
        <div class="w-full flex items-center justify-center pointer-events-auto">
            <div class="bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-slate-200/80 shadow-md text-[11px] text-slate-600 flex items-center gap-3">
                <span>Tekan <kbd class="px-1.5 py-0.5 bg-slate-100 text-slate-800 border border-slate-200 rounded font-mono text-[10px]">Esc</kbd> untuk menutup</span>
                <span class="text-slate-300">&bull;</span>
                <span>Gunakan kontrol perbesar untuk resolusi penuh</span>
            </div>
        </div>

    </div>
</div>

<script>
    function adminLightbox() {
        return {
            open: false,
            src: '',
            title: '',
            caption: '',
            type: 'image',
            zoom: 1,
            loading: false,
            hasError: false,

            get pdfViewerSrc() {
                if (!this.src) return '';
                const baseSrc = this.src.split('#')[0];
                return baseSrc + '#view=FitH';
            },

            openModal(data) {
                if (!data || !data.src) return;
                this.hasError = false;
                this.loading = true;
                this.src = data.src;
                this.title = data.title || 'Pratinjau Berkas';
                this.caption = data.caption || '';
                
                const lowerSrc = this.src.toLowerCase();
                if (data.type) {
                    this.type = data.type;
                } else if (lowerSrc.endsWith('.pdf')) {
                    this.type = 'pdf';
                } else {
                    this.type = 'image';
                }

                this.zoom = 1;
                this.open = true;
            },

            close() {
                this.open = false;
                this.loading = false;
                this.hasError = false;
                this.zoom = 1;
                setTimeout(() => {
                    if (!this.open) {
                        this.src = '';
                    }
                }, 200);
            },

            zoomIn() {
                this.zoom = Math.min(+(this.zoom + 0.25).toFixed(2), 3);
            },

            zoomOut() {
                this.zoom = Math.max(+(this.zoom - 0.25).toFixed(2), 0.5);
            },

            resetZoom() {
                this.zoom = 1;
            }
        };
    }

    // Global JavaScript Helper Accessible Everywhere
    window.openAdminLightbox = function(src, title, caption, type) {
        if (!src || src === '#' || src === 'null' || src === 'undefined') return;
        window.dispatchEvent(new CustomEvent('open-admin-lightbox', {
            detail: {
                src: src,
                title: title || 'Pratinjau Berkas',
                caption: caption || '',
                type: type || null
            }
        }));
    };
</script>