<?php

namespace App\Support;

class PreventivePm1Checklist
{
    public const TYPE_CONDITION = 'condition';

    public const TYPE_ACTION = 'action';

    public const TYPE_MEASUREMENT = 'measurement';

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
                    'emergency_button' => ['Tombol Emergency', 'Berfungsi'],
                    'nozzle_safety_door' => ['Pintu Safety Nozzle', ''],
                ],
            ),
            self::group(
                'hydraulic',
                'Hydraulic Section Check',
                self::TYPE_CONDITION,
                [
                    'oil_clamp_cylinder' => ['Oli dari Silinder Clamping', 'Tidak Bocor'],
                    'oil_injection_cylinder' => ['Oli dari Silinder Injeksi', 'Tidak Bocor'],
                    'oil_ejector_cylinder' => ['Oli dari Silinder Ejector', 'Tidak Bocor'],
                    'oil_block_valve' => ['Oli dari Semua Block Valve', 'Tidak Bocor'],
                    'oil_pressure_gauge' => ['Oli dari Pressure Gauge', ''],
                    'oil_pipe_hose' => ['Oli dari Pipa/ Selang Hidrolik', 'Tidak Bocor/Tidak Retak'],
                    'clamping_pressure' => ['Clamping Pressure', ''],
                    'injection_pressure' => ['Injection Pressure', ''],
                    'hydraulic_oil_level' => ['Level Oli Hidrolik', 'Min. Middle'],
                    'hydraulic_oil_temp' => ['Temperatur Oli Hidrolik', '< 55°C', [
                        'placeholder' => '50',
                        'suffix' => '°C',
                    ]],
                    'servo_pump_noise' => ['Servo Pump/ Motor (Noise)', 'Tidak Berisik'],
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
                ],
            ),
            self::group(
                'lubrication',
                'Lubrication Section Check',
                self::TYPE_CONDITION,
                [
                    'grease_level' => ['Level Grease Lubrikasi', 'Min. Terisi'],
                    'lubrication_oil_level' => ['Level Oli Lubrikasi', 'Setengah'],
                    'lubrication_flow' => ['Aliran Lubrikasi', 'Berfungsi'],
                    'lubrication_nipple_hose' => ['Kondisi Nepel & Selang Lubrikasi', 'Berfungsi'],
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
                'outlook',
                'Outlook of Machine',
                self::TYPE_ACTION,
                [
                    'clean_cover_under_machine' => ['Bersihkan Cover & Bawah Mesin', ''],
                    'clean_electrical_panel' => ['Bersihkan Panel Elektrik Mesin', 'Bersih'],
                ],
            ),
            self::group(
                'additional',
                'ADDITIONAL PM',
                self::TYPE_ACTION,
                static::additionalItems(),
            ),
        ];

        array_unshift($groups, self::voltageGroup());

        return $groups;
    }

    /**
     * @return array{key: string, label: string, layout: string, answers: array<string, string>, has_input: bool, items: array<string, array{label: string, standard: string, type: string, input?: array{placeholder: string, suffix: string}}>}
     */
    protected static function voltageGroup(): array
    {
        $group = self::group(
            'voltage',
            'Tegangan',
            self::TYPE_MEASUREMENT,
            [
                'voltage_rs' => ['R-S', '', ['placeholder' => '380', 'suffix' => 'VAC']],
                'voltage_st' => ['S-T', '', ['placeholder' => '380', 'suffix' => 'VAC']],
                'voltage_rt' => ['R-T', '', ['placeholder' => '380', 'suffix' => 'VAC']],
                'voltage_pe_n' => ['PE-N', '', ['placeholder' => '0', 'suffix' => 'VAC']],
                'voltage_power_supply' => ['Tegangan Power Supply', '', ['placeholder' => '24', 'suffix' => 'VDC']],
            ],
        );
        $group['layout'] = 'voltage';

        return $group;
    }

    /**
     * @return array<string, array{0: string, 1: string, 2?: array{placeholder: string, suffix: string}}>
     */
    protected static function additionalItems(): array
    {
        return [
            'grease_gearbox_injection' => ['Tambah Grease Gearbox Injection', 'Isi Ulang'],
            'clean_air_filter' => ['Bersihkan Filter Udara', 'Tidak Berdebu'],
            'machine_foot_check' => ['Cek Sepatu Mesin', 'Tidak Goyang'],
            'clean_cooling_barrel' => ['Bersihkan Cooling Barrel', 'Tidak Mampet'],
            'autoclamp_hose_pressure' => ['Cek Selang & Pressure Autoclamp', 'Tidak Bocor'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function items(): array
    {
        $items = [];

        foreach (static::groups() as $group) {
            if (($group['answers'] ?? []) === []) {
                continue;
            }

            foreach ($group['items'] as $key => $item) {
                $items[$key] = $item['label'];
            }
        }

        return $items;
    }

    /**
     * @return list<string>
     */
    public static function readingKeys(): array
    {
        $keys = [];

        foreach (static::groups() as $group) {
            foreach ($group['items'] as $key => $item) {
                if (! empty($item['input'])) {
                    $keys[] = $key;
                }
            }
        }

        return $keys;
    }

    /**
     * @return array<string, string>
     */
    public static function valueLabels(string $type): array
    {
        return $type === self::TYPE_ACTION
            ? [
                PreventiveChecklist::OK => 'Dilakukan',
                PreventiveChecklist::NOT_OK => 'Belum Dilakukan',
            ]
            : [
                PreventiveChecklist::OK => 'Good',
                PreventiveChecklist::NOT_OK => 'No Good',
                PreventiveChecklist::NA => 'N/A',
            ];
    }

    /**
     * @param  array<string, array{0: string, 1: string, 2?: array{placeholder: string, suffix: string}}>  $rows
     * @return array{key: string, label: string, answers: array<string, string>, has_input: bool, items: array<string, array{label: string, standard: string, type: string, input?: array{placeholder: string, suffix: string}}>}
     */
    protected static function group(string $key, string $label, string $type, array $rows): array
    {
        $items = [];

        foreach ($rows as $itemKey => $row) {
            $item = [
                'label' => $row[0],
                'standard' => $row[1],
                'type' => $type,
            ];

            if (isset($row[2]) && is_array($row[2])) {
                $item['input'] = $row[2];
            }

            $items[$itemKey] = $item;
        }

        return [
            'key' => $key,
            'label' => $label,
            'answers' => $type === self::TYPE_MEASUREMENT ? [] : static::valueLabels($type),
            'has_input' => collect($items)->contains(fn (array $item): bool => ! empty($item['input'])),
            'items' => $items,
        ];
    }
}
