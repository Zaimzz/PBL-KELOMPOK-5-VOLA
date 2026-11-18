<?php

namespace App\Models;

use App\Enums\ReportStatus;
use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'reporter_id',
        'reported_id',
        'reason',
        'description',
        'evidence_path',
        'status',
        'action_taken',
        'admin_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $appends = [
        'evidence_url',
        'evidence_is_image',
    ];

    /**
     * URL publik berkas bukti laporan (Supabase Storage).
     */
    protected function evidenceUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => MediaStorage::evidenceUrl($this->evidence_path),
        );
    }

    /**
     * Cek apakah bukti berupa file gambar.
     */
    protected function evidenceIsImage(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => MediaStorage::isImage($this->evidence_path),
        );
    }

    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reported(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
