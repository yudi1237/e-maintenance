<?php

namespace Tests\Feature;

use App\Models\DamageCategory;
use App\Models\MachineName;
use App\Models\MaintenanceReport;
use App\Models\OperatorReport;
use App\Models\User;
use App\Support\RepairStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSparepartHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_has_history_spare_part_menu(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('History Spare Part')
            ->assertSee(route('admin.sparepart-history.index'), false);
    }

    public function test_admin_sees_op_sparepart_rows_newest_first(): void
    {
        $admin = User::factory()->admin()->create();
        $this->completedOpReport('2026-09-01', 1, 'Budi PIC', [
            ['name' => 'Seal hydraulic', 'qty' => 1],
        ]);
        $this->completedOpReport('2026-09-10', 2, 'Andi PIC', [
            ['name' => 'Bearing 6205', 'qty' => 2],
            ['name' => 'O-ring', 'qty' => 4],
        ]);
        $this->completedOpReport('2026-09-08', 3, 'Cici PIC', [], 'Tidak ada');
        $this->completedOpReport('2026-09-12', 4, 'Dedi PIC', [
            ['name' => 'Hose 1/2', 'qty' => 3],
        ], status: RepairStatus::CLOSE_BY_TECHNICIAN);
        $this->completedOpReport('2026-09-13', 5, 'Eko PIC', [
            ['name' => 'Valve 24V', 'qty' => 1],
        ], status: RepairStatus::APPROVE_PRODUKSI_INJECTION);

        $this->actingAs($admin)
            ->get(route('admin.sparepart-history.index'))
            ->assertOk()
            ->assertSee('History Spare Part')
            ->assertSee('Tanggal')
            ->assertSee('Sparepart')
            ->assertSee('Qty')
            ->assertSee('Mesin No')
            ->assertSee('PIC Teknisi')
            ->assertSee('Bearing 6205')
            ->assertSee('O-ring')
            ->assertSee('Seal hydraulic')
            ->assertSee('Andi PIC')
            ->assertSee('Budi PIC')
            ->assertDontSee('Tidak ada')
            ->assertDontSee('Cici PIC')
            ->assertDontSee('Hose 1/2')
            ->assertDontSee('Dedi PIC')
            ->assertDontSee('Valve 24V')
            ->assertDontSee('Eko PIC');

        $html = $this->actingAs($admin)
            ->get(route('admin.sparepart-history.index'))
            ->getContent();

        $this->assertLessThan(
            strpos($html, 'Seal hydraulic'),
            strpos($html, 'Bearing 6205')
        );
    }

    public function test_admin_can_filter_history_spare_part(): void
    {
        $admin = User::factory()->admin()->create();
        $this->completedOpReport('2026-09-01', 1, 'Budi PIC', [
            ['name' => 'Seal hydraulic', 'qty' => 1],
        ]);
        $this->completedOpReport('2026-09-10', 2, 'Andi PIC', [
            ['name' => 'Bearing 6205', 'qty' => 2],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.sparepart-history.index', [
                'start_date' => '2026-09-05',
                'end_date' => '2026-09-15',
                'machine_number' => 2,
                'sparepart' => 'Bearing',
                'pic' => 'Andi',
            ]))
            ->assertOk()
            ->assertSee('Bearing 6205')
            ->assertSee('Andi PIC')
            ->assertDontSee('Seal hydraulic')
            ->assertDontSee('Budi PIC');
    }

    public function test_admin_can_export_history_spare_part_excel(): void
    {
        $admin = User::factory()->admin()->create();
        $this->completedOpReport('2026-09-10', 2, 'Andi PIC', [
            ['name' => 'Bearing 6205', 'qty' => 2],
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.sparepart-history.export'));

        $response->assertOk();
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }

    /**
     * @param  list<array{name: string, qty: int}>  $items
     */
    private function completedOpReport(
        string $date,
        int $machineNumber,
        string $picName,
        array $items,
        ?string $text = null,
        string $status = RepairStatus::CLOSE,
    ): MaintenanceReport {
        $technician = User::factory()->create(['name' => $picName]);
        $category = DamageCategory::query()->first() ?? DamageCategory::query()->create([
            'name' => 'Mesin Injection',
            'allows_other' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => $machineNumber,
            'name' => 'Mesin '.$machineNumber,
            'is_active' => true,
        ]);
        $ticket = OperatorReport::query()->create([
            'operator_name' => 'Budi',
            'npk' => '123456',
            'item_name' => 'Cover',
            'machine_name_id' => $machine->id,
            'machine_number' => $machineNumber,
            'stop_date' => $date,
            'stop_time' => '14:30',
            'problem' => 'Mesin stuck',
            'claimed_by' => $technician->id,
            'claimed_at' => $date.' 08:00:00',
            'status' => $status,
        ]);

        $sparepart = MaintenanceReport::persistSparepart(
            $items === [] ? 'none' : 'sparepart',
            $items
        );

        return MaintenanceReport::query()->create([
            'report_date' => $date,
            'machine_number' => $machineNumber,
            'machine_name_id' => $machine->id,
            'damage_category_id' => $category->id,
            'user_id' => $technician->id,
            'pic_name' => $picName,
            'work_shift' => 'shift_1',
            'repair_started_at' => $date.' 08:00:00',
            'repair_finished_at' => $date.' 10:00:00',
            'penggunaan_sparepart' => $text ?? $sparepart['text'],
            'sparepart_items' => $sparepart['items'],
            'status_perbaikan' => $status,
            'operator_report_id' => $ticket->id,
            'ada_op' => true,
            'step2_completed_at' => $date.' 10:00:00',
        ]);
    }
}
