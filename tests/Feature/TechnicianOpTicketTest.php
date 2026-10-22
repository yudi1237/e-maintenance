<?php

namespace Tests\Feature;

use App\Models\DamageCategory;
use App\Models\MachineName;
use App\Models\MaintenanceReport;
use App\Models\OperatorReport;
use App\Models\Technician;
use App\Models\User;
use App\Support\RepairStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TechnicianOpTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_incoming_op_ticket_is_open_on_home(): void
    {
        $user = User::factory()->create();
        $ticket = $this->ticket($this->machine());

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Tiket Input OP')
            ->assertSee($ticket->item_name)
            ->assertSee('Open')
            ->assertSee('Ambil');
    }

    public function test_taking_ticket_requires_pic_and_pin(): void
    {
        $user = User::factory()->create(['name' => 'Teknisi Maintenance']);
        $ticket = $this->ticket($this->machine());

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('reports.tickets.claim', $ticket))
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('technician_id');

        $this->assertSame(RepairStatus::OPEN, $ticket->fresh()->status);
    }

    public function test_taking_ticket_rejects_wrong_pin(): void
    {
        $user = User::factory()->create();
        $pic = $this->pic();
        $ticket = $this->ticket($this->machine());

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic, '0000'))
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('pin');

        $this->assertSame(RepairStatus::OPEN, $ticket->fresh()->status);
    }

    public function test_taking_ticket_sets_on_progress(): void
    {
        $user = User::factory()->create(['name' => 'Teknisi Maintenance']);
        $pic = $this->pic(['name' => 'Andi PIC']);
        $ticket = $this->ticket($this->machine());

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic))
            ->assertRedirect(route('home'));

        $ticket->refresh();
        $this->assertSame(RepairStatus::ON_PROGRESS, $ticket->status);
        $this->assertSame($user->id, $ticket->claimed_by);
        $this->assertSame($pic->id, $ticket->claimed_technician_id);
        $this->assertNotNull($ticket->claimed_at);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertSee('On Progress')
            ->assertSee('Andi PIC')
            ->assertSee('Close by teknisi');
    }

    public function test_laporan_form_hides_op_ticket_board_when_opened_from_ticket(): void
    {
        $user = User::factory()->create(['name' => 'Teknisi Maintenance']);
        $pic = $this->pic(['name' => 'Andi PIC']);
        $ticket = $this->ticket($this->machine());

        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic));

        $this->actingAs($user)
            ->get(route('reports.create', ['ticket' => $ticket->id]))
            ->assertOk()
            ->assertDontSee('Tiket Input OP')
            ->assertDontSee('>Ambil</button>', false)
            ->assertDontSee('Masuk sebagai Open. Ambil tiket')
            ->assertSee('Pilih Tiket')
            ->assertSee('name="operator_report_id"', false)
            ->assertSee('value="'.$ticket->id.'"', false)
            ->assertSee('Lanjut ke Form 2');
    }

    public function test_laporan_form_defaults_start_from_claim_and_finish_from_close_time(): void
    {
        $user = User::factory()->create(['name' => 'Teknisi Maintenance']);
        $pic = $this->pic(['name' => 'Andi PIC']);
        $ticket = $this->ticket($this->machine());

        $this->travelTo(Carbon::parse('2026-10-16 08:15:00', 'Asia/Jakarta'));
        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic));

        $this->travelTo(Carbon::parse('2026-10-16 14:40:00', 'Asia/Jakarta'));
        $this->actingAs($user)->post(route('reports.tickets.close', $ticket), $this->claimPayload($pic));

        $response = $this->actingAs($user)
            ->get(route('reports.create', ['ticket' => $ticket->id]))
            ->assertOk()
            ->assertSee('Default: waktu OP diambil. Masih bisa diubah.')
            ->assertSee('Default: waktu OP Close by teknisi. Masih bisa diubah.');

        $html = $response->getContent();
        $this->assertTrue(
            (bool) preg_match('/id="repair_started_hour"[^>]*>[\s\S]*?<option value="8"\s+selected/u', $html),
            'Waktu mulai harus default jam OP diambil (08).'
        );
        $this->assertTrue(
            (bool) preg_match('/id="repair_started_minute"[^>]*>[\s\S]*?<option value="15"\s+selected/u', $html),
            'Waktu mulai harus default menit OP diambil (15).'
        );
        $this->assertTrue(
            (bool) preg_match('/id="repair_finished_hour"[^>]*>[\s\S]*?<option value="14"\s+selected/u', $html),
            'Waktu selesai harus default jam Close by teknisi (14).'
        );
        $this->assertTrue(
            (bool) preg_match('/id="repair_finished_minute"[^>]*>[\s\S]*?<option value="40"\s+selected/u', $html),
            'Waktu selesai harus default menit Close by teknisi (40).'
        );
    }

    public function test_laporan_requires_claimed_on_progress_ticket(): void
    {
        $user = User::factory()->create();
        $machine = $this->machine();
        $ticket = $this->ticket($machine);
        $category = $this->category();
        $pic = $this->pic();

        $this->actingAs($user)->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine))
            ->assertSessionHasErrors('operator_report_id');

        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic));

        $this->actingAs($user)->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine))
            ->assertSessionHasErrors('operator_report_id');

        $this->actingAs($user)
            ->post(route('reports.tickets.close', $ticket), $this->claimPayload($pic))
            ->assertRedirect(route('reports.create', ['ticket' => $ticket->id]));

        $response = $this->actingAs($user)->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine));

        $report = MaintenanceReport::query()->first();
        $this->assertNotNull($report);
        $response->assertRedirect(route('reports.step2', $report));
        $this->assertSame($machine->id, $report->machine_name_id);
        $this->assertSame(RepairStatus::ON_PROGRESS, $report->status_perbaikan);
    }

    public function test_completing_laporan_sets_close_by_technician(): void
    {
        $user = User::factory()->create();
        $machine = $this->machine();
        $ticket = $this->ticket($machine);
        $category = $this->category();
        $pic = $this->pic();

        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic));
        $this->actingAs($user)->post(route('reports.tickets.close', $ticket), $this->claimPayload($pic));
        $this->actingAs($user)->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine));

        $report = MaintenanceReport::query()->first();

        $this->actingAs($user)
            ->get(route('reports.step2', $report))
            ->assertOk()
            ->assertSee('Close by teknisi')
            ->assertSee('PIN PIC')
            ->assertSee('Penggunaan Sparepart')
            ->assertSee('Tidak ada')
            ->assertDontSee('Ada OP');

        $this->actingAs($user)->post(route('reports.step2.store', $report), $this->step2Payload([
            'pin' => null,
        ]))
            ->assertSessionHasErrors('pin');

        $this->actingAs($user)->post(route('reports.step2.store', $report), $this->step2Payload([
            'pin' => '0000',
        ]))
            ->assertSessionHasErrors('pin');

        $this->assertSame(RepairStatus::ON_PROGRESS, $ticket->fresh()->status);

        $this->actingAs($user)->post(route('reports.step2.store', $report), $this->step2Payload())
            ->assertRedirect(route('home'))
            ->assertSessionHas('data_terkirim', true);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Data Terkirim');

        $this->assertSame(RepairStatus::CLOSE_BY_TECHNICIAN, $report->fresh()->status_perbaikan);
        $this->assertSame(RepairStatus::CLOSE_BY_TECHNICIAN, $ticket->fresh()->status);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password', $user->fresh()->getRawOriginal('password')));

        $this->post('/logout');
        $this->assertGuest();

        $this->post('/login', [
            'npk' => $user->npk,
            'password' => 'password',
        ])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_close_ticket_rejects_pin_of_a_different_pic(): void
    {
        $user = User::factory()->create();
        $ownerPic = $this->pic(['name' => 'Andi PIC', 'npk' => '510001']);
        $otherPic = $this->pic(['name' => 'Budi PIC', 'npk' => '510002']);
        $ticket = $this->ticket($this->machine());

        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($ownerPic));

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('reports.tickets.close', $ticket), $this->claimPayload($otherPic))
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('technician_id');

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('reports.tickets.close', $ticket), $this->claimPayload($ownerPic, '0000'))
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('pin');

        $this->assertSame(RepairStatus::ON_PROGRESS, $ticket->fresh()->status);
    }

    public function test_only_claiming_pic_pin_can_close_by_technician(): void
    {
        $owner = User::factory()->create(['name' => 'Teknisi Maintenance']);
        $other = User::factory()->create(['name' => 'Teknisi Lain']);
        $ownerPic = $this->pic(['name' => 'Andi PIC', 'npk' => '510001']);
        $machine = $this->machine();
        $ticket = $this->ticket($machine);
        $category = $this->category();

        $this->actingAs($owner)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($ownerPic));

        $this->actingAs($other)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('On Progress')
            ->assertSee('Close by teknisi');

        $this->actingAs($other)
            ->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine))
            ->assertSessionHasErrors('operator_report_id');

        $this->actingAs($owner)->post(route('reports.tickets.close', $ticket), $this->claimPayload($ownerPic));
        $this->actingAs($owner)->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine));
        $report = MaintenanceReport::query()->first();

        $this->actingAs($other)
            ->get(route('reports.step2', $report))
            ->assertForbidden();

        $this->actingAs($other)
            ->post(route('reports.step2.store', $report), $this->step2Payload())
            ->assertForbidden();

        $this->assertSame(RepairStatus::ON_PROGRESS, $ticket->fresh()->status);
    }

    public function test_cannot_identify_as_a_different_pic(): void
    {
        $user = User::factory()->create();
        $ownerPic = $this->pic(['name' => 'Andi PIC', 'npk' => '510001']);
        $otherPic = $this->pic(['name' => 'Budi PIC', 'npk' => '510002']);
        $ticket = $this->ticket($this->machine());

        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($ownerPic));
        $this->assertSame($ownerPic->id, $ticket->fresh()->claimed_technician_id);

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('reports.tickets.identify', $ticket), $this->claimPayload($otherPic))
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('technician_id');
    }

    public function test_open_ticket_cannot_skip_to_close_by_technician(): void
    {
        $user = User::factory()->create();
        $machine = $this->machine();
        $ticket = $this->ticket($machine);
        $category = $this->category();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertSee('Ambil')
            ->assertDontSee('>Close by teknisi</option>', false);

        $this->actingAs($user)
            ->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine))
            ->assertSessionHasErrors('operator_report_id');

        $this->assertSame(RepairStatus::OPEN, $ticket->fresh()->status);
        $this->assertSame(0, MaintenanceReport::query()->count());
    }

    public function test_on_progress_ticket_shows_close_action_even_if_form2_is_complete(): void
    {
        $user = User::factory()->create();
        $pic = $this->pic(['name' => 'Budi PIC']);
        $machine = $this->machine();
        $ticket = $this->ticket($machine);
        $category = $this->category();

        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic));

        MaintenanceReport::query()->create([
            'report_date' => '2026-08-20',
            'machine_number' => $machine->machine_number,
            'machine_name_id' => $machine->id,
            'damage_category_id' => $category->id,
            'user_id' => $user->id,
            'pic_name' => $pic->name,
            'work_shift' => 'shift_1',
            'repair_started_at' => now()->subMinutes(40),
            'repair_finished_at' => now()->subMinutes(10),
            'status_perbaikan' => RepairStatus::ON_PROGRESS,
            'operator_report_id' => $ticket->id,
            'ada_op' => true,
            'step2_completed_at' => now()->subMinutes(5),
            'masalah' => 'Gripper robot tidak hold.',
            'analisa_penyebab_kerusakan' => 'Selang vacuum bocor.',
            'tindakan_perbaikan' => 'Ganti fitting vacuum.',
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee($ticket->item_name)
            ->assertSee('On Progress')
            ->assertSee('Close by teknisi');

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('reports.tickets.close', $ticket), $this->claimPayload($pic))
            ->assertRedirect(route('home'));

        $this->assertSame(RepairStatus::CLOSE_BY_TECHNICIAN, $ticket->fresh()->status);
        $this->assertSame(RepairStatus::CLOSE_BY_TECHNICIAN, $ticket->fresh()->maintenanceReport->status_perbaikan);
    }

    public function test_wrong_pin_shows_popup_instead_of_banner(): void
    {
        $user = User::factory()->create();
        $pic = $this->pic(['name' => 'Budi PIC']);
        $ticket = $this->ticket($this->machine());

        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic));

        $this->actingAs($user)
            ->from(route('home'))
            ->followingRedirects()
            ->post(route('reports.tickets.close', $ticket), $this->claimPayload($pic, '0000'))
            ->assertOk()
            ->assertSee('data-flash-popup', false)
            ->assertSee('PIN tidak sesuai')
            ->assertSee('PIN PIC teknisi tidak sesuai.')
            ->assertDontSee('list-disc');
    }

    public function test_verified_pin_shows_popup_on_form_1(): void
    {
        $user = User::factory()->create();
        $pic = $this->pic(['name' => 'Budi PIC']);
        $machine = $this->machine();
        $ticket = $this->ticket($machine);
        $category = $this->category();

        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic));
        $this->actingAs($user)->post(route('reports.tickets.close', $ticket), $this->claimPayload($pic));
        $this->actingAs($user)->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine));

        $this->actingAs($user)
            ->from(route('home'))
            ->followingRedirects()
            ->post(route('reports.tickets.close', $ticket), $this->claimPayload($pic))
            ->assertOk()
            ->assertSee('data-flash-popup', false)
            ->assertSee('PIN terverifikasi')
            ->assertSee('document.getElementById(\'flash-popup\')', false)
            ->assertSee('PIN PIC '.$pic->name.' terverifikasi. Isi Form 1 untuk Close ticket.')
            ->assertSee('Lanjut ke Form 2');
    }

    public function test_incomplete_form_2_does_not_resume_from_form_2(): void
    {
        $user = User::factory()->create();
        $machine = $this->machine();
        $ticket = $this->ticket($machine);
        $category = $this->category();
        $pic = $this->pic();

        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic));
        $this->actingAs($user)->post(route('reports.tickets.close', $ticket), $this->claimPayload($pic));
        $this->actingAs($user)->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine));

        $report = MaintenanceReport::query()->first();
        $this->assertNotNull($report);
        $this->assertNull($report->step2_completed_at);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('reports.step2', $report))
            ->assertRedirect(route('reports.create', ['ticket' => $ticket->id]));

        $this->actingAs($user)
            ->post(route('reports.tickets.close', $ticket), $this->claimPayload($pic))
            ->assertRedirect(route('reports.create', ['ticket' => $ticket->id]));

        $this->actingAs($user)
            ->get(route('reports.create', ['ticket' => $ticket->id]))
            ->assertOk()
            ->assertSee('Lanjut ke Form 2')
            ->assertSee('value="'.$ticket->id.'"', false);

        $this->actingAs($user)
            ->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine, [
                'work_shift' => 'shift_2',
            ]))
            ->assertRedirect(route('reports.step2', $report));

        $this->assertSame(1, MaintenanceReport::query()->count());
        $this->assertSame('shift_2', $report->fresh()->work_shift);

        $this->actingAs($user)
            ->post(route('reports.step2.store', $report), $this->step2Payload())
            ->assertRedirect(route('home'))
            ->assertSessionHas('data_terkirim', true);

        $this->assertSame(RepairStatus::CLOSE_BY_TECHNICIAN, $report->fresh()->status_perbaikan);
        $this->assertSame(MaintenanceReport::SPAREPART_NONE_LABEL, $report->fresh()->penggunaan_sparepart);
    }

    public function test_step2_requires_sparepart_choice(): void
    {
        $user = User::factory()->create();
        $machine = $this->machine();
        $ticket = $this->ticket($machine);
        $category = $this->category();
        $pic = $this->pic();

        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic));
        $this->actingAs($user)->post(route('reports.tickets.close', $ticket), $this->claimPayload($pic));
        $this->actingAs($user)->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine));
        $report = MaintenanceReport::query()->first();

        $this->actingAs($user)
            ->post(route('reports.step2.store', $report), $this->step2Payload([
                'penggunaan_sparepart_choice' => null,
            ]))
            ->assertSessionHasErrors('penggunaan_sparepart_choice');
    }

    public function test_step2_sparepart_requires_name_and_qty(): void
    {
        $user = User::factory()->create();
        $machine = $this->machine();
        $ticket = $this->ticket($machine);
        $category = $this->category();
        $pic = $this->pic();

        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic));
        $this->actingAs($user)->post(route('reports.tickets.close', $ticket), $this->claimPayload($pic));
        $this->actingAs($user)->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine));
        $report = MaintenanceReport::query()->first();

        $this->actingAs($user)
            ->post(route('reports.step2.store', $report), $this->step2Payload([
                'penggunaan_sparepart_choice' => 'sparepart',
                'sparepart_items' => [
                    ['name' => '', 'qty' => ''],
                ],
            ]))
            ->assertSessionHasErrors('sparepart_items');

        $this->actingAs($user)
            ->post(route('reports.step2.store', $report), $this->step2Payload([
                'penggunaan_sparepart_choice' => 'sparepart',
                'sparepart_items' => [
                    ['name' => 'Bearing 6205', 'qty' => 0],
                ],
            ]))
            ->assertSessionHasErrors('sparepart_items.0.qty');
    }

    public function test_step2_sparepart_persists_text_and_json_items(): void
    {
        $user = User::factory()->create();
        $machine = $this->machine();
        $ticket = $this->ticket($machine);
        $category = $this->category();
        $pic = $this->pic();

        $this->actingAs($user)->post(route('reports.tickets.claim', $ticket), $this->claimPayload($pic));
        $this->actingAs($user)->post(route('reports.tickets.close', $ticket), $this->claimPayload($pic));
        $this->actingAs($user)->post(route('reports.store'), $this->reportPayload($ticket, $category, $machine));
        $report = MaintenanceReport::query()->first();

        $this->actingAs($user)
            ->post(route('reports.step2.store', $report), $this->step2Payload([
                'penggunaan_sparepart_choice' => 'sparepart',
                'sparepart_items' => [
                    ['name' => 'Bearing 6205', 'qty' => 2],
                    ['name' => 'Seal hydraulic', 'qty' => 1],
                ],
            ]))
            ->assertRedirect(route('home'));

        $report->refresh();
        $this->assertSame("- Bearing 6205 x 2 pcs\n- Seal hydraulic x 1 pcs", $report->penggunaan_sparepart);
        $this->assertSame([
            ['name' => 'Bearing 6205', 'qty' => 2],
            ['name' => 'Seal hydraulic', 'qty' => 1],
        ], $report->sparepart_items);
    }

    private function claimPayload(Technician $pic, string $pin = '1234'): array
    {
        return [
            'technician_id' => $pic->id,
            'pin' => $pin,
        ];
    }

    private function pic(array $overrides = []): Technician
    {
        return Technician::factory()->create(array_merge([
            'name' => 'Andi PIC',
            'npk' => fake()->unique()->numerify('######'),
            'pin' => '1234',
        ], $overrides));
    }

    private function reportPayload(OperatorReport $ticket, DamageCategory $category, MachineName $machine, array $overrides = []): array
    {
        return array_merge([
            'operator_report_id' => $ticket->id,
            'machine_name_id' => $machine->id,
            'damage_category_id' => $category->id,
            'work_shift' => 'shift_1',
            'repair_started_date' => '2026-08-20',
            'repair_started_hour' => 8,
            'repair_started_minute' => 0,
            'repair_finished_date' => '2026-08-20',
            'repair_finished_hour' => 9,
            'repair_finished_minute' => 0,
        ], $overrides);
    }

    private function step2Payload(array $overrides = []): array
    {
        $payload = array_merge([
            'masalah' => 'Mesin stuck',
            'analisa_penyebab_kerusakan' => 'Seal aus',
            'tindakan_perbaikan' => 'Ganti seal',
            'penggunaan_sparepart_choice' => 'none',
            'pin' => '1234',
        ], $overrides);

        if (array_key_exists('pin', $overrides) && $overrides['pin'] === null) {
            unset($payload['pin']);
        }

        if (array_key_exists('penggunaan_sparepart_choice', $overrides) && $overrides['penggunaan_sparepart_choice'] === null) {
            unset($payload['penggunaan_sparepart_choice']);
        }

        return $payload;
    }

    private function machine(): MachineName
    {
        return MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
    }

    private function category(): DamageCategory
    {
        return DamageCategory::query()->create([
            'name' => 'Mesin Injection',
            'allows_other' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    private function ticket(MachineName $machine): OperatorReport
    {
        return OperatorReport::query()->create([
            'operator_name' => 'Budi',
            'npk' => '123456',
            'item_name' => 'Cover Dashboard',
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'stop_date' => '2026-08-20',
            'stop_time' => '14:30',
            'problem' => 'Mesin stuck di clamping',
            'status' => RepairStatus::OPEN,
        ]);
    }
}
