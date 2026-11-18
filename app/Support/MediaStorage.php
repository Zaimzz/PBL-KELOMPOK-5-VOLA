<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaStorage
{
    /**
     * URL publik default Supabase Storage bucket 'posters'.
     */
    public const DEFAULT_SUPABASE_PUBLIC_URL = 'https://wumumuzcpelkqazhdkcx.supabase.co/storage/v1/object/public/posters';

    /**
     * Resolusi URL publik seragam untuk berkas media di Supabase Storage.
     *
     * @param  string|null  $path  Path berkas (relatif atau full URL)
     * @param  string|null  $fallback  Fallback URL jika path kosong
     */
    public static function url(?string $path, ?string $fallback = null): ?string
    {
        if (blank($path) || $path === 'null') {
            return $fallback;
        }

        $trimmed = trim($path);

        // Jika sudah merupakan full URL (misal Google Avatar atau URL eksternal)
        if (Str::startsWith($trimmed, ['http://', 'https://', '//'])) {
            return $trimmed;
        }

        // Hapus prefix storage/ jika ada
        $cleanPath = ltrim(preg_replace('#^storage/#', '', $trimmed), '/');

        // Ambil base public URL dari config atau env Supabase
        $baseUrl = config('filesystems.disks.supabase.url')
            ?: config('filesystems.disks.public.url')
            ?: env('SUPABASE_STORAGE_PUBLIC_URL', self::DEFAULT_SUPABASE_PUBLIC_URL);

        if (! empty($baseUrl)) {
            return rtrim((string) $baseUrl, '/').'/'.$cleanPath;
        }

        return Storage::disk('public')->url($cleanPath);
    }

    /**
     * Resolusi URL poster / banner event dengan fallback SVG placeholder.
     */
    public static function posterUrl(?string $path): string
    {
        return self::url($path, asset('images/event-placeholder.svg')) ?? asset('images/event-placeholder.svg');
    }

    /**
     * Resolusi URL foto profil / avatar pengguna dengan fallback SVG placeholder.
     */
    public static function avatarUrl(?string $path): string
    {
        return self::url($path, asset('images/avatar-placeholder.svg')) ?? asset('images/avatar-placeholder.svg');
    }

    /**
     * Resolusi URL dokumen (PDF / legalitas EO / CV / Portfolio).
     */
    public static function documentUrl(?string $path, ?string $fallback = null): ?string
    {
        return self::url($path, $fallback);
    }

    /**
     * Resolusi URL berkas bukti pendukung laporan (evidence).
     */
    public static function evidenceUrl(?string $path, ?string $fallback = null): ?string
    {
        return self::url($path, $fallback);
    }

    /**
     * Cek apakah path atau nama berkas merupakan format gambar.
     */
    public static function isImage(?string $path): bool
    {
        if (blank($path)) {
            return false;
        }

        $extension = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?? $path, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'bmp', 'avif'], true);
    }

    /**
     * Menghapus berkas dari storage secara aman tanpa melempar uncaught exception.
     */
    public static function deleteSafe(?string $path, string $disk = 'public'): bool
    {
        if (blank($path) || Str::startsWith(trim($path), ['http://', 'https://', '//'])) {
            return true;
        }

        $cleanPath = ltrim(preg_replace('#^storage/#', '', trim($path)), '/');

        try {
            return Storage::disk($disk)->delete($cleanPath);
        } catch (\Throwable $e) {
            Log::warning('MediaStorage::deleteSafe gagal menghapus berkas di storage: '.$e->getMessage(), [
                'path' => $cleanPath,
                'disk' => $disk,
            ]);

            return false;
        }
    }
}
