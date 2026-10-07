<x-layouts.eo title="Profil Organisasi — Vola">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Profil Organisasi</h1>
        <p class="text-gray-500 mt-1">Kelola informasi organisasi Anda.</p>
    </div>

    <x-card>
        <x-empty-state
            title="Profil Organisasi"
            description="Fitur ini sedang dalam pengembangan oleh tim Fase 1. Hubungi admin jika membutuhkan perubahan data organisasi."
        >
            <x-slot name="action">
                <a href="{{ route('eo.dashboard') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                    Kembali ke Beranda
                </a>
            </x-slot>
        </x-empty-state>
    </x-card>
</x-layouts.eo>
