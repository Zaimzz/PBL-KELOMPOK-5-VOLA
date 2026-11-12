<?php

namespace App\Support;

/**
 * Catalogue of Vola publication packages.
 *
 * All prices are in IDR (integer rupiah).
 * Slugs are used as the canonical identifier stored in `payments.package_name`.
 */
final class PublicationPackage
{
    /** @var array<string, array{name: string, duration_days: int, price: int, description: string, badge: string|null}> */
    private const PACKAGES = [
        'professional' => [
            'slug' => 'professional',
            'name' => 'Professional',
            'duration_days' => 7,
            'price' => 35_000,
            'description' => 'Publikasi event selama 7 hari, cocok untuk event skala kecil.',
            'badge' => null,
        ],
        'business' => [
            'slug' => 'business',
            'name' => 'Business',
            'duration_days' => 14,
            'price' => 55_000,
            'description' => 'Publikasi event selama 14 hari dengan visibilitas lebih luas.',
            'badge' => null,
        ],
        'premium_business' => [
            'slug' => 'premium_business',
            'name' => 'Premium Business',
            'duration_days' => 30,
            'price' => 85_000,
            'description' => 'Publikasi event selama 30 hari, direkomendasikan untuk event menengah.',
            'badge' => 'Rekomendasi',
        ],
        'corporate' => [
            'slug' => 'corporate',
            'name' => 'Corporate',
            'duration_days' => 60,
            'price' => 125_000,
            'description' => 'Publikasi event selama 60 hari untuk event berskala besar.',
            'badge' => null,
        ],
    ];

    /**
     * Return all packages as an array of associative arrays.
     *
     * @return array<string, array{slug: string, name: string, duration_days: int, price: int, description: string, badge: string|null}>
     */
    public static function all(): array
    {
        return self::PACKAGES;
    }

    /**
     * Find a single package by slug. Returns null if the slug is unknown.
     *
     * @return array{slug: string, name: string, duration_days: int, price: int, description: string, badge: string|null}|null
     */
    public static function find(string $slug): ?array
    {
        return self::PACKAGES[$slug] ?? null;
    }

    /**
     * Return the canonical price for a package slug.
     * Throws if the slug is unknown to prevent price tampering.
     */
    public static function price(string $slug): int
    {
        $pkg = self::find($slug);

        if ($pkg === null) {
            throw new \InvalidArgumentException("Unknown package slug: {$slug}");
        }

        return $pkg['price'];
    }

    /**
     * Return all valid slugs.
     *
     * @return list<string>
     */
    public static function slugs(): array
    {
        return array_keys(self::PACKAGES);
    }
}

