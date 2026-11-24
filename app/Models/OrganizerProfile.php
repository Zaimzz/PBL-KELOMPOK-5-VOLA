<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'organization_name', 'organization_type', 'description',
        'city', 'pic_name', 'pic_phone', 'social_link', 'document_path',
        'verification_status', 'verified_by', 'verified_at', 'rejection_reason',
    ];

    protected $appends = [
        'document_url',
        'document_type',
        'avatar_url',
        'logo_url',
    ];

    /**
     * URL publik dokumen legalitas EO (Supabase Storage).
     */
    protected function documentUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => MediaStorage::documentUrl($this->document_path),
        );
    }

    /**
     * Tipe dokumen legalitas EO ('image' atau 'pdf').
     */
    protected function documentType(): Attribute
    {
        return Attribute::make(
            get: fn (): string => MediaStorage::isImage($this->document_path) ? 'image' : 'pdf',
        );
    }

    /**
     * URL publik foto profil / logo EO.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => MediaStorage::avatarUrl($this->user?->avatar_path),
        );
    }

    /**
     * Alias logo_url untuk avatar EO.
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->avatar_url,
        );
    }

    protected function casts(): array
    {
        return [
            'verification_status' => VerificationStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }
}
