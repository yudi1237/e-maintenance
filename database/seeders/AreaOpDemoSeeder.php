<?php

namespace Database\Seeders;

use App\Models\AreaOperatorReport;
use App\Models\Technician;
use App\Models\User;
use App\Support\RepairStatus;
use Illuminate\Database\Seeder;

class AreaOpDemoSeeder extends Seeder
{
    public function run(): void
    {
        AreaOperatorReport::query()
            ->where('reporter_name', 'like', 'Demo %')
            ->delete();

        $now = now()->timezone('Asia/Jakarta');
        $technicianUser = User::query()->where('role', User::ROLE_TECHNICIAN)->orderBy('id')->first();
        $pics = Technician::query()->where('is_active', true)->orderBy('id')->limit(2)->get();
        $picA = $pics->get(0);
        $picB = $pics->get(1) ?? $picA;

        $rows = [
            [
                'area' => 'Gudang',
                'reporter_name' => 'Demo Sari',
                'part_name' => 'Box part',
                'equipment_name' => 'Hand pallet',
                'stop_date' => $now->toDateString(),
                'stop_time' => '08:20',
                'problem' => 'Roda hand pallet macet, tidak bisa mengangkat beban.',
                'chronology' => 'Terjadi saat mengambil box dari rak B.',
                'status' => RepairStatus::OPEN,
            ],
            [
                'area' => 'Painting L1',
                'reporter_name' => 'Demo Rudi',
                'part_name' => 'Cover depan',
                'equipment_name' => 'Spray gun',
                'stop_date' => $now->toDateString(),
                'stop_time' => '09:05',
                'problem' => 'Spray gun tersumbat, hasil cat belang.',
                'chronology' => 'Cat mulai belang setelah ganti warna.',
                'status' => RepairStatus::OPEN,
            ],
            [
                'area' => 'Crusher',
                'reporter_name' => 'Demo Dedi',
                'part_name' => 'Runner',
                'equipment_name' => 'Crusher 1',
                'stop_date' => $now->toDateString(),
                'stop_time' => '10:40',
                'problem' => 'Crusher berhenti mendadak dan berbunyi kasar.',
                'chronology' => 'Bunyi muncul saat memasukkan runner basah.',
                'status' => RepairStatus::OPEN,
            ],
            [
                'area' => 'Mixing',
                'reporter_name' => 'Demo Lina',
                'part_name' => 'Compound hitam',
                'equipment_name' => 'Mixer 2',
                'stop_date' => $now->toDateString(),
                'stop_time' => '07:50',
                'problem' => 'Mixer tidak berputar setelah tombol start ditekan.',
                'chronology' => 'Panel menyala, motor tidak merespons.',
                'status' => RepairStatus::ON_PROGRESS,
                'pic' => $picA,
            ],
            [
                'area' => 'Compounding',
                'reporter_name' => 'Demo Agus',
                'part_name' => 'Pellet',
                'equipment_name' => 'Extruder',
                'stop_date' => $now->copy()->subDay()->toDateString(),
                'stop_time' => '14:15',
                'problem' => 'Suhu extruder naik melewati batas dan alarm berbunyi.',
                'chronology' => 'Alarm muncul di tengah batch kedua.',
                'status' => RepairStatus::ON_PROGRESS,
                'pic' => $picB,
            ],
            [
                'area' => 'Assy Helm',
                'reporter_name' => 'Demo Wati',
                'part_name' => 'Visor',
                'equipment_name' => 'Press visor',
                'stop_date' => $now->copy()->subDay()->toDateString(),
                'stop_time' => '11:30',
                'problem' => 'Press visor tidak turun sampai titik bawah.',
                'chronology' => 'Sudah diganti sensor, mesin kembali normal.',
                'status' => RepairStatus::CLOSE_BY_TECHNICIAN,
                'pic' => $picA,
            ],
            [
                'area' => 'Double Seat',
                'reporter_name' => 'Demo Joko',
                'part_name' => 'Seat cover',
                'equipment_name' => 'Sewing machine',
                'stop_date' => $now->copy()->subDays(2)->toDateString(),
                'stop_time' => '13:10',
                'problem' => 'Benang sering putus dan jahitan loncat.',
                'chronology' => 'Tension disetel ulang, uji 10 pcs hasil rapi.',
                'status' => RepairStatus::CLOSE_BY_TECHNICIAN,
                'pic' => $picB,
            ],
            [
                'area' => 'Centralisasi Piovan',
                'reporter_name' => 'Demo Eko',
                'part_name' => null,
                'equipment_name' => 'Hopper loader',
                'stop_date' => $now->toDateString(),
                'stop_time' => '06:45',
                'problem' => 'Material tidak terhisap ke hopper mesin.',
                'chronology' => 'Selang hisap bocor, sudah diganti dan diuji.',
                'status' => RepairStatus::CLOSE_BY_TECHNICIAN,
                'pic' => $picA,
            ],
        ];

        foreach ($rows as $row) {
            $pic = $row['pic'] ?? null;
            unset($row['pic']);
            $claimed = $row['status'] !== RepairStatus::OPEN && $pic instanceof Technician;

            AreaOperatorReport::query()->create([
                ...$row,
                'claimed_by' => $claimed ? $technicianUser?->id : null,
                'claimed_technician_id' => $claimed ? $pic->id : null,
                'claimed_at' => $claimed ? $now->copy()->subHours(2) : null,
                'closed_at' => $row['status'] === RepairStatus::CLOSE_BY_TECHNICIAN ? $now->copy()->subHour() : null,
            ]);
        }

        $this->command?->info('area_op_demo='.AreaOperatorReport::query()->where('reporter_name', 'like', 'Demo %')->count());
    }
}
