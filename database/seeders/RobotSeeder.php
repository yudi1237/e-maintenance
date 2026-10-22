<?php

namespace Database\Seeders;

use App\Models\Robot;
use App\Support\InjectionMachine;
use Illuminate\Database\Seeder;

class RobotSeeder extends Seeder
{
    public function run(): void
    {
        $count = InjectionMachine::count();

        for ($number = 1; $number <= $count; $number++) {
            $robotNumber = sprintf('R%02d', $number);
            $existing = Robot::query()->where('robot_number', $robotNumber)->first();

            if ($existing && ! $this->isDemoPlaceholder($existing)) {
                continue;
            }

            Robot::updateOrCreate(
                ['robot_number' => $robotNumber],
                [
                    'robot_type' => $this->demoType($number),
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * @return list<string>
     */
    public static function demoTypes(): array
    {
        return [
            'Yushin YC-150',
            'Yushin YC-200',
            'Wittmann W818',
            'Star XZ-1500',
            'Sepro S5-25',
            'Harmo EX-II',
        ];
    }

    private function demoType(int $number): string
    {
        $types = self::demoTypes();

        return $types[($number - 1) % count($types)];
    }

    private function isDemoPlaceholder(Robot $robot): bool
    {
        return in_array($robot->robot_type, self::demoTypes(), true)
            || str_starts_with((string) $robot->robot_type, 'Demo');
    }
}
