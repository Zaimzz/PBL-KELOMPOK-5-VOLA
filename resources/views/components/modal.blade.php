@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
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
@endphp

<div
    x-data="{
        show: @js($show),
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
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
            $dispatch('modal-closed', '{{ $name }}');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.window="$event.detail == '{{ $name }}' || !$event.detail ? show = false : null"
    x-on:close="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
>
    <template x-teleport="body">
        <div
            x-show="show"
            x-cloak
            class="fixed inset-0 z-[9999] overflow-y-auto overscroll-y-contain"
            style="display: {{ $show ? 'block' : 'none' }};"
        >
            <!-- Fullscreen Backdrop Overlay: WAJIB menutupi 100% seluruh layar termasuk Top Navbar dan Sidebar kiri -->
            <div
                x-show="show"
                class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
                x-on:click="show = false"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            ></div>

            <!-- Centering Wrapper: Full viewport overlay dengan safe padding minimal 24px-32px (p-6 sm:p-8) -->
            <div
                class="fixed inset-0 z-[9999] {{ $isAdmin ? 'lg:pl-64' : '' }} flex items-center justify-center p-6 sm:p-8 cursor-pointer"
                x-on:click="show = false"
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
                    @click.outside="show = false"
                >
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>
