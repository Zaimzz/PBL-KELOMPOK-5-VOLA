<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\Cache;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_profile_id',
        'position_id',
        'status',
        'cover_letter',
        'motivation',
        'applied_at',
        'decided_at',
        'decision_note',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $application) {
            if ($application->volunteer_profile_id) {
                Cache::forget("crew_nav_counts_{$application->volunteer_profile_id}");
            }
        });

        static::deleted(function (self $application) {
            if ($application->volunteer_profile_id) {
                Cache::forget("crew_nav_counts_{$application->volunteer_profile_id}");
            }
        });
    }

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'applied_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function volunteerProfile(): BelongsTo
    {
        return $this->belongsTo(VolunteerProfile::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(EventPosition::class, 'position_id');
    }

    /**
     * Relasi ke Event melalui EventPosition
     */
    public function event(): HasOneThrough
    {
        return $this->hasOneThrough(
            Event::class,
            EventPosition::class,
            'id',          // Foreign key di event_positions (primary key)
            'id',          // Foreign key di events (primary key)
            'position_id', // Local key di applications
            'event_id'     // Local key di event_positions
        );
    }

    /**
     * Alias accessor & mutator untuk motivation -> cover_letter.
     * Memastikan kompatibilitas antara input form 'motivation' dan kolom database 'cover_letter'.
     */
    protected function motivation(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cover_letter,
            set: fn ($value) => ['cover_letter' => $value],
        );
    }
}
