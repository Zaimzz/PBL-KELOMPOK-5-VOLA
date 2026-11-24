<script>
    /**
     * VOLA Admin Interaction & Performance Enhancements
     * - Subtle Top Loading Bar
     * - Global Single-Click Protection (Anti Double-Click)
     * - Seamless HTML Partial Swapping (fetch + history.pushState + Alpine.initTree)
     */
    (function () {
        // 1. Loading Top Progress Bar
        window.AdminLoading = {
            bar: null,
            timer: null,
            active: false,
            init() {
                this.bar = document.getElementById('admin-top-progress');
            },
            start() {
                if (!this.bar) this.init();
                if (!this.bar) return;
                this.active = true;
                clearTimeout(this.timer);
                this.bar.style.transition = 'width 10s cubic-bezier(0.1, 0.5, 0.1, 1), opacity 0.2s ease';
                this.bar.style.opacity = '1';
                this.bar.style.width = '75%';

                const content = document.getElementById('admin-main-content');
                if (content) {
                    content.classList.add('transition-opacity', 'duration-150', 'opacity-60', 'pointer-events-none');
                }
            },
            stop() {
                if (!this.bar) this.init();
                if (!this.bar) return;
                this.active = false;
                clearTimeout(this.timer);
                this.bar.style.transition = 'width 0.2s ease, opacity 0.3s ease';
                this.bar.style.width = '100%';

                const content = document.getElementById('admin-main-content');
                if (content) {
                    content.classList.remove('opacity-60', 'pointer-events-none');
                }

                this.timer = setTimeout(() => {
                    if (!this.active) {
                        this.bar.style.opacity = '0';
                        setTimeout(() => {
                            if (!this.active) {
                                this.bar.style.width = '0%';
                            }
                        }, 250);
                    }
                }, 200);
            }
        };

        // 2. Global Single-Click Protection & Button Loading Guard
        window.initAdminSingleClickGuard = function () {
            document.addEventListener('submit', function (e) {
                // Abaikan jika submit dibatalkan oleh validasi client-side
                if (e.defaultPrevented) return;

                const form = e.target;
                if (!form || !(form instanceof HTMLFormElement)) return;

                // Abaikan form dengan atribut data-no-guard atau form GET
                if (form.hasAttribute('data-no-guard') || form.method.toUpperCase() === 'GET') {
                    return;
                }

                // Jangan kunci jika validasi HTML5 bawaan gagal
                if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                    return;
                }

                // Cari submit button terkait
                const submitBtn = e.submitter || form.querySelector('button[type="submit"]');
                if (!submitBtn || submitBtn.hasAttribute('data-loading')) {
                    return;
                }

                // Jangan kunci tombol yang bertipe toggle atau pengecualian
                if (submitBtn.hasAttribute('data-no-lock')) {
                    return;
                }

                // Kunci tombol
                submitBtn.setAttribute('data-loading', 'true');
                submitBtn.setAttribute('aria-busy', 'true');
                submitBtn.dataset.originalHtml = submitBtn.innerHTML;

                // Kunci lebar tombol agar stabil dan layout tidak bergeser
                const originalWidth = submitBtn.offsetWidth;
                if (originalWidth > 0) {
                    submitBtn.style.minWidth = originalWidth + 'px';
                    submitBtn.style.width = originalWidth + 'px';
                }

                // Spinner animasi halus
                const spinnerSvg = `
                    <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-current shrink-0 inline-block align-middle" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                `;

                // Sisipkan spinner di samping teks tombol jika belum ada spinner di dalamnya
                if (!submitBtn.querySelector('.animate-spin')) {
                    const existingSvg = submitBtn.querySelector('svg');
                    if (existingSvg) {
                        existingSvg.outerHTML = spinnerSvg;
                    } else {
                        submitBtn.insertAdjacentHTML('afterbegin', spinnerSvg);
                    }
                }

                submitBtn.classList.add('pointer-events-none', 'opacity-75', 'cursor-not-allowed');

                // Nonaktifkan tombol secara asinkron agar payload submit form native tetap terkirim
                setTimeout(() => {
                    submitBtn.disabled = true;
                }, 0);

                // Safety fallback: Lepas kunci jika request macet lebih dari 15 detik
                setTimeout(() => {
                    if (submitBtn && submitBtn.hasAttribute('data-loading')) {
                        window.unlockAdminButton(submitBtn);
                    }
                }, 15000);
            }, false);
        };

        window.unlockAdminButton = function (btn) {
            if (!btn) return;
            if (btn.dataset.originalHtml) {
                btn.innerHTML = btn.dataset.originalHtml;
                delete btn.dataset.originalHtml;
            }
            btn.disabled = false;
            btn.removeAttribute('aria-busy');
            btn.removeAttribute('data-loading');
            btn.classList.remove('pointer-events-none', 'opacity-75', 'cursor-not-allowed');
            btn.style.width = '';
            btn.style.minWidth = '';
        };

        window.unlockAdminForm = function (form) {
            if (!form) return;
            const buttons = form.querySelectorAll('button[data-loading="true"], button[type="submit"]');
            buttons.forEach(btn => window.unlockAdminButton(btn));
        };

        // 3. Native Partial Swapper Engine
        window.AdminPartialSwap = {
            abortController: null,
            isSwapping: false,

            async navigate(url, options = {}) {
                if (this.isSwapping && this.abortController) {
                    this.abortController.abort();
                }

                this.isSwapping = true;
                this.abortController = new AbortController();

                // Simpan id input search dan posisi kursor jika ada
                const activeEl = document.activeElement;
                let activeSearchMeta = null;
                if (activeEl && activeEl.matches && activeEl.matches('input[name="search"]')) {
                    activeSearchMeta = {
                        name: activeEl.name,
                        value: activeEl.value,
                        selectionStart: activeEl.selectionStart,
                        selectionEnd: activeEl.selectionEnd,
                    };
                }

                window.AdminLoading.start();

                try {
                    const response = await fetch(url, {
                        signal: this.abortController.signal,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const htmlText = await response.text();
                    const parser = new DOMParser();
                    const newDoc = parser.parseFromString(htmlText, 'text/html');

                    // Perbarui judul halaman
                    if (newDoc.title) {
                        document.title = newDoc.title;
                    }

                    // Tentukan target selector yang didukung
                    const supportedSelectors = [
                        '#admin-metrics-container',
                        '#admin-content-container',
                        '#admin-detail-container',
                    ];

                    let anySwapped = false;
                    supportedSelectors.forEach(selector => {
                        const currentEl = document.querySelector(selector);
                        const newEl = newDoc.querySelector(selector);

                        if (currentEl && newEl) {
                            // Hancurkan tree Alpine lama jika tersedia
                            if (window.Alpine && typeof window.Alpine.destroyTree === 'function') {
                                try {
                                    window.Alpine.destroyTree(currentEl);
                                } catch (_) {}
                            }

                            currentEl.outerHTML = newEl.outerHTML;

                            // Inisialisasi ulang Alpine pada elemen baru
                            const freshEl = document.querySelector(selector);
                            if (window.Alpine && freshEl && typeof window.Alpine.initTree === 'function') {
                                window.Alpine.initTree(freshEl);
                            }

                            anySwapped = true;
                        }
                    });

                    // Jika tidak ada selector spesifik, coba fallback ke #admin-slot-container
                    if (!anySwapped) {
                        const currentSlot = document.getElementById('admin-slot-container');
                        const newSlot = newDoc.getElementById('admin-slot-container');
                        if (currentSlot && newSlot) {
                            if (window.Alpine && typeof window.Alpine.destroyTree === 'function') {
                                try {
                                    window.Alpine.destroyTree(currentSlot);
                                } catch (_) {}
                            }
                            currentSlot.innerHTML = newSlot.innerHTML;
                            if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                                window.Alpine.initTree(currentSlot);
                            }
                            anySwapped = true;
                        }
                    }

                    // Sinkronkan URL di Address Bar
                    if (options.pushState !== false) {
                        window.history.pushState({ url: url }, '', url);
                    }

                    // Pulihkan fokus input search jika sebelumnya sedang aktif mengetik
                    if (activeSearchMeta) {
                        setTimeout(() => {
                            const freshSearchInput = document.querySelector('input[name="search"]');
                            if (freshSearchInput) {
                                freshSearchInput.focus();
                                if (typeof activeSearchMeta.selectionStart === 'number') {
                                    freshSearchInput.setSelectionRange(activeSearchMeta.selectionStart, activeSearchMeta.selectionEnd);
                                }
                            }
                        }, 50);
                    }

                    // Dispatch custom event untuk komponen yang ingin merespons swap
                    window.dispatchEvent(new CustomEvent('admin:partial-swap-done', { detail: { url } }));

                } catch (err) {
                    if (err.name !== 'AbortError') {
                        console.warn('Partial swap gagal, beralih ke navigasi normal:', err);
                        window.location.href = url;
                    }
                } finally {
                    this.isSwapping = false;
                    window.AdminLoading.stop();
                }
            }
        };

        // 3.5 Helper Submit Form via AJAX Filter
        window.adminSubmitForm = function (formOrElement) {
            if (!formOrElement) return;
            const form = formOrElement instanceof HTMLFormElement 
                ? formOrElement 
                : (formOrElement.form || formOrElement.closest('form'));
            if (!form) return;
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
            }
        };

        // 4. Intercept Form GET di Admin Main Content
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || !(form instanceof HTMLFormElement)) return;

            if (form.method && form.method.toUpperCase() === 'GET' && form.closest('#admin-main-content')) {
                e.preventDefault();
                clearTimeout(searchDebounceTimer);
                const formData = new FormData(form);
                const params = new URLSearchParams();
                for (const [key, value] of formData.entries()) {
                    const strVal = typeof value === 'string' ? value.trim() : value;
                    if (strVal !== '' && strVal !== null && strVal !== 'all') {
                        params.append(key, strVal);
                    }
                }

                const actionUrl = (form.action || window.location.pathname).split('?')[0];
                const targetUrl = params.toString() ? `${actionUrl}?${params.toString()}` : actionUrl;

                window.AdminPartialSwap.navigate(targetUrl);
            }
        });

        // 5. Debounce Realtime Search (400ms)
        let searchDebounceTimer = null;
        document.addEventListener('input', function (e) {
            const input = e.target;
            if (!input || !input.matches || !input.matches('input[name="search"]')) return;
            if (!input.closest('#admin-main-content')) return;

            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                const form = input.closest('form');
                if (form && form.method && form.method.toUpperCase() === 'GET') {
                    const formData = new FormData(form);
                    const params = new URLSearchParams();
                    for (const [key, value] of formData.entries()) {
                        const strVal = typeof value === 'string' ? value.trim() : value;
                        if (strVal !== '' && strVal !== null && strVal !== 'all') {
                            params.append(key, strVal);
                        }
                    }

                    const actionUrl = (form.action || window.location.pathname).split('?')[0];
                    const targetUrl = params.toString() ? `${actionUrl}?${params.toString()}` : actionUrl;

                    window.AdminPartialSwap.navigate(targetUrl);
                }
            }, 400);
        });

        // 6. Intercept Pagination & Filter Links
        document.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (!link || !link.href) return;
            if (!link.closest('#admin-main-content')) return;

            // Abaikan link eksternal, tab baru, unduh berkas, atau anchor modal
            if (link.target === '_blank' || link.hasAttribute('download') || link.getAttribute('href').startsWith('#') || link.hasAttribute('data-no-swap')) {
                return;
            }

            const url = new URL(link.href, window.location.origin);
            const isSameOrigin = url.origin === window.location.origin;
            const isSamePath = url.pathname === window.location.pathname;

            // Cek apakah ini link paginasi atau link filter/reset pada halaman admin yang sama
            const isPagination = link.closest('.pagination, nav[role="navigation"]') || url.searchParams.has('page');
            const isFilterOrReset = isSamePath && (
                url.searchParams.has('search') ||
                url.searchParams.has('status') ||
                url.searchParams.has('category') ||
                url.searchParams.has('role') ||
                url.searchParams.has('period') ||
                url.searchParams.has('month') ||
                link.classList.contains('filter-link') ||
                link.hasAttribute('data-reset') ||
                (window.location.search !== '' && (url.search === '' || url.search === '?'))
            );

            if (isSameOrigin && (isPagination || isFilterOrReset)) {
                e.preventDefault();
                window.AdminPartialSwap.navigate(link.href);
            }
        });

        // 7. Handle Browser Back & Forward (Popstate)
        let lastKnownPathname = window.location.pathname;
        window.addEventListener('popstate', function (e) {
            if (window.location.pathname !== lastKnownPathname || (e.state && e.state.adminPartialSwap)) {
                lastKnownPathname = window.location.pathname;
                window.AdminPartialSwap.navigate(window.location.href, { pushState: false });
            }
        });

        // Inisialisasi saat DOM siap
        document.addEventListener('DOMContentLoaded', function () {
            window.AdminLoading.init();
            window.initAdminSingleClickGuard();
        });

        // Jika script dieksekusi setelah DOM ready
        if (document.readyState === 'interactive' || document.readyState === 'complete') {
            window.AdminLoading.init();
            window.initAdminSingleClickGuard();
        }
    })();
</script>
