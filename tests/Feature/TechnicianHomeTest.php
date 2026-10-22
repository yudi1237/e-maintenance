<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\MachineName;
use App\Support\EquipmentCategory;
use App\Models\OperatorReport;
use App\Models\PreventiveConveyorReport;
use App\Models\PreventiveReport;
use App\Models\PreventiveSchedule;
use App\Models\Robot;
use App\Models\Technician;
use App\Models\User;
use App\Support\PreventiveChecklist;
use App\Support\PreventiveConveyorChecklist;
use App\Support\PreventiveMachineChecklist;
use App\Support\RepairStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TechnicianHomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_home_shows_tickets_only(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('>Laporan Problem</h2>', false);
        $response->assertDontSee(route('reports.create'), false);
        $response->assertSee('Tiket Input OP');
        $response->assertDontSee('Halo,');
        $response->assertDontSee('Close ticket wajib PIN PIC yang mengambil OP');
        $response->assertSee('Preventive Mesin, Conveyor, Robot & Utility');
        $response->assertDontSee('Preventive MTC');
        $response->assertDontSee('Preventive HOPPER');
        $response->assertDontSee('Preventive DEHUM');
        $response->assertDontSee('Preventive ROBOT');
        $response->assertDontSee('Preventive HOT');
        $response->assertDontSee('Preventive Conveyor');
        $response->assertDontSee('Pilih jenis pekerjaan');
        $response->assertDontSee('Buka form');
        $response->assertSee('Live');
        $response->assertSee('Update setiap 5 detik');
        $response->assertSee(route('preventive.pic'), false);
        $response->assertSee('data-pic-pin-open', false);
        $response->assertSee('data-destination="menu"', false);
        $response->assertDontSee('data-destination="machines"', false);
        $response->assertDontSee('data-destination="conveyor"', false);
        $response->assertDontSee('data-destination="MTC"', false);
        $response->assertSee(route('preventive.menu'), false);
        $response->assertDontSee(route('preventive-conveyor.create'), false);
    }

    public function test_technician_home_tickets_endpoint_returns_live_payload(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        OperatorReport::query()->create([
            'operator_name' => 'Budi',
            'npk' => '123456',
            'item_name' => 'Cover Dashboard',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => '2026-08-20',
            'stop_time' => '14:30',
            'problem' => 'Mesin stuck di clamping',
            'status' => RepairStatus::OPEN,
        ]);

        $this->actingAs($user)
            ->getJson(route('home.tickets'))
            ->assertOk()
            ->assertJsonStructure(['html', 'signature', 'updated_at'])
            ->assertSee('Cover Dashboard', false)
            ->assertSee('Ambil', false);
    }

    public function test_technician_home_hides_tickets_closed_by_admin(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);
        OperatorReport::query()->create([
            'operator_name' => 'Budi',
            'npk' => '123456',
            'item_name' => 'Cover Dashboard',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => '2026-08-20',
            'stop_time' => '14:30',
            'problem' => 'Mesin stuck di clamping',
            'status' => RepairStatus::OPEN,
        ]);
        OperatorReport::query()->create([
            'operator_name' => 'Siti',
            'npk' => '654321',
            'item_name' => 'Panel Pintu',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => '2026-08-20',
            'stop_time' => '11:00',
            'problem' => 'Sudah ditutup admin',
            'status' => RepairStatus::CLOSE,
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Cover Dashboard')
            ->assertDontSee('Panel Pintu')
            ->assertDontSee('Sudah ditutup admin');

        $this->actingAs($user)
            ->getJson(route('home.tickets'))
            ->assertOk()
            ->assertSee('Cover Dashboard', false)
            ->assertDontSee('Panel Pintu', false);
    }

    public function test_technician_home_lists_tickets_open_then_on_progress_then_approval(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 2,
            'name' => 'Mesin Uji 2',
            'is_active' => true,
        ]);

        OperatorReport::query()->create([
            'operator_name' => 'Siti',
            'npk' => '222222',
            'item_name' => 'Tiket Approve Quality',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => '2026-08-20',
            'stop_time' => '08:00',
            'problem' => 'Sudah approve quality',
            'status' => RepairStatus::APPROVE_QUALITY,
            'created_at' => now()->subMinutes(5),
        ]);
        OperatorReport::query()->create([
            'operator_name' => 'Rina',
            'npk' => '333333',
            'item_name' => 'Tiket Approve Produksi',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => '2026-08-20',
            'stop_time' => '09:00',
            'problem' => 'Sudah approve produksi',
            'status' => RepairStatus::APPROVE_PRODUKSI_INJECTION,
            'created_at' => now()->subMinutes(4),
        ]);
        OperatorReport::query()->create([
            'operator_name' => 'Andi',
            'npk' => '444444',
            'item_name' => 'Tiket Close Teknisi',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => '2026-08-20',
            'stop_time' => '10:00',
            'problem' => 'Sudah close teknisi',
            'status' => RepairStatus::CLOSE_BY_TECHNICIAN,
            'created_at' => now()->subMinutes(3),
        ]);
        OperatorReport::query()->create([
            'operator_name' => 'Budi',
            'npk' => '555555',
            'item_name' => 'Tiket On Progress',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => '2026-08-20',
            'stop_time' => '11:00',
            'problem' => 'Sedang dikerjakan',
            'status' => RepairStatus::ON_PROGRESS,
            'created_at' => now()->subMinutes(2),
        ]);
        OperatorReport::query()->create([
            'operator_name' => 'Cici',
            'npk' => '666666',
            'item_name' => 'Tiket Open',
            'machine_name_id' => $machine->id,
            'machine_number' => 2,
            'stop_date' => '2026-08-20',
            'stop_time' => '12:00',
            'problem' => 'Baru masuk',
            'status' => RepairStatus::OPEN,
            'created_at' => now()->subMinute(),
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder([
                'Tiket Open',
                'Tiket On Progress',
                'Tiket Close Teknisi',
                'Tiket Approve Produksi',
                'Tiket Approve Quality',
            ]);
    }

    public function test_technician_cannot_open_preventive_without_pic_pin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('preventive.create'))
            ->assertRedirect(route('home'));

        $this->actingAs($user)
            ->get(route('preventive.menu'))
            ->assertRedirect(route('home'));

        $this->actingAs($user)
            ->get(route('preventive-conveyor.create'))
            ->assertRedirect(route('home'));
    }

    public function test_preventive_pic_pin_fills_pic_name_from_technician_not_account(): void
    {
        $user = User::factory()->create(['name' => 'Teknisi Akun']);
        $pic = Technician::factory()->create(['name' => 'Andi PIC', 'pin' => '1234']);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('preventive.pic'), [
                'technician_id' => $pic->id,
                'pin' => '1234',
                'destination' => 'machines',
            ])
            ->assertRedirect(route('preventive.create'));

        $this->get(route('preventive.create'))
            ->assertOk()
            ->assertSee('<strong>Andi PIC</strong>', false);

        $this->get(route('preventive.form', [$machine->machine_number, 'PM1']))
            ->assertOk()
            ->assertSee('<strong>Andi PIC</strong>', false);
    }

    public function test_preventive_pic_pin_rejects_wrong_pin(): void
    {
        $user = User::factory()->create();
        $pic = Technician::factory()->create(['name' => 'Andi PIC', 'pin' => '1234']);

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('preventive.pic'), [
                'technician_id' => $pic->id,
                'pin' => '0000',
                'destination' => 'machines',
            ])
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('pin');

        $this->get(route('preventive.create'))
            ->assertRedirect(route('home'));
    }

    public function test_preventive_pic_pin_opens_menu_of_forms(): void
    {
        $user = User::factory()->create(['name' => 'Teknisi Akun']);
        $pic = Technician::factory()->create(['name' => 'Andi PIC', 'pin' => '1234']);

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('preventive.pic'), [
                'technician_id' => $pic->id,
                'pin' => '1234',
                'destination' => 'menu',
            ])
            ->assertRedirect(route('preventive.menu'));

        $this->get(route('preventive.menu'))
            ->assertOk()
            ->assertSee('Preventive Mesin, Conveyor, Robot & Utility')
            ->assertSee('Jadwal Preventive')
            ->assertSee('Belum ada jadwal preventive dari admin.')
            ->assertSee('Additional Preventive')
            ->assertSee('Preventive Mesin')
            ->assertSee('Preventive Conveyor')
            ->assertSee('Preventive Mold Temperature Control')
            ->assertSee('Preventive Hopper')
            ->assertSee('Preventive Dehum')
            ->assertSee('Preventive Robot')
            ->assertSee('Preventive Hot Runner')
            ->assertDontSee('Jadwal preventive tampil di bawah')
            ->assertDontSee('Form tambahan ada di Additional Preventive')
            ->assertSeeInOrder(['Tanggal:', 'PIC aktif:', 'Andi PIC'])
            ->assertSee('<strong class="text-white">Andi PIC</strong>', false)
            ->assertSee(route('preventive.create'), false)
            ->assertSee(route('preventive-conveyor.create'), false)
            ->assertSee(route('preventive.create', ['category' => 'MTC']), false)
            ->assertSee(route('preventive.create', ['category' => 'HOPPER']), false)
            ->assertSee(route('preventive.create', ['category' => 'DEHUM']), false)
            ->assertSee(route('preventive.create', ['category' => 'ROBOT']), false)
            ->assertSee(route('preventive.create', ['category' => 'HOT']), false)
            ->assertDontSee('M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', false)
            ->assertSee('M9 10.5V6.2L12 3.5', false)
            ->assertSee('cx="6.2" cy="15.7"', false)
            ->assertSee('M15 7.2h4.2v3.2h-2.4v5.2H20v3.2h-6.4', false)
            ->assertSee('L13.6 17.6h-3.2L7.2 10V4.2z', false)
            ->assertSee('M18.6 13.2s-2.4 3-2.4 4.6a2.4 2.4 0 004.8 0c0-1.6-2.4-4.6-2.4-4.6z', false)
            ->assertSee('M4.8 20.2h6.4v-2.6H4.8z', false)
            ->assertSee('M12 3.2c.3 3.6-3.2 5.2-3.2 8.7a3.2 3.2 0 006.4 0c0-3.5-3.5-5.1-3.2-8.7z', false);
    }

    public function test_preventive_menu_shows_open_admin_schedules_like_op_tickets(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $this->actingAsWithPic($user);
        $openMachine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Haitian Mars 250T',
            'brand' => 'Haitian',
            'tonnage' => 250,
            'is_active' => true,
        ]);
        $doneMachine = MachineName::query()->create([
            'machine_number' => 8,
            'name' => 'Mesin Selesai',
            'is_active' => true,
        ]);
        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $yesterday = now()->timezone('Asia/Jakarta')->subDay()->toDateString();

        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $openMachine->id,
            'machine_number' => 7,
            'machine_name' => $openMachine->viewLabel(),
            'machine_type' => 'Injection',
            'plan_date' => $today,
            'preventive_type' => 'PM1',
            'notes' => 'Jadwal admin PM1',
        ]);
        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $doneMachine->id,
            'machine_number' => 8,
            'machine_name' => $doneMachine->viewLabel(),
            'machine_type' => 'Injection',
            'plan_date' => $yesterday,
            'preventive_type' => 'PM2',
            'notes' => 'Sudah dikerjakan',
        ]);
        PreventiveReport::query()->create([
            'user_id' => $user->id,
            'pic_name' => 'Andi PIC',
            'machine_name_id' => $doneMachine->id,
            'machine_number' => 8,
            'category' => 'PM2',
            'work_date' => $yesterday,
            'work_shift' => 'shift_1',
            'items' => ['heater' => 'ok'],
            'notes' => 'OK',
            'result' => 'ok',
        ]);

        $this->get(route('preventive.menu'))
            ->assertOk()
            ->assertSee('Jadwal Preventive')
            ->assertSee('Jadwal admin PM1')
            ->assertSee('Hari ini')
            ->assertSee('Isi')
            ->assertSee('Mesin 7')
            ->assertDontSee('Sudah dikerjakan')
            ->assertSee(route('preventive.form', [
                'machineNumber' => 7,
                'category' => 'PM1',
                'from' => 'schedule',
            ]), false);

        $this->get(route('preventive.form', [
            'machineNumber' => 7,
            'category' => 'PM1',
            'from' => 'schedule',
        ]))
            ->assertOk()
            ->assertSee('Kembali ke jadwal')
            ->assertSee('name="from"', false);

        $this->get(route('preventive.schedules'))
            ->assertOk()
            ->assertJsonStructure(['html', 'signature', 'updated_at'])
            ->assertSee('Jadwal admin PM1', false);
    }

    public function test_preventive_board_no_is_sequential_display_index(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $this->actingAsWithPic($user);
        $firstMachine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Haitian Mars 250T',
            'brand' => 'Haitian',
            'tonnage' => 250,
            'is_active' => true,
        ]);
        $secondMachine = MachineName::query()->create([
            'machine_number' => 8,
            'name' => 'Mesin Kedua',
            'is_active' => true,
        ]);
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        $hidden = PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $firstMachine->id,
            'machine_number' => 7,
            'machine_name' => $firstMachine->viewLabel(),
            'machine_type' => 'Injection',
            'plan_date' => $today,
            'preventive_type' => 'PM1',
            'notes' => 'Baris yang hilang.',
        ]);
        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $secondMachine->id,
            'machine_number' => 8,
            'machine_name' => $secondMachine->viewLabel(),
            'machine_type' => 'Injection',
            'plan_date' => $today,
            'preventive_type' => 'PM2',
            'notes' => 'Baris tampil pertama.',
        ]);
        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $secondMachine->id,
            'machine_number' => 8,
            'machine_name' => $secondMachine->viewLabel(),
            'machine_type' => 'Injection',
            'plan_date' => $today,
            'preventive_type' => 'PM1',
            'notes' => 'Baris tampil kedua.',
        ]);

        PreventiveReport::query()->create([
            'user_id' => $user->id,
            'pic_name' => 'Andi PIC',
            'machine_name_id' => $firstMachine->id,
            'machine_number' => 7,
            'category' => 'PM1',
            'work_date' => $today,
            'work_shift' => 'shift_1',
            'items' => ['heater' => 'ok'],
            'notes' => 'OK',
            'result' => 'ok',
        ]);

        $html = $this->get(route('preventive.menu'))
            ->assertOk()
            ->assertDontSee('Baris yang hilang.')
            ->assertSee('Baris tampil pertama.')
            ->assertSee('Baris tampil kedua.')
            ->assertDontSee('>#'.$hidden->id.'<', false)
            ->getContent();

        preg_match_all('/em-schedule-no[^>]*>\s*(\d+)/', $html, $matches);

        $this->assertSame(['1', '2'], $matches[1]);
    }

    public function test_preventive_board_status_badges_use_contrast_classes(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $this->actingAsWithPic($user);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Haitian Mars 250T',
            'is_active' => true,
        ]);
        $now = now()->timezone('Asia/Jakarta');

        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 7,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => 'Injection',
            'plan_date' => $now->copy()->subDay()->toDateString(),
            'preventive_type' => 'PM1',
            'notes' => 'Overdue board.',
        ]);
        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 7,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => 'Injection',
            'plan_date' => $now->toDateString(),
            'preventive_type' => 'PM2',
            'notes' => 'Today board.',
        ]);
        PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => 7,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => 'Injection',
            'plan_date' => $now->copy()->addDays(3)->toDateString(),
            'preventive_type' => 'PM3',
            'notes' => 'Open board.',
        ]);

        $this->get(route('preventive.menu'))
            ->assertOk()
            ->assertSee('em-status-badge-overdue', false)
            ->assertSee('em-status-badge-today', false)
            ->assertSee('em-status-badge-open', false)
            ->assertSeeInOrder(['Overdue board.', 'Today board.', 'Open board.']);
    }

    public function test_storing_scheduled_preventive_returns_to_schedule_board(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_TECHNICIAN]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->post(route('preventive.store'), [
                'machine_name_id' => $machine->id,
                'category' => 'PM1',
                'work_shift' => 'shift_2',
                'from' => 'schedule',
                'items' => PreventiveMachineChecklist::filledOk('PM1'),
                'readings' => array_merge(PreventiveMachineChecklist::filledReadings('PM1'), [
                    'electrical_component_temp' => '36',
                    'hydraulic_oil_temp' => '50',
                    'nozzle_hole_radius' => '3.5 / R15',
                ]),
            ])
            ->assertRedirect(route('preventive.menu'));
    }

    public function test_conveyor_pic_pin_fills_pic_name_from_technician(): void
    {
        $user = User::factory()->create(['name' => 'Teknisi Akun']);
        $pic = Technician::factory()->create(['name' => 'Budi PIC', 'pin' => '1234']);

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('preventive.pic'), [
                'technician_id' => $pic->id,
                'pin' => '1234',
                'destination' => 'conveyor',
            ])
            ->assertRedirect(route('preventive-conveyor.create'));

        $this->get(route('preventive-conveyor.create'))
            ->assertOk()
            ->assertSee('<strong>Budi PIC</strong>', false);
    }

    public function test_utility_pic_pin_opens_machine_picker_for_that_category(): void
    {
        $user = User::factory()->create(['name' => 'Teknisi Akun']);
        $pic = Technician::factory()->create(['name' => 'Andi PIC', 'pin' => '1234']);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->from(route('home'))
            ->post(route('preventive.pic'), [
                'technician_id' => $pic->id,
                'pin' => '1234',
                'destination' => 'MTC',
            ])
            ->assertRedirect(route('preventive.create', ['category' => 'MTC']));

        $this->get(route('preventive.create', ['category' => 'MTC']))
            ->assertOk()
            ->assertSee('Preventive Mold Temperature Control')
            ->assertSee('<strong>Andi PIC</strong>', false)
            ->assertSee(route('preventive.form', [$machine->machine_number, 'MTC']), false);
    }

    public function test_technician_can_open_preventive_form(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'machine_model' => 'Injection 180T',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.create'))
            ->assertOk()
            ->assertSee('Preventive Mesin')
            ->assertSee('Preventive Mesin, Conveyor, Robot & Utility')
            ->assertSee('Andi PIC')
            ->assertSee((string) $machine->machine_number)
            ->assertSee(route('preventive.categories', $machine->machine_number), false)
            ->assertSee('grid-cols-3 sm:grid-cols-5 md:grid-cols-8 lg:grid-cols-10', false)
            ->assertDontSee('Pilih Mesin')
            ->assertDontSee('Checklist Preventive');
    }

    public function test_technician_can_open_preventive_categories_after_choosing_machine(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.categories', $machine->machine_number))
            ->assertOk()
            ->assertSee('Kategori Preventive')
            ->assertSee('PM1')
            ->assertSee('PM2')
            ->assertSee('PM3')
            ->assertSee('PM4')
            ->assertSee('P1E')
            ->assertSee('JSW EL')
            ->assertDontSee('>MTC<', false)
            ->assertDontSee('>HOPPER<', false)
            ->assertDontSee('>DEHUM<', false)
            ->assertDontSee('>ROBOT<', false)
            ->assertDontSee('>HOT<', false)
            ->assertDontSee(route('preventive.form', [$machine->machine_number, 'MTC']), false)
            ->assertSee(route('preventive.form', [$machine->machine_number, 'JSWEL']), false)
            ->assertSee('grid-cols-5', false)
            ->assertSee('em-shell', false)
            ->assertSee(route('preventive.form', [$machine->machine_number, 'PM1']), false);
    }

    public function test_technician_can_open_preventive_category_form(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.form', [$machine->machine_number, 'ROBOT']))
            ->assertOk()
            ->assertSee('Preventive Robot')
            ->assertSee('Mesin 7')
            ->assertSee('Mekanikal & Pneumatik');
    }

    public function test_technician_can_open_pm1_checklist_form(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.form', [$machine->machine_number, 'PM1']))
            ->assertOk()
            ->assertSee('Preventive Maintenance 1 (PM1)')
            ->assertSee('Tegangan Utama')
            ->assertSee('Tegangan Power Supply')
            ->assertSee('name="readings[voltage_rs]"', false)
            ->assertSee('name="readings[voltage_st]"', false)
            ->assertSee('name="readings[voltage_rt]"', false)
            ->assertSee('name="readings[voltage_pe_n]"', false)
            ->assertSee('name="readings[voltage_power_supply]"', false)
            ->assertSee('Electrical Section Check')
            ->assertSee('Thermocouple')
            ->assertSee('Coil/ Band Heater')
            ->assertSee('Safety Section Check')
            ->assertSee('Hydraulic Section Check')
            ->assertSee('Mechanical Section Check')
            ->assertSee('Lubrication Section Check')
            ->assertSee('Pelumasan Manual')
            ->assertSee('Tie-bar & Nut Tie-bar')
            ->assertSee('Outlook of Machine')
            ->assertSee('ADDITIONAL PM')
            ->assertSee('Tambah Grease Gearbox Injection')
            ->assertSee('Good')
            ->assertSee('No Good')
            ->assertSee('Dilakukan')
            ->assertSee('Belum Dilakukan')
            ->assertSee('Isi Manual')
            ->assertSee('name="readings[electrical_component_temp]"', false)
            ->assertSee('name="readings[hydraulic_oil_temp]"', false)
            ->assertSee('name="readings[nozzle_hole_radius]"', false)
            ->assertDontSee('N/A');
    }

    public function test_technician_can_open_pm2_checklist_form(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.form', [$machine->machine_number, 'PM2']))
            ->assertOk()
            ->assertSee('Preventive Maintenance 2 (PM2)')
            ->assertSee('Tegangan Utama')
            ->assertSee('name="readings[voltage_rs]"', false)
            ->assertSee('Electrical Section Check')
            ->assertSee('Hydraulic Section Check')
            ->assertSee('ADDITIONAL PM')
            ->assertSee('Bersihkan Oil Cooler')
            ->assertSee('Ganti Filter Tissue Oli')
            ->assertSee('Bersihkan Filter Oli')
            ->assertSee('Ambil Sampel, Cek Warna Oli')
            ->assertSee('Cek Check Ring & Barrel')
            ->assertDontSee('Tambah Grease Gearbox Injection')
            ->assertSee('Isi Manual')
            ->assertSee('name="readings[electrical_component_temp]"', false)
            ->assertSee('name="readings[hydraulic_oil_temp]"', false)
            ->assertSee('name="readings[nozzle_hole_radius]"', false);
    }

    public function test_technician_can_open_pm3_checklist_form(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.form', [$machine->machine_number, 'PM3']))
            ->assertOk()
            ->assertSee('Preventive Maintenance 3 (PM3)')
            ->assertSee('Tegangan Utama')
            ->assertSee('name="readings[voltage_rs]"', false)
            ->assertSee('ADDITIONAL PM')
            ->assertSee('Ganti O-ring Screw Maju/Mundur')
            ->assertSee('Ganti O-ring Inject Maju-mundur')
            ->assertSee('Ganti O-ring Suckback')
            ->assertSee('Ganti O-ring Proportional Valve')
            ->assertDontSee('Bersihkan Oil Cooler')
            ->assertDontSee('Tambah Grease Gearbox Injection')
            ->assertSee('Isi Manual')
            ->assertSee('name="readings[electrical_component_temp]"', false);
    }

    public function test_technician_can_open_pm4_checklist_form(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.form', [$machine->machine_number, 'PM4']))
            ->assertOk()
            ->assertSee('Preventive Maintenance 4 (PM4)')
            ->assertSee('Tegangan Utama')
            ->assertSee('name="readings[voltage_rs]"', false)
            ->assertSee('ADDITIONAL PM')
            ->assertSee('Ganti O-ring Valve Open')
            ->assertSee('Ganti O-ring Valve Close')
            ->assertSee('Ganti O-ring Valve Mold Adjust')
            ->assertSee('Ganti O-ring Valve Mold Core')
            ->assertSee('Ganti O-ring Valve Ejector')
            ->assertDontSee('Ganti O-ring Screw Maju/Mundur')
            ->assertDontSee('Bersihkan Oil Cooler')
            ->assertSee('Isi Manual')
            ->assertSee('name="readings[electrical_component_temp]"', false);
    }

    public function test_technician_can_open_p1e_checklist_form(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.form', [$machine->machine_number, 'P1E']))
            ->assertOk()
            ->assertSee('Preventive P1E')
            ->assertSee('Tegangan Utama')
            ->assertSee('name="readings[voltage_rs]"', false)
            ->assertSee('Safety Foot Mold (850T)')
            ->assertSee('Hydraulic Section Check (850T)')
            ->assertSee('Oli Autoclamp')
            ->assertSee('Hydraulic Corepull (jika dipakai)')
            ->assertSee('Panel Elektrik')
            ->assertSee('Cek Suhu Komponen (Thermal)')
            ->assertSee('Ganti Selang-selang Air')
            ->assertSee('Ganti Selang-selang angin')
            ->assertDontSee('ADDITIONAL PM')
            ->assertSee('Isi Manual')
            ->assertSee('name="readings[electrical_component_temp]"', false)
            ->assertSee('name="readings[nozzle_hole_radius]"', false)
            ->assertSee('name="readings[component_thermal_temp]"', false);
    }

    public function test_technician_can_open_mtc_checklist_form(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);
        Equipment::query()->create([
            'category' => EquipmentCategory::MTC,
            'number' => 'MTC-01',
            'type' => 'Sterlco TCU',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.form', [$machine->machine_number, 'MTC']))
            ->assertOk()
            ->assertSee('Preventive Mold Temperature Control')
            ->assertSee('Nomor Mold Temperature Control')
            ->assertSee('Pilih nomor Mold Temperature Control')
            ->assertSee('Master Data Mold Temperature Control')
            ->assertDontSee('Nomor Equipment')
            ->assertSee('name="equipment_id"', false)
            ->assertDontSee('name="equipment_number"', false)
            ->assertSee('MTC-01')
            ->assertSee('Sterlco TCU')
            ->assertSee('Mekanikal & Saluran Fluida')
            ->assertSee('Kebocoran Air/Oli pada Selang & Fitting')
            ->assertSee('Tekanan Air Inlet / Outlet (Pressure Gauge)')
            ->assertSee('Kondisi Pompa Sirkulasi (Suara & Getaran)')
            ->assertSee('Kondisi Strainer / Filter Air Inflow')
            ->assertSee('Kelistrikan & Kontrol')
            ->assertSee('Display Suhu Controller (PID)')
            ->assertSee('Pengencangan Terminal Listrik & Kabel')
            ->assertSee('Sistem Proteksi & Safety')
            ->assertSee('Fungsi Alarm & Sensor Overheat / Dry Burn')
            ->assertSee('OK')
            ->assertSee('Not OK')
            ->assertDontSee('Isi Manual')
            ->assertDontSee('ADDITIONAL PM')
            ->assertDontSee('Good');
    }

    public function test_technician_can_open_hopper_checklist_form(): void
    {
        $user = User::factory()->create(['name' => 'Teknisi Uji']);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);
        Equipment::query()->create([
            'category' => EquipmentCategory::HOPPER,
            'number' => 'HP-01',
            'type' => 'Matsui Hopper',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.form', [$machine->machine_number, 'HOPPER']))
            ->assertOk()
            ->assertSee('Preventive Hopper')
            ->assertSee('Nomor Hopper')
            ->assertSee('Pilih nomor Hopper')
            ->assertSee('Master Data Hopper')
            ->assertDontSee('Nomor Equipment')
            ->assertSee('name="equipment_id"', false)
            ->assertDontSee('name="equipment_number"', false)
            ->assertSee('HP-01')
            ->assertSee('Matsui Hopper')
            ->assertSee('Sistem Udara & Blower')
            ->assertSee('Kebersihan Filter Udara Masuk (Air Inflow Filter)')
            ->assertSee('Suara & Getaran Blower Motor')
            ->assertSee('Sistem Pemanas')
            ->assertSee('Kondisi & Fungsi Pemanas (Heater Pipe)')
            ->assertSee('Fisik Tanki & Mekanikal')
            ->assertSee('Kebersihan Dalam Tangki Hopper & Magnet Trap')
            ->assertSee('Kondisi Baut Dudukan Hopper')
            ->assertSee('OK')
            ->assertSee('Not OK')
            ->assertSee('Upload File Evidence')
            ->assertSee('Tambah foto')
            ->assertSee('Catatan (opsional)')
            ->assertSee('name="evidences[]"', false)
            ->assertDontSee('Isi Manual')
            ->assertDontSee('ADDITIONAL PM')
            ->assertDontSee('Thermocouple')
            ->assertDontSee('Good');
    }

    public function test_technician_can_open_dehum_checklist_form(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);
        Equipment::query()->create([
            'category' => EquipmentCategory::DEHUM,
            'number' => 'DH-01',
            'type' => 'Moretto D10',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.form', [$machine->machine_number, 'DEHUM']))
            ->assertOk()
            ->assertSee('Preventive Dehum')
            ->assertSee('Nomor Dehum')
            ->assertSee('Pilih nomor Dehum')
            ->assertSee('Master Data Dehum')
            ->assertDontSee('Nomor Equipment')
            ->assertSee('name="equipment_id"', false)
            ->assertDontSee('name="equipment_number"', false)
            ->assertSee('DH-01')
            ->assertSee('Moretto D10')
            ->assertSee('Sistem Udara & Dew Point')
            ->assertSee('Nilai Dew Point Udara Kering (Dew Point Meter)')
            ->assertSee('Kebersihan Process Air Filter & Return Filter')
            ->assertSee('Desiccant Rotor')
            ->assertSee('Putaran Honeycomb Rotor & V-Belt / Chain Drive')
            ->assertSee('Sistem Pemanas Regenerasi & Kelistrikan')
            ->assertSee('Pemeriksaan Arus Pemanas (Heater Ampere)')
            ->assertSee('OK')
            ->assertSee('Not OK')
            ->assertSee('Isi Manual')
            ->assertSee('name="readings[dry_air_dew_point]"', false)
            ->assertDontSee('ADDITIONAL PM')
            ->assertDontSee('Thermocouple')
            ->assertDontSee('Good');
    }

    public function test_technician_can_open_robot_checklist_form(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);
        Robot::query()->create([
            'robot_number' => 'R07',
            'robot_type' => 'Yushin YC-150',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.form', [$machine->machine_number, 'ROBOT']))
            ->assertOk()
            ->assertSee('Preventive Robot')
            ->assertSee('Mekanikal & Pneumatik')
            ->assertSee('Tekanan Angin Utama (Air Pressure Gauge)')
            ->assertSee('Kondisi Pad Vakum & Selang Pneumatik')
            ->assertSee('Pelumasan Guide Rail & Ball Screw (X, Y, Z Axis)')
            ->assertSee('Kekencangan Baut EOAT (Chuck Plate)')
            ->assertSee('Timing Belt (X/Y/Z Axis)')
            ->assertSee('Air Filter & Water Trap')
            ->assertSee('Baut Structural / Frame')
            ->assertSee('Cylinder Arm')
            ->assertSee('Baut Trail Kabel Arm Robot')
            ->assertSee('Tanki Lubrikasi')
            ->assertSee('Elektrik & Safety')
            ->assertSee('Fungsi Emergency Stop & Safety Interlock Mold')
            ->assertSee('Kondisi Kabel Controller (E-Touch) & Cable Carrier')
            ->assertSee('Fungsi Safety Door Interlock')
            ->assertSee('Lampu Indikator')
            ->assertSee('Kipas Pendingin Panel')
            ->assertSee('Cover Kipas')
            ->assertSee('Push Button Power On')
            ->assertSee('Nomor Robot')
            ->assertSee('Pilih nomor Robot')
            ->assertSee('Master Data Robot')
            ->assertDontSee('Nomor Equipment')
            ->assertDontSee('name="equipment_number"', false)
            ->assertSee('R07')
            ->assertSee('Yushin YC-150')
            ->assertSee('Pelumasan & Kebersihan Base')
            ->assertSee('Kebersihan Area Robot')
            ->assertSee('Linear Guide Rail (X/Y/Z)')
            ->assertSee('Ball Screw / Rack Gear')
            ->assertSee('OK')
            ->assertSee('Not OK')
            ->assertSee('Isi Manual')
            ->assertSee('name="readings[main_air_pressure]"', false)
            ->assertDontSee('ADDITIONAL PM')
            ->assertDontSee('Thermocouple')
            ->assertDontSee('Good');
    }

    public function test_technician_can_open_hot_checklist_form(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.form', [$machine->machine_number, 'HOT']))
            ->assertOk()
            ->assertSee('Preventive Hot Runner')
            ->assertSee('Nomor Hot Runner')
            ->assertDontSee('Nomor Equipment')
            ->assertSee('name="equipment_number"', false)
            ->assertSee('Hot Runner Controller')
            ->assertSee('Akurasi Suhu Tiap Zone / Nozzle (Yudo TBM/D-Type)')
            ->assertSee('Sistem Soft-Start / Dehumidify Feature')
            ->assertSee('Valve Gate Sequencer (SVG)')
            ->assertSee('Tekanan Udara / Hidrolik Valve Gate (Air Pressure Regulator)')
            ->assertSee('Timing & Urutan Buka-Tutup Valve Pin (Timer/Position)')
            ->assertSee('Kondisi Solenoid Valve & Selang Pneumatik Sequencer')
            ->assertSee('Kabel & Socket Pin')
            ->assertSee('Kondisi Kabel Power & Thermocouple Mold (Heavy Duty Connector)')
            ->assertSee('Resistansi Pemanas (Heater Ohm) & Short-to-Ground')
            ->assertSee('OK')
            ->assertSee('Not OK')
            ->assertSee('Isi Manual')
            ->assertSee('name="readings[valve_gate_pressure]"', false)
            ->assertDontSee('ADDITIONAL PM')
            ->assertDontSee('Electrical Section Check')
            ->assertDontSee('Good');
    }

    public function test_technician_can_open_jsw_el_checklist_form(): void
    {
        $user = User::factory()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)
            ->get(route('preventive.form', [$machine->machine_number, 'JSWEL']))
            ->assertOk()
            ->assertSee('Preventive JSW EL')
            ->assertSee('C. Inject Unit')
            ->assertSee('Servo Motor Inject')
            ->assertSee('Servo Motor Plasticization')
            ->assertSee('Nozzle Center & Radius Nozzle')
            ->assertSee('Dia. Lubang & Radius Nozzle')
            ->assertSee('D. System Pneumatic Low Pressure')
            ->assertSee('Pressure Regulator')
            ->assertSee('E. Safety Section')
            ->assertSee('Limit Switch Pintu Depan (Operator)')
            ->assertSee('Limit Switch Pintu Belakang')
            ->assertSee('Pintu Safety Nozzle')
            ->assertSee('Tombol Emergency')
            ->assertSee('F. Panel Mesin')
            ->assertSee('Tegangan Utama 3 Phase')
            ->assertSee('Exhaust Fan')
            ->assertSee('Power Supply')
            ->assertSee('Good')
            ->assertSee('No Good')
            ->assertSee('Isi Manual')
            ->assertSee('name="readings[nozzle_center_radius]"', false)
            ->assertSee('name="readings[nozzle_hole_radius]"', false)
            ->assertSee('name="readings[limit_switch_front_door]"', false)
            ->assertSee('name="readings[limit_switch_rear_door]"', false)
            ->assertSee('name="readings[nozzle_safety_door]"', false)
            ->assertSee('name="readings[emergency_button]"', false)
            ->assertSee('name="readings[main_voltage_3_phase]"', false)
            ->assertDontSee('name="readings[servo_motor_inject]"', false)
            ->assertDontSee('ADDITIONAL PM');
    }

    public function test_technician_can_store_preventive_report(): void
    {
        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $response = $this->actingAsWithPic($user)->post(route('preventive.store'), [
            'machine_name_id' => $machine->id,
            'category' => 'PM1',
            'work_shift' => 'shift_2',
            'items' => PreventiveMachineChecklist::filledOk('PM1'),
            'readings' => array_merge(PreventiveMachineChecklist::filledReadings('PM1'), [
                'electrical_component_temp' => '36',
                'hydraulic_oil_temp' => '50',
                'nozzle_hole_radius' => '3.5 / R15',
            ]),
            'notes' => 'Semua OK',
        ]);

        $response->assertRedirect(route('preventive.categories', $machine->machine_number));
        $this->assertDatabaseHas('preventive_reports', [
            'user_id' => $user->id,
            'pic_name' => 'Andi PIC',
            'machine_number' => 7,
            'category' => 'PM1',
            'result' => 'ok',
            'notes' => 'Semua OK',
        ]);
        $this->assertSame(1, PreventiveReport::query()->count());
        $this->assertSame([
            'voltage_rs' => '380',
            'voltage_st' => '380',
            'voltage_rt' => '380',
            'voltage_pe_n' => '0',
            'voltage_power_supply' => '24',
            'electrical_component_temp' => '36',
            'hydraulic_oil_temp' => '50',
            'nozzle_hole_radius' => '3.5 / R15',
        ], PreventiveReport::query()->first()->readings);
    }

    public function test_technician_can_store_pm2_preventive_report(): void
    {
        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $response = $this->actingAsWithPic($user)->post(route('preventive.store'), [
            'machine_name_id' => $machine->id,
            'category' => 'PM2',
            'work_shift' => 'shift_2',
            'items' => PreventiveMachineChecklist::filledOk('PM2'),
            'readings' => array_merge(PreventiveMachineChecklist::filledReadings('PM2'), [
                'electrical_component_temp' => '37',
                'hydraulic_oil_temp' => '51',
                'nozzle_hole_radius' => '3.4 / R14',
            ]),
            'notes' => 'PM2 selesai',
        ]);

        $response->assertRedirect(route('preventive.categories', $machine->machine_number));
        $this->assertDatabaseHas('preventive_reports', [
            'user_id' => $user->id,
            'machine_number' => 7,
            'category' => 'PM2',
            'result' => 'ok',
            'notes' => 'PM2 selesai',
        ]);
        $this->assertArrayHasKey('clean_oil_cooler', PreventiveReport::query()->first()->items);
    }

    public function test_technician_can_store_pm3_preventive_report(): void
    {
        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $response = $this->actingAsWithPic($user)->post(route('preventive.store'), [
            'machine_name_id' => $machine->id,
            'category' => 'PM3',
            'work_shift' => 'shift_1',
            'items' => PreventiveMachineChecklist::filledOk('PM3'),
            'readings' => PreventiveMachineChecklist::filledReadings('PM3'),
            'notes' => 'PM3 selesai',
        ]);

        $response->assertRedirect(route('preventive.categories', $machine->machine_number));
        $this->assertDatabaseHas('preventive_reports', [
            'category' => 'PM3',
            'result' => 'ok',
            'notes' => 'PM3 selesai',
        ]);
        $this->assertArrayHasKey('oring_proportional_valve', PreventiveReport::query()->first()->items);
    }

    public function test_technician_can_store_pm4_preventive_report(): void
    {
        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $response = $this->actingAsWithPic($user)->post(route('preventive.store'), [
            'machine_name_id' => $machine->id,
            'category' => 'PM4',
            'work_shift' => 'shift_1',
            'items' => PreventiveMachineChecklist::filledOk('PM4'),
            'readings' => PreventiveMachineChecklist::filledReadings('PM4'),
            'notes' => 'PM4 selesai',
        ]);

        $response->assertRedirect(route('preventive.categories', $machine->machine_number));
        $this->assertDatabaseHas('preventive_reports', [
            'category' => 'PM4',
            'result' => 'ok',
            'notes' => 'PM4 selesai',
        ]);
        $this->assertArrayHasKey('oring_valve_ejector', PreventiveReport::query()->first()->items);
    }

    public function test_technician_can_store_p1e_preventive_report(): void
    {
        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $response = $this->actingAsWithPic($user)->post(route('preventive.store'), [
            'machine_name_id' => $machine->id,
            'category' => 'P1E',
            'work_shift' => 'shift_1',
            'items' => PreventiveMachineChecklist::filledOk('P1E'),
            'readings' => PreventiveMachineChecklist::filledReadings('P1E'),
            'notes' => 'P1E selesai',
        ]);

        $response->assertRedirect(route('preventive.categories', $machine->machine_number));
        $this->assertDatabaseHas('preventive_reports', [
            'category' => 'P1E',
            'result' => 'ok',
            'notes' => 'P1E selesai',
        ]);
        $this->assertArrayHasKey('safety_foot_mold', PreventiveReport::query()->first()->items);
        $this->assertArrayHasKey('component_thermal_temp', PreventiveReport::query()->first()->readings);
    }

    public function test_technician_can_store_mtc_preventive_report(): void
    {
        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);
        $equipment = Equipment::query()->create([
            'category' => EquipmentCategory::MTC,
            'number' => 'MTC-01',
            'type' => 'Sterlco TCU',
            'is_active' => true,
        ]);

        $response = $this->actingAsWithPic($user)->post(route('preventive.store'), [
            'machine_name_id' => $machine->id,
            'category' => 'MTC',
            'equipment_id' => $equipment->id,
            'work_shift' => 'shift_1',
            'items' => PreventiveMachineChecklist::filledOk('MTC'),
            'notes' => 'MTC selesai',
        ]);

        $response->assertRedirect(route('preventive.create', ['category' => 'MTC']));
        $this->assertDatabaseHas('preventive_reports', [
            'category' => 'MTC',
            'result' => 'ok',
            'notes' => 'MTC selesai',
            'equipment_id' => $equipment->id,
            'equipment_number' => 'MTC-01',
            'equipment_type' => 'Sterlco TCU',
        ]);
        $this->assertArrayHasKey('overheat_dry_burn_alarm', PreventiveReport::query()->first()->items);
    }

    public function test_technician_can_store_hopper_preventive_report(): void
    {
        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);
        $equipment = Equipment::query()->create([
            'category' => EquipmentCategory::HOPPER,
            'number' => 'HP-01',
            'type' => 'Matsui Hopper',
            'is_active' => true,
        ]);

        $response = $this->actingAsWithPic($user)->post(route('preventive.store'), [
            'machine_name_id' => $machine->id,
            'category' => 'HOPPER',
            'equipment_id' => $equipment->id,
            'work_shift' => 'shift_1',
            'items' => PreventiveMachineChecklist::filledOk('HOPPER'),
            'notes' => 'HOPPER selesai',
        ]);

        $response->assertRedirect(route('preventive.create', ['category' => 'HOPPER']));
        $this->assertDatabaseHas('preventive_reports', [
            'category' => 'HOPPER',
            'result' => 'ok',
            'notes' => 'HOPPER selesai',
            'equipment_id' => $equipment->id,
            'equipment_number' => 'HP-01',
            'equipment_type' => 'Matsui Hopper',
        ]);
        $this->assertArrayHasKey('hopper_mounting_bolts', PreventiveReport::query()->first()->items);
        $this->assertNull(PreventiveReport::query()->first()->readings);
    }

    public function test_technician_can_upload_multiple_preventive_evidence_photos(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);
        $equipment = Equipment::query()->create([
            'category' => EquipmentCategory::HOPPER,
            'number' => 'HP-02',
            'type' => 'Matsui Hopper',
            'is_active' => true,
        ]);

        $this->actingAsWithPic($user)->post(route('preventive.store'), [
            'machine_name_id' => $machine->id,
            'category' => 'HOPPER',
            'equipment_id' => $equipment->id,
            'work_shift' => 'shift_1',
            'items' => PreventiveMachineChecklist::filledOk('HOPPER'),
            'notes' => 'Foto terlampir',
            'evidences' => [
                UploadedFile::fake()->image('hopper-depan.jpg'),
                UploadedFile::fake()->image('hopper-samping.png'),
            ],
        ])->assertRedirect(route('preventive.create', ['category' => 'HOPPER']));

        $report = PreventiveReport::query()->first();
        $this->assertNotNull($report);
        $this->assertCount(2, $report->evidence_paths ?? []);
        foreach ($report->evidence_paths as $path) {
            Storage::disk('public')->assertExists($path);
        }
        $this->assertCount(2, $report->evidence_urls);
    }

    public function test_technician_can_store_dehum_preventive_report(): void
    {
        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);
        $equipment = Equipment::query()->create([
            'category' => EquipmentCategory::DEHUM,
            'number' => 'DH-01',
            'type' => 'Moretto D10',
            'is_active' => true,
        ]);

        $response = $this->actingAsWithPic($user)->post(route('preventive.store'), [
            'machine_name_id' => $machine->id,
            'category' => 'DEHUM',
            'equipment_id' => $equipment->id,
            'work_shift' => 'shift_1',
            'items' => PreventiveMachineChecklist::filledOk('DEHUM'),
            'readings' => PreventiveMachineChecklist::filledReadings('DEHUM'),
            'notes' => 'DEHUM selesai',
        ]);

        $response->assertRedirect(route('preventive.create', ['category' => 'DEHUM']));
        $this->assertDatabaseHas('preventive_reports', [
            'category' => 'DEHUM',
            'result' => 'ok',
            'notes' => 'DEHUM selesai',
            'equipment_id' => $equipment->id,
            'equipment_number' => 'DH-01',
            'equipment_type' => 'Moretto D10',
        ]);
        $this->assertArrayHasKey('honeycomb_rotor_drive', PreventiveReport::query()->first()->items);
        $this->assertSame('-45', PreventiveReport::query()->first()->readings['dry_air_dew_point']);
    }

    public function test_technician_can_store_robot_preventive_report(): void
    {
        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);
        $robot = Robot::query()->create([
            'robot_number' => 'R01',
            'robot_type' => 'Yushin YC-150',
            'is_active' => true,
        ]);

        $response = $this->actingAsWithPic($user)->post(route('preventive.store'), [
            'machine_name_id' => $machine->id,
            'category' => 'ROBOT',
            'robot_id' => $robot->id,
            'work_shift' => 'shift_1',
            'items' => PreventiveMachineChecklist::filledOk('ROBOT'),
            'readings' => PreventiveMachineChecklist::filledReadings('ROBOT'),
            'notes' => 'ROBOT selesai',
        ]);

        $response->assertRedirect(route('preventive.create', ['category' => 'ROBOT']));
        $this->assertDatabaseHas('preventive_reports', [
            'category' => 'ROBOT',
            'result' => 'ok',
            'notes' => 'ROBOT selesai',
            'robot_id' => $robot->id,
            'robot_number' => 'R01',
            'robot_type' => 'Yushin YC-150',
            'equipment_number' => 'R01',
        ]);
        $this->assertArrayHasKey('main_air_pressure', PreventiveReport::query()->first()->items);
        $this->assertArrayHasKey('ball_screw_rack_gear', PreventiveReport::query()->first()->items);
        $this->assertSame('0.54', PreventiveReport::query()->first()->readings['main_air_pressure']);
    }

    public function test_technician_can_store_hot_preventive_report(): void
    {
        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $response = $this->actingAsWithPic($user)->post(route('preventive.store'), [
            'machine_name_id' => $machine->id,
            'category' => 'HOT',
            'equipment_number' => 'HR-01',
            'work_shift' => 'shift_1',
            'items' => PreventiveMachineChecklist::filledOk('HOT'),
            'readings' => PreventiveMachineChecklist::filledReadings('HOT'),
            'notes' => 'HOT selesai',
        ]);

        $response->assertRedirect(route('preventive.create', ['category' => 'HOT']));
        $this->assertDatabaseHas('preventive_reports', [
            'category' => 'HOT',
            'result' => 'ok',
            'notes' => 'HOT selesai',
            'equipment_number' => 'HR-01',
        ]);
        $this->assertArrayHasKey('soft_start_dehumidify', PreventiveReport::query()->first()->items);
        $this->assertSame('5', PreventiveReport::query()->first()->readings['valve_gate_pressure']);
    }

    public function test_technician_can_store_jsw_el_preventive_report(): void
    {
        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);
        $machine = MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Mesin Uji',
            'is_active' => true,
        ]);

        $response = $this->actingAsWithPic($user)->post(route('preventive.store'), [
            'machine_name_id' => $machine->id,
            'category' => 'JSWEL',
            'work_shift' => 'shift_1',
            'items' => PreventiveMachineChecklist::filledOk('JSWEL'),
            'readings' => PreventiveMachineChecklist::filledReadings('JSWEL'),
            'notes' => 'JSW EL selesai',
        ]);

        $response->assertRedirect(route('preventive.categories', $machine->machine_number));
        $this->assertDatabaseHas('preventive_reports', [
            'category' => 'JSWEL',
            'result' => 'ok',
            'notes' => 'JSW EL selesai',
        ]);
        $report = PreventiveReport::query()->first();
        $this->assertArrayHasKey('servo_motor_inject', $report->items);
        $this->assertArrayHasKey('main_voltage_3_phase', $report->items);
        $this->assertSame('R-S / S-T / R-T', $report->readings['main_voltage_3_phase']);
        $this->assertSame('Dia / Radius', $report->readings['nozzle_hole_radius']);
    }

    public function test_technician_can_open_preventive_conveyor_form(): void
    {
        $user = User::factory()->create();

        $this->actingAsWithPic($user)
            ->get(route('preventive-conveyor.create'))
            ->assertOk()
            ->assertSee('Preventive Conveyor')
            ->assertSee('Nama / No Conveyor')
            ->assertSee('Belt (Sabuk)')
            ->assertSee('Kondisi Fisik Belt')
            ->assertSee('Unit Penggerak')
            ->assertSee('Pelumasan Block Bearing')
            ->assertSee('Kelistrikan')
            ->assertSee('Fungsi tombol E-Stop & Safety lainnya')
            ->assertSee('Upload File Evidence')
            ->assertSee('Catatan (opsional)')
            ->assertDontSee('N/A');
    }

    public function test_technician_can_store_preventive_conveyor_report(): void
    {
        $user = User::factory()->create([
            'name' => 'Teknisi Uji',
            'role' => User::ROLE_TECHNICIAN,
        ]);

        $items = [];
        foreach (array_keys(PreventiveConveyorChecklist::items()) as $key) {
            $items[$key] = PreventiveChecklist::OK;
        }

        $response = $this->actingAsWithPic($user)->post(route('preventive-conveyor.store'), [
            'conveyor_name' => 'Conveyor Line 1',
            'work_shift' => 'shift_2',
            'items' => $items,
            'notes' => 'Belt kencang',
        ]);

        $response->assertRedirect(route('preventive-conveyor.create'));
        $this->assertDatabaseHas('preventive_conveyor_reports', [
            'user_id' => $user->id,
            'pic_name' => 'Andi PIC',
            'conveyor_name' => 'Conveyor Line 1',
            'result' => 'ok',
            'notes' => 'Belt kencang',
        ]);
        $this->assertSame(1, PreventiveConveyorReport::query()->count());
    }

    public function test_admin_cannot_open_preventive_conveyor_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('preventive-conveyor.create'))
            ->assertRedirect(route('admin.dashboard'));
    }
}
