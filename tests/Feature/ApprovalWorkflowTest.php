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

class ApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengguna_page_offers_produksi_injection_and_admin_roles(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Quality')
            ->assertSee('Produksi Injection')
            ->assertSee('Management View Dashboard')
            ->assertSee('Admin Maintenance');
    }

    public function test_admin_can_create_quality_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'User Quality',
            'npk' => '310001',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_QUALITY,
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['npk' => '310001', 'role' => User::ROLE_QUALITY]);
    }

    public function test_admin_can_create_management_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'User Management',
            'npk' => '500101',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_MANAGEMENT,
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['npk' => '500101', 'role' => User::ROLE_MANAGEMENT]);
    }

    public function test_admin_can_create_produksi_injection_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'User Produksi',
            'npk' => '410001',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_PRODUKSI_INJECTION,
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['npk' => '410001', 'role' => User::ROLE_PRODUKSI_INJECTION]);
    }

    public function test_produksi_injection_can_open_admin_dashboard(): void
    {
        $produksi = User::factory()->produksiInjection()->create();

        $this->actingAs($produksi)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Semua Laporan Masuk')
            ->assertSee('Total Laporan Harian')
            ->assertSee('Belum Approve (Produksi Injection)')
            ->assertSee('Status Approve (Produksi Injection) · hari ini')
            ->assertDontSee('Belum Approve (Quality)')
            ->assertDontSee('Belum Approve (Admin Maintenance)')
            ->assertDontSee('Performer Teknisi')
            ->assertDontSee('PM Status')
            ->assertDontSee('Export Laporan')
            ->assertDontSee('Peta')
            ->assertSee('Preventive Mesin')
            ->assertDontSee('Log Temuan')
            ->assertDontSee('History Spare Part')
            ->assertDontSee('Master Data Mesin')
            ->assertDontSee('Master Data Equipment')
            ->assertDontSee('Master Kategori')
            ->assertDontSee('Master Data Preventive');
    }

    public function test_produksi_injection_cannot_open_admin_master_menus(): void
    {
        $produksi = User::factory()->produksiInjection()->create();

        $this->actingAs($produksi)->get(route('admin.preventive.index'))->assertForbidden();
        $this->actingAs($produksi)->get(route('admin.finding-recaps.index'))->assertForbidden();
        $this->actingAs($produksi)->get(route('admin.finding-recaps.export'))->assertForbidden();
        $this->actingAs($produksi)->get(route('admin.sparepart-history.index'))->assertForbidden();
        $this->actingAs($produksi)->get(route('admin.sparepart-history.export'))->assertForbidden();
        $this->actingAs($produksi)->get(route('admin.machine-names.index'))->assertForbidden();
        $this->actingAs($produksi)->get(route('admin.robots.index'))->assertForbidden();
        $this->actingAs($produksi)->get(route('admin.damage-categories.index'))->assertForbidden();
        $this->actingAs($produksi)->get(route('admin.preventive-checklist.index'))->assertForbidden();
    }

    public function test_dashboard_stat_cards_follow_login_role(): void
    {
        $admin = User::factory()->admin()->create();
        $quality = User::factory()->quality()->create();
        $produksi = User::factory()->produksiInjection()->create();
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        $this->ticketAt(1, RepairStatus::CLOSE_BY_TECHNICIAN, $today);
        $this->ticketAt(2, RepairStatus::APPROVE_PRODUKSI_INJECTION, $today);
        $this->ticketAt(3, RepairStatus::APPROVE_QUALITY, $today);
        $this->ticketAt(4, RepairStatus::CLOSE, $today);

        $this->actingAs($produksi)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Belum Approve (Produksi Injection)')
            ->assertSee('Status Approve (Produksi Injection) · hari ini');

        $this->actingAs($produksi)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->assertJsonPath('active_machines', 1)
            ->assertJsonPath('closed', 3);

        $this->actingAs($quality)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Belum Approve (Quality)')
            ->assertSee('Status Approve (Quality) · hari ini');
        $this->actingAs($quality)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->assertJsonPath('active_machines', 1)
            ->assertJsonPath('closed', 2);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Belum Approve (Admin Maintenance)')
            ->assertSee('Status Approve (Admin Maintenance) · hari ini');
        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->assertJsonPath('active_machines', 1)
            ->assertJsonPath('closed', 1);
    }

    public function test_produksi_injection_cannot_manage_users(): void
    {
        $produksi = User::factory()->produksiInjection()->create();

        $this->actingAs($produksi)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_technician_cannot_open_admin_dashboard(): void
    {
        $technician = User::factory()->create();

        $this->actingAs($technician)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('home'));
    }

    public function test_produksi_login_redirects_to_dashboard(): void
    {
        $produksi = User::factory()->produksiInjection()->create();

        $this->post('/login', ['npk' => $produksi->npk, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_quality_can_open_admin_dashboard(): void
    {
        $quality = User::factory()->quality()->create();

        $this->actingAs($quality)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Semua Laporan Masuk')
            ->assertSee('Belum Approve (Quality)')
            ->assertDontSee('Performer Teknisi')
            ->assertDontSee('Peta')
            ->assertDontSee('Preventive Mesin');
    }

    public function test_quality_login_redirects_to_dashboard(): void
    {
        $quality = User::factory()->quality()->create();

        $this->post('/login', ['npk' => $quality->npk, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_management_dashboard_shows_machine_cards_and_charts(): void
    {
        $management = User::factory()->management()->create();
        $this->ticketAt(1, RepairStatus::OPEN, now()->timezone('Asia/Jakarta')->toDateString());

        $this->assertNull(RepairStatus::dashboardTicketOrderSql($management));

        $this->actingAs($management)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Management')
            ->assertSee('Peta')
            ->assertDontSee('Performer Teknisi')
            ->assertDontSee('MTTR Teknisi')
            ->assertSee('Grafik Line Stop')
            ->assertSee('PM Status')
            ->assertSee('Preventive Mesin')
            ->assertSee('Log Temuan')
            ->assertSee('Belum Approve (Semua proses)')
            ->assertDontSee('Download Excel')
            ->assertDontSee('Download semua QR')
            ->assertDontSee('Kelola log temuan')
            ->assertDontSee('Master Data Mesin')
            ->assertDontSee('Pengguna');

        $this->actingAs($management)->get(route('admin.preventive.index'))->assertForbidden();
        $this->actingAs($management)->get(route('admin.finding-recaps.index'))->assertForbidden();
        $this->actingAs($management)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($management)->get(route('admin.reports.export'))->assertForbidden();
    }

    public function test_management_login_redirects_to_dashboard(): void
    {
        $management = User::factory()->management()->create();

        $this->post('/login', ['npk' => $management->npk, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_close_by_technician_must_follow_produksi_then_quality_then_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $quality = User::factory()->quality()->create();
        $produksi = User::factory()->produksiInjection()->create();
        $report = $this->closedByTechnicianReport();

        $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->patch(route('admin.reports.update-status', $report), [
                'status_perbaikan' => RepairStatus::CLOSE,
                'tindakan_perbaikan' => 'Selesai dicek admin',
                'damage_cause_category' => 'component_failure',
            ])
            ->assertSessionHasErrors('status_perbaikan');

        $this->actingAs($produksi)
            ->from(route('admin.dashboard'))
            ->patch(route('admin.reports.update-status', $report), [
                'status_perbaikan' => RepairStatus::APPROVE_PRODUKSI_INJECTION,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(RepairStatus::APPROVE_PRODUKSI_INJECTION, $report->fresh()->status_perbaikan);
        $this->assertSame(RepairStatus::APPROVE_PRODUKSI_INJECTION, $report->operatorReport->fresh()->status);

        $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->patch(route('admin.reports.update-status', $report), [
                'status_perbaikan' => RepairStatus::CLOSE,
                'tindakan_perbaikan' => 'Selesai dicek admin',
                'damage_cause_category' => 'component_failure',
            ])
            ->assertSessionHasErrors('status_perbaikan');

        $this->actingAs($quality)
            ->from(route('admin.dashboard'))
            ->patch(route('admin.reports.update-status', $report), [
                'status_perbaikan' => RepairStatus::APPROVE_QUALITY,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(RepairStatus::APPROVE_QUALITY, $report->fresh()->status_perbaikan);

        $this->actingAs($quality)
            ->from(route('admin.dashboard'))
            ->patch(route('admin.reports.update-status', $report), [
                'status_perbaikan' => RepairStatus::CLOSE,
                'tindakan_perbaikan' => 'Selesai dicek QC',
                'damage_cause_category' => 'component_failure',
            ])
            ->assertSessionHasErrors('status_perbaikan');

        $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->patch(route('admin.reports.update-status', $report), [
                'status_perbaikan' => RepairStatus::CLOSE,
                'tindakan_perbaikan' => 'Selesai dicek admin',
            ])
            ->assertSessionHasErrors('damage_cause_category');

        $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->patch(route('admin.reports.update-status', $report), [
                'status_perbaikan' => RepairStatus::CLOSE,
                'tindakan_perbaikan' => 'Selesai dicek admin',
                'damage_cause_category' => 'component_failure',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(RepairStatus::CLOSE, $report->fresh()->status_perbaikan);
        $this->assertSame(RepairStatus::CLOSE, $report->operatorReport->fresh()->status);
        $this->assertSame('component_failure', $report->fresh()->damage_cause_category);
    }

    public function test_dashboard_shows_next_action_for_the_matching_role(): void
    {
        $admin = User::factory()->admin()->create();
        $quality = User::factory()->quality()->create();
        $produksi = User::factory()->produksiInjection()->create();
        $report = $this->closedByTechnicianReport();

        $this->actingAs($produksi)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Injection')
            ->assertSee('QC')
            ->assertSee('Maintenance')
            ->assertSee('data-approve-step="'.RepairStatus::APPROVE_PRODUKSI_INJECTION.'"', false)
            ->assertDontSee('data-approve-step="'.RepairStatus::APPROVE_QUALITY.'"', false)
            ->assertDontSee('data-approve-step="'.RepairStatus::CLOSE.'"', false);

        $report->update(['status_perbaikan' => RepairStatus::APPROVE_PRODUKSI_INJECTION]);
        $report->operatorReport->update(['status' => RepairStatus::APPROVE_PRODUKSI_INJECTION]);

        $this->actingAs($quality)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-approve-step="'.RepairStatus::APPROVE_QUALITY.'"', false)
            ->assertDontSee('data-approve-step="'.RepairStatus::CLOSE.'"', false);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('data-approve-step="'.RepairStatus::CLOSE.'"', false);

        $report->update(['status_perbaikan' => RepairStatus::APPROVE_QUALITY]);
        $report->operatorReport->update(['status' => RepairStatus::APPROVE_QUALITY]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-approve-step="'.RepairStatus::CLOSE.'"', false)
            ->assertSee('Kategori Penyebab Kerusakan')
            ->assertSee('Hydaulic Problem')
            ->assertSee('Tersumbat Gramm')
            ->assertSee('Nozzle Blobor')
            ->assertSee('Lain-lain');
    }

    public function test_dashboard_approval_progress_marks_completed_steps(): void
    {
        $produksi = User::factory()->produksiInjection()->create();
        $report = $this->closedByTechnicianReport();

        $this->actingAs($produksi)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-approve-step="'.RepairStatus::APPROVE_PRODUKSI_INJECTION.'"', false)
            ->assertSee('em-status-approve-btn', false)
            ->assertSee('em-approve-loading', false)
            ->assertSee('Menunggu approve Injection', false)
            ->assertSee('Belum QC', false)
            ->assertSee('Belum Maintenance', false);

        $report->update(['status_perbaikan' => RepairStatus::APPROVE_PRODUKSI_INJECTION]);
        $report->operatorReport->update(['status' => RepairStatus::APPROVE_PRODUKSI_INJECTION]);

        $this->actingAs($produksi)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Injection sudah approve', false)
            ->assertSee('em-approve-done', false)
            ->assertSee('Menunggu approve QC', false)
            ->assertSee('Belum Maintenance', false);
    }

    public function test_dashboard_status_approve_button_approves_the_current_step(): void
    {
        $produksi = User::factory()->produksiInjection()->create();
        $report = $this->closedByTechnicianReport();

        $this->actingAs($produksi)
            ->from(route('admin.dashboard'))
            ->patch(route('admin.reports.update-status', $report), [
                'status_perbaikan' => RepairStatus::APPROVE_PRODUKSI_INJECTION,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(RepairStatus::APPROVE_PRODUKSI_INJECTION, $report->fresh()->status_perbaikan);
    }

    private function closedByTechnicianReport(): MaintenanceReport
    {
        $technician = User::factory()->create(['name' => 'Teknisi Uji']);
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
        $ticket = OperatorReport::query()->create([
            'operator_name' => 'Budi',
            'npk' => '123456',
            'item_name' => 'Cover Dashboard',
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'stop_date' => '2026-08-20',
            'stop_time' => '14:30',
            'problem' => 'Mesin stuck',
            'claimed_by' => $technician->id,
            'claimed_at' => now()->subHour(),
            'status' => RepairStatus::CLOSE_BY_TECHNICIAN,
        ]);

        return MaintenanceReport::query()->create([
            'report_date' => '2026-08-20',
            'machine_number' => $machine->machine_number,
            'machine_name_id' => $machine->id,
            'damage_category_id' => $category->id,
            'user_id' => $technician->id,
            'pic_name' => $technician->name,
            'work_shift' => 'shift_1',
            'repair_started_at' => now()->subHour(),
            'repair_finished_at' => now()->subMinutes(10),
            'masalah' => 'Mesin stuck',
            'analisa_penyebab_kerusakan' => 'Seal aus',
            'tindakan_perbaikan' => 'Ganti seal',
            'status_perbaikan' => RepairStatus::CLOSE_BY_TECHNICIAN,
            'operator_report_id' => $ticket->id,
            'ada_op' => true,
            'step2_completed_at' => now()->subMinutes(10),
        ]);
    }

    private function ticketAt(int $machineNumber, string $status, string $reportDate): MaintenanceReport
    {
        $technician = User::factory()->create(['name' => 'Teknisi Mesin '.$machineNumber]);
        $machine = MachineName::query()->create([
            'machine_number' => $machineNumber,
            'name' => 'Mesin Uji '.$machineNumber,
            'is_active' => true,
        ]);
        $category = DamageCategory::query()->firstOrCreate(
            ['name' => 'Mesin Injection'],
            ['allows_other' => false, 'is_active' => true, 'sort_order' => 1]
        );
        $ticket = OperatorReport::query()->create([
            'operator_name' => 'Operator '.$machineNumber,
            'npk' => (string) (120000 + $machineNumber),
            'item_name' => 'Part '.$machineNumber,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'stop_date' => $reportDate,
            'stop_time' => '14:30',
            'problem' => 'Masalah mesin '.$machineNumber,
            'claimed_by' => $technician->id,
            'claimed_at' => now()->subHour(),
            'status' => $status,
        ]);

        return MaintenanceReport::query()->create([
            'report_date' => $reportDate,
            'machine_number' => $machine->machine_number,
            'machine_name_id' => $machine->id,
            'damage_category_id' => $category->id,
            'user_id' => $technician->id,
            'pic_name' => $technician->name,
            'work_shift' => 'shift_1',
            'repair_started_at' => now()->subHour(),
            'repair_finished_at' => now()->subMinutes(10),
            'masalah' => 'Masalah mesin '.$machineNumber,
            'analisa_penyebab_kerusakan' => 'Analisa',
            'tindakan_perbaikan' => 'Perbaikan',
            'status_perbaikan' => $status,
            'operator_report_id' => $ticket->id,
            'ada_op' => true,
            'step2_completed_at' => now()->subMinutes(10),
        ]);
    }
}
