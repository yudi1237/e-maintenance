<?php

namespace App\Support;

class PreventiveDehumChecklist extends PreventivePm1Checklist
{
    /**
     * @return list<array{key: string, label: string, answers: array<string, string>, items: array<string, array{label: string, standard: string, type: string}>}>
     */
    public static function groups(): array
    {
        return [
            self::group(
                'air_dew_point',
                'Sistem Udara & Dew Point',
                self::TYPE_CONDITION,
                [
                    'dry_air_dew_point' => ['Nilai Dew Point Udara Kering (Dew Point Meter)', 'Mencapai standar -40°C s/d -50°C', [
                        'placeholder' => '-45',
                        'suffix' => '°C',
                    ]],
                    'process_return_filter' => ['Kebersihan Process Air Filter & Return Filter', 'Bebas sumbatan debu plastik (Semprot Angin)'],
                ],
            ),
            self::group(
                'desiccant_rotor',
                'Desiccant Rotor',
                self::TYPE_CONDITION,
                [
                    'honeycomb_rotor_drive' => ['Putaran Honeycomb Rotor & V-Belt / Chain Drive', 'Rotor berputar lancar, belt tidak kendur'],
                ],
            ),
            self::group(
                'regen_heater_electrical',
                'Sistem Pemanas Regenerasi & Kelistrikan',
                self::TYPE_CONDITION,
                [
                    'heater_ampere' => ['Pemeriksaan Arus Pemanas (Heater Ampere)', 'Arus fasa R-S-T seimbang sesuai spesifikasi'],
                ],
            ),
        ];
    }

}
