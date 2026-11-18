<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'position_type_id', 'name', 'category', 'description', 'requirements', 'quota',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function positionType(): BelongsTo
    {
        return $this->belongsTo(PositionType::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'position_id');
    }
}
