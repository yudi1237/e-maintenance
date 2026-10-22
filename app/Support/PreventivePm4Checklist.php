<?php

namespace App\Support;

class PreventivePm4Checklist extends PreventivePm1Checklist
{
    /**
     * @return array<string, array{0: string, 1: string, 2?: array{placeholder: string, suffix: string}}>
     */
    protected static function additionalItems(): array
    {
        return [
            'oring_valve_open' => ['Ganti O-ring Valve Open', 'Tidak Bocor'],
            'oring_valve_close' => ['Ganti O-ring Valve Close', 'Tidak Bocor'],
            'oring_valve_mold_adjust' => ['Ganti O-ring Valve Mold Adjust', 'Tidak Bocor'],
            'oring_valve_mold_core' => ['Ganti O-ring Valve Mold Core', 'Tidak Bocor'],
            'oring_valve_ejector' => ['Ganti O-ring Valve Ejector', 'Tidak Bocor'],
        ];
    }
}
