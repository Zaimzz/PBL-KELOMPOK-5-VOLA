<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organizer_id', 'category_id', 'title', 'slug', 'description', 'poster_path',
        'location', 'city', 'start_date', 'end_date', 'start_time', 'end_time',
        'registration_deadline', 'contact_info', 'coordination_link', 'pic_name',
        'pic_phone', 'benefits', 'status', 'rejection_reason', 'reviewed_by',
        'reviewed_at', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => EventStatus::class,
            'benefits' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'registration_deadline' => 'date',
            'reviewed_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(OrganizerProfile::class, 'organizer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(EventPosition::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
