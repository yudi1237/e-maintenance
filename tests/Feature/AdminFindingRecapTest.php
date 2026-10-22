<?php

namespace Tests\Feature;

use App\Models\FindingRecap;
use App\Models\FindingRecapStatusNote;
use App\Models\MachineName;
use App\Models\User;
use App\Support\FindingRecapStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFindingRecapTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_navigation_and_dashboard_include_rekap_temuan(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Log Temuan')
            ->assertSee(route('admin.finding-recaps.index'), false);
    }

    public function test_admin_can_open_rekap_temuan_form(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();

        $this->actingAs($admin)
            ->get(route('admin.finding-recaps.index'))
            ->assertOk()
            ->assertSee('Input Log Temuan')
            ->assertSee('Form Log Temuan')
            ->assertSee('Due Date')
            ->assertSee((string) $machine->machine_number)
            ->assertSee('Nama Equipment')
            ->assertSee('Catatan Status')
            ->assertDontSee('+ Tambah Baris');
    }

    public function test_admin_can_export_finding_recaps_excel(): void
    {
        $admin = User::factory()->admin()->create();
        $this->recap($admin, 'Heater band zona 1');

        $this->actingAs($admin)
            ->get(route('admin.finding-recaps.index'))
            ->assertOk()
            ->assertSee('Download Excel');

        $response = $this->actingAs($admin)
            ->get(route('admin.finding-recaps.export'))
            ->assertOk();

        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            (string) $response->headers->get('content-type')
        );
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));
        $this->assertNotEmpty($response->streamedContent());
    }

    public function test_admin_can_edit_and_delete_finding_recap(): void
    {
        $admin = User::factory()->admin()->create();
        $recap = $this->recap($admin, 'Heater band zona 1');
        $otherMachine = MachineName::query()->create([
            'machine_number' => 9,
            'name' => 'Sumitomo SE180',
            'is_active' => true,
        ]);
        $dueDate = now()->timezone('Asia/Jakarta')->addDays(3)->toDateString();

        $this->actingAs($admin)
            ->get(route('admin.finding-recaps.index'))
            ->assertOk()
            ->assertSee('Edit')
            ->assertSee('Delete')
            ->assertSee(route('admin.finding-recaps.edit', $recap), false);

        $this->actingAs($admin)
            ->get(route('admin.finding-recaps.edit', $recap))
            ->assertOk()
            ->assertSee('Edit Log Temuan')
            ->assertSee('Heater band zona 1');

        $this->actingAs($admin)
            ->put(route('admin.finding-recaps.update', $recap), [
                'due_date' => $dueDate,
                'machine_name_id' => $otherMachine->id,
                'equipment_name' => 'Oil pump zona 2',
            ])
            ->assertRedirect(route('admin.finding-recaps.index'));

        $recap->refresh();
        $this->assertSame($dueDate, $recap->due_date->toDateString());
        $this->assertSame(9, $recap->machine_number);
        $this->assertSame('Sumitomo SE180', $recap->machine_name);
        $this->assertSame('Oil pump zona 2', $recap->equipment_name);

        $this->actingAs($admin)
            ->delete(route('admin.finding-recaps.destroy', $recap))
            ->assertRedirect(route('admin.finding-recaps.index'));

        $this->assertDatabaseMissing('finding_recaps', ['id' => $recap->id]);
        $this->assertDatabaseMissing('finding_recap_status_notes', ['finding_recap_id' => $recap->id]);
    }

    public function test_dashboard_rekap_temuan_does_not_show_edit_delete(): void
    {
        $admin = User::factory()->admin()->create();
        $recap = $this->recap($admin, 'Limit switch safety door');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Limit switch safety door')
            ->assertDontSee(route('admin.finding-recaps.edit', $recap), false);
    }

    public function test_admin_can_store_finding_recap_with_status_note(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        $this->actingAs($admin)
            ->post(route('admin.finding-recaps.store'), [
                'due_date' => $today,
                'machine_name_id' => $machine->id,
                'equipment_name' => 'Heater band zona 1',
                'status' => FindingRecapStatus::OPEN,
                'note' => 'Temuan heater band putus saat inspeksi.',
            ])
            ->assertRedirect(route('admin.finding-recaps.index'));

        $recap = FindingRecap::query()->first();
        $this->assertNotNull($recap);
        $this->assertSame($today, $recap->found_date->toDateString());
        $this->assertSame($today, $recap->due_date->toDateString());
        $this->assertSame(7, $recap->machine_number);
        $this->assertSame('Haitian Mars 250T', $recap->machine_name);
        $this->assertSame('Heater band zona 1', $recap->equipment_name);
        $this->assertSame(FindingRecapStatus::OPEN, $recap->status);
        $this->assertDatabaseHas('finding_recap_status_notes', [
            'finding_recap_id' => $recap->id,
            'status' => FindingRecapStatus::OPEN,
            'note' => 'Temuan heater band putus saat inspeksi.',
            'user_id' => $admin->id,
        ]);
    }

    public function test_found_date_defaults_to_today_and_can_be_set_manually(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $manualDate = now()->timezone('Asia/Jakarta')->subDays(2)->toDateString();

        $this->actingAs($admin)
            ->post(route('admin.finding-recaps.store'), [
                'due_date' => $today,
                'machine_name_id' => $machine->id,
                'equipment_name' => 'Oil pump',
                'status' => FindingRecapStatus::OPEN,
                'note' => 'Pompa oli bocor.',
            ])
            ->assertRedirect(route('admin.finding-recaps.index'));

        $this->assertSame($today, FindingRecap::query()->first()?->found_date->toDateString());

        FindingRecap::query()->delete();

        $this->actingAs($admin)
            ->post(route('admin.finding-recaps.store'), [
                'found_date' => $manualDate,
                'due_date' => $today,
                'machine_name_id' => $machine->id,
                'equipment_name' => 'Heater band',
                'status' => FindingRecapStatus::OPEN,
                'note' => 'Heater band putus.',
            ])
            ->assertRedirect(route('admin.finding-recaps.index'));

        $this->assertSame($manualDate, FindingRecap::query()->first()?->found_date->toDateString());
    }

    public function test_admin_can_filter_finding_recaps_by_status(): void
    {
        $admin = User::factory()->admin()->create();
        $this->recap($admin, 'Temuan open heater');
        $closedMachine = MachineName::query()->create([
            'machine_number' => 8,
            'name' => 'Mesin Filter 8',
            'is_active' => true,
        ]);
        $today = now()->timezone('Asia/Jakarta')->toDateString();
        FindingRecap::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $closedMachine->id,
            'machine_number' => 8,
            'machine_name' => $closedMachine->name,
            'equipment_name' => 'Temuan close pump',
            'found_date' => $today,
            'due_date' => $today,
            'status' => FindingRecapStatus::CLOSE,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.finding-recaps.index', ['status' => FindingRecapStatus::CLOSE]))
            ->assertOk()
            ->assertSee('Filter Status')
            ->assertSee('Temuan close pump')
            ->assertDontSee('Temuan open heater');

        $this->actingAs($admin)
            ->get(route('admin.finding-recaps.index', ['status' => FindingRecapStatus::OPEN]))
            ->assertOk()
            ->assertSee('Temuan open heater')
            ->assertDontSee('Temuan close pump');
    }

    public function test_finding_recaps_show_overdue_and_reminder_labels(): void
    {
        $admin = User::factory()->admin()->create();
        $today = now()->timezone('Asia/Jakarta')->startOfDay();
        $machine = $this->machine();

        FindingRecap::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'machine_name' => $machine->name,
            'equipment_name' => 'Temuan overdue valve',
            'found_date' => $today->toDateString(),
            'due_date' => $today->copy()->subDay()->toDateString(),
            'status' => FindingRecapStatus::OPEN,
        ]);
        FindingRecap::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'machine_name' => $machine->name,
            'equipment_name' => 'Temuan reminder sensor',
            'found_date' => $today->toDateString(),
            'due_date' => $today->copy()->addDays(3)->toDateString(),
            'status' => FindingRecapStatus::OPEN,
        ]);
        FindingRecap::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'machine_name' => $machine->name,
            'equipment_name' => 'Temuan masih jauh',
            'found_date' => $today->toDateString(),
            'due_date' => $today->copy()->addDays(4)->toDateString(),
            'status' => FindingRecapStatus::OPEN,
        ]);

        $html = $this->actingAs($admin)
            ->get(route('admin.finding-recaps.index'))
            ->assertOk()
            ->assertSee('Temuan overdue valve')
            ->assertSee('Temuan reminder sensor')
            ->assertSee('Temuan masih jauh')
            ->assertSee('Overdue')
            ->assertSee('Reminder')
            ->getContent();

        $this->assertTrue(str_contains($html, 'Temuan overdue valve'));
        $this->assertEquals(1, substr_count($html, '>Overdue</span>'));
        $this->assertEquals(1, substr_count($html, '>Reminder</span>'));
    }

    public function test_admin_must_enter_note_when_changing_status(): void
    {
        $admin = User::factory()->admin()->create();
        $recap = $this->recap($admin);

        $this->actingAs($admin)
            ->patch(route('admin.finding-recaps.update-status', $recap), [
                'status' => FindingRecapStatus::ON_PROGRESS,
            ])
            ->assertSessionHasErrors('note');

        $this->assertSame(FindingRecapStatus::OPEN, $recap->fresh()->status);
    }

    public function test_admin_can_change_status_with_note_and_view_notes_on_dashboard(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Temuan']);
        $recap = $this->recap($admin, 'Limit switch safety door');

        $this->actingAs($admin)
            ->patch(route('admin.finding-recaps.update-status', $recap), [
                'status' => FindingRecapStatus::MONITORING,
                'note' => 'Sudah diganti, masih dipantau 2 hari.',
            ])
            ->assertRedirect();

        $this->assertSame(FindingRecapStatus::MONITORING, $recap->fresh()->status);
        $this->assertSame(2, FindingRecapStatusNote::query()->where('finding_recap_id', $recap->id)->count());

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Limit switch safety door')
            ->assertSee('Sudah diganti, masih dipantau 2 hari.')
            ->assertSee('Temuan awal saat cek mesin.');
    }

    public function test_store_requires_note_and_equipment_name(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();

        $this->actingAs($admin)
            ->post(route('admin.finding-recaps.store'), [
                'due_date' => now()->timezone('Asia/Jakarta')->toDateString(),
                'machine_name_id' => $machine->id,
                'status' => FindingRecapStatus::OPEN,
            ])
            ->assertSessionHasErrors(['equipment_name', 'note']);
    }

    public function test_technician_cannot_open_rekap_temuan(): void
    {
        $technician = User::factory()->create();

        $this->actingAs($technician)
            ->get(route('admin.finding-recaps.index'))
            ->assertRedirect(route('home'));

        $this->actingAs($technician)
            ->get(route('admin.finding-recaps.export'))
            ->assertRedirect(route('home'));
    }

    private function machine(): MachineName
    {
        return MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Haitian Mars 250T',
            'is_active' => true,
        ]);
    }

    private function recap(User $admin, string $equipment = 'Heater band'): FindingRecap
    {
        $machine = $this->machine();
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        $recap = FindingRecap::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'machine_name' => $machine->name,
            'equipment_name' => $equipment,
            'found_date' => $today,
            'due_date' => $today,
            'status' => FindingRecapStatus::OPEN,
        ]);

        $recap->addStatusNote(FindingRecapStatus::OPEN, 'Temuan awal saat cek mesin.', $admin->id);

        return $recap;
    }
}
