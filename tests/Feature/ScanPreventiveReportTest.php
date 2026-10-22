<?php

namespace Tests\Feature;

use App\Models\MachineName;
use App\Models\PreventiveReport;
use App\Models\User;
use App\Support\MachineQrCode;
use App\Support\PreventiveChecklist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanPreventiveReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_scan_preventive_page_is_available_for_each_machine(): void
    {
        $token = MachineQrCode::token(1);

        $this->get(route('scan.preventive.index', [1, $token]))
            ->assertOk()
            ->assertSee('Hasil Preventive Mesin 1')
            ->assertSee('Belum ada hasil preventive untuk mesin ini.');
    }

    public function test_scan_preventive_lists_results_for_scanned_machine_only(): void
    {
        $user = User::factory()->create(['name' => 'Teknisi Uji']);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $other = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Lain',
            'is_active' => true,
        ]);

        $this->report($user, $machine, 'Semua OK', 'ok');
        $this->report($user, $other, 'Temuan mesin lain', 'follow_up');

        $token = MachineQrCode::token(2);

        $this->get(route('scan.preventive.index', [2, $token]))
            ->assertOk()
            ->assertSee('Hasil Preventive Mesin 2')
            ->assertSee('Mesin Uji 2')
            ->assertSee('Teknisi Uji')
            ->assertSee('OK')
            ->assertDontSee('Temuan mesin lain')
            ->assertDontSee('Mesin Lain');
    }

    public function test_scan_preventive_detail_shows_checklist(): void
    {
        $user = User::factory()->create(['name' => 'Teknisi Uji']);
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        $report = $this->report($user, $machine, 'Heater perlu dicek ulang', 'follow_up', [
            'heater' => PreventiveChecklist::NOT_OK,
        ]);
        $token = MachineQrCode::token(2);

        $report->update(['evidence_paths' => ['preventive-evidence/scan.jpg']]);

        $this->get(route('scan.preventive.show', [2, $token, $report]))
            ->assertOk()
            ->assertSee('Detail Preventive')
            ->assertSee('Cek suhu heater & barrel')
            ->assertSee('Tidak OK')
            ->assertSee('Heater perlu dicek ulang')
            ->assertSee('Ada temuan')
            ->assertSee('Evidence')
            ->assertSee('preventive-evidence/scan.jpg');
    }

    public function test_scan_preventive_detail_rejects_other_machine(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Lain',
            'is_active' => true,
        ]);
        $report = $this->report($user, $machine, 'Catatan mesin 7', 'ok');
        $token = MachineQrCode::token(2);

        $this->get(route('scan.preventive.show', [2, $token, $report]))
            ->assertNotFound();
    }

    /**
     * @param  array<string, string>  $overrides
     */
    private function report(
        User $user,
        MachineName $machine,
        string $notes,
        string $result,
        array $overrides = []
    ): PreventiveReport {
        $items = [];
        foreach (array_keys(PreventiveChecklist::items()) as $key) {
            $items[$key] = PreventiveChecklist::OK;
        }
        $items = array_merge($items, $overrides);

        return PreventiveReport::query()->create([
            'user_id' => $user->id,
            'pic_name' => $user->name,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'work_date' => '2026-08-20',
            'work_shift' => 'shift_1',
            'items' => $items,
            'notes' => $notes,
            'result' => $result,
        ]);
    }
}
