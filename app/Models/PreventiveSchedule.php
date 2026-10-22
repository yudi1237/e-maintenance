<?php

namespace App\Models;

use App\Support\PreventiveCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class PreventiveSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'machine_name_id',
        'machine_number',
        'machine_name',
        'machine_type',
        'pic_name',
        'plan_date',
        'preventive_type',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'plan_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function machineName(): BelongsTo
    {
        return $this->belongsTo(MachineName::class);
    }

    public function getPreventiveTypeLabelAttribute(): string
    {
        return PreventiveCategory::label($this->preventive_type);
    }

    public function getScheduleGroupKeyAttribute(): ?string
    {
        return PreventiveCategory::scheduleGroupKey($this->preventive_type);
    }

    public function getScheduleGroupLabelAttribute(): string
    {
        return PreventiveCategory::scheduleGroupLabel($this->preventive_type);
    }

    public function getMachineNameDisplayAttribute(): string
    {
        $this->loadMissing('machineName');

        return $this->machineName?->viewLabel()
            ?: $this->machine_name
            ?: 'Mesin '.$this->machine_number;
    }

    public function scopeOpen(Builder $query): Builder
    {
        $closedIds = static::closedIds();

        if ($closedIds === []) {
            return $query;
        }

        return $query->whereNotIn($query->qualifyColumn('id'), $closedIds);
    }

    /**
     * One report closes every still-open schedule of the same machine and type
     * whose plan month is before the work month. Remaining reports then close
     * current/future schedules one-to-one, earliest plan date first.
     *
     * @return list<int>
     */
    public static function closedIds(): array
    {
        $remaining = static::query()
            ->orderBy('plan_date')
            ->orderBy('id')
            ->get()
            ->keyBy('id');

        $reports = PreventiveReport::query()
            ->orderBy('work_date')
            ->orderBy('id')
            ->get(['id', 'machine_number', 'category', 'work_date']);

        $closed = [];

        foreach ($reports as $report) {
            $machineNumber = (int) $report->machine_number;
            $type = (string) $report->category;
            $workMonth = substr($report->work_date->toDateString(), 0, 7);

            $overdue = $remaining->filter(function (self $schedule) use ($machineNumber, $type, $workMonth): bool {
                return (int) $schedule->machine_number === $machineNumber
                    && $schedule->preventive_type === $type
                    && substr($schedule->plan_date->toDateString(), 0, 7) < $workMonth;
            });

            if ($overdue->isNotEmpty()) {
                foreach ($overdue as $schedule) {
                    $closed[] = $schedule->id;
                    $remaining->forget($schedule->id);
                }

                continue;
            }

            $next = $remaining->first(function (self $schedule) use ($machineNumber, $type): bool {
                return (int) $schedule->machine_number === $machineNumber
                    && $schedule->preventive_type === $type;
            });

            if ($next) {
                $closed[] = $next->id;
                $remaining->forget($next->id);
            }
        }

        return $closed;
    }

    public function scopePriorityByPlanDate(Builder $query, ?Carbon $today = null): Builder
    {
        $todayDate = ($today ?? now()->timezone('Asia/Jakarta'))->toDateString();

        return $query
            ->orderByRaw('case when plan_date < ? then 0 else 1 end', [$todayDate])
            ->orderBy('plan_date')
            ->orderBy('id');
    }

    public function technicianStatus(?Carbon $today = null): string
    {
        $todayDate = ($today ?? now()->timezone('Asia/Jakarta'))->toDateString();
        $planDate = $this->plan_date->toDateString();

        if ($planDate < $todayDate) {
            return 'overdue';
        }

        if ($planDate === $todayDate) {
            return 'today';
        }

        return 'open';
    }

    public function getTechnicianStatusLabelAttribute(): string
    {
        return match ($this->technicianStatus()) {
            'overdue' => 'Overdue',
            'today' => 'Hari ini',
            default => 'Open',
        };
    }

    public function getTechnicianStatusBadgeClassAttribute(): string
    {
        return match ($this->technicianStatus()) {
            'overdue' => 'em-status-badge-overdue',
            'today' => 'em-status-badge-today',
            default => 'em-status-badge-open',
        };
    }

    public function scopeForPlanMonth(Builder $query, ?string $yearMonth): Builder
    {
        if (! filled($yearMonth) || ! preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            return $query;
        }

        [$year, $month] = explode('-', $yearMonth);

        return $query
            ->whereYear('plan_date', (int) $year)
            ->whereMonth('plan_date', (int) $month);
    }

    public function scopeForScheduleGroup(Builder $query, ?string $group): Builder
    {
        $types = array_keys(PreventiveCategory::typesForScheduleGroup($group));

        if ($types === []) {
            return $query;
        }

        return $query->whereIn($query->qualifyColumn('preventive_type'), $types);
    }
}
