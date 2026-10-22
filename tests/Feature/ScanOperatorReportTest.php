<?php

namespace Tests\Feature;

use App\Models\FindingRecap;
use App\Models\MachineName;
use App\Models\OperatorReport;
use App\Models\User;
use App\Support\FindingRecapStatus;
use App\Support\MachineQrCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanOperatorReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_scan_hub_shows_input_op_button_and_machine_data(): void
    {
        $machineNumber = 2;
        $token = MachineQrCode::token($machineNumber);

        $this->get(route('scan.machines.show', [$machineNumber, $token]))
            ->assertOk()
            ->assertSee('INPUT OP')
            ->assertSee('Spesifikasi Mesin')
            ->assertSee('Riwayat Problem')
            ->assertSee('Hasil Preventive')
            ->assertSee('Log Temuan')
            ->assertDontSee('INFO MESIN 2')
            ->assertDontSee('HASIL PREVENTIVE MESIN 2')
            ->assertDontSee('Tampilkan rekap temuan')
            ->assertDontSee('Login staf')
            ->assertDontSee(route('login'), false)
            ->assertSee(route('scan.operator-reports.create', [$machineNumber, $token]), false)
            ->assertDontSee(route('scan.machines.info', [$machineNumber, $token]), false)
            ->assertDontSee(route('scan.preventive.index', [$machineNumber, $token]), false)
            ->assertDontSee(route('scan.finding-recaps.index', [$machineNumber, $token]), false);
    }

    public function test_invalid_scan_token_returns_not_found(): void
    {
        $this->get('/scan/mesin/2/'.str_repeat('a', 64))
            ->assertNotFound();
    }

    public function test_info_mesin_page_is_available_from_scan(): void
    {
        $machineNumber = 2;
        $token = MachineQrCode::token($machineNumber);

        $this->get(route('scan.machines.info', [$machineNumber, $token]))
            ->assertOk()
            ->assertSee('Mesin 2')
            ->assertSee('Filter Riwayat Problem');
    }

    public function test_input_op_form_shows_required_fields_and_preselects_scanned_machine(): void
    {
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $token = MachineQrCode::token(2);

        $this->get(route('scan.operator-reports.create', [2, $token]))
            ->assertOk()
            ->assertSee('Nama (Wajib Diisi)')
            ->assertSee('NPK (Wajib Diisi)')
            ->assertSee('Nama Barang (Wajib Diisi)')
            ->assertSee('Mesin No')
            ->assertSee('Otomatis dari barcode yang discan')
            ->assertSee('Stop Tanggal (Wajib Diisi)')
            ->assertSee('Jam (Wajib Diisi)')
            ->assertSee('Masalah (Wajib Diisi)')
            ->assertSee('Kronologi (Opsional)')
            ->assertSee('Mesin 2 — Mesin Uji 2')
            ->assertDontSee('Pilih mesin dari master');
    }

    public function test_input_op_rejects_script_and_html_injection(): void
    {
        MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $token = MachineQrCode::token(2);
        $payload = [
            'operator_name' => 'Budi Santoso',
            'npk' => '123456',
            'item_name' => 'Cover Dashboard',
            'stop_date' => '2026-08-20',
            'stop_time' => '14:30',
            'problem' => 'Mesin stuck di clamping',
        ];

        $this->from(route('scan.operator-reports.create', [2, $token]))
            ->post(route('scan.operator-reports.store', [2, $token]), array_merge($payload, [
                'operator_name' => 'Budi <script>alert(1)</script>',
            ]))
            ->assertSessionHasErrors('operator_name');

        $this->from(route('scan.operator-reports.create', [2, $token]))
            ->post(route('scan.operator-reports.store', [2, $token]), array_merge($payload, [
                'problem' => '<img src=x onerror=alert(1)> Mesin rusak',
            ]))
            ->assertSessionHasErrors('problem');

        $this->from(route('scan.operator-reports.create', [2, $token]))
            ->post(route('scan.operator-reports.store', [2, $token]), array_merge($payload, [
                'npk' => '123456; DROP TABLE users',
            ]))
            ->assertSessionHasErrors('npk');

        $this->assertSame(0, OperatorReport::query()->count());
    }

    public function test_operator_can_store_input_op_from_scan(): void
    {
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $token = MachineQrCode::token(2);

        $response = $this->post(route('scan.operator-reports.store', [2, $token]), [
            'operator_name' => 'Budi Santoso',
            'npk' => '123456',
            'item_name' => 'Cover Dashboard',
            'stop_date' => '2026-08-20',
            'stop_time' => '14:30',
            'problem' => 'Mesin stuck di clamping',
            'chronology' => 'Setelah ganti mold, mesin berhenti.',
        ]);

        $response->assertRedirect(route('scan.machines.show', [2, $token]));
        $this->assertDatabaseHas('operator_reports', [
            'operator_name' => 'Budi Santoso',
            'npk' => '123456',
            'item_name' => 'Cover Dashboard',
            'machine_number' => 2,
            'machine_name_id' => $machine->id,
            'problem' => 'Mesin stuck di clamping',
        ]);
        $this->assertSame(1, OperatorReport::query()->count());
    }

    public function test_input_op_ignores_posted_machine_and_uses_scanned_barcode(): void
    {
        $scanned = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Lain',
            'is_active' => true,
        ]);
        $token = MachineQrCode::token(2);

        $this->post(route('scan.operator-reports.store', [2, $token]), [
            'operator_name' => 'Budi Santoso',
            'npk' => '123456',
            'item_name' => 'Cover Dashboard',
            'machine_name_id' => 7,
            'stop_date' => '2026-08-20',
            'stop_time' => '14:30',
            'problem' => 'Mesin stuck di clamping',
        ]);

        $this->assertDatabaseHas('operator_reports', [
            'machine_number' => 2,
            'machine_name_id' => $scanned->id,
        ]);
    }

    public function test_input_op_requires_mandatory_fields(): void
    {
        MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $token = MachineQrCode::token(2);

        $this->from(route('scan.operator-reports.create', [2, $token]))
            ->post(route('scan.operator-reports.store', [2, $token]), [])
            ->assertRedirect(route('scan.operator-reports.create', [2, $token]))
            ->assertSessionHasErrors([
                'operator_name',
                'npk',
                'item_name',
                'stop_date',
                'stop_time',
                'problem',
            ]);
    }

    public function test_scan_finding_recaps_show_closed_items_for_that_machine_only(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 4,
            'name' => 'Hwa Chin HC 125 SE 125T',
            'brand' => 'Hwa Chin',
            'machine_model' => 'HC 125 SE',
            'tonnage' => 125,
            'is_active' => true,
        ]);
        $other = MachineName::query()->create([
            'machine_number' => 8,
            'name' => 'Mesin Lain',
            'brand' => 'Sumitomo',
            'tonnage' => 180,
            'is_active' => true,
        ]);
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        FindingRecap::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 4,
            'machine_name' => $machine->viewLabel(),
            'equipment_name' => 'Heater close mesin 4',
            'found_date' => $today,
            'due_date' => $today,
            'status' => FindingRecapStatus::CLOSE,
        ]);
        FindingRecap::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 4,
            'machine_name' => $machine->viewLabel(),
            'equipment_name' => 'Pump masih open',
            'found_date' => $today,
            'due_date' => $today,
            'status' => FindingRecapStatus::OPEN,
        ]);
        FindingRecap::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $other->id,
            'machine_number' => 8,
            'machine_name' => $other->viewLabel(),
            'equipment_name' => 'Temuan mesin lain close',
            'found_date' => $today,
            'due_date' => $today,
            'status' => FindingRecapStatus::CLOSE,
        ]);

        $token = MachineQrCode::token(4);

        $this->get(route('scan.finding-recaps.index', [4, $token]))
            ->assertOk()
            ->assertSee('Log Temuan Mesin 4')
            ->assertSee('Hwa Chin 125T')
            ->assertSee('Heater close mesin 4')
            ->assertDontSee('Pump masih open')
            ->assertDontSee('Temuan mesin lain close')
            ->assertDontSee('Hwa Chin HC 125 SE 125T');

        $this->get(route('scan.machines.show', [4, $token]))
            ->assertOk()
            ->assertSee('Heater close mesin 4')
            ->assertDontSee('Pump masih open')
            ->assertDontSee('Temuan mesin lain close');
    }
}
