<?php

namespace App\Support;

class PreventiveRobotChecklist extends PreventivePm1Checklist
{
    /**
     * @return list<array{key: string, label: string, answers: array<string, string>, items: array<string, array{label: string, standard: string, type: string}>}>
     */
    public static function groups(): array
    {
        return [
            self::group(
                'mechanical_pneumatic',
                'Mekanikal & Pneumatik',
                self::TYPE_CONDITION,
                [
                    'main_air_pressure' => ['Tekanan Angin Utama (Air Pressure Gauge)', 'Stabil pada 0.49 - 0.59 MPa (5-6 bar)', [
                        'placeholder' => '0.54',
                        'suffix' => 'MPa',
                    ]],
                    'vacuum_pad_pneumatic_hose' => ['Kondisi Pad Vakum & Selang Pneumatik', 'Vakum pad tidak robek, selang tidak bocor'],
                    'guide_rail_ball_screw_lube' => ['Pelumasan Guide Rail & Ball Screw (X, Y, Z Axis)', 'Terlapisi kris/grease khusus secara merata'],
                    'eoat_chuck_plate_bolts' => ['Kekencangan Baut EOAT (Chuck Plate)', 'Baut terpasang kencang, tidak bergetar'],
                    'timing_belt_xyz' => ['Timing Belt (X/Y/Z Axis)', 'Ketegangan belt sesuai standar, tidak ada retak atau benang aus'],
                    'air_filter_water_trap' => ['Air Filter & Water Trap', 'Tabung filter udara bersih, buang endapan air/embun'],
                    'structural_frame_bolts' => ['Baut Structural / Frame', 'Baut mounting frame utama dan arm kencang (sesuai torsi)'],
                    'cylinder_arm' => ['Cylinder Arm', 'Pergerakan stabil dan tidak ada rembesan oli'],
                    'robot_arm_cable_trail_bolts' => ['Baut Trail Kabel Arm Robot', 'Baut lengkap, tidak goyang/longgar'],
                    'lubrication_tank' => ['Tanki Lubrikasi', 'Cek level oli, isi jika kurang (minimal terisi 1/3)'],
                ],
            ),
            self::group(
                'electrical_safety',
                'Elektrik & Safety',
                self::TYPE_CONDITION,
                [
                    'emergency_stop_mold_interlock' => ['Fungsi Emergency Stop & Safety Interlock Mold', 'Mesin & robot langsung berhenti saat E-Stop ditekan'],
                    'controller_cable_carrier' => ['Kondisi Kabel Controller (E-Touch) & Cable Carrier', 'Kabel tidak terjepit / terkelupas di dalam carrier'],
                    'safety_door_interlock' => ['Fungsi Safety Door Interlock', 'Robot otomatis berhenti saat pintu pagar keselamatan dibuka'],
                    'indicator_lights' => ['Lampu Indikator', 'Lampu Signal Tower (Red/Yellow/Green) menyala sesuai status'],
                    'panel_cooling_fan' => ['Kipas Pendingin Panel', 'Kipas pendingin (fan) berputar lancar, filter udara panel bersih'],
                    'fan_cover' => ['Cover Kipas', 'Cover & Filter'],
                    'power_on_push_button' => ['Push Button Power On', 'Tombol berfungsi untuk menyalakan robot'],
                ],
            ),
            self::group(
                'lubrication_base_cleanliness',
                'Pelumasan & Kebersihan Base',
                self::TYPE_CONDITION,
                [
                    'robot_area_cleanliness' => ['Kebersihan Area Robot', 'Bebas dari ceceran oli, grease berlebih, debu'],
                    'linear_guide_rail_xyz' => ['Linear Guide Rail (X/Y/Z)', 'Lapisan grease tipis merata, tidak ada akumulasi gram/kotoran'],
                    'ball_screw_rack_gear' => ['Ball Screw / Rack Gear', 'Pelumasan memadai, pergerakan mulus tanpa suara kasar'],
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
