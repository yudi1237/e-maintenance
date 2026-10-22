<?php

namespace App\Models;

use App\Support\DamageCauseCategory;
use App\Support\RepairStatus;
use App\Support\WorkShift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceReport extends Model
{
    protected $fillable = [
        'report_date',
        'machine_number',
        'machine_name_id',
        'machine_name_text',
        'damage_category_id',
        'damage_category_other',
        'mold_assy_utility',
        'user_id',
        'pic_name',
        'work_shift',
        'repair_started_at',
        'repair_finished_at',
        'masalah',
        'analisa_penyebab_kerusakan',
        'damage_cause_category',
        'tindakan_perbaikan',
        'tindakan_perbaikan_2',
        'evidence_path',
        'penggunaan_sparepart',
        'sparepart_items',
        'ada_op',
        'status_perbaikan',
        'step2_completed_at',
        'operator_report_id',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'repair_started_at' => 'datetime',
            'repair_finished_at' => 'datetime',
            'ada_op' => 'boolean',
            'sparepart_items' => 'array',
            'step2_completed_at' => 'datetime',
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

    public function damageCategory(): BelongsTo
    {
        return $this->belongsTo(DamageCategory::class);
    }

    public function operatorReport(): BelongsTo
    {
        return $this->belongsTo(OperatorReport::class);
    }

    public function scopeActiveStatus($query)
    {
        return $query->whereIn('status_perbaikan', RepairStatus::activeValues());
    }

    public function scopeClosedStatus($query)
    {
        return $query->where('status_perbaikan', RepairStatus::CLOSE);
    }

    public function scopeToday($query, string $timezone = 'Asia/Jakarta')
    {
        return $query->whereDate(
            'report_date',
            now()->timezone($timezone)->toDateString()
        );
    }

    public const SPAREPART_NONE_LABEL = 'Tidak ada';

    public function isStep2Completed(): bool
    {
        return $this->step2_completed_at !== null;
    }

    /**
     * @param  array<int, array{name?: mixed, qty?: mixed}>  $items
     */
    public static function formatSparepartItems(array $items): string
    {
        $lines = [];

        foreach ($items as $item) {
            $name = trim((string) ($item['name'] ?? ''));
            $qty = (int) ($item['qty'] ?? 0);
            if ($name === '' || $qty < 1) {
                continue;
            }

            $lines[] = '- '.$name.' x '.$qty.' pcs';
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<int, array{name?: mixed, qty?: mixed}>  $items
     * @return array{text: string, items: ?array<int, array{name: string, qty: int}>}
     */
    public static function persistSparepart(string $choice, array $items): array
    {
        if ($choice !== 'sparepart') {
            return [
                'text' => self::SPAREPART_NONE_LABEL,
                'items' => null,
            ];
        }

        $normalized = [];
        foreach ($items as $item) {
            $name = trim((string) ($item['name'] ?? ''));
            $qty = (int) ($item['qty'] ?? 0);
            if ($name === '' || $qty < 1) {
                continue;
            }
            $normalized[] = ['name' => $name, 'qty' => $qty];
        }

        return [
            'text' => self::formatSparepartItems($normalized),
            'items' => $normalized,
        ];
    }

    public function getMachineNameDisplayAttribute(): string
    {
        return $this->exportMachineName();
    }

    public function exportMachineName(): string
    {
        $this->loadMissing('machineName');

        if ($this->machineName) {
            return $this->machineName->viewLabel();
        }

        if ($this->machine_name_text) {
            return $this->machine_name_text;
        }

        return 'Mesin '.$this->machine_number;
    }

    public function exportMachineNameMaster(): string
    {
        $this->loadMissing('machineName');

        return $this->machineName?->name ?? '';
    }

    public function exportMachineNameManual(): string
    {
        return $this->machine_name_text ?? '';
    }

    public function getDamageCategoryDisplayAttribute(): string
    {
        $label = $this->damageCategory?->displayLabel() ?? '-';

        if ($this->damage_category_other) {
            return $label.' — '.$this->damage_category_other;
        }

        return $label;
    }

    public function getDamageCauseCategoryLabelAttribute(): string
    {
        return DamageCauseCategory::label($this->damage_cause_category);
    }

    public function getWorkShiftLabelAttribute(): string
    {
        return WorkShift::labels()[$this->work_shift] ?? $this->work_shift;
    }

    public function getStatusPerbaikanLabelAttribute(): string
    {
        return RepairStatus::labels()[$this->status_perbaikan] ?? $this->status_perbaikan;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return RepairStatus::badgeClass($this->status_perbaikan ?? RepairStatus::OPEN);
    }

    public function getStatusBadgeClassDarkAttribute(): string
    {
        return RepairStatus::badgeClassDark($this->status_perbaikan ?? RepairStatus::OPEN);
    }

    public function getAdaOpLabelAttribute(): string
    {
        return $this->ada_op ? 'Yes' : 'No';
    }

    public function getEvidenceUrlAttribute(): ?string
    {
        return $this->evidence_path ? asset('storage/'.$this->evidence_path) : null;
    }
}
