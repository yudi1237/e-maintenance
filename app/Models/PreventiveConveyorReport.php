<?php

namespace App\Models;

use App\Support\PreventiveConveyorChecklist;
use App\Support\PreventiveEvidence;
use App\Support\WorkShift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreventiveConveyorReport extends Model
{
    protected $fillable = [
        'user_id',
        'pic_name',
        'conveyor_name',
        'work_date',
        'work_shift',
        'team_members',
        'items',
        'item_remarks',
        'notes',
        'penggunaan_sparepart',
        'sparepart_items',
        'evidence_paths',
        'result',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'team_members' => 'array',
            'items' => 'array',
            'item_remarks' => 'array',
            'sparepart_items' => 'array',
            'evidence_paths' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function itemLabel(string $key): string
    {
        return PreventiveConveyorChecklist::items()[$key] ?? $key;
    }

    public function itemValueLabel(string $key): string
    {
        $value = $this->items[$key] ?? null;

        return PreventiveConveyorChecklist::valueLabels()[$value] ?? '-';
    }
}
