<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Robot;
use App\Support\EquipmentCategory;
use Illuminate\Database\Seeder;

class EquipmentDemoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rows() as $row) {
            $equipment = Equipment::query()->updateOrCreate(
                [
                    'category' => $row['category'],
                    'number' => $row['number'],
                ],
                [
                    'type' => $row['specification'],
                    'specification' => $row['specification'],
                    'area' => $row['area'],
                    'is_active' => true,
                ]
            );

            if ($equipment->category !== EquipmentCategory::ROBOT) {
                continue;
            }

            Robot::query()->updateOrCreate(
                ['robot_number' => $equipment->number],
                [
                    'robot_type' => $equipment->specification,
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * @return list<array{category: string, number: string, specification: string, area: string}>
     */
    public static function rows(): array
    {
        return [
            ['category' => 'robot', 'number' => 'RB-01', 'specification' => 'Yushin YC-150', 'area' => 'injection_line'],
            ['category' => 'robot', 'number' => 'RB-02', 'specification' => 'Wittmann W818', 'area' => 'injection_line'],
            ['category' => 'robot', 'number' => 'RB-03', 'specification' => 'Star XZ-1500', 'area' => 'painting_line_1'],
            ['category' => 'robot', 'number' => 'RB-04', 'specification' => 'Sepro S5-25', 'area' => 'painting_line_2'],

            ['category' => 'dehum', 'number' => 'DH-01', 'specification' => 'Moretto XD 30', 'area' => 'injection_line'],
            ['category' => 'dehum', 'number' => 'DH-02', 'specification' => 'Matsui MJ3-150', 'area' => 'painting_line_1'],
            ['category' => 'dehum', 'number' => 'DH-03', 'specification' => 'Kawata DV-25', 'area' => 'compounding'],

            ['category' => 'hopper', 'number' => 'HP-01', 'specification' => 'Hopper 50 L', 'area' => 'injection_line'],
            ['category' => 'hopper', 'number' => 'HP-02', 'specification' => 'Hopper 100 L', 'area' => 'injection_line'],
            ['category' => 'hopper', 'number' => 'HP-03', 'specification' => 'Hopper 200 L', 'area' => 'compounding'],

            ['category' => 'mtc', 'number' => 'MTC-01', 'specification' => 'Shini STM-910', 'area' => 'injection_line'],
            ['category' => 'mtc', 'number' => 'MTC-02', 'specification' => 'Regloplas 90Smart', 'area' => 'painting_line_3'],

            ['category' => 'motor', 'number' => 'MT-01', 'specification' => 'Motor 5.5 kW', 'area' => 'injection_line'],
            ['category' => 'motor', 'number' => 'MT-02', 'specification' => 'Motor 7.5 kW', 'area' => 'compounding'],
            ['category' => 'motor', 'number' => 'MT-03', 'specification' => 'Motor 11 kW', 'area' => 'cooling_tower'],

            ['category' => 'blower', 'number' => 'BL-01', 'specification' => 'Blower 2.2 kW', 'area' => 'painting_line_1'],
            ['category' => 'blower', 'number' => 'BL-02', 'specification' => 'Blower 3.7 kW', 'area' => 'painting_line_2'],
            ['category' => 'blower', 'number' => 'BL-03', 'specification' => 'Blower 5.5 kW', 'area' => 'cooling_tower'],

            ['category' => 'mesin_crusher', 'number' => 'CR-01', 'specification' => 'Crusher 15 HP', 'area' => 'compounding'],
            ['category' => 'mesin_crusher', 'number' => 'CR-02', 'specification' => 'Crusher 20 HP', 'area' => 'injection_line'],

            ['category' => 'pompa', 'number' => 'PM-01', 'specification' => 'Pompa 3 kW', 'area' => 'cooling_tower'],
            ['category' => 'pompa', 'number' => 'PM-02', 'specification' => 'Pompa 5.5 kW', 'area' => 'painting_line_3'],
            ['category' => 'pompa', 'number' => 'PM-03', 'specification' => 'Pompa 7.5 kW', 'area' => 'compounding'],

            ['category' => 'squencer', 'number' => 'SQ-01', 'specification' => 'Sequencer 8 Zone', 'area' => 'injection_line'],
            ['category' => 'squencer', 'number' => 'SQ-02', 'specification' => 'Sequencer 12 Zone', 'area' => 'painting_line_1'],

            ['category' => 'hot_runner', 'number' => 'HR-01', 'specification' => 'Hot Runner 8 Point', 'area' => 'injection_line'],
            ['category' => 'hot_runner', 'number' => 'HR-02', 'specification' => 'Hot Runner 16 Point', 'area' => 'painting_line_2'],

            ['category' => 'autoloader', 'number' => 'AL-01', 'specification' => 'Autoloader 1.5 kW', 'area' => 'injection_line'],
            ['category' => 'autoloader', 'number' => 'AL-02', 'specification' => 'Autoloader 2.2 kW', 'area' => 'compounding'],
            ['category' => 'autoloader', 'number' => 'AL-03', 'specification' => 'Autoloader 3.7 kW', 'area' => 'painting_line_3'],
        ];
    }
}
