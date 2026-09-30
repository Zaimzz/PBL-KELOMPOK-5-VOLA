<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_profile_id', 'position_id', 'status', 'cover_letter',
        'applied_at', 'decided_at', 'decision_note',
    ];

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
}
