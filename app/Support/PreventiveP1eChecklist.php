<?php

namespace App\Support;

class PreventiveP1eChecklist extends PreventivePm1Checklist
{
    /**
     * @return list<array{key: string, label: string, answers: array<string, string>, items: array<string, array{label: string, standard: string, type: string}>}>
     */
    public static function groups(): array
    {
        $groups = [
            self::group(
                'electrical',
                'Electrical Section Check',
                self::TYPE_CONDITION,
                [
                    'thermocouple' => ['Thermocouple', 'Berfungsi'],
                    'coil_band_heater' => ['Coil/ Band Heater', 'Berfungsi'],
                    'heater_wiring' => ['Heater Wiring', 'Rapi'],
                    'heater_ssr_mcb_fuse' => ['Heater, SSR, MCB, Fuse', 'Berfungsi'],
                    'magnetic_contactor' => ['Magnetic Contactor', 'Berfungsi'],
                    'operation_panel' => ['Operation Panel', 'Berfungsi'],
                    'selector_switch' => ['Selector Switch', 'Berfungsi'],
                    'cooling_fan_panel' => ['Kipas Pendingin Panel', ''],
                    'cooling_fan_motor_servo' => ['Kipas Pendingin Motor/Servo', ''],
                    'electrical_component_temp' => ['Temperatur Komponen Elektrik', '< 40°C', [
                        'placeholder' => '36',
                        'suffix' => '°C',
                    ]],
                ],
            ),
            self::group(
                'safety',
                'Safety Section Check',
                self::TYPE_CONDITION,
                [
                    'limit_switch_front_door' => ['Limit Switch Pintu bag. Depan', 'Berfungsi'],
                    'limit_switch_side_door' => ['Limit Switch Pintu bag. Samping', 'Berfungsi'],
                    'safety_foot_mold' => ['Safety Foot Mold (850T)', 'Berfungsi'],
                    'emergency_button' => ['Tombol Emergency', 'Berfungsi'],
                    'nozzle_safety_door' => ['Pintu Safety Nozzle', 'Berfungsi'],
                ],
            ),
            self::group(
                'hydraulic',
                'Hydraulic Section Check (850T)',
                self::TYPE_CONDITION,
                [
                    'autoclamp_oil' => ['Oli Autoclamp', 'Min. Terisi Setengah'],
                    'autoclamp_hose' => ['Selang Autoclamp', 'Tidak bocor'],
                    'autoclamp_pressure' => ['Pressure Autoclamp', ''],
                    'hydraulic_corepull' => ['Hydraulic Corepull (jika dipakai)', 'Tidak Bocor'],
                ],
            ),
            self::group(
                'mechanical',
                'Mechanical Section Check',
                self::TYPE_CONDITION,
                [
                    'nozzle_center' => ['Nozzle Center', 'Tidak Blobor'],
                    'nozzle_hole_radius' => ['Dia. Lubang & Radius Nozzle', 'Sesuai Std', [
                        'placeholder' => 'Dia / Radius',
                        'suffix' => '',
                    ]],
                    'machine_level' => ['Machine Level', 'Seimbang'],
                    'toggle_part_abrasion' => ['Abrasi Part Toggle', 'Tidak Aus'],
                    'lm_guide_sliding_plate' => ['Kondisi LM Guide/ Sliding Plate', ''],
                    'clamping_adjusting_motor' => ['Clamping Adjusting Motor', 'Berfungsi'],
                    'check_ring_barrel' => ['Cek Check Ring & Barrel', 'Tidak Bocor'],
                ],
            ),
            self::group(
                'mechanical_action',
                'Mechanical — Perawatan',
                self::TYPE_ACTION,
                [
                    'grease_gearbox_injection' => ['Tambah Grease Gearbox Injection', 'Isi Ulang'],
                    'clean_cooling_barrel' => ['Bersihkan Cooling Barrel', 'Tidak Mampet'],
                ],
            ),
            self::group(
                'lubrication',
                'Lubrication Section',
                self::TYPE_CONDITION,
                [
                    'lube_grease_replace' => ['Lube Grease, Ganti Jika Habis', 'Lube Grease'],
                    'lubrication_flow_nipple' => ['Aliran Lubrikasi & Kondisi Nepel/Selang', 'Berfungsi'],
                ],
            ),
            self::group(
                'lubrication_manual',
                'Pelumasan Manual',
                self::TYPE_ACTION,
                [
                    'lube_tie_bar' => ['Tie-bar & Nut Tie-bar', 'Terlubrikasi'],
                    'lube_sliding_injection' => ['Sliding Plate Injection', 'Terlubrikasi'],
                    'lube_sliding_clamp' => ['Sliding Plate Clamp', 'Terlubrikasi'],
                    'lube_gear_adjust_mold' => ['Gear Adjust Mold', 'Terlubrikasi'],
                ],
            ),
            self::group(
                'electrical_panel',
                'Panel Elektrik',
                self::TYPE_ACTION,
                [
                    'clean_electrical_panel' => ['Bersihkan Panel Elektrik Mesin', 'Tidak Berdebu'],
                    'clean_panel_fan' => ['Bersihkan Kipas Panel', 'Tidak Berdebu'],
                    'clean_air_filter' => ['Bersihkan Filter Udara', 'Tidak Berdebu'],
                    'clean_servo_fan' => ['Bersihkan Kipas Servo', 'Tidak Berdebu'],
                    'component_thermal_temp' => ['Cek Suhu Komponen (Thermal)', '< 50°C', [
                        'placeholder' => '45',
                        'suffix' => '°C',
                    ]],
                ],
            ),
            self::group(
                'outlook',
                'Outlook Of Machine',
                self::TYPE_ACTION,
                [
                    'clean_cover_under_machine' => ['Bersihkan Cover & Bawah Mesin', 'Bersih'],
                    'machine_foot_check' => ['Cek Sepatu Mesin', 'Tidak Goyang'],
                    'replace_water_hoses' => ['Ganti Selang-selang Air', 'Tidak kaku'],
                    'replace_air_hoses' => ['Ganti Selang-selang angin', 'Tidak kaku'],
                ],
            ),
        ];

        array_unshift($groups, self::voltageGroup());

        return $groups;
    }
}
