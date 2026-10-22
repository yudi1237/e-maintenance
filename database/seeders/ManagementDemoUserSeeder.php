<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManagementDemoUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['npk' => '500001'],
            [
                'name' => 'Management Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_MANAGEMENT,
                'email_verified_at' => now(),
            ]
        );
    }
}
