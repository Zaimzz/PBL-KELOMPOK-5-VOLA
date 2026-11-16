<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PositionType extends Model
{
    use HasFactory;

    public const OTHER_CATEGORY = 'Lainnya';

    protected $fillable = [
        'name', 'category', 'default_description', 'default_requirements', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function eventPositions(): HasMany
    {
        return $this->hasMany(EventPosition::class);
    }

    /**
     * @param  Builder<PositionType>  $query
     * @return Builder<PositionType>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Cari rekomendasi deskripsi dan kualifikasi berdasarkan nama posisi.
     * Prioritas: nama yang sama persis dengan katalog, lalu kata kunci umum.
     * Mengembalikan null jika tidak ada rekomendasi yang relevan.
     *
     * @return array{description: string, requirements: string}|null
     */
    public static function recommendationFor(string $name): ?array
    {
        $normalized = Str::lower(trim((string) preg_replace('/\s+/', ' ', $name)));

        if ($normalized === '') {
            return null;
        }

        $catalogMatch = static::query()
            ->active()
            ->whereRaw('LOWER(name) = ?', [$normalized])
            ->first();

        if ($catalogMatch) {
            return [
                'description' => (string) $catalogMatch->default_description,
                'requirements' => (string) $catalogMatch->default_requirements,
            ];
        }

        foreach ((array) config('position_recommendations', []) as $entry) {
            foreach ($entry['keywords'] as $keyword) {
                if (preg_match('/\b'.preg_quote($keyword, '/').'\b/u', $normalized) === 1) {
                    return [
                        'description' => $entry['description'],
                        'requirements' => implode("\n", $entry['requirements']),
                    ];
                }
            }
        }

        return null;
    }
}
