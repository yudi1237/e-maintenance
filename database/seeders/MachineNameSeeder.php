<?php

namespace Database\Seeders;

use App\Models\MachineName;
use App\Support\InjectionMachine;
use Illuminate\Database\Seeder;

class MachineNameSeeder extends Seeder
{
    public function run(): void
    {
        $count = InjectionMachine::count();

        for ($number = 1; $number <= $count; $number++) {
            $existing = MachineName::query()->where('machine_number', $number)->first();

            if ($existing && ! $this->isDemoPlaceholder($existing)) {
                continue;
            }

            $payload = [
                'name' => "Mesin Demo {$number}",
                'brand' => 'Demo',
                'machine_model' => "Mesin Demo {$number}",
                'is_active' => true,
            ];

            if (! $existing?->tonnage) {
                $payload['tonnage'] = $this->demoTonnage($number);
            }

            if (! filled($existing?->asset_number)) {
                $payload['asset_number'] = sprintf('DEMO-%03d', $number);
            }

            if (! filled($existing?->machine_type)) {
                $payload['machine_type'] = 'Injection';
            }

            if (! filled($existing?->clamping_type)) {
                $payload['clamping_type'] = 'Toggle';
            }

            MachineName::updateOrCreate(
                ['machine_number' => $number],
                $payload
            );
        }
    }

    private function isDemoPlaceholder(MachineName $machine): bool
    {
        if (strcasecmp((string) $machine->brand, 'Demo') === 0) {
            return true;
        }

        $name = trim((string) $machine->name);

        return preg_match('/^Mesin Demo \d+$/i', $name) === 1
            || str_starts_with($name, 'Demo Injection');
    }

    private function demoTonnage(int $number): int
    {
        $options = [120, 160, 200, 250, 300];

        return $options[($number - 1) % count($options)];
    }
}
