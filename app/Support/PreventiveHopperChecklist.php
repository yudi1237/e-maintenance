<?php

namespace App\Support;

class PreventiveHopperChecklist extends PreventivePm1Checklist
{
    /**
     * @return list<array{key: string, label: string, answers: array<string, string>, items: array<string, array{label: string, standard: string, type: string}>}>
     */
    public static function groups(): array
    {
        return [
            self::group(
                'air_blower',
                'Sistem Udara & Blower',
                self::TYPE_CONDITION,
                [
                    'air_inflow_filter' => ['Kebersihan Filter Udara Masuk (Air Inflow Filter)', 'Bebas dari debu & partikel (Semprot Angin)'],
                    'blower_motor_sound_vibration' => ['Suara & Getaran Blower Motor', 'Berputar halus, tidak bisik/getar (Auditori)'],
                ],
            ),
            self::group(
                'heating',
                'Sistem Pemanas',
                self::TYPE_CONDITION,
                [
                    'heater_pipe' => ['Kondisi & Fungsi Pemanas (Heater Pipe)', 'Suhu tercapai sesuai setpoint (Sensor Test)'],
                ],
            ),
            self::group(
                'tank_mechanical',
                'Fisik Tanki & Mekanikal',
                self::TYPE_CONDITION,
                [
                    'hopper_tank_magnet_trap' => ['Kebersihan Dalam Tangki Hopper & Magnet Trap', 'Bersih total dari debu & serbuk logam'],
                    'hopper_mounting_bolts' => ['Kondisi Baut Dudukan Hopper', 'Baut terpasang lengkap & kencang, tidak goyang'],
                ],
            ),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function valueLabels(string $type): array
    {
        return [
            PreventiveChecklist::OK => 'OK',
            PreventiveChecklist::NOT_OK => 'Not OK',
        ];
    }
}
