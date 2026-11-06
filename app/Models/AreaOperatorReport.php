<?php

namespace App\Models;

use App\Support\RepairStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaOperatorReport extends Model
{
    protected $fillable = [
        'area',
        'reporter_name',
        'part_name',
        'equipment_name',
        'stop_date',
        'stop_time',
        'problem',
        'chronology',
        'claimed_by',
        'claimed_technician_id',
        'claimed_at',
        'closed_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'stop_date' => 'date',
            'claimed_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function claimedTechnician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'claimed_technician_id');
    }

    public function scopeBoard(Builder $query): Builder
    {
        return $query
            ->with(['claimedBy', 'claimedTechnician'])
            ->orderByRaw("case status when 'open' then 0 when 'on_progress' then 1 when 'close_by_technician' then 2 else 3 end")
            ->orderByDesc('id');
    }

    public function isOpen(): bool
    {
        return $this->status === RepairStatus::OPEN;
    }

    public function isOnProgress(): bool
    {
        return $this->status === RepairStatus::ON_PROGRESS;
    }

    public function canAttemptCloseByTechnician(?User $user): bool
    {
        return $this->isOnProgress()
            && $user?->isTechnician()
            && $this->claimed_technician_id !== null;
    }

    public function getPicNameAttribute(): string
    {
        $this->loadMissing(['claimedTechnician', 'claimedBy']);

        return $this->claimedTechnician?->name
            ?: $this->claimedBy?->name
            ?: 'Belum diambil';
    }

    public function getStatusLabelAttribute(): string
    {
        return RepairStatus::labels()[$this->status] ?? $this->status;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return RepairStatus::badgeClassDark($this->status ?? RepairStatus::OPEN);
    }

    public function getStopTimeLabelAttribute(): string
    {
        return substr((string) $this->stop_time, 0, 5);
    }

    public function getStopDateTimeLabelAttribute(): string
    {
        $date = $this->stop_date?->format('d/m/Y') ?? '—';

        return trim($date.' '.$this->stop_time_label);
    }
}
