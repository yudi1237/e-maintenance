<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SparepartHistoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        (new DemoDashboardSeeder)->seedSparepartHistoryDummy();
    }
}
