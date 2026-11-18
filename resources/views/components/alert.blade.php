@props([
    'type' => 'info',
    'dismissible' => true,
    'autoDismiss' => null,
])

@php
    // Auto-dismiss setelah 4 detik secara default untuk notifikasi flash tipe success
    if (is_null($autoDismiss)) {
        $autoDismiss = ($type === 'success');
    }

    $styles = match ($type) {
        'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80 shadow-2xs',
        'error' => 'bg-rose-50 text-rose-800 border-rose-200/80 shadow-2xs',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-200/80 shadow-2xs',
        default => 'bg-blue-50 text-blue-800 border-blue-200/80 shadow-2xs',
    };

    $iconColor = match ($type) {
        'success' => 'text-emerald-600',
        'error' => 'text-rose-600',
        'warning' => 'text-amber-600',
        default => 'text-blue-600',
    };
@endphp

<div x-data="{
        show: true,
        timer: null,
        init() {
            @if($autoDismiss)
                this.timer = setTimeout(() => {
                    this.close();
                }, 4000);
            @endif
        },
        close() {
            this.show = false;
            clearTimeout(this.timer);
            setTimeout(() => {
                if (this.$el && this.$el.parentNode) {
                    this.$el.remove();
                }
            }, 350);
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
    {{ $attributes->merge(['class' => "border rounded-2xl px-4 py-3 text-xs sm:text-sm font-medium flex items-center justify-between gap-3 transition-all $styles"]) }}>
    
    <div class="flex items-center gap-2.5 min-w-0">
        @if($type === 'success')
            <svg class="w-5 h-5 {{ $iconColor }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        @elseif($type === 'error')
            <svg class="w-5 h-5 {{ $iconColor }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        @elseif($type === 'warning')
            <svg class="w-5 h-5 {{ $iconColor }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        @else
            <svg class="w-5 h-5 {{ $iconColor }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        @endif

        <div class="leading-snug">
            {{ $slot }}
        </div>
    </div>

    @if($dismissible)
        <button type="button" 
                @click="close()" 
                class="w-6 h-6 rounded-lg flex items-center justify-center text-current opacity-60 hover:opacity-100 hover:bg-black/5 active:scale-95 transition-all shrink-0 ml-auto cursor-pointer"
                aria-label="Tutup notifikasi">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    @endif
</div>