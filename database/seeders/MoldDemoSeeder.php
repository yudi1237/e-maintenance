<?php

namespace Database\Seeders;

use App\Models\Mold;
use Illuminate\Database\Seeder;

class MoldDemoSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Cover Dashboard', 'customer' => 'Toyota', 'dimension' => '320 x 180 x 2.5'],
            ['name' => 'Panel Pintu Kanan', 'customer' => 'Daihatsu', 'dimension' => '450 x 220 x 2.0'],
            ['name' => 'Bumper Rear', 'customer' => 'Toyota', 'dimension' => '680 x 240 x 3.0'],
            ['name' => 'Grill Depan', 'customer' => 'Honda', 'dimension' => '510 x 160 x 2.2'],
            ['name' => 'Console Box', 'customer' => 'Suzuki', 'dimension' => '280 x 190 x 2.8'],
            ['name' => 'Spoiler Belakang', 'customer' => 'Mitsubishi', 'dimension' => '390 x 140 x 2.0'],
            ['name' => 'Fender Depan Kiri', 'customer' => 'Honda', 'dimension' => '540 x 210 x 2.4'],
            ['name' => 'Handle Pintu', 'customer' => 'Suzuki', 'dimension' => '180 x 70 x 1.8'],
        ];

        foreach ($rows as $row) {
            Mold::query()->updateOrCreate(
                ['name' => $row['name'], 'customer' => $row['customer']],
                ['dimension' => $row['dimension'], 'is_active' => true]
            );
        }
    }
}
