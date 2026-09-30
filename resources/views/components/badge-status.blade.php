@props(['status' => 'pending'])

@php
    $styles = match ($status) {
        'active', 'accepted', 'verified', 'paid' => 'bg-green-100 text-green-700',
        'pending', 'pending_review', 'awaiting_payment' => 'bg-yellow-100 text-yellow-700',
        'rejected', 'failed', 'expired', 'withdrawn', 'suspended' => 'bg-red-100 text-red-700',
        'draft' => 'bg-gray-100 text-gray-600',
        'closed', 'finished', 'cancelled' => 'bg-gray-200 text-gray-700',
        default => 'bg-gray-100 text-gray-600',
    };

    $labels = [
        'draft' => 'Draft',
        'pending_review' => 'Menunggu Review',
        'awaiting_payment' => 'Menunggu Pembayaran',
        'active' => 'Aktif',
        'closed' => 'Ditutup',
        'finished' => 'Selesai',
        'rejected' => 'Ditolak',
        'pending' => 'Menunggu',
        'accepted' => 'Diterima',
        'withdrawn' => 'Dibatalkan',
        'verified' => 'Terverifikasi',
        'paid' => 'Lunas',
        'failed' => 'Gagal',
        'expired' => 'Kedaluwarsa',
        'cancelled' => 'Dibatalkan',
        'suspended' => 'Ditangguhkan',
    ];

    $label = $labels[$status] ?? ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium $styles"]) }}>
    {{ $label }}
</span>