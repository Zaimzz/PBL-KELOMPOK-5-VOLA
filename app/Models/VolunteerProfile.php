<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VolunteerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'institution',
        'city',
        'photo_path',
        'skills',
        'experience',
        'cv_path',
        'portfolio_path',
    ];

    protected $appends = [
        'photo_url',
        'cv_url',
        'portfolio_url',
    ];

    /**
     * URL publik foto profil relawan (Supabase Storage atau SVG default).
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => MediaStorage::avatarUrl($this->photo_path),
        );
    }

    /**
     * URL berkas CV relawan.
     */
    protected function cvUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => MediaStorage::documentUrl($this->cv_path),
        );
    }

    /**
     * URL berkas portofolio relawan.
     */
    protected function portfolioUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => MediaStorage::documentUrl($this->portfolio_path),
        );
    }

    protected function casts(): array
    {
        return [
            'skills' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
