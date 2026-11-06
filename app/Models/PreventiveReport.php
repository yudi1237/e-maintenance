<?php

namespace App\Models;

use App\Support\PreventiveCategory;
use App\Support\PreventiveEvidence;
use App\Support\PreventiveMachineChecklist;
use App\Support\PreventiveStatus;
use App\Support\WorkShift;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreventiveReport extends Model
{
    protected $fillable = [
        'user_id',
        'pic_name',
        'machine_name_id',
        'machine_number',
        'equipment_number',
        'equipment_id',
        'equipment_type',
        'robot_id',
        'robot_number',
        'robot_type',
        'category',
        'work_date',
        'work_shift',
        'team_members',
        'items',
        'readings',
        'item_remarks',
        'notes',
        'penggunaan_sparepart',
        'sparepart_items',
        'evidence_paths',
        'result',
        'status',
        'injection_approved_by',
        'quality_approved_by',
        'maintenance_approved_by',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'team_members' => 'array',
            'items' => 'array',
            'readings' => 'array',
            'item_remarks' => 'array',
            'sparepart_items' => 'array',
            'evidence_paths' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function injectionApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'injection_approved_by');
    }

    public function qualityApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'quality_approved_by');
    }

    public function maintenanceApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'maintenance_approved_by');
    }

    public function machineName(): BelongsTo
    {
        return $this->belongsTo(MachineName::class);
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('work_date')->orderByDesc('id');
    }

    public function robot(): BelongsTo
    {
        return $this->belongsTo(Robot::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function getEquipmentLabelAttribute(): string
    {
        if ($this->category === 'ROBOT') {
            return $this->robot_label;
        }

        $this->loadMissing('equipment');

        if ($this->equipment) {
            return $this->equipment->label;
        }

        if (filled($this->equipment_number) && filled($this->equipment_type)) {
            return $this->equipment_number.' — '.$this->equipment_type;
        }

        return filled($this->equipment_number) ? (string) $this->equipment_number : '-';
    }

    public function getRobotLabelAttribute(): string
    {
        if (! filled($this->robot_number)) {
            return '-';
        }

        return filled($this->robot_type)
            ? $this->robot_number.' — '.$this->robot_type
            : (string) $this->robot_number;
    }

    public function getWorkShiftLabelAttribute(): string
    {
        return WorkShift::labels()[$this->work_shift] ?? $this->work_shift;
    }

    public function getTeamMembersLabelAttribute(): string
    {
        $names = collect($this->team_members ?? [])
            ->map(fn ($member) => is_array($member) ? ($member['name'] ?? '') : (string) $member)
            ->filter()
            ->values();

        return $names->isEmpty() ? '—' : $names->implode(', ');
    }

    /**
     * @return list<string>
     */
    public function getEvidenceUrlsAttribute(): array
    {
        return PreventiveEvidence::urls($this->evidence_paths);
    }

    public function getResultLabelAttribute(): string
    {
        return $this->result === 'follow_up' ? 'Ada temuan' : 'OK';
    }

    public function getResultBadgeClassAttribute(): string
    {
        return $this->result === 'ok'
            ? 'bg-green-100 text-green-800'
            : 'bg-amber-100 text-amber-800';
    }

    public function getStatusLabelAttribute(): string
    {
        $status = $this->status ?: PreventiveStatus::CLOSE_BY_TECHNICIAN;

        return PreventiveStatus::labels()[$status] ?? $status;
    }

    public function getCategoryLabelAttribute(): string
    {
        if (! is_string($this->category) || $this->category === '') {
            return '-';
        }

        return PreventiveCategory::labels()[$this->category] ?? $this->category;
    }

    public function getScheduleGroupLabelAttribute(): string
    {
        return PreventiveCategory::scheduleGroupLabel($this->category);
    }

    public function getMachineNameDisplayAttribute(): string
    {
        $this->loadMissing('machineName');

        return $this->machineName?->viewLabel()
            ?: 'Mesin '.$this->machine_number;
    }

    public function itemLabel(string $key): string
    {
        return PreventiveMachineChecklist::itemLabel($this->category, $key);
    }

    public function itemValueLabel(string $key): string
    {
        $value = $this->items[$key] ?? null;

        return PreventiveMachineChecklist::itemValueLabel($this->category, $key, $value);
    }

    /**
     * @return list<array{key: string, label: string, answers: array<string, string>, items: array<string, array{label: string, standard: string, type?: string}>}>
     */
    public function checklistGroups(): array
    {
        return PreventiveMachineChecklist::groupsForReport($this->category, $this->items ?? []);
    }
}
