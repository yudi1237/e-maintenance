<?php

namespace Tests\Feature;

use App\Models\FindingRecap;
use App\Models\MachineName;
use App\Models\OperatorReport;
use App\Models\PreventiveReport;
use App\Models\User;
use App\Support\FindingRecapStatus;
use App\Support\PreventiveStatus;
use App\Support\RepairStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTablePriorityTest extends TestCase
{
    use RefreshDatabase;

    public function test_incoming_reports_put_pending_approvals_first_for_each_role(): void
    {
        $admin = User::factory()->admin()->create();
        $quality = User::factory()->quality()->create();
        $injection = User::factory()->produksiInjection()->create();
        $machine = $this->machine(3, 'Mesin Prioritas OP');

        $this->ticket($machine, RepairStatus::APPROVE_QUALITY, 'Tiket-Need-Maintenance');
        $this->ticket($machine, RepairStatus::OPEN, 'Tiket-Open-Biasa');
        $this->ticket($machine, RepairStatus::CLOSE_BY_TECHNICIAN, 'Tiket-Need-Injection');
        $this->ticket($machine, RepairStatus::APPROVE_PRODUKSI_INJECTION, 'Tiket-Need-QC');

        $this->actingAs($injection)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['Tiket-Need-Injection', 'Tiket-Need-QC', 'Tiket-Open-Biasa', 'Tiket-Need-Maintenance']);

        $this->actingAs($quality)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['Tiket-Need-QC', 'Tiket-Need-Injection', 'Tiket-Open-Biasa', 'Tiket-Need-Maintenance']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['Tiket-Need-Maintenance', 'Tiket-Need-QC', 'Tiket-Need-Injection', 'Tiket-Open-Biasa']);
    }

    public function test_incoming_reports_can_be_filtered_by_machine_and_stop_date(): void
    {
        $admin = User::factory()->admin()->create();
        $one = $this->machine(1, 'Mesin Filter Satu');
        $two = $this->machine(2, 'Mesin Filter Dua');

        $this->ticket($one, RepairStatus::OPEN, 'Tiket-Mesin-1', '2026-10-01');
        $this->ticket($two, RepairStatus::OPEN, 'Tiket-Mesin-2-Awal', '2026-10-01');
        $this->ticket($two, RepairStatus::ON_PROGRESS, 'Tiket-Mesin-2-Akhir', '2026-10-16');

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['machine_number' => 2]))
            ->assertOk()
            ->assertSee('Tiket-Mesin-2-Awal')
            ->assertSee('Tiket-Mesin-2-Akhir')
            ->assertDontSee('Tiket-Mesin-1');

        $this->actingAs($admin)
            ->get(route('admin.dashboard', [
                'machine_number' => 2,
                'start_date' => '2026-10-10',
                'end_date' => '2026-10-20',
            ]))
            ->assertOk()
            ->assertSee('Tiket-Mesin-2-Akhir')
            ->assertDontSee('Tiket-Mesin-2-Awal')
            ->assertDontSee('Tiket-Mesin-1');
    }

    public function test_preventive_mesin_lists_newest_work_date_first(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create(['name' => 'PIC Preventive']);
        $oldMachine = $this->machine(11, 'Mesin Preventive Lama');
        $newMachine = $this->machine(12, 'Mesin Preventive Baru');

        $this->preventive($technician, $oldMachine, '2026-08-01');
        $this->preventive($technician, $newMachine, '2026-09-08');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['Mesin Preventive Baru', 'Mesin Preventive Lama']);
    }

    public function test_preventive_mesin_puts_pending_approvals_first_for_each_role(): void
    {
        $admin = User::factory()->admin()->create();
        $injection = User::factory()->produksiInjection()->create();
        $technician = User::factory()->create(['name' => 'PIC Preventive']);
        $needMaintenance = $this->machine(13, 'Mesin PM Need Maintenance');
        $needInjection = $this->machine(14, 'Mesin PM Need Injection');
        $alreadyClose = $this->machine(15, 'Mesin PM Sudah Close');

        $this->preventive($technician, $needMaintenance, '2026-09-01', 'PIC-Need-Maintenance', PreventiveStatus::APPROVE_PRODUKSI_INJECTION);
        $this->preventive($technician, $needInjection, '2026-09-08', 'PIC-Need-Injection', PreventiveStatus::CLOSE_BY_TECHNICIAN);
        $this->preventive($technician, $alreadyClose, '2026-09-10', 'PIC-Sudah-Close', PreventiveStatus::CLOSE);

        $this->actingAs($injection)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['PIC-Need-Injection', 'PIC-Sudah-Close', 'PIC-Need-Maintenance']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['PIC-Need-Maintenance', 'PIC-Sudah-Close', 'PIC-Need-Injection']);
    }

    public function test_rekap_temuan_lists_overdue_then_nearest_due_date(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine(8, 'Mesin Rekap Prioritas');
        $today = now()->timezone('Asia/Jakarta');

        $this->recap($admin, $machine, 'Temuan-Due-Minggu-Depan', $today->copy()->addDays(7)->toDateString());
        $this->recap($admin, $machine, 'Temuan-Overdue', $today->copy()->subDays(3)->toDateString());
        $this->recap($admin, $machine, 'Temuan-Due-Besok', $today->copy()->addDay()->toDateString());
        $this->recap($admin, $machine, 'Temuan-Sudah-Close', $today->copy()->subDays(10)->toDateString(), FindingRecapStatus::CLOSE);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['Temuan-Overdue', 'Temuan-Due-Besok', 'Temuan-Due-Minggu-Depan', 'Temuan-Sudah-Close']);
    }

    private function machine(int $number, string $name): MachineName
    {
        return MachineName::query()->create([
            'machine_number' => $number,
            'name' => $name,
            'is_active' => true,
        ]);
    }

    private function ticket(MachineName $machine, string $status, string $itemName, ?string $stopDate = null): OperatorReport
    {
        return OperatorReport::query()->create([
            'operator_name' => 'Operator Uji',
            'npk' => '880100',
            'item_name' => $itemName,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'stop_date' => $stopDate ?? now()->timezone('Asia/Jakarta')->toDateString(),
            'stop_time' => '08:00',
            'problem' => 'Masalah '.$itemName,
            'status' => $status,
        ]);
    }

    private function preventive(
        User $user,
        MachineName $machine,
        string $workDate,
        string $picName = 'PIC Preventive',
        string $status = PreventiveStatus::CLOSE_BY_TECHNICIAN
    ): PreventiveReport {
        return PreventiveReport::query()->create([
            'user_id' => $user->id,
            'pic_name' => $picName,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'category' => 'PM1',
            'work_date' => $workDate,
            'work_shift' => 'shift_1',
            'items' => ['heater' => 'ok'],
            'notes' => $picName,
            'result' => 'ok',
            'status' => $status,
        ]);
    }

    private function recap(
        User $admin,
        MachineName $machine,
        string $equipment,
        string $dueDate,
        string $status = FindingRecapStatus::OPEN
    ): FindingRecap {
        return FindingRecap::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'machine_name' => $machine->name,
            'equipment_name' => $equipment,
            'found_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'due_date' => $dueDate,
            'status' => $status,
        ]);
    }
}
