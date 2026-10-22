<?php

namespace Tests\Feature;

use App\Models\MachineName;
use App\Models\PreventiveReport;
use App\Models\User;
use App\Support\PreventiveChecklist;
use App\Support\PreventiveMachineChecklist;
use App\Support\PreventiveStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPreventiveReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_header_has_preventive_mesin(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Preventive Mesin')
            ->assertSee('Kategori Preventive')
            ->assertSee('Tipe Preventive')
            ->assertSee(route('admin.preventive.index'), false);
    }

    public function test_admin_can_view_preventive_results(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $technician = User::factory()->create(['name' => 'Teknisi Uji']);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $this->report($technician, $machine);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertSee('Preventive Mesin')
            ->assertSee('Kategori Preventive')
            ->assertSee('Tipe Preventive')
            ->assertSee('Mesin Uji 2')
            ->assertSee('Teknisi Uji');
    }

    public function test_admin_can_view_preventive_detail(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $technician = User::factory()->create(['name' => 'Teknisi Uji']);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $report = $this->report($technician, $machine, 'Heater panas', [
            'heater' => PreventiveChecklist::NOT_OK,
        ], 'follow_up');
        $report->update(['evidence_paths' => ['preventive-evidence/heater.jpg']]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.show', $report))
            ->assertOk()
            ->assertSee('Detail Preventive')
            ->assertSee('Kategori Preventive')
            ->assertSee('Tipe Preventive')
            ->assertSee('Cek suhu heater & barrel')
            ->assertSee('Tidak OK')
            ->assertSee('Heater panas')
            ->assertSee('Evidence')
            ->assertSee('preventive-evidence/heater.jpg');
    }

    public function test_admin_can_view_pm1_checklist_detail(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $technician = User::factory()->create(['name' => 'Teknisi Uji']);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $items = PreventiveMachineChecklist::filledOk('PM1');
        $items['thermocouple'] = PreventiveChecklist::NOT_OK;
        $report = $this->report($technician, $machine, 'Thermocouple rusak', $items, 'follow_up', 'PM1', null, [
            'voltage_rs' => '381',
            'voltage_st' => '382',
            'voltage_rt' => '383',
            'voltage_pe_n' => '0',
            'voltage_power_supply' => '24',
            'electrical_component_temp' => '36',
            'hydraulic_oil_temp' => '50',
            'nozzle_hole_radius' => '3.5 / R15',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.show', $report))
            ->assertOk()
            ->assertSee('Tegangan Utama')
            ->assertSee('381')
            ->assertSee('Electrical Section Check')
            ->assertSee('Thermocouple')
            ->assertSee('No Good')
            ->assertSee('Isi Manual')
            ->assertSee('36')
            ->assertSee('50')
            ->assertSee('3.5 / R15')
            ->assertSee('Pelumasan Manual')
            ->assertSee('ADDITIONAL PM')
            ->assertSee('Thermocouple rusak');
    }

    public function test_admin_can_view_pm2_checklist_detail(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $technician = User::factory()->create(['name' => 'Teknisi Uji']);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $items = PreventiveMachineChecklist::filledOk('PM2');
        $report = $this->report($technician, $machine, 'Oil cooler kotor', $items, 'ok', 'PM2', null, [
            'electrical_component_temp' => '38',
            'hydraulic_oil_temp' => '52',
            'nozzle_hole_radius' => '3.2 / R12',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.show', $report))
            ->assertOk()
            ->assertSee('Bersihkan Oil Cooler')
            ->assertSee('Ganti Filter Tissue Oli')
            ->assertSee('Cek Check Ring & Barrel')
            ->assertDontSee('Tambah Grease Gearbox Injection')
            ->assertSee('38')
            ->assertSee('Oil cooler kotor');
    }

    public function test_technician_cannot_open_admin_preventive(): void
    {
        $technician = User::factory()->create();

        $this->actingAs($technician)
            ->get(route('admin.preventive.index'))
            ->assertRedirect(route('home'));
    }

    public function test_preventive_results_follow_injection_then_maintenance_approval(): void
    {
        $admin = User::factory()->admin()->create();
        $quality = User::factory()->quality()->create();
        $produksi = User::factory()->produksiInjection()->create();
        $technician = User::factory()->create(['name' => 'Teknisi Uji']);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $report = $this->report($technician, $machine);

        $this->assertSame(PreventiveStatus::CLOSE_BY_TECHNICIAN, $report->fresh()->status);

        $this->actingAs($quality)
            ->get(route('admin.preventive.show', $report))
            ->assertForbidden();

        $this->actingAs($quality)
            ->patch(route('admin.preventive.update-status', $report), [
                'status' => PreventiveStatus::APPROVE_PRODUKSI_INJECTION,
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->patch(route('admin.preventive.update-status', $report), [
                'status' => PreventiveStatus::CLOSE,
            ])
            ->assertSessionHasErrors('status');

        $this->actingAs($produksi)
            ->get(route('admin.preventive.show', $report))
            ->assertOk()
            ->assertSee('Detail Preventive')
            ->assertSee('data-preventive-approve-step="'.PreventiveStatus::APPROVE_PRODUKSI_INJECTION.'"', false)
            ->assertDontSee('data-preventive-approve-step="'.PreventiveStatus::CLOSE.'"', false);

        $this->actingAs($produksi)
            ->from(route('admin.dashboard'))
            ->patch(route('admin.preventive.update-status', $report), [
                'status' => PreventiveStatus::APPROVE_PRODUKSI_INJECTION,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(PreventiveStatus::APPROVE_PRODUKSI_INJECTION, $report->fresh()->status);

        $this->actingAs($produksi)
            ->from(route('admin.dashboard'))
            ->patch(route('admin.preventive.update-status', $report), [
                'status' => PreventiveStatus::CLOSE,
            ])
            ->assertSessionHasErrors('status');

        $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->patch(route('admin.preventive.update-status', $report), [
                'status' => PreventiveStatus::CLOSE,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(PreventiveStatus::CLOSE, $report->fresh()->status);
    }

    public function test_dashboard_shows_preventive_approval_for_injection_and_maintenance(): void
    {
        $admin = User::factory()->admin()->create();
        $quality = User::factory()->quality()->create();
        $produksi = User::factory()->produksiInjection()->create();
        $technician = User::factory()->create(['name' => 'Teknisi Uji']);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $report = $this->report($technician, $machine);

        $this->actingAs($quality)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Hasil preventive sesuai mesin yang dipilih teknisi')
            ->assertDontSee('data-preventive-approve-step', false);

        $this->actingAs($produksi)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Preventive Mesin')
            ->assertSee('Approval: Injection lalu Maintenance')
            ->assertSee('data-preventive-approve-step="'.PreventiveStatus::APPROVE_PRODUKSI_INJECTION.'"', false)
            ->assertSee('Menunggu approve Injection', false)
            ->assertSee('Belum Maintenance', false)
            ->assertDontSee('Menunggu approve QC', false);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Preventive Mesin')
            ->assertDontSee('data-preventive-approve-step="'.PreventiveStatus::CLOSE.'"', false)
            ->assertSee('Menunggu approve Injection', false);

        $report->update(['status' => PreventiveStatus::APPROVE_PRODUKSI_INJECTION]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-preventive-approve-step="'.PreventiveStatus::CLOSE.'"', false)
            ->assertSee('Injection sudah approve', false)
            ->assertSee('Menunggu approve Maintenance', false);
    }

    public function test_admin_dashboard_shows_preventive_for_the_selected_machine(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create(['name' => 'Teknisi Uji']);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $other = MachineName::query()->create([
            'machine_number' => 5,
            'name' => 'Mesin Lain 5',
            'is_active' => true,
        ]);
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        $this->report($technician, $machine, 'Hopper OK', [], 'ok', 'HOPPER', $today);
        $this->report($technician, $other, 'PM mesin lain', [], 'ok', 'PM1', $today);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Hasil preventive sesuai mesin yang dipilih teknisi')
            ->assertSee('Hopper')
            ->assertSee('Mesin Uji 2')
            ->assertSee('Mesin Lain 5')
            ->assertSee('PM1');

        $this->actingAs($admin)
            ->get(route('admin.machines.show', 2))
            ->assertOk()
            ->assertSee('Preventive Mesin 2')
            ->assertSee('Hopper')
            ->assertSee('Download')
            ->assertSee(route('admin.preventive.export', PreventiveReport::query()->where('machine_number', 2)->first()), false)
            ->assertDontSee('Mesin Lain 5')
            ->assertDontSee('PM mesin lain');

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->assertJsonPath('preventive_today.2.0', 'Hopper')
            ->assertJsonPath('preventive_today.5.0', 'PM1');
    }

    public function test_admin_can_download_preventive_result_excel(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $technician = User::factory()->create(['name' => 'Teknisi Uji']);
        $machine = MachineName::query()->create([
            'machine_number' => 1,
            'name' => 'Mesin Uji 1',
            'is_active' => true,
        ]);
        $items = PreventiveMachineChecklist::filledOk('PM1');
        $report = $this->report($technician, $machine, 'Semua item dicek', $items, 'ok', 'PM1', '2026-08-20', [
            'voltage_rs' => '381',
            'electrical_component_temp' => '36',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.preventive.export', $report))
            ->assertOk();

        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            (string) $response->headers->get('content-type')
        );
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString(
            'preventive-mesin-1-PM1-20260820.xlsx',
            (string) $response->headers->get('content-disposition')
        );
        $this->assertNotEmpty($response->streamedContent());
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }

    /**
     * @param  array<string, string>  $overrides
     */
    private function report(
        User $user,
        MachineName $machine,
        string $notes = 'Semua OK',
        array $overrides = [],
        string $result = 'ok',
        string $category = 'PM1',
        ?string $workDate = null,
        array $readings = []
    ): PreventiveReport {
        $items = [];
        foreach (array_keys(PreventiveChecklist::items()) as $key) {
            $items[$key] = PreventiveChecklist::OK;
        }

        return PreventiveReport::query()->create([
            'user_id' => $user->id,
            'pic_name' => $user->name,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'category' => $category,
            'work_date' => $workDate ?? '2026-08-20',
            'work_shift' => 'shift_1',
            'items' => array_merge($items, $overrides),
            'readings' => $readings === [] ? null : $readings,
            'notes' => $notes,
            'result' => $result,
        ]);
    }
}
