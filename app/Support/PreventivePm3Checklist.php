<?php

namespace App\Support;

class PreventivePm3Checklist extends PreventivePm1Checklist
{
    /**
     * @return array<string, array{0: string, 1: string, 2?: array{placeholder: string, suffix: string}}>
     */
    protected static function additionalItems(): array
    {
        return [
            'oring_screw_maju_mundur' => ['Ganti O-ring Screw Maju/Mundur', 'Tidak Bocor'],
            'oring_inject_maju_mundur' => ['Ganti O-ring Inject Maju-mundur', 'Tidak Bocor'],
            'oring_suckback' => ['Ganti O-ring Suckback', 'Tidak Bocor'],
            'oring_proportional_valve' => ['Ganti O-ring Proportional Valve', 'Tidak Bocor'],
        ];
    }
}
