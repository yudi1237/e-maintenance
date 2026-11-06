<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MachineName extends Model
{
    protected $fillable = [
        'machine_number',
        'asset_number',
        'screw_diameter_mm',
        'machine_photos',
        'brand',
        'machine_model',
        'tonnage',
        'clamping_type',
        'machine_type',
        'tie_bar_distance_mm',
        'platen_size_mm',
        'mold_thickness_min_mm',
        'mold_thickness_max_mm',
        'clamp_stroke_mm',
        'power_pump_kw',
        'power_heater_kw',
        'power_total_kw',
        'shot_weight_small_gr',
        'shot_weight_med_gr',
        'manufacture_year',
        'name',
        'mold_id',
        'plant',
        'specification',
        'is_active',
        'severity_conditions',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'severity_conditions' => 'array',
            'machine_number' => 'integer',
            'screw_diameter_mm' => 'integer',
            'machine_photos' => 'array',
            'tonnage' => 'integer',
            'mold_thickness_min_mm' => 'integer',
            'mold_thickness_max_mm' => 'integer',
            'clamp_stroke_mm' => 'integer',
            'power_pump_kw' => 'decimal:2',
            'power_heater_kw' => 'decimal:2',
            'power_total_kw' => 'decimal:2',
            'shot_weight_small_gr' => 'integer',
            'shot_weight_med_gr' => 'integer',
            'manufacture_year' => 'integer',
        ];
    }

    public function maintenanceReports(): HasMany
    {
        return $this->hasMany(MaintenanceReport::class);
    }

    public function operatorReports(): HasMany
    {
        return $this->hasMany(OperatorReport::class);
    }

    public function findingRecaps(): HasMany
    {
        return $this->hasMany(FindingRecap::class);
    }

    public function preventiveSchedules(): HasMany
    {
        return $this->hasMany(PreventiveSchedule::class);
    }

    public const PLANT_CIKARANG = 'plant_1_cikarang';

    public const PLANT_KARAWACI = 'plant_2_karawaci';

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOperational($query)
    {
        return $query->where('plant', self::PLANT_CIKARANG);
    }

    /**
     * @return array<string, string>
     */
    public static function plantOptions(): array
    {
        return [
            self::PLANT_CIKARANG => 'Plant 1 Cikarang',
            self::PLANT_KARAWACI => 'Plant 2 Karawaci',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function severityOptions(): array
    {
        return [
            'quick_fix' => 'Quick Fix',
            'minor_issue' => 'Minor Issue',
            'medium_breakdown' => 'Medium Breakdown',
            'major_breakdown' => 'Major Breakdown',
            'total_breakdown' => 'Total Breakdown',
        ];
    }

    public function mold(): BelongsTo
    {
        return $this->belongsTo(Mold::class);
    }

    public function viewLabel(): string
    {
        $brand = trim((string) $this->brand);
        $tonnage = $this->tonnage ? $this->tonnage.'T' : '';
        $label = trim($brand.' '.$tonnage);

        if ($label !== '') {
            return $label;
        }

        return $this->name ?: ('Mesin '.($this->machine_number ?: ''));
    }

    public function getViewLabelAttribute(): string
    {
        return $this->viewLabel();
    }

    /** @return list<string> */
    public function qrCaptionLines(): array
    {
        return [
            'No Asset: '.($this->asset_number ?: '—'),
            'Brand: '.($this->brand ?: '—'),
            'Tonage: '.($this->tonnage ? $this->tonnage.'T' : '—'),
            'Machine Model: '.($this->machine_model ?: '—'),
        ];
    }
}
