@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'zIndex' => '40',
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    '4xl' => 'sm:max-w-4xl',
    '5xl' => 'sm:max-w-5xl',
][$maxWidth] ?? 'sm:max-w-2xl';

$isAdmin = request()->is('admin*');

$zLevel = in_array((string)$zIndex, ['50', 'z-50']) ? '50' : '40';
$zClass = 'z-' . $zLevel;
@endphp

<div
    x-data="{
        show: @js($show),
        modalName: '{{ $name }}',
        focusables() {
            // All focusable element types...
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                // All non-disabled elements...
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
    }"
    x-init="
        $watch('show', value => {
            window.openAdminModals = window.openAdminModals || [];
            if (value) {
                if (!window.openAdminModals.includes(modalName)) {
                    window.openAdminModals.push(modalName);
                }
                document.body.classList.add('overflow-y-hidden');
                {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
            } else {
                window.openAdminModals = window.openAdminModals.filter(m => m !== modalName);
                if (window.openAdminModals.length === 0) {
                    document.body.classList.remove('overflow-y-hidden');
                }
                $dispatch('modal-closed', modalName);
            }
        })
    "
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close="show = false"
    x-on:keydown.escape.window="
        if (window.adminLightboxOpen || document.querySelector('[data-admin-lightbox][data-open=\'true\']')) return;
        if (window.openAdminModals && window.openAdminModals.length > 0) {
            const topModal = window.openAdminModals[window.openAdminModals.length - 1];
            if (topModal === modalName) {
                show = false;
            }
        } else if (show) {
            show = false;
        }
    "
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
>
    <template x-teleport="body">
        <div
            x-show="show"
            x-cloak
            class="fixed inset-0 {{ $zClass }} overflow-y-auto overscroll-y-contain"
            style="z-index: {{ $zLevel }}; display: {{ $show ? 'block' : 'none' }};"
        >
            <!-- Fullscreen Backdrop Overlay: Menutupi seluruh layar -->
            <div
                x-show="show"
                class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity {{ $zClass }}"
                style="z-index: {{ $zLevel }};"
                x-on:click="if (window.adminLightboxOpen || document.querySelector('[data-admin-lightbox][data-open=\'true\']')) return; show = false"
                x-on:mousedown.self="if (window.adminLightboxOpen || document.querySelector('[data-admin-lightbox][data-open=\'true\']')) return;"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            ></div>

            <!-- Centering Wrapper: Safe padding p-6 sm:p-8 -->
            <div
                class="fixed inset-0 {{ $zClass }} {{ $isAdmin ? 'lg:pl-64' : '' }} flex items-center justify-center p-6 sm:p-8 cursor-pointer"
                style="z-index: {{ $zLevel }};"
                x-on:click="if (window.adminLightboxOpen || document.querySelector('[data-admin-lightbox][data-open=\'true\']')) return; show = false"
                x-on:mousedown.self="if (window.adminLightboxOpen || document.querySelector('[data-admin-lightbox][data-open=\'true\']')) return;"
            >
                <div
                    x-show="show"
                    class="bg-white rounded-2xl overflow-hidden shadow-2xl relative border border-gray-100 transform transition-all w-full {{ $maxWidth }} my-auto max-h-[85vh] max-h-[85dvh] flex flex-col cursor-default"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-on:click.stop
                >
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>
