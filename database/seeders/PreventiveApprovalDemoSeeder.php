<?php

namespace Database\Seeders;

use App\Models\MachineName;
use App\Models\PreventiveReport;
use App\Models\Robot;
use App\Models\Technician;
use App\Models\User;
use App\Support\PreventiveChecklist;
use App\Support\PreventiveMachineChecklist;
use App\Support\PreventiveStatus;
use App\Support\WorkShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PreventiveApprovalDemoSeeder extends Seeder
{
    public const MARKER = '[DEMO APPROVAL]';

    public function run(): void
    {
        if (MachineName::query()->active()->whereNotNull('machine_number')->doesntExist()) {
            $this->call(MachineNameSeeder::class);
        }

        $technicianUser = User::query()->where('npk', '200001')->first()
            ?? User::query()->where('role', User::ROLE_TECHNICIAN)->orderBy('id')->first();

        if ($technicianUser === null) {
            return;
        }

        $pics = Technician::query()->active()->orderBy('id')->get();
        $andi = $pics->firstWhere('npk', '510001')
            ?? $pics->first()
            ?? Technician::updateOrCreate(
                ['npk' => '510001'],
                ['name' => 'Andi PIC', 'pin' => '1234', 'is_active' => true]
            );
        $budi = $pics->firstWhere('npk', '510002')
            ?? $pics->get(1)
            ?? Technician::updateOrCreate(
                ['npk' => '510002'],
                ['name' => 'Budi PIC', 'pin' => '1234', 'is_active' => true]
            );
        $cici = $pics->firstWhere('npk', '510003')
            ?? $pics->get(2)
            ?? Technician::updateOrCreate(
                ['npk' => '510003'],
                ['name' => 'Cici PIC', 'pin' => '1234', 'is_active' => true]
            );

        $today = now()->timezone('Asia/Jakarta');

        $reports = [
            [
                'machine_number' => 3,
                'category' => 'PM1',
                'pic' => $andi,
                'result' => 'ok',
                'status' => PreventiveStatus::CLOSE_BY_TECHNICIAN,
                'notes' => self::MARKER.' PM1 selesai teknisi. Menunggu approve Injection.',
            ],
            [
                'machine_number' => 8,
                'category' => 'PM2',
                'pic' => $budi,
                'result' => 'follow_up',
                'status' => PreventiveStatus::CLOSE_BY_TECHNICIAN,
                'notes' => self::MARKER.' Oil cooler kotor. Menunggu approve Injection.',
                'not_ok' => 'clean_oil_cooler',
            ],
            [
                'machine_number' => 11,
                'category' => 'HOPPER',
                'pic' => $andi,
                'result' => 'ok',
                'status' => PreventiveStatus::APPROVE_PRODUKSI_INJECTION,
                'notes' => self::MARKER.' Hopper OK. Injection sudah approve, menunggu Maintenance.',
            ],
            [
                'machine_number' => 16,
                'category' => 'ROBOT',
                'pic' => $cici,
                'result' => 'ok',
                'status' => PreventiveStatus::APPROVE_PRODUKSI_INJECTION,
                'notes' => self::MARKER.' Robot OK. Injection sudah approve, menunggu Maintenance.',
            ],
            [
                'machine_number' => 21,
                'category' => 'PM1',
                'pic' => $budi,
                'result' => 'ok',
                'status' => PreventiveStatus::CLOSE,
                'notes' => self::MARKER.' PM1 sudah di-approve Injection dan Maintenance.',
            ],
            [
                'machine_number' => 27,
                'category' => 'DEHUM',
                'pic' => $andi,
                'result' => 'follow_up',
                'status' => PreventiveStatus::CLOSE,
                'notes' => self::MARKER.' Filter dehum perlu ganti. Sudah close Maintenance.',
            ],
        ];

        foreach ($reports as $row) {
            $this->upsertDemoReport($today, $technicianUser, $row);
        }
    }

    /**
     * @param  array{machine_number: int, category: string, pic: Technician, result: string, status: string, notes: string, not_ok?: string}  $row
     */
    private function upsertDemoReport(Carbon $today, User $technicianUser, array $row): void
    {
        $machine = MachineName::query()->where('machine_number', $row['machine_number'])->first();
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
                ?? Robot::query()->orderBy('robot_number')->first()
            : null;

        PreventiveReport::query()->updateOrCreate(
            [
                'machine_number' => $row['machine_number'],
                'category' => $row['category'],
                'work_date' => $today->toDateString(),
                'notes' => $row['notes'],
            ],
            [
                'user_id' => $technicianUser->id,
                'pic_name' => $row['pic']->name,
                'machine_name_id' => $machine?->id,
                'equipment_number' => $robot?->robot_number,
                'robot_id' => $robot?->id,
                'robot_number' => $robot?->robot_number,
                'robot_type' => $robot?->robot_type,
                'work_shift' => WorkShift::SHIFT_1,
                'items' => $items,
                'readings' => $readings === [] ? null : $readings,
                'result' => $row['result'],
                'status' => $row['status'],
                'item_remarks' => ($key = array_key_first($items)) === null ? null : [$key => 'Catatan demo approval.'],
                'injection_approved_by' => in_array($row['status'], [PreventiveStatus::APPROVE_PRODUKSI_INJECTION, PreventiveStatus::CLOSE], true)
                    ? User::query()->where('role', User::ROLE_PRODUKSI_INJECTION)->orderBy('id')->value('id')
                    : null,
                'maintenance_approved_by' => $row['status'] === PreventiveStatus::CLOSE
                    ? User::query()->where('role', User::ROLE_ADMIN)->orderBy('id')->value('id')
                    : null,
            ]
        );
    }
}
