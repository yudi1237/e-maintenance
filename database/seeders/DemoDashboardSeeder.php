<?php

namespace Database\Seeders;

use App\Models\DamageCategory;
use App\Models\FindingRecap;
use App\Models\MachineName;
use App\Models\MaintenanceReport;
use App\Models\OperatorReport;
use App\Models\PreventiveReport;
use App\Models\Robot;
use App\Models\Technician;
use App\Models\User;
use App\Support\FindingRecapStatus;
use App\Support\PreventiveChecklist;
use App\Support\PreventiveMachineChecklist;
use App\Support\PreventiveStatus;
use App\Support\RepairStatus;
use App\Support\WorkShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoDashboardSeeder extends Seeder
{
    public function run(): void
    {
        if (MachineName::query()->active()->whereNotNull('machine_number')->doesntExist()) {
            $this->call(MachineNameSeeder::class);
        }

        if (Robot::query()->doesntExist()) {
            $this->call(RobotSeeder::class);
        }

        $admin = User::query()->where('npk', '100001')->first()
            ?? User::query()->where('role', User::ROLE_ADMIN)->orderBy('id')->first();
        $technicianUser = User::query()->where('npk', '200001')->first()
            ?? User::query()->where('role', User::ROLE_TECHNICIAN)->orderBy('id')->first();

        if ($admin === null || $technicianUser === null) {
            return;
        }

        $pics = Technician::query()->active()->orderBy('id')->get();
        $andi = $pics->firstWhere('npk', '510001') ?? $pics->get(0);
        $budi = $pics->firstWhere('npk', '510002') ?? $pics->get(1);
        $cici = $pics->firstWhere('npk', '510003') ?? $pics->get(2);

        $andi ??= Technician::updateOrCreate(
            ['npk' => '510001'],
            ['name' => 'Andi PIC', 'pin' => '1234', 'is_active' => true]
        );
        $budi ??= Technician::updateOrCreate(
            ['npk' => '510002'],
            ['name' => 'Budi PIC', 'pin' => '1234', 'is_active' => true]
        );
        $cici ??= Technician::updateOrCreate(
            ['npk' => '510003'],
            ['name' => 'Cici PIC', 'pin' => '1234', 'is_active' => true]
        );

        $today = now()->timezone('Asia/Jakarta');
        $categoryByName = DamageCategory::query()->pluck('id', 'name')->all();

        $this->seedTickets($today, $technicianUser, $andi, $budi, $cici, $categoryByName);
        $this->seedSparepartHistoryDummy();
        $this->seedPreventive($today, $technicianUser, $andi, $budi, $cici);
        $this->seedFindingRecaps($today, $admin);
    }

    public function seedSparepartHistoryDummy(): void
    {
        $technicianUser = User::query()->where('npk', '200001')->first()
            ?? User::query()->where('role', User::ROLE_TECHNICIAN)->orderBy('id')->first();

        if ($technicianUser === null) {
            return;
        }

        $pics = Technician::query()->active()->orderBy('id')->get();
        $andi = $pics->firstWhere('npk', '510001') ?? $pics->get(0);
        $budi = $pics->firstWhere('npk', '510002') ?? $pics->get(1);
        $cici = $pics->firstWhere('npk', '510003') ?? $pics->get(2);

        $andi ??= Technician::updateOrCreate(
            ['npk' => '510001'],
            ['name' => 'Andi PIC', 'pin' => '1234', 'is_active' => true]
        );
        $budi ??= Technician::updateOrCreate(
            ['npk' => '510002'],
            ['name' => 'Budi PIC', 'pin' => '1234', 'is_active' => true]
        );
        $cici ??= Technician::updateOrCreate(
            ['npk' => '510003'],
            ['name' => 'Cici PIC', 'pin' => '1234', 'is_active' => true]
        );

        $this->seedSparepartHistory(
            now()->timezone('Asia/Jakarta'),
            $technicianUser,
            $andi,
            $budi,
            $cici,
            DamageCategory::query()->pluck('id', 'name')->all()
        );
    }

    /**
     * @param  array<string, int>  $categoryByName
     */
    private function seedTickets(
        Carbon $today,
        User $technicianUser,
        Technician $andi,
        Technician $budi,
        Technician $cici,
        array $categoryByName
    ): void {
        $injection = $categoryByName['Mesin Injection'] ?? DamageCategory::query()->value('id');
        $mold = $categoryByName['Mold'] ?? $injection;
        $utility = $categoryByName['Utility (Dehum, MTC, Hopper, Selang, Crusher, Hot Runner, Squencer, dll)'] ?? $injection;
        $robot = $categoryByName['Robot'] ?? $injection;

        $tickets = [
            [
                'npk' => '880001',
                'operator_name' => 'Rina Wulandari',
                'item_name' => 'Cover Dashboard Avanza',
                'machine_number' => 3,
                'stop_time' => '07:15',
                'problem' => 'Mesin alarm hydraulic pressure low, clamping tidak mengunci.',
                'chronology' => 'Alarm muncul saat mulai cycle pertama shift 1.',
                'status' => RepairStatus::OPEN,
            ],
            [
                'npk' => '880002',
                'operator_name' => 'Joko Santoso',
                'item_name' => 'Panel Pintu Kanan',
                'machine_number' => 7,
                'stop_time' => '08:40',
                'problem' => 'Heater zona 2 tidak naik suhu, material dingin di nozzle.',
                'chronology' => 'Suhu zona 2 stuck di 80°C selama 10 menit.',
                'status' => RepairStatus::OPEN,
            ],
            [
                'npk' => '880003',
                'operator_name' => 'Sari Dewi',
                'item_name' => 'Grill Depan Rush',
                'machine_number' => 12,
                'stop_time' => '09:05',
                'problem' => 'Mold tidak menutup rata, produk flash di sisi kiri.',
                'chronology' => 'Setelah ganti mold pagi, flash muncul di shot ke-3.',
                'status' => RepairStatus::ON_PROGRESS,
                'pic' => $andi,
                'claimed_minutes_ago' => 40,
                'form2' => false,
                'category_id' => $mold,
                'work_minutes' => 0,
            ],
            [
                'npk' => '880004',
                'operator_name' => 'Dedi Pratama',
                'item_name' => 'Bumper Rear Calya',
                'machine_number' => 5,
                'stop_time' => '09:50',
                'problem' => 'Robot gripper tidak hold produk, produk jatuh ke conveyor.',
                'chronology' => 'Vacuum gripper lemah setelah 2 jam produksi.',
                'status' => RepairStatus::ON_PROGRESS,
                'pic' => $budi,
                'claimed_minutes_ago' => 55,
                'form2' => false,
                'category_id' => $robot,
                'work_minutes' => 0,
            ],
            [
                'npk' => '880005',
                'operator_name' => 'Agus Rahman',
                'item_name' => 'Fender Depan Kiri',
                'machine_number' => 8,
                'stop_time' => '06:20',
                'problem' => 'Kebocoran oli hidrolik di selang clamping.',
                'chronology' => 'Oli menetes ke lantai setelah cycle ke-40.',
                'status' => RepairStatus::CLOSE_BY_TECHNICIAN,
                'pic' => $andi,
                'claimed_minutes_ago' => 180,
                'form2' => true,
                'category_id' => $injection,
                'work_minutes' => 45,
                'masalah' => 'Selang hidrolik clamping bocor.',
                'analisa' => 'Hose clamp kendor dan selang retak.',
                'tindakan' => 'Ganti selang hidrolik dan kencangkan clamp.',
                'sparepart' => 'Selang hidrolik 1/2" — 1 pcs',
            ],
            [
                'npk' => '880006',
                'operator_name' => 'Lina Kusuma',
                'item_name' => 'Console Box',
                'machine_number' => 15,
                'stop_time' => '07:55',
                'problem' => 'Thermocouple zona 1 error, heater trip.',
                'chronology' => 'Alarm thermocouple broken saat auto.',
                'status' => RepairStatus::APPROVE_PRODUKSI_INJECTION,
                'pic' => $budi,
                'claimed_minutes_ago' => 160,
                'form2' => true,
                'category_id' => $injection,
                'work_minutes' => 35,
                'masalah' => 'Thermocouple zona 1 putus.',
                'analisa' => 'Kabel thermocouple terkelupas dekat heater band.',
                'tindakan' => 'Ganti thermocouple dan rapihkan kabel.',
                'sparepart' => 'Thermocouple type J — 1 pcs',
            ],
            [
                'npk' => '880007',
                'operator_name' => 'Bayu Firmansyah',
                'item_name' => 'Spoiler Belakang',
                'machine_number' => 22,
                'stop_time' => '05:10',
                'problem' => 'Safety door interlock tidak merespons, mesin tidak auto.',
                'chronology' => 'Setelah istirahat, interlock tidak detect pintu tertutup.',
                'status' => RepairStatus::APPROVE_QUALITY,
                'pic' => $andi,
                'claimed_minutes_ago' => 240,
                'form2' => true,
                'category_id' => $injection,
                'work_minutes' => 70,
                'masalah' => 'Interlock safety door mati.',
                'analisa' => 'Limit switch aus dan kabel longgar.',
                'tindakan' => 'Ganti limit switch dan tes interlock 5x.',
                'sparepart' => 'Limit switch safety door — 1 pcs',
            ],
            [
                'npk' => '880008',
                'operator_name' => 'Maya Putri',
                'item_name' => 'Cover Spion',
                'machine_number' => 1,
                'stop_time' => '10:15',
                'problem' => 'Hopper dryer tidak panas, material basah.',
                'chronology' => 'Titik embun hopper naik, produk silver streak.',
                'status' => RepairStatus::CLOSE_BY_TECHNICIAN,
                'pic' => $cici,
                'claimed_minutes_ago' => 90,
                'form2' => true,
                'category_id' => $utility,
                'work_minutes' => 50,
                'masalah' => 'Heater hopper dryer tidak nyala.',
                'analisa' => 'Kontaktor heater hopper terbakar.',
                'tindakan' => 'Ganti kontaktor dan reset overheat.',
                'sparepart' => 'Kontaktor 3P 18A — 1 pcs',
            ],
            [
                'npk' => '880009',
                'operator_name' => 'Eko Wijaya',
                'item_name' => 'Trim Pintu Dalam',
                'machine_number' => 4,
                'stop_time' => '04:30',
                'problem' => 'Pompa hidrolik berisik, tekanan naik-turun.',
                'chronology' => 'Suara cavitation terdengar sejak awal shift.',
                'status' => RepairStatus::CLOSE,
                'pic' => $andi,
                'claimed_minutes_ago' => 300,
                'form2' => true,
                'category_id' => $injection,
                'work_minutes' => 25,
                'masalah' => 'Level oli hidrolik rendah dan filter kotor.',
                'analisa' => 'Filter suction tersumbat, oli di bawah min.',
                'tindakan' => 'Isi oli dan ganti filter suction.',
                'sparepart' => 'Filter oli hidrolik — 1 pcs, Oli hidrolik 20 L',
                'sparepart_items' => [
                    ['name' => 'Filter oli hidrolik', 'qty' => 1],
                    ['name' => 'Oli hidrolik 20 L', 'qty' => 1],
                ],
            ],
            [
                'npk' => '880010',
                'operator_name' => 'Nur Aisyah',
                'item_name' => 'Handle Pintu',
                'machine_number' => 9,
                'stop_time' => '03:45',
                'problem' => 'MTC tidak stabil, mold terlalu dingin.',
                'chronology' => 'Suhu mold turun 15°C dari setting.',
                'status' => RepairStatus::CLOSE,
                'pic' => $budi,
                'claimed_minutes_ago' => 280,
                'form2' => true,
                'category_id' => $utility,
                'work_minutes' => 50,
                'masalah' => 'Pompa MTC lemah, heater MTC cycling.',
                'analisa' => 'Filter air MTC tersumbat kerak.',
                'tindakan' => 'Bersihkan filter dan flushing saluran mold.',
                'sparepart' => MaintenanceReport::SPAREPART_NONE_LABEL,
            ],
            [
                'npk' => '880011',
                'operator_name' => 'Hendra Gunawan',
                'item_name' => 'Cover Radiator',
                'machine_number' => 18,
                'stop_time' => '02:20',
                'problem' => 'Screw stuck, injection tidak maju.',
                'chronology' => 'Material hang di barrel setelah idle 40 menit.',
                'status' => RepairStatus::CLOSE,
                'pic' => $cici,
                'claimed_minutes_ago' => 360,
                'form2' => true,
                'category_id' => $injection,
                'work_minutes' => 95,
                'masalah' => 'Material hang di barrel, screw macet.',
                'analisa' => 'Suhu zona 3 drop saat idle, material padat.',
                'tindakan' => 'Purge material, naikkan suhu, cek heater band.',
                'sparepart' => 'Heater band zona 3 — 1 pcs',
                'sparepart_items' => [
                    ['name' => 'Heater band zona 3', 'qty' => 1],
                ],
            ],
            [
                'npk' => '880012',
                'operator_name' => 'Fitriani',
                'item_name' => 'Air Vent Grill',
                'machine_number' => 11,
                'stop_time' => '11:05',
                'problem' => 'Cooling tower supply kurang, barrel overheat.',
                'chronology' => 'Suhu cooling water 42°C, di atas standar 32°C.',
                'status' => RepairStatus::ON_PROGRESS,
                'pic' => $cici,
                'claimed_minutes_ago' => 25,
                'form2' => false,
                'category_id' => $utility,
                'work_minutes' => 0,
            ],
        ];

        foreach ($tickets as $row) {
            $this->upsertTicket($today, $technicianUser, $row);
        }
    }

    /**
     * Dummy OP yang sudah Close + semua approval, untuk menu History Spare Part.
     *
     * @param  array<string, int>  $categoryByName
     */
    private function seedSparepartHistory(
        Carbon $today,
        User $technicianUser,
        Technician $andi,
        Technician $budi,
        Technician $cici,
        array $categoryByName
    ): void {
        $injection = $categoryByName['Mesin Injection'] ?? DamageCategory::query()->value('id');
        $mold = $categoryByName['Mold'] ?? $injection;
        $utility = $categoryByName['Utility (Dehum, MTC, Hopper, Selang, Crusher, Hot Runner, Squencer, dll)'] ?? $injection;

        $rows = [
            [
                'npk' => '990001',
                'operator_name' => 'Rudi Hartono',
                'item_name' => 'Cover Dashboard',
                'machine_number' => 2,
                'stop_time' => '08:10',
                'days_ago' => 1,
                'problem' => 'Seal clamping bocor, oli menetes.',
                'status' => RepairStatus::CLOSE,
                'pic' => $andi,
                'claimed_minutes_ago' => 30,
                'form2' => true,
                'category_id' => $injection,
                'work_minutes' => 55,
                'masalah' => 'Seal clamping aus.',
                'analisa' => 'Umur seal sudah habis.',
                'tindakan' => 'Ganti seal clamping.',
                'sparepart_items' => [
                    ['name' => 'Seal clamping 50x70x10', 'qty' => 2],
                    ['name' => 'O-ring P20', 'qty' => 4],
                ],
            ],
            [
                'npk' => '990002',
                'operator_name' => 'Wati Suryani',
                'item_name' => 'Panel Pintu',
                'machine_number' => 6,
                'stop_time' => '09:40',
                'days_ago' => 2,
                'problem' => 'Heater zona 1 mati.',
                'status' => RepairStatus::CLOSE,
                'pic' => $budi,
                'claimed_minutes_ago' => 40,
                'form2' => true,
                'category_id' => $injection,
                'work_minutes' => 70,
                'masalah' => 'Heater band zona 1 putus.',
                'analisa' => 'Heater band terbakar.',
                'tindakan' => 'Ganti heater band.',
                'sparepart_items' => [
                    ['name' => 'Heater band zona 1', 'qty' => 1],
                ],
            ],
            [
                'npk' => '990003',
                'operator_name' => 'Tono Wijaya',
                'item_name' => 'Bumper Depan',
                'machine_number' => 10,
                'stop_time' => '13:20',
                'days_ago' => 3,
                'problem' => 'Selang hidrolik pecah.',
                'status' => RepairStatus::CLOSE,
                'pic' => $cici,
                'claimed_minutes_ago' => 25,
                'form2' => true,
                'category_id' => $injection,
                'work_minutes' => 80,
                'masalah' => 'Hose hidrolik retak.',
                'analisa' => 'Tekanan tinggi dan umur selang.',
                'tindakan' => 'Ganti selang dan fitting.',
                'sparepart_items' => [
                    ['name' => 'Selang hidrolik 1/2"', 'qty' => 1],
                    ['name' => 'Fitting hidrolik 1/2"', 'qty' => 2],
                ],
            ],
            [
                'npk' => '990004',
                'operator_name' => 'Sinta Lestari',
                'item_name' => 'Grill Depan',
                'machine_number' => 14,
                'stop_time' => '07:05',
                'days_ago' => 5,
                'problem' => 'Limit switch ejector error.',
                'status' => RepairStatus::CLOSE,
                'pic' => $andi,
                'claimed_minutes_ago' => 20,
                'form2' => true,
                'category_id' => $injection,
                'work_minutes' => 40,
                'masalah' => 'Limit switch ejector aus.',
                'analisa' => 'Kontak switch sudah patah.',
                'tindakan' => 'Ganti limit switch.',
                'sparepart_items' => [
                    ['name' => 'Limit switch ejector', 'qty' => 1],
                ],
            ],
            [
                'npk' => '990005',
                'operator_name' => 'Fajar Nugroho',
                'item_name' => 'Console Box',
                'machine_number' => 20,
                'stop_time' => '15:45',
                'days_ago' => 7,
                'problem' => 'Pompa MTC tidak stabil.',
                'status' => RepairStatus::CLOSE,
                'pic' => $budi,
                'claimed_minutes_ago' => 35,
                'form2' => true,
                'category_id' => $utility,
                'work_minutes' => 90,
                'masalah' => 'Mechanical seal pompa MTC bocor.',
                'analisa' => 'Seal aus dan impeller kotor.',
                'tindakan' => 'Ganti mechanical seal dan bersihkan impeller.',
                'sparepart_items' => [
                    ['name' => 'Mechanical seal pompa MTC', 'qty' => 1],
                    ['name' => 'Filter air MTC', 'qty' => 2],
                ],
            ],
            [
                'npk' => '990006',
                'operator_name' => 'Dewi Anggraini',
                'item_name' => 'Cover Spion',
                'machine_number' => 25,
                'stop_time' => '11:30',
                'days_ago' => 8,
                'problem' => 'Proximity mold tidak detect.',
                'status' => RepairStatus::CLOSE,
                'pic' => $cici,
                'claimed_minutes_ago' => 15,
                'form2' => true,
                'category_id' => $mold,
                'work_minutes' => 35,
                'masalah' => 'Sensor proximity mold rusak.',
                'analisa' => 'Kabel sensor putus dekat connector.',
                'tindakan' => 'Ganti proximity sensor.',
                'sparepart_items' => [
                    ['name' => 'Proximity sensor M12', 'qty' => 1],
                    ['name' => 'Connector sensor 4 pin', 'qty' => 1],
                ],
            ],
        ];

        foreach ($rows as $row) {
            $persisted = MaintenanceReport::persistSparepart('sparepart', $row['sparepart_items']);
            $row['sparepart'] = $persisted['text'];
            $row['sparepart_items'] = $persisted['items'];
            $this->upsertTicket($today, $technicianUser, $row);
        }
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function upsertTicket(Carbon $today, User $technicianUser, array $row): void
    {
        $machine = $this->machine((int) $row['machine_number']);
        $pic = $row['pic'] ?? null;
        $claimedMinutesAgo = (int) ($row['claimed_minutes_ago'] ?? 0);
        $workMinutes = (int) ($row['work_minutes'] ?? 0);
        $workDate = $today->copy()->subDays((int) ($row['days_ago'] ?? 0))->startOfDay();
        $claimedAt = $pic ? $workDate->copy()->setTimeFrom($today)->subMinutes($claimedMinutesAgo) : null;
        $finishedAt = $claimedAt && $workMinutes > 0
            ? $claimedAt->copy()->addMinutes($workMinutes)
            : ($claimedAt?->copy());

        $ticket = OperatorReport::query()->updateOrCreate(
            ['npk' => $row['npk']],
            [
                'operator_name' => $row['operator_name'],
                'item_name' => $row['item_name'],
                'machine_name_id' => $machine?->id,
                'machine_number' => $row['machine_number'],
                'stop_date' => $workDate->toDateString(),
                'stop_time' => $row['stop_time'],
                'problem' => $row['problem'],
                'chronology' => $row['chronology'] ?? null,
                'claimed_by' => $pic ? $technicianUser->id : null,
                'claimed_technician_id' => $pic?->id,
                'claimed_at' => $claimedAt,
                'status' => $row['status'],
            ]
        );

        if ($claimedAt && $finishedAt) {
            $ticket->forceFill([
                'created_at' => $claimedAt->copy()->subMinutes(8),
                'updated_at' => $finishedAt,
            ])->save();
        }

        if (empty($row['form2']) && empty($row['category_id'])) {
            MaintenanceReport::query()->where('operator_report_id', $ticket->id)->delete();

            return;
        }

        if (empty($row['category_id']) || $claimedAt === null) {
            return;
        }

        $step2At = ! empty($row['form2']) ? ($finishedAt ?? $claimedAt) : null;
        $picName = $pic?->name ?? $technicianUser->name;

        MaintenanceReport::query()->updateOrCreate(
            ['operator_report_id' => $ticket->id],
            [
                'report_date' => $workDate->toDateString(),
                'machine_number' => $row['machine_number'],
                'machine_name_id' => $machine?->id,
                'damage_category_id' => $row['category_id'],
                'user_id' => $technicianUser->id,
                'pic_name' => $picName,
                'work_shift' => WorkShift::SHIFT_2,
                'repair_started_at' => $claimedAt,
                'repair_finished_at' => $finishedAt ?? $claimedAt,
                'masalah' => $row['masalah'] ?? $row['problem'],
                'analisa_penyebab_kerusakan' => $row['analisa'] ?? null,
                'tindakan_perbaikan' => $row['tindakan'] ?? null,
                'tindakan_perbaikan_2' => null,
                'penggunaan_sparepart' => $row['sparepart'] ?? null,
                'sparepart_items' => $row['sparepart_items'] ?? null,
                'ada_op' => true,
                'status_perbaikan' => $row['status'],
                'step2_completed_at' => $step2At,
            ]
        );
    }

    private function seedPreventive(Carbon $today, User $technicianUser, Technician $andi, Technician $budi, Technician $cici): void
    {
        $reports = [
            ['machine_number' => 1, 'category' => 'PM1', 'pic' => $andi, 'result' => 'ok', 'status' => PreventiveStatus::CLOSE_BY_TECHNICIAN, 'notes' => 'PM1 selesai, kondisi mesin normal.'],
            ['machine_number' => 6, 'category' => 'PM2', 'pic' => $budi, 'result' => 'follow_up', 'status' => PreventiveStatus::CLOSE_BY_TECHNICIAN, 'notes' => 'Oil cooler kotor, perlu flushing lanjutan.', 'not_ok' => 'oil_cooler'],
            ['machine_number' => 10, 'category' => 'HOPPER', 'pic' => $andi, 'result' => 'ok', 'status' => PreventiveStatus::APPROVE_PRODUKSI_INJECTION, 'notes' => 'Hopper dryer dan filter dalam kondisi baik.'],
            ['machine_number' => 14, 'category' => 'ROBOT', 'pic' => $cici, 'result' => 'ok', 'status' => PreventiveStatus::APPROVE_PRODUKSI_INJECTION, 'notes' => 'Robot dan gripper berfungsi normal.'],
            ['machine_number' => 20, 'category' => 'PM1', 'pic' => $budi, 'result' => 'ok', 'status' => PreventiveStatus::CLOSE, 'notes' => 'PM1 mesin 20 OK, greasing sudah dilakukan.'],
            ['machine_number' => 25, 'category' => 'DEHUM', 'pic' => $andi, 'result' => 'follow_up', 'status' => PreventiveStatus::CLOSE, 'notes' => 'Filter dehum perlu diganti minggu ini.'],
        ];

        foreach ($reports as $row) {
            $machine = $this->machine((int) $row['machine_number']);
            $items = PreventiveMachineChecklist::filledOk($row['category']);
            $readings = PreventiveMachineChecklist::filledReadings($row['category']);

            if (! empty($row['not_ok']) && array_key_exists($row['not_ok'], $items)) {
                $items[$row['not_ok']] = PreventiveChecklist::NOT_OK;
            } elseif ($row['result'] === 'follow_up') {
                $firstKey = array_key_first($items);
                if ($firstKey !== null) {
                    $items[$firstKey] = PreventiveChecklist::NOT_OK;
                }
            }

            $robot = $row['category'] === 'ROBOT'
                ? Robot::query()->where('robot_number', sprintf('R%02d', $row['machine_number']))->first()
                    ?? Robot::query()->active()->orderBy('robot_number')->first()
                : null;

            PreventiveReport::query()->updateOrCreate(
                [
                    'machine_number' => $row['machine_number'],
                    'category' => $row['category'],
                    'work_date' => $today->toDateString(),
                ],
                [
                    'user_id' => $technicianUser->id,
                    'pic_name' => $row['pic']->name,
                    'machine_name_id' => $machine?->id,
                    'equipment_number' => $robot?->robot_number,
                    'robot_id' => $robot?->id,
                    'robot_number' => $robot?->robot_number,
                    'robot_type' => $robot?->robot_type,
                    'work_shift' => WorkShift::SHIFT_2,
                    'items' => $items,
                    'readings' => $readings === [] ? null : $readings,
                    'notes' => $row['notes'],
                    'result' => $row['result'],
                    'status' => $row['status'],
                ]
            );
        }
    }

    private function seedFindingRecaps(Carbon $today, User $admin): void
    {
        $rows = [
            [
                'machine_number' => 6,
                'equipment_name' => 'Heater band zona 1',
                'status' => FindingRecapStatus::OPEN,
                'due_days' => 3,
                'notes' => [
                    FindingRecapStatus::OPEN => 'Heater band zona 1 retak saat PM2, suhu tidak stabil.',
                ],
            ],
            [
                'machine_number' => 11,
                'equipment_name' => 'Oil cooler',
                'status' => FindingRecapStatus::ON_PROGRESS,
                'due_days' => 5,
                'notes' => [
                    FindingRecapStatus::OPEN => 'Sirip oil cooler kotor berat.',
                    FindingRecapStatus::ON_PROGRESS => 'Sedang flushing, menunggu spare gasket.',
                ],
            ],
            [
                'machine_number' => 14,
                'equipment_name' => 'Gripper robot vacuum',
                'status' => FindingRecapStatus::MONITORING,
                'due_days' => 7,
                'notes' => [
                    FindingRecapStatus::OPEN => 'Vacuum gripper kadang lepas saat cycle cepat.',
                    FindingRecapStatus::ON_PROGRESS => 'Sudah ganti pad gripper.',
                    FindingRecapStatus::MONITORING => 'Dipantau 2 shift, belum terulang.',
                ],
            ],
            [
                'machine_number' => 20,
                'equipment_name' => 'Filter oli hidrolik',
                'status' => FindingRecapStatus::CLOSE,
                'due_days' => -1,
                'notes' => [
                    FindingRecapStatus::OPEN => 'Filter oli kotor, pressure drop tinggi.',
                    FindingRecapStatus::CLOSE => 'Filter diganti, pressure kembali normal.',
                ],
            ],
            [
                'machine_number' => 8,
                'equipment_name' => 'Selang hidrolik clamping',
                'status' => FindingRecapStatus::OPEN,
                'due_days' => -2,
                'notes' => [
                    FindingRecapStatus::OPEN => 'Selang retak, sudah dilaporkan overdue untuk penggantian.',
                ],
            ],
        ];

        foreach ($rows as $row) {
            $machine = $this->machine((int) $row['machine_number']);
            $recap = FindingRecap::query()->updateOrCreate(
                [
                    'machine_number' => $row['machine_number'],
                    'equipment_name' => $row['equipment_name'],
                ],
                [
                    'user_id' => $admin->id,
                    'machine_name_id' => $machine?->id,
                    'machine_name' => $machine?->name ?? 'Mesin '.$row['machine_number'],
                    'found_date' => $today->toDateString(),
                    'due_date' => $today->copy()->addDays($row['due_days'])->toDateString(),
                    'status' => $row['status'],
                ]
            );

            $existing = $recap->statusNotes()->pluck('note', 'status');
            foreach ($row['notes'] as $status => $note) {
                if (($existing[$status] ?? null) === $note) {
                    continue;
                }

                $recap->addStatusNote($status, $note, $admin->id);
            }
        }
    }

    private function machine(int $number): ?MachineName
    {
        return MachineName::query()->where('machine_number', $number)->first();
    }
}
