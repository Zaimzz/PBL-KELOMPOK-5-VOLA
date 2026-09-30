@props(['title' => 'Belum ada data', 'description' => null, 'icon' => null])

<div class="text-center py-12">
    @if ($icon)
        <div class="mx-auto w-12 h-12 text-gray-300 mb-4">
            {{ $icon }}
        </div>
    @endif

    <h3 class="text-sm font-medium text-gray-900">{{ $title }}</h3>

    @if ($description)
        <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
    @endif

    @if (isset($action))
        <div class="mt-4">
            {{ $action }}
        </div>
    @endif
</div>