@props(['padding' => 'p-6'])

<div {{ $attributes->merge(['class' => "bg-white rounded-xl shadow-sm border border-gray-100 $padding"]) }}>
    {{ $slot }}
</div>