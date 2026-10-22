<?php

namespace App\Support;

class DamageCauseCategory
{
    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            'component_failure' => 'Component Failure',
            'error_program' => 'Error Program',
            'human_error' => 'Human Error',
            'hydraulic_problem' => 'Hydaulic Problem',
            'tersumbat_gramm' => 'Tersumbat Gramm',
            'product_machine_setting' => 'Product/Machine Setting',
            'air_pressure_low' => 'Air Pressure Low',
            'water_pressure_low' => 'Water Pressure Low',
            'machine_cooling_circulation' => 'Machine Cooling Circulation',
            'nozzle_blobor' => 'Nozzle Blobor',
            'lain_lain' => 'Lain-lain',
        ];
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_keys(self::labels());
    }

    public static function label(?string $value): string
    {
        if (! is_string($value) || $value === '') {
            return '—';
        }

        return self::labels()[$value] ?? $value;
    }
}
