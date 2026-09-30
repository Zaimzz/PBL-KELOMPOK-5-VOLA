<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VolunteerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'bio', 'institution', 'city',
        'skills', 'experience', 'cv_path', 'portfolio_path',
    ];

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
