<?php

namespace App\Support;

class PreventiveJswElChecklist extends PreventivePm1Checklist
{
    /**
     * @return list<array{key: string, label: string, answers: array<string, string>, items: array<string, array{label: string, standard: string, type: string}>}>
     */
    public static function groups(): array
    {
        return [
            self::group(
                'inject_unit',
                'C. Inject Unit',
                self::TYPE_CONDITION,
                [
                    'servo_motor_inject' => ['Servo Motor Inject', 'Cek temp max 80°C & 145 Ampere motor'],
                    'servo_motor_plasticization' => ['Servo Motor Plasticization', 'Cek temp max 80°C & 145 Ampere motor'],
                    'cooling_fan_servo_screw' => ['Cooling Fan Servo Motor Screw', 'Berfungsi & bersih'],
                    'cooling_fan_servo_inject' => ['Cooling Fan Servo Motor Inject', 'Berfungsi & bersih'],
                    'ball_screw' => ['Ball Screw', 'Bersih'],
                    'gear_box' => ['Gear Box', 'Level oil'],
                    'automatic_grease_lubrication' => ['Automatic Grease Lubrication', 'Berfungsi minimal 5 Bar'],
                    'hopper_cooler' => ['Hopper Cooler', 'Temperatur sesuai setting (50°C s/d 80°C)'],
                    'heater_nozzle' => ['Heater Nozzle', 'Temperatur actual sesuai setting'],
                    'hydraulic_cylinder' => ['Hydraulic Cylinder', 'Bersih & tidak bocor'],
                    'grease_cartridge' => ['Grease Cartridge', 'Level grease 1/4 ganti baru'],
                    'nozzle_center_radius' => ['Nozzle Center & Radius Nozzle', 'Nozzle center, tidak gompal', [
                        'placeholder' => 'OK / catatan',
                        'suffix' => '',
                    ]],
                    'nozzle_hole_radius' => ['Dia. Lubang & Radius Nozzle', 'Lubang nozzle 1 mm < lubang mold', [
                        'placeholder' => 'Dia / Radius',
                        'suffix' => '',
                    ]],
                ],
            ),
            self::group(
                'pneumatic',
                'D. System Pneumatic Low Pressure',
                self::TYPE_CONDITION,
                [
                    'pressure_regulator' => ['Pressure Regulator', 'Pressure 5 Bar'],
                    'filter_regulator' => ['Filter Regulator', 'Bersih & tidak ada air di dalam filter'],
                    'valve_pneumatic' => ['Valve Pneumatic', 'Tidak bocor'],
                ],
            ),
            self::group(
                'safety',
                'E. Safety Section',
                self::TYPE_CONDITION,
                [
                    'limit_switch_front_door' => ['Limit Switch Pintu Depan (Operator)', 'Bersih & berfungsi', [
                        'placeholder' => 'OK',
                        'suffix' => '',
                    ]],
                    'limit_switch_rear_door' => ['Limit Switch Pintu Belakang', 'Bersih & berfungsi', [
                        'placeholder' => 'OK',
                        'suffix' => '',
                    ]],
                    'nozzle_safety_door' => ['Pintu Safety Nozzle', 'Bersih & berfungsi', [
                        'placeholder' => 'OK',
                        'suffix' => '',
                    ]],
                    'emergency_button' => ['Tombol Emergency', 'Bersih & berfungsi', [
                        'placeholder' => 'OK',
                        'suffix' => '',
                    ]],
                ],
            ),
            self::group(
                'panel',
                'F. Panel Mesin',
                self::TYPE_CONDITION,
                [
                    'main_voltage_3_phase' => ['Tegangan Utama 3 Phase', 'Tegangan output 380V ±10%', [
                        'placeholder' => 'R-S / S-T / R-T',
                        'suffix' => 'VAC',
                    ]],
                    'exhaust_fan' => ['Exhaust Fan', 'Bersih & berfungsi'],
                    'filter_electrical_cabin' => ['Filter Electrical Cabin', 'Bersih'],
                    'electro_magnetic_contactor' => ['Electro Magnetic Contactor', 'Temperature / 150 Ampere'],
                    'power_supply' => ['Power Supply', 'Tegangan output 24 Volt DC'],
                    'breaker_cb0' => ['Breaker CB 0', 'Temperature / 140 Ampere / tegangan'],
                    'running_hours_meter' => ['Running Hours Meter', 'Berfungsi'],
                    'cover_bolts' => ['Baut Cover', 'Lengkap dan kencang'],
                ],
            ),
        ];
    }
}
