@props(['type' => 'info'])

@php
    $styles = match ($type) {
        'success' => 'bg-green-50 text-green-800 border-green-200',
        'error' => 'bg-red-50 text-red-800 border-red-200',
        'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
        default => 'bg-blue-50 text-blue-800 border-blue-200',
    };
@endphp

<div {{ $attributes->merge(['class' => "border rounded-lg px-4 py-3 text-sm $styles"]) }}>
    {{ $slot }}
</div>