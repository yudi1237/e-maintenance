<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class QualityDemoUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['npk' => '300001'],
            [
                'name' => 'Quality Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_QUALITY,
                'email_verified_at' => now(),
            ]
        );
    }
}
