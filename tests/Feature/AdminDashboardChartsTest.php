<?php

namespace Tests\Feature;

use App\Models\DamageCategory;
use App\Models\FindingRecap;
use App\Models\MachineName;
use App\Models\MaintenanceReport;
use App\Models\OperatorReport;
use App\Models\PreventiveReport;
use App\Models\PreventiveSchedule;
use App\Models\User;
use App\Support\DashboardAnalytics;
use App\Support\FindingRecapStatus;
use App\Support\RepairStatus;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardChartsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_chart_sections(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Performer Teknisi')
            ->assertSee('MTTR Teknisi')
            ->assertSee('Grafik Line Stop')
            ->assertSee('PM Status')
            ->assertSee('Semua Laporan Masuk')
            ->assertSee('Export Laporan')
            ->assertSee('Download Excel')
            ->assertSee(route('admin.reports.export'), false)
            ->assertSee('Filter daftar tiket')
            ->assertSee('Target')
            ->assertSee('Actual')
            ->assertSee('Planned')
            ->assertSee('Pending')
            ->assertSee('Completed')
            ->assertSee('Remaining')
            ->assertSee('Unplanned')
            ->assertDontSee('Grafik Mesin')
            ->assertSee(now()->timezone('Asia/Jakarta')->format('Y'))
            ->assertSee('Normal')
            ->assertSee('Ada OP')
            ->assertSee('Belum Close Approval / Log Temuan')
            ->assertDontSee('Ada OP / Log Temuan')
            ->assertSee('em-machine-alert', false)
            ->assertSee('em-machine-alert-dot', false)
            ->assertSee('em-machine-pending', false)
            ->assertSee('em-machine-pending-dot', false)
            ->assertSee('machine-card-kicker', false)
            ->assertDontSee('em-fs-hide-kicker', false)
            ->assertDontSee('filter: brightness', false)
            ->assertDontSee('bg-amber-500/80', false)
            ->assertDontSee('Preventive hari ini')
            ->assertSee('function toggleChart', false)
            ->assertSee('dashboard-kiosk', false)
            ->assertSee('dashboard-fullscreen-btn', false)
            ->assertSee('Fullscreen')
            ->assertSee('fitKioskMapGrid', false)
            ->assertSee('em-pm-filter', false);
    }

    public function test_machine_map_shows_open_op_and_open_log_temuan(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 4,
            'name' => 'Hwa Chin HC 600 SE 600T',
            'brand' => 'Hwa Chin',
            'tonnage' => 600,
            'is_active' => true,
        ]);
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        OperatorReport::query()->create([
            'operator_name' => 'Budi',
            'npk' => '123456',
            'item_name' => 'Cover',
            'machine_name_id' => $machine->id,
            'machine_number' => 4,
            'stop_date' => $today,
            'stop_time' => '08:00',
            'problem' => 'Mesin stop',
            'status' => RepairStatus::CLOSE_BY_TECHNICIAN,
        ]);
        OperatorReport::query()->create([
            'operator_name' => 'Siti',
            'npk' => '654321',
            'item_name' => 'Tiket Close',
            'machine_name_id' => $machine->id,
            'machine_number' => 4,
            'stop_date' => $today,
            'stop_time' => '09:00',
            'problem' => 'Sudah approve maintenance',
            'status' => RepairStatus::CLOSE,
        ]);

        FindingRecap::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 4,
            'machine_name' => $machine->viewLabel(),
            'equipment_name' => 'Heater band',
            'found_date' => $today,
            'due_date' => $today,
            'status' => FindingRecapStatus::OPEN,
        ]);
        FindingRecap::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 4,
            'machine_name' => $machine->viewLabel(),
            'equipment_name' => 'Temuan sudah close',
            'found_date' => $today,
            'due_date' => $today,
            'status' => FindingRecapStatus::CLOSE,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Peta')
            ->assertSee('Hwa Chin 600T')
            ->assertSee('OP :')
            ->assertSee('Log Temuan :')
            ->assertSee('Planned PM Due :')
            ->assertSee('em-machine-card--pending', false)
            ->assertDontSee('data-machine="4" data-alert="active"', false);

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->assertJsonPath('machines.4.op', 1)
            ->assertJsonPath('machines.4.findings', 1)
            ->assertJsonPath('machines.4.alert', 'pending')
            ->assertJsonPath('machines.4.pm_due', null);
    }

    public function test_machine_map_blinks_red_for_open_op_and_stays_normal_when_clear(): void
    {
        $admin = User::factory()->admin()->create();
        $openMachine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Open OP',
            'is_active' => true,
        ]);
        MachineName::query()->create([
            'machine_number' => 8,
            'name' => 'Mesin Normal',
            'is_active' => true,
        ]);
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        OperatorReport::query()->create([
            'operator_name' => 'Budi',
            'npk' => '123456',
            'item_name' => 'Cover',
            'machine_name_id' => $openMachine->id,
            'machine_number' => 7,
            'stop_date' => $today,
            'stop_time' => '08:00',
            'problem' => 'Mesin stop',
            'status' => RepairStatus::OPEN,
        ]);

        $html = $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/data-machine="7"[^>]*em-machine-card--active/u',
            $html
        );
        $this->assertMatchesRegularExpression(
            '/data-machine="8"[^>]*em-machine-card--idle/u',
            $html
        );

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->assertJsonPath('machines.7.alert', 'active')
            ->assertJsonPath('machines.8.alert', 'idle');
    }

    public function test_machine_map_shows_nearest_planned_pm_due_from_schedule(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 4,
            'name' => 'Hwa Chin HC 600 SE 600T',
            'brand' => 'Hwa Chin',
            'tonnage' => 600,
            'is_active' => true,
        ]);
        $other = MachineName::query()->create([
            'machine_number' => 8,
            'name' => 'Mesin Lain',
            'is_active' => true,
        ]);
        $today = now()->timezone('Asia/Jakarta');
        $nearest = $today->copy()->addDays(3);
        $later = $today->copy()->addDays(10);
        $otherDate = $today->copy()->addDays(1);

        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 4,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => $machine->machine_type,
            'plan_date' => $later->toDateString(),
            'preventive_type' => 'PM1',
        ]);
        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 4,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => $machine->machine_type,
            'plan_date' => $nearest->toDateString(),
            'preventive_type' => 'PM2',
        ]);
        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $other->id,
            'machine_number' => 8,
            'machine_name' => $other->viewLabel(),
            'machine_type' => $other->machine_type,
            'plan_date' => $otherDate->toDateString(),
            'preventive_type' => 'PM1',
        ]);

        $nearestLabel = $nearest->format('d/m/Y');
        $laterLabel = $later->format('d/m/Y');
        $otherLabel = $otherDate->format('d/m/Y');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Planned PM Due :')
            ->assertSee($nearestLabel)
            ->assertDontSee($laterLabel);

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->assertJsonPath('machines.4.pm_due', $nearestLabel)
            ->assertJsonPath('machines.8.pm_due', $otherLabel);
    }

    public function test_machine_map_shows_overdue_pm_when_no_upcoming_schedule(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 5,
            'name' => 'Mesin Overdue',
            'is_active' => true,
        ]);
        $today = now()->timezone('Asia/Jakarta');
        $older = $today->copy()->subDays(10);
        $recentOverdue = $today->copy()->subDays(2);

        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 5,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => $machine->machine_type,
            'plan_date' => $older->toDateString(),
            'preventive_type' => 'PM1',
        ]);
        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 5,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => $machine->machine_type,
            'plan_date' => $recentOverdue->toDateString(),
            'preventive_type' => 'PM2',
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->assertJsonPath('machines.5.pm_due', $recentOverdue->format('d/m/Y'));
    }

    public function test_machine_map_pm_due_moves_to_next_open_schedule_after_completion(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 4,
            'name' => 'Hwa Chin HC 600 SE 600T',
            'brand' => 'Hwa Chin',
            'tonnage' => 600,
            'is_active' => true,
        ]);
        $today = now()->timezone('Asia/Jakarta');
        $nearest = $today->copy()->addDays(1);
        $later = $today->copy()->addDays(8);

        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 4,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => $machine->machine_type,
            'plan_date' => $nearest->toDateString(),
            'preventive_type' => 'PM1',
        ]);
        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 4,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => $machine->machine_type,
            'plan_date' => $later->toDateString(),
            'preventive_type' => 'PM2',
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->assertJsonPath('machines.4.pm_due', $nearest->format('d/m/Y'));

        PreventiveReport::query()->create([
            'user_id' => $technician->id,
            'pic_name' => $technician->name,
            'machine_name_id' => $machine->id,
            'machine_number' => 4,
            'category' => 'PM1',
            'work_date' => $today->toDateString(),
            'work_shift' => 'shift_1',
            'items' => ['heater' => 'ok'],
            'notes' => 'OK',
            'result' => 'ok',
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->assertJsonPath('machines.4.pm_due', $later->format('d/m/Y'));
    }

    public function test_dashboard_lists_open_and_on_progress_op_tickets(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $technician = User::factory()->create(['name' => 'Andi Teknisi']);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);

        OperatorReport::query()->create([
            'operator_name' => 'Budi',
            'npk' => '123456',
            'item_name' => 'Cover Dashboard OP',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'stop_time' => '14:30',
            'problem' => 'Mesin stuck di clamping',
            'status' => RepairStatus::OPEN,
        ]);
        OperatorReport::query()->create([
            'operator_name' => 'Siti',
            'npk' => '654321',
            'item_name' => 'Panel Pintu OP',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'stop_time' => '11:00',
            'problem' => 'Sensor tidak merespons',
            'claimed_by' => $technician->id,
            'claimed_at' => now()->subMinutes(20),
            'status' => RepairStatus::ON_PROGRESS,
        ]);
        OperatorReport::query()->create([
            'operator_name' => 'Andi',
            'npk' => '111111',
            'item_name' => 'Tiket Sudah Close',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'stop_time' => '08:00',
            'problem' => 'Sudah ditutup admin',
            'status' => RepairStatus::CLOSE,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Tiket OP')
            ->assertSee('Cover Dashboard OP')
            ->assertSee('Mesin stuck di clamping')
            ->assertSee('Panel Pintu OP')
            ->assertSee('Andi Teknisi')
            ->assertSee('Belum diambil')
            ->assertDontSee('Tiket Sudah Close')
            ->assertDontSee('Sudah ditutup admin');

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->assertJsonPath('active_machines', 0)
            ->assertJsonPath('total', 3);
    }

    public function test_admin_can_open_op_ticket_detail(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $ticket = OperatorReport::query()->create([
            'operator_name' => 'Budi',
            'npk' => '123456',
            'item_name' => 'Cover Dashboard OP',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => '2026-08-20',
            'stop_time' => '14:30',
            'problem' => 'Mesin stuck di clamping',
            'chronology' => 'Stop saat clamping',
            'status' => RepairStatus::OPEN,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.operator-reports.show', $ticket))
            ->assertOk()
            ->assertSee('Tiket #'.$ticket->id)
            ->assertSee('Cover Dashboard OP')
            ->assertSee('Mesin stuck di clamping')
            ->assertSee('Stop saat clamping')
            ->assertSee('Belum diambil');
    }

    public function test_stats_payload_includes_charts_from_claimed_op(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $andi = User::factory()->create(['name' => 'Andi Teknisi']);
        $budi = User::factory()->create(['name' => 'Budi Teknisi']);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $category = DamageCategory::query()->create([
            'name' => 'Mesin Injection',
            'allows_other' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->closedTicket($andi, $machine, $category, 40);
        $this->closedTicket($andi, $machine, $category, 90);
        OperatorReport::query()->create([
            'operator_name' => 'Operator',
            'npk' => '111111',
            'item_name' => 'Cover',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'stop_time' => '10:00',
            'problem' => 'Masih dikerjakan',
            'claimed_by' => $budi->id,
            'claimed_at' => now()->subMinutes(15),
            'status' => RepairStatus::ON_PROGRESS,
        ]);

        $charts = DashboardAnalytics::charts();

        $this->assertContains('Andi Teknisi', $charts['performer']['labels']);
        $this->assertContains('Budi Teknisi', $charts['performer']['labels']);
        $this->assertContains('Andi Teknisi', $charts['technician_mttr']['labels']);
        $this->assertNotContains('Budi Teknisi', $charts['technician_mttr']['labels']);
        $this->assertSame(65, $charts['technician_mttr']['values'][0]);
        $this->assertSame(65, $charts['technician_mttr']['avg_minutes']);
        $this->assertContains('Mesin 2', $charts['machines']['labels']);
        $this->assertSame(65, $charts['machines']['values'][0]);
        $this->assertStringContainsString((string) now()->timezone('Asia/Jakarta')->year, $charts['period_label']);

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->assertJsonPath('charts.technician_mttr.labels.0', 'Andi Teknisi')
            ->assertJsonPath('charts.technician_mttr.values.0', 65)
            ->assertJsonPath('charts.machines.labels.0', 'Mesin 2');
    }

    public function test_admin_can_delete_op_ticket_from_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $ticket = $this->openTicket();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Delete')
            ->assertSee(route('admin.operator-reports.destroy', $ticket), false);

        $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->delete(route('admin.operator-reports.destroy', $ticket))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseMissing('operator_reports', ['id' => $ticket->id]);
        $this->assertDatabaseMissing('maintenance_reports', ['operator_report_id' => $ticket->id]);
    }

    public function test_pm_status_counts_planned_pending_completed_remaining_and_unplanned(): void
    {
        $admin = User::factory()->admin()->create();
        $this->travelTo(now()->timezone('Asia/Jakarta')->setDate(2026, 9, 15)->startOfDay());
        $now = now()->timezone('Asia/Jakarta');
        $year = $now->year;
        $month = $now->month;
        $current = $now->copy()->startOfMonth()->addDays(9)->toDateString();
        $previous = $now->copy()->subMonth()->startOfMonth()->addDays(14)->toDateString();

        $doneMachine = $this->namedMachine(1);
        $pendingMachine = $this->namedMachine(2);
        $remainingMachine = $this->namedMachine(3);
        $unplannedMachine = $this->namedMachine(4);

        $this->schedule($admin, $doneMachine, $current, 'PM1');
        $this->preventive($admin, $doneMachine, $current, 'PM1');
        $this->schedule($admin, $pendingMachine, $previous, 'PM1');
        $this->schedule($admin, $remainingMachine, $current, 'PM2');
        $this->preventive($admin, $unplannedMachine, $current, 'PM3');

        $status = DashboardAnalytics::pmStatus($year, $month);

        $this->assertSame(2, $status['planned']);
        $this->assertSame(1, $status['pending']);
        $this->assertSame(1, $status['completed']);
        $this->assertSame(2, $status['remaining']);
        $this->assertSame(1, $status['unplanned']);
        $this->assertSame(3, $status['target']);
        $this->assertSame(1, $status['actual']);
        $this->assertSame(33.33, $status['percent']);
        $this->assertSame('33.33%', $status['percent_label']);

        $yearly = DashboardAnalytics::pmStatus($year, null);
        $this->assertSame(3, $yearly['planned']);
        $this->assertSame(0, $yearly['pending']);
        $this->assertSame(1, $yearly['completed']);
        $this->assertSame(2, $yearly['remaining']);
        $this->assertSame(1, $yearly['unplanned']);

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats', [
                'pm_year' => $year,
                'pm_month' => $month,
            ]))
            ->assertOk()
            ->assertJsonPath('pm_status.planned', 2)
            ->assertJsonPath('pm_status.pending', 1)
            ->assertJsonPath('pm_status.completed', 1)
            ->assertJsonPath('pm_status.remaining', 2)
            ->assertJsonPath('pm_status.unplanned', 1);

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats', [
                'pm_year' => $year,
                'pm_month' => '',
            ]))
            ->assertOk()
            ->assertJsonPath('pm_status.planned', 3)
            ->assertJsonPath('pm_status.pending', 0)
            ->assertJsonPath('pm_status.month', null);
    }

    public function test_produksi_injection_cannot_delete_op_tickets(): void
    {
        $produksi = User::factory()->produksiInjection()->create();
        $ticket = $this->openTicket();

        $this->actingAs($produksi)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Cover Dashboard OP')
            ->assertDontSee('Hapus tiket OP');

        $this->actingAs($produksi)
            ->delete(route('admin.operator-reports.destroy', $ticket))
            ->assertForbidden();

        $this->assertDatabaseHas('operator_reports', ['id' => $ticket->id]);
    }

    public function test_machine_history_shows_repair_columns_and_admin_can_delete(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create(['name' => 'Andi Teknisi']);
        $machine = MachineName::query()->create([
            'machine_number' => 1,
            'name' => 'Mesin Uji 1',
            'is_active' => true,
        ]);
        $category = DamageCategory::query()->create([
            'name' => 'Mesin Injection',
            'allows_other' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $ticket = OperatorReport::query()->create([
            'operator_name' => 'Budi',
            'npk' => '123456',
            'item_name' => 'Cover',
            'machine_name_id' => $machine->id,
            'machine_number' => 1,
            'stop_date' => '2026-08-20',
            'stop_time' => '14:30',
            'problem' => 'Mesin stuck di clamping',
            'claimed_by' => $technician->id,
            'claimed_at' => now()->subHour(),
            'status' => RepairStatus::CLOSE,
        ]);
        $report = MaintenanceReport::query()->create([
            'report_date' => '2026-08-20',
            'machine_number' => 1,
            'machine_name_id' => $machine->id,
            'damage_category_id' => $category->id,
            'user_id' => $technician->id,
            'pic_name' => 'Andi Teknisi',
            'work_shift' => 'shift_1',
            'repair_started_at' => now()->subHour(),
            'repair_finished_at' => now()->subMinutes(10),
            'masalah' => 'Mesin stuck di clamping',
            'analisa_penyebab_kerusakan' => 'Seal aus pada clamping',
            'tindakan_perbaikan' => 'Ganti seal clamping',
            'penggunaan_sparepart' => 'Seal 50x70x10',
            'status_perbaikan' => RepairStatus::CLOSE,
            'operator_report_id' => $ticket->id,
            'ada_op' => true,
            'step2_completed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.machines.show', 1))
            ->assertOk()
            ->assertSee('Analisa Penyebab Kerusakan')
            ->assertSee('Kategori Penyebab Kerusakan')
            ->assertSee('Tindakan Perbaikan')
            ->assertSee('Penggunaan Sparepart')
            ->assertSee('Seal aus pada clamping')
            ->assertSee('Ganti seal clamping')
            ->assertSee('Seal 50x70x10')
            ->assertSee('Andi Teknisi')
            ->assertSee('Delete');

        $this->actingAs($admin)
            ->from(route('admin.machines.show', 1))
            ->delete(route('admin.reports.destroy', $report))
            ->assertRedirect(route('admin.machines.show', 1));

        $this->assertDatabaseMissing('maintenance_reports', ['id' => $report->id]);
        $this->assertDatabaseMissing('operator_reports', ['id' => $ticket->id]);
    }

    public function test_charts_only_include_current_month_and_reset_next_month(): void
    {
        $andi = User::factory()->create(['name' => 'Andi Teknisi']);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $category = DamageCategory::query()->create([
            'name' => 'Mesin Injection',
            'allows_other' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->closedTicket($andi, $machine, $category, 40);
        $this->closedTicket($andi, $machine, $category, 90, now()->timezone('Asia/Jakarta')->subMonth());

        $charts = DashboardAnalytics::charts();

        $this->assertSame(40, $charts['machines']['values'][0] ?? 0);
        $this->assertSame(['Andi Teknisi'], $charts['technician_mttr']['labels']);
        $this->assertSame([40], $charts['technician_mttr']['values']);
        $this->assertSame(40, $charts['technician_mttr']['avg_minutes']);
    }

    private function namedMachine(int $number): MachineName
    {
        return MachineName::query()->create([
            'machine_number' => $number,
            'name' => 'Mesin PM '.$number,
            'is_active' => true,
        ]);
    }

    private function schedule(User $user, MachineName $machine, string $planDate, string $type): PreventiveSchedule
    {
        return PreventiveSchedule::query()->create([
            'user_id' => $user->id,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => $machine->machine_type,
            'plan_date' => $planDate,
            'preventive_type' => $type,
            'notes' => null,
        ]);
    }

    private function preventive(User $user, MachineName $machine, string $workDate, string $category): PreventiveReport
    {
        return PreventiveReport::query()->create([
            'user_id' => $user->id,
            'pic_name' => $user->name,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'category' => $category,
            'work_date' => $workDate,
            'work_shift' => 'shift_1',
            'items' => ['heater' => 'ok'],
            'notes' => 'OK',
            'result' => 'ok',
        ]);
    }

    private function openTicket(): OperatorReport
    {
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);

        return OperatorReport::query()->create([
            'operator_name' => 'Budi',
            'npk' => '123456',
            'item_name' => 'Cover Dashboard OP',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'stop_time' => '14:30',
            'problem' => 'Mesin stuck di clamping',
            'status' => RepairStatus::OPEN,
        ]);
    }

    private function closedTicket(
        User $technician,
        MachineName $machine,
        DamageCategory $category,
        int $workMinutes,
        ?CarbonInterface $at = null
    ): void {
        $at = $at?->timezone('Asia/Jakarta') ?? now()->timezone('Asia/Jakarta');
        $claimedAt = $at->copy()->subMinutes($workMinutes + 5);
        $finishedAt = $claimedAt->copy()->addMinutes($workMinutes);

        $ticket = OperatorReport::query()->create([
            'operator_name' => 'Operator',
            'npk' => '222222',
            'item_name' => 'Cover',
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'stop_date' => $at->toDateString(),
            'stop_time' => '08:00',
            'problem' => 'Mesin stop',
            'claimed_by' => $technician->id,
            'claimed_at' => $claimedAt,
            'status' => RepairStatus::CLOSE_BY_TECHNICIAN,
        ]);
        $ticket->forceFill([
            'created_at' => $claimedAt,
            'updated_at' => $finishedAt,
        ])->save();

        $report = MaintenanceReport::query()->create([
            'report_date' => $at->toDateString(),
            'machine_number' => $machine->machine_number,
            'machine_name_id' => $machine->id,
            'damage_category_id' => $category->id,
            'user_id' => $technician->id,
            'pic_name' => $technician->name,
            'work_shift' => 'shift_1',
            'repair_started_at' => $claimedAt,
            'repair_finished_at' => $finishedAt,
            'status_perbaikan' => RepairStatus::CLOSE_BY_TECHNICIAN,
            'operator_report_id' => $ticket->id,
            'ada_op' => true,
            'step2_completed_at' => $finishedAt,
        ]);
        $report->forceFill([
            'created_at' => $claimedAt,
            'updated_at' => $finishedAt,
        ])->save();
    }
}
