<?php

namespace Database\Seeders;

use App\Models\DamageCategory;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['npk' => '100001'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['npk' => '200001'],
            [
                'name' => 'Teknisi Maintenance',
                'password' => Hash::make('password'),
                'role' => User::ROLE_TECHNICIAN,
                'email_verified_at' => now(),
            ]
        );

        foreach ([
            ['name' => 'Andi PIC', 'npk' => '510001', 'pin' => '1234'],
            ['name' => 'Budi PIC', 'npk' => '510002', 'pin' => '1234'],
        ] as $pic) {
            Technician::updateOrCreate(
                ['npk' => $pic['npk']],
                [
                    'name' => $pic['name'],
                    'pin' => $pic['pin'],
                    'is_active' => true,
                ]
            );
        }

        $this->call(QualityDemoUserSeeder::class);
        $this->call(ManagementDemoUserSeeder::class);

        User::updateOrCreate(
            ['npk' => '400001'],
            [
                'name' => 'Produksi Injection Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PRODUKSI_INJECTION,
                'email_verified_at' => now(),
            ]
        );

        $categories = [
            ['name' => 'Mesin Injection', 'allows_other' => false, 'sort_order' => 1],
            ['name' => 'Mold', 'allows_other' => false, 'sort_order' => 2],
            ['name' => 'Utility (Dehum, MTC, Hopper, Selang, Crusher, Hot Runner, Squencer, dll)', 'allows_other' => false, 'sort_order' => 3],
            ['name' => 'Robot', 'allows_other' => false, 'sort_order' => 4],
            ['name' => 'Assy (Roller BBP, K1AL, Helm, Dll)', 'allows_other' => false, 'sort_order' => 5],
            ['name' => 'Painting', 'allows_other' => false, 'sort_order' => 6],
            ['name' => 'Jig Product', 'allows_other' => false, 'sort_order' => 7],
            ['name' => 'Other', 'allows_other' => true, 'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            DamageCategory::updateOrCreate(
                ['name' => $category['name']],
                array_merge($category, ['is_active' => true])
            );
        }

        $this->call(MachineNameSeeder::class);
        $this->call(RobotSeeder::class);
        $this->call(PreventiveChecklistSeeder::class);
        $this->call(DemoDashboardSeeder::class);
        $this->call(PreventiveApprovalDemoSeeder::class);
    }
}
