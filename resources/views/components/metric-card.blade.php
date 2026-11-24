@props([
    'label',
    'value',
    'bg' => 'bg-indigo-50',
    'text' => 'text-indigo-600',
    'valueClass' => 'text-slate-900',
])

<div {{ $attributes->merge(['class' => 'bg-white border border-slate-200/90 rounded-2xl px-4 py-3 sm:py-3.5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/10 hover:border-indigo-200/80 flex items-center gap-3.5 min-h-[74px] h-auto w-full sm:w-auto sm:min-w-[185px] shrink-0']) }}>
    <div class="w-10 h-10 rounded-xl {{ $bg }} {{ $text }} flex items-center justify-center shrink-0">
        {{ $slot }}
    </div>
    <div class="min-w-0 flex-1">
        <div class="text-lg sm:text-xl font-extrabold {{ $valueClass }} leading-tight tracking-tight break-words">{{ $value }}</div>
        <div class="text-[11px] sm:text-xs text-slate-600 font-medium mt-1 leading-snug break-words whitespace-normal">{{ $label }}</div>
    </div>
</div>
