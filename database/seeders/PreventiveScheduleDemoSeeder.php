<?php

namespace Database\Seeders;

use App\Models\MachineName;
use App\Models\PreventiveSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class PreventiveScheduleDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('npk', '100001')->first()
            ?? User::query()->where('role', User::ROLE_ADMIN)->orderBy('id')->first();

        if ($admin === null) {
            return;
        }

        $today = now()->timezone('Asia/Jakarta')->startOfDay();
        $rows = [
            ['machine_number' => 1, 'type' => 'PM1', 'days' => 1, 'pic' => 'Andi PIC', 'notes' => 'Jadwal PM1 mesin 1, besok.'],
            ['machine_number' => 3, 'type' => 'PM2', 'days' => 3, 'pic' => 'Budi PIC', 'notes' => 'Jadwal PM2 mesin 3.'],
            ['machine_number' => 6, 'type' => 'HOPPER', 'days' => 0, 'pic' => 'Cici PIC', 'notes' => 'Jadwal hopper hari ini.'],
            ['machine_number' => 8, 'type' => 'DEHUM', 'days' => 5, 'pic' => 'Andi PIC', 'notes' => 'Jadwal dehum minggu ini.'],
            ['machine_number' => 12, 'type' => 'ROBOT', 'days' => -1, 'pic' => 'Budi PIC', 'notes' => 'Jadwal robot terlambat 1 hari.'],
            ['machine_number' => 14, 'type' => 'MTC', 'days' => 7, 'pic' => 'Cici PIC', 'notes' => 'Jadwal MTC minggu depan.'],
            ['machine_number' => 21, 'type' => 'PM1', 'days' => 0, 'pic' => 'Budi PIC', 'notes' => 'Jadwal PM1 hari ini, sudah dikerjakan.'],
            ['machine_number' => 1, 'type' => 'PM3', 'months_ago' => 1, 'pic' => 'Andi PIC', 'notes' => 'Jadwal PM3 bulan lalu, belum dikerjakan.'],
        ];

        foreach ($rows as $row) {
            $machine = MachineName::query()->where('machine_number', $row['machine_number'])->first();
            if ($machine === null) {
                continue;
            }

            $planDate = isset($row['months_ago'])
                ? $today->copy()->subMonths((int) $row['months_ago'])->startOfMonth()->toDateString()
                : $today->copy()->addDays($row['days'])->toDateString();

            PreventiveSchedule::query()->updateOrCreate(
                [
                    'machine_number' => $row['machine_number'],
                    'preventive_type' => $row['type'],
                    'plan_date' => $planDate,
                ],
                [
                    'user_id' => $admin->id,
                    'machine_name_id' => $machine->id,
                    'machine_name' => $machine->name ?? ('Mesin '.$row['machine_number']),
                    'machine_type' => $machine->machine_type,
                    'pic_name' => $row['pic'],
                    'notes' => $row['notes'],
                ]
            );
        }
    }
}
