<?php

namespace App\Support;

class PreventiveMtcChecklist extends PreventivePm1Checklist
{
    /**
     * @return list<array{key: string, label: string, answers: array<string, string>, items: array<string, array{label: string, standard: string, type: string}>}>
     */
    public static function groups(): array
    {
        return [
            self::group(
                'mechanical_fluid',
                'Mekanikal & Saluran Fluida',
                self::TYPE_CONDITION,
                [
                    'hose_fitting_leak' => ['Kebocoran Air/Oli pada Selang & Fitting', 'Tidak ada bocor/rembes (Visual)'],
                    'water_inlet_outlet_pressure' => ['Tekanan Air Inlet / Outlet (Pressure Gauge)', 'Stabil sesuai standar spesifikasi (Visual)'],
                    'circulation_pump' => ['Kondisi Pompa Sirkulasi (Suara & Getaran)', 'Suara halus, tidak ada getaran abnormal (Auditori)'],
                    'water_inflow_strainer' => ['Kondisi Strainer / Filter Air Inflow', 'Bersih dari kerak & kotoran (Pembersihan)'],
                ],
            ),
            self::group(
                'electrical_control',
                'Kelistrikan & Kontrol',
                self::TYPE_CONDITION,
                [
                    'pid_controller_display' => ['Display Suhu Controller (PID)', 'Indikator temperatur menyala & akurat'],
                    'electrical_terminal_tightness' => ['Pengencangan Terminal Listrik & Kabel', 'Tidak kendor / tidak ada tanda gosong (Torque)'],
                ],
            ),
            self::group(
                'protection_safety',
                'Sistem Proteksi & Safety',
                self::TYPE_CONDITION,
                [
                    'overheat_dry_burn_alarm' => ['Fungsi Alarm & Sensor Overheat / Dry Burn', 'Alarm berbunyi & memotong arus saat overheat'],
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
