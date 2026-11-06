<?php

namespace App\Models;

use App\Support\PicSession;
use App\Support\RepairStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OperatorReport extends Model
{
    protected $fillable = [
        'operator_name',
        'npk',
        'item_name',
        'machine_name_id',
        'machine_number',
        'stop_date',
        'stop_time',
        'problem',
        'chronology',
        'claimed_by',
        'claimed_technician_id',
        'claimed_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'stop_date' => 'date',
            'claimed_at' => 'datetime',
        ];
    }

    public function machineName(): BelongsTo
    {
        return $this->belongsTo(MachineName::class);
    }

    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function claimedTechnician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'claimed_technician_id');
    }

    public function maintenanceReport(): HasOne
    {
        return $this->hasOne(MaintenanceReport::class);
    }

    public function scopeUnclaimed(Builder $query): Builder
    {
        return $query->whereNull('claimed_at');
    }

    public function scopeBoard(Builder $query): Builder
    {
        return $query
            ->with(['machineName', 'claimedBy', 'claimedTechnician', 'maintenanceReport'])
            ->orderByRaw(RepairStatus::dashboardTicketOrderSql(auth()->user()))
            ->orderByDesc('id');
    }

    public function scopeForDashboard(Builder $query, ?User $user = null): Builder
    {
        $orderSql = RepairStatus::dashboardTicketOrderSql($user ?? auth()->user());

        $query
            ->with([
                'machineName',
                'claimedBy',
                'claimedTechnician',
                'maintenanceReport.injectionApprover',
                'maintenanceReport.qualityApprover',
                'maintenanceReport.maintenanceApprover',
            ]);

        if ($orderSql !== null) {
            $query->orderByRaw($orderSql);
        }

        return $query->orderByDesc('id');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', RepairStatus::OPEN);
    }

    public function scopeClaimableBy(Builder $query, int $userId, ?int $technicianId = null): Builder
    {
        $query
            ->where('status', RepairStatus::ON_PROGRESS)
            ->where(function (Builder $inner) {
                $inner->whereDoesntHave('maintenanceReport')
                    ->orWhereHas('maintenanceReport', function (Builder $report) {
                        $report->whereNull('step2_completed_at');
                    });
            });

        if ($technicianId) {
            return $query->where('claimed_technician_id', $technicianId);
        }

        return $query
            ->where('claimed_by', $userId)
            ->whereNull('claimed_technician_id');
    }

    public function isOpen(): bool
    {
        return $this->status === RepairStatus::OPEN;
    }

    public function isOnProgress(): bool
    {
        return $this->status === RepairStatus::ON_PROGRESS;
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user !== null && (int) $this->claimed_by === (int) $user->id;
    }

    public function isOwnedByPic(?Technician $technician): bool
    {
        return $technician !== null && (int) $this->claimed_technician_id === (int) $technician->id;
    }

    public function canAttemptCloseByTechnician(?User $user): bool
    {
        if (! $this->isOnProgress() || $user === null || ! $user->isTechnician()) {
            return false;
        }

        return $this->claimed_technician_id !== null || $this->isOwnedBy($user);
    }

    public function canCloseByTechnician(?User $user): bool
    {
        if (! $this->canAttemptCloseByTechnician($user)) {
            return false;
        }

        if ($this->claimed_technician_id) {
            return PicSession::matches($this->claimed_technician_id)
                && PicSession::isCloseAuthorized($this->id);
        }

        return $this->isOwnedBy($user) && PicSession::isCloseAuthorized($this->id);
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

    public function getStatusBadgeClassDarkAttribute(): string
    {
        return RepairStatus::badgeClassDark($this->status ?? RepairStatus::OPEN);
    }

    public function isClaimed(): bool
    {
        return $this->claimed_at !== null;
    }

    public function getMachineNameDisplayAttribute(): string
    {
        $this->loadMissing('machineName');

        return $this->machineName?->viewLabel()
            ?: 'Mesin '.$this->machine_number;
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

    public function getTicketLabelAttribute(): string
    {
        return sprintf(
            '#%d · Mesin %s — %s · %s · %s',
            $this->id,
            $this->machine_number,
            $this->machine_name_display,
            $this->item_name,
            $this->stop_date_time_label
        );
    }
}
