<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Support\MediaStorage;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'email_verified_at', 'password', 'google_id', 'phone', 'avatar_path', 'role', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'avatar_url',
    ];

    /**
     * URL publik foto profil / avatar pengguna (Google avatar, Supabase Storage, atau SVG default).
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $path = $this->avatar_path ?? $this->volunteerProfile?->photo_path;

                return MediaStorage::avatarUrl($path);
            },
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    public function volunteerProfile(): HasOne
    {
        return $this->hasOne(VolunteerProfile::class);
    }

    public function organizerProfile(): HasOne
    {
        return $this->hasOne(OrganizerProfile::class);
    }

    public function reportsFiled(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function reportsAgainst(): HasMany
    {
        return $this->hasMany(Report::class, 'reported_id');
    }
}
