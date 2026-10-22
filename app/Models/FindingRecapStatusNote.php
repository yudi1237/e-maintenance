<?php

namespace App\Models;

use App\Support\FindingRecapStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FindingRecapStatusNote extends Model
{
    protected $fillable = [
        'finding_recap_id',
        'user_id',
        'status',
        'note',
    ];

    public function findingRecap(): BelongsTo
    {
        return $this->belongsTo(FindingRecap::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return FindingRecapStatus::label($this->status);
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->user?->name ?? 'Admin';
    }
}
