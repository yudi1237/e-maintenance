<?php

namespace App\Support;

class PreventiveHotChecklist extends PreventivePm1Checklist
{
    /**
     * @return list<array{key: string, label: string, answers: array<string, string>, items: array<string, array{label: string, standard: string, type: string}>}>
     */
    public static function groups(): array
    {
        return [
            self::group(
                'hot_runner_controller',
                'Hot Runner Controller',
                self::TYPE_CONDITION,
                [
                    'zone_nozzle_temp_accuracy' => ['Akurasi Suhu Tiap Zone / Nozzle (Yudo TBM/D-Type)', 'Suhu aktual sesuai setpoint, tidak over/under heat'],
                    'soft_start_dehumidify' => ['Sistem Soft-Start / Dehumidify Feature', 'Fungsi soft-start aktif saat awal pemanasan'],
                ],
            ),
            self::group(
                'valve_gate_sequencer',
                'Valve Gate Sequencer (SVG)',
                self::TYPE_CONDITION,
                [
                    'valve_gate_pressure' => ['Tekanan Udara / Hidrolik Valve Gate (Air Pressure Regulator)', 'Stabil sesuai standar spesifikasi (4 - 6 Bar)', [
                        'placeholder' => '5',
                        'suffix' => 'Bar',
                    ]],
                    'valve_pin_timing' => ['Timing & Urutan Buka-Tutup Valve Pin (Timer/Position)', 'Gerakan pin sesuai urutan (sekuensial), tidak macet'],
                    'solenoid_pneumatic_hose' => ['Kondisi Solenoid Valve & Selang Pneumatik Sequencer', 'Tidak ada bocor angin/oli, respon solenoid cepat'],
                ],
            ),
            self::group(
                'cable_socket_pin',
                'Kabel & Socket Pin',
                self::TYPE_CONDITION,
                [
                    'power_thermocouple_cables' => ['Kondisi Kabel Power & Thermocouple Mold (Heavy Duty Connector)', 'Pin connector tidak bengkok, tidak ada gosong/longgar'],
                    'heater_ohm_short_to_ground' => ['Resistansi Pemanas (Heater Ohm) & Short-to-Ground', 'Nilai Ohm stabil, tidak ada kebocoran arus ke bodi mold'],
                ],
            ),
        ];
    }

}
