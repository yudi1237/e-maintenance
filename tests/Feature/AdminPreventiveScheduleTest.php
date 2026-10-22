<?php

namespace Tests\Feature;

use App\Models\MachineName;
use App\Models\PreventiveChecklistItem;
use App\Models\PreventiveReport;
use App\Models\PreventiveSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPreventiveScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_page_and_see_schedule_preventive(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertSee('Schedule Preventive')
            ->assertSee('Pilih Kategori Preventive')
            ->assertSee('Kategori Injection')
            ->assertSee('JSW EL, P1E, PM1, PM2, PM3, PM4')
            ->assertSee('Berdiri sendiri')
            ->assertSee('Hopper')
            ->assertSee('Dehum')
            ->assertSee('Ganti kategori')
            ->assertSee('Nomer Mesin')
            ->assertSee('Nama Mesin')
            ->assertSee('Tipe Mesin')
            ->assertSee('Plan Tanggal PM')
            ->assertSee('Tipe Preventive')
            ->assertSee('+ Tambah Baris')
            ->assertSee('SIMPAN JADWAL')
            ->assertSee('Batal')
            ->assertSee((string) $machine->machine_number)
            ->assertSee('Jadwal Preventive')
            ->assertSee('Kategori Preventive')
            ->assertSee('Filter kategori')
            ->assertSee('Filter bulan')
            ->assertSee('return index + 1', false)
            ->assertDontSee('nextNumber')
            ->assertSee('Edit')
            ->assertSee('Hapus');
    }

    public function test_jadwal_preventive_shows_overdue_label_when_plan_date_has_passed(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $now = now()->timezone('Asia/Jakarta');

        $this->schedule($admin, $machine, [
            'plan_date' => $now->copy()->subDay()->toDateString(),
            'notes' => 'Jadwal sudah lewat.',
        ]);
        $this->schedule($admin, $machine, [
            'plan_date' => $now->toDateString(),
            'notes' => 'Jadwal hari ini.',
        ]);
        $this->schedule($admin, $machine, [
            'plan_date' => $now->copy()->addDay()->toDateString(),
            'notes' => 'Jadwal besok.',
        ]);

        $html = $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertSee('Jadwal sudah lewat.')
            ->assertSee('Jadwal hari ini.')
            ->assertSee('Jadwal besok.')
            ->getContent();

        $this->assertSame(1, substr_count($html, 'em-schedule-overdue'));
        $this->assertTrue(str_contains($html, 'Jadwal sudah lewat.'));
        $this->assertTrue(strpos($html, 'Jadwal sudah lewat.') < strpos($html, 'Jadwal hari ini.'));
        $this->assertTrue(strpos($html, 'Jadwal hari ini.') < strpos($html, 'Jadwal besok.'));
        $this->assertFalse(str_contains(substr($html, strpos($html, 'Jadwal hari ini.')), 'em-schedule-overdue'));
    }

    public function test_jadwal_preventive_sorts_overdue_then_today_then_nearest(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $now = now()->timezone('Asia/Jakarta');

        $this->schedule($admin, $machine, [
            'plan_date' => $now->copy()->addDays(5)->toDateString(),
            'notes' => 'Jadwal kemudian.',
        ]);
        $this->schedule($admin, $machine, [
            'plan_date' => $now->toDateString(),
            'notes' => 'Jadwal hari ini.',
        ]);
        $this->schedule($admin, $machine, [
            'plan_date' => $now->copy()->subDays(2)->toDateString(),
            'notes' => 'Jadwal overdue.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertSeeInOrder([
                'Jadwal overdue.',
                'Jadwal hari ini.',
                'Jadwal kemudian.',
            ]);
    }

    public function test_jadwal_preventive_month_filter_hides_other_months(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $now = now()->timezone('Asia/Jakarta');
        $selectedMonth = $now->copy()->subMonth();

        $this->schedule($admin, $machine, [
            'plan_date' => $selectedMonth->copy()->startOfMonth()->addDays(9)->toDateString(),
            'notes' => 'Jadwal bulan dipilih.',
        ]);
        $this->schedule($admin, $machine, [
            'plan_date' => $now->toDateString(),
            'notes' => 'Jadwal bulan ini.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index', [
                'schedule_month' => $selectedMonth->format('Y-m'),
            ]))
            ->assertOk()
            ->assertSee('Jadwal bulan dipilih.')
            ->assertDontSee('Jadwal bulan ini.')
            ->assertSee('name="schedule_month"', false)
            ->assertSee('>Semua</a>', false);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertSee('Jadwal bulan dipilih.')
            ->assertSee('Jadwal bulan ini.');
    }

    public function test_jadwal_preventive_shows_category_and_can_filter_by_group(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();

        $this->schedule($admin, $machine, [
            'preventive_type' => 'PM1',
            'notes' => 'Jadwal kategori injection.',
        ]);
        $this->schedule($admin, $machine, [
            'preventive_type' => 'HOPPER',
            'notes' => 'Jadwal kategori hopper.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertSee('Kategori Preventive')
            ->assertSee('Jadwal kategori injection.')
            ->assertSee('Jadwal kategori hopper.');

        $this->actingAs($admin)
            ->get(route('admin.preventive.index', ['schedule_category' => 'injection']))
            ->assertOk()
            ->assertSee('Jadwal kategori injection.')
            ->assertDontSee('Jadwal kategori hopper.')
            ->assertSee('name="schedule_category"', false)
            ->assertSee('>Semua</a>', false);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index', ['schedule_category' => 'HOPPER']))
            ->assertOk()
            ->assertSee('Jadwal kategori hopper.')
            ->assertDontSee('Jadwal kategori injection.');
    }

    public function test_schedule_form_no_starts_from_one_even_when_ids_are_higher(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $this->schedule($admin, $machine, [
            'notes' => 'Sudah tersimpan.',
        ]);
        $this->schedule($admin, $machine, [
            'plan_date' => now()->timezone('Asia/Jakarta')->addDay()->toDateString(),
            'notes' => 'Jadwal kedua.',
        ]);

        $html = $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('return index + 1', $html);
        $this->assertStringNotContainsString('nextNumber', $html);
        $this->assertStringNotContainsString('max(id)', $html);
    }

    public function test_jadwal_preventive_no_is_sequential_after_a_row_disappears(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create();
        $firstMachine = $this->machine();
        $secondMachine = MachineName::query()->create([
            'machine_number' => 12,
            'name' => 'Sumitomo SE180HS',
            'brand' => 'Sumitomo',
            'tonnage' => 180,
            'machine_type' => 'Injection',
            'is_active' => true,
        ]);
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        $this->schedule($admin, $firstMachine, [
            'preventive_type' => 'PM1',
            'notes' => 'Baris yang hilang.',
        ]);
        $this->schedule($admin, $secondMachine, [
            'preventive_type' => 'PM2',
            'notes' => 'Baris tampil pertama.',
        ]);
        $this->schedule($admin, $secondMachine, [
            'preventive_type' => 'PM1',
            'notes' => 'Baris tampil kedua.',
        ]);

        PreventiveReport::query()->create([
            'user_id' => $technician->id,
            'pic_name' => $technician->name,
            'machine_name_id' => $firstMachine->id,
            'machine_number' => $firstMachine->machine_number,
            'category' => 'PM1',
            'work_date' => $today,
            'work_shift' => 'shift_1',
            'items' => ['heater' => 'ok'],
            'notes' => 'OK',
            'result' => 'ok',
        ]);

        $html = $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertDontSee('Baris yang hilang.')
            ->assertSee('Baris tampil pertama.')
            ->assertSee('Baris tampil kedua.')
            ->getContent();

        preg_match_all('/em-schedule-no[^>]*>\s*(\d+)/', $html, $matches);

        $this->assertSame(['1', '2'], $matches[1]);
    }

    public function test_completed_schedule_disappears_from_jadwal_after_technician_report(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create();
        $openMachine = $this->machine();
        $doneMachine = MachineName::query()->create([
            'machine_number' => 12,
            'name' => 'Sumitomo SE180HS',
            'brand' => 'Sumitomo',
            'tonnage' => 180,
            'machine_type' => 'Injection',
            'is_active' => true,
        ]);
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        $this->schedule($admin, $openMachine, [
            'preventive_type' => 'PM1',
            'notes' => 'Masih terbuka.',
        ]);
        $this->schedule($admin, $doneMachine, [
            'preventive_type' => 'PM2',
            'plan_date' => $today,
            'notes' => 'Sudah dikerjakan teknisi.',
        ]);

        PreventiveReport::query()->create([
            'user_id' => $technician->id,
            'pic_name' => $technician->name,
            'machine_name_id' => $doneMachine->id,
            'machine_number' => $doneMachine->machine_number,
            'category' => 'PM2',
            'work_date' => $today,
            'work_shift' => 'shift_1',
            'items' => ['heater' => 'ok'],
            'notes' => 'OK',
            'result' => 'ok',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertSee('Masih terbuka.')
            ->assertDontSee('Sudah dikerjakan teknisi.')
            ->assertSee($technician->name);
    }

    public function test_schedule_hides_when_report_is_filled_before_plan_date(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create();
        $machine = $this->machine();
        $planDate = now()->timezone('Asia/Jakarta')->addDay()->toDateString();
        $workDate = now()->timezone('Asia/Jakarta')->toDateString();

        $this->schedule($admin, $machine, [
            'preventive_type' => 'PM1',
            'plan_date' => $planDate,
            'notes' => 'Dikerjakan lebih awal.',
        ]);

        PreventiveReport::query()->create([
            'user_id' => $technician->id,
            'pic_name' => $technician->name,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'category' => 'PM1',
            'work_date' => $workDate,
            'work_shift' => 'shift_1',
            'items' => ['heater' => 'ok'],
            'notes' => 'OK',
            'result' => 'ok',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertDontSee('Dikerjakan lebih awal.');
    }

    public function test_one_report_closes_only_the_earliest_matching_schedule(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create();
        $machine = $this->machine();

        $this->schedule($admin, $machine, [
            'preventive_type' => 'PM1',
            'plan_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'notes' => 'Jadwal pertama.',
        ]);
        $this->schedule($admin, $machine, [
            'preventive_type' => 'PM1',
            'plan_date' => now()->timezone('Asia/Jakarta')->addMonth()->toDateString(),
            'notes' => 'Jadwal berikutnya.',
        ]);

        PreventiveReport::query()->create([
            'user_id' => $technician->id,
            'pic_name' => $technician->name,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'category' => 'PM1',
            'work_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'work_shift' => 'shift_1',
            'items' => ['heater' => 'ok'],
            'notes' => 'OK',
            'result' => 'ok',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertDontSee('Jadwal pertama.')
            ->assertSee('Jadwal berikutnya.');
    }

    public function test_one_report_closes_only_overdue_prior_month_schedules_of_same_machine_and_type(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create();
        $machine = $this->machine();
        $now = now()->timezone('Asia/Jakarta');

        $this->schedule($admin, $machine, [
            'preventive_type' => 'PM1',
            'plan_date' => $now->copy()->subMonth()->startOfMonth()->addDays(9)->toDateString(),
            'notes' => 'Jadwal bulan lalu overdue.',
        ]);
        $this->schedule($admin, $machine, [
            'preventive_type' => 'PM1',
            'plan_date' => $now->copy()->subMonths(2)->startOfMonth()->addDays(9)->toDateString(),
            'notes' => 'Jadwal dua bulan lalu overdue.',
        ]);
        $this->schedule($admin, $machine, [
            'preventive_type' => 'PM1',
            'plan_date' => $now->copy()->startOfMonth()->addDays(14)->toDateString(),
            'notes' => 'Jadwal bulan ini tetap terbuka.',
        ]);
        $this->schedule($admin, $machine, [
            'preventive_type' => 'PM2',
            'plan_date' => $now->copy()->subMonth()->startOfMonth()->addDays(9)->toDateString(),
            'notes' => 'Kategori lain tidak tertutup.',
        ]);

        PreventiveReport::query()->create([
            'user_id' => $technician->id,
            'pic_name' => $technician->name,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'category' => 'PM1',
            'work_date' => $now->toDateString(),
            'work_shift' => 'shift_1',
            'items' => ['heater' => 'ok'],
            'notes' => 'OK',
            'result' => 'ok',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertDontSee('Jadwal bulan lalu overdue.')
            ->assertDontSee('Jadwal dua bulan lalu overdue.')
            ->assertSee('Jadwal bulan ini tetap terbuka.')
            ->assertSee('Kategori lain tidak tertutup.');
    }

    public function test_second_report_closes_current_month_after_overdue_catch_up(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create();
        $machine = $this->machine();
        $now = now()->timezone('Asia/Jakarta');

        $this->schedule($admin, $machine, [
            'preventive_type' => 'PM1',
            'plan_date' => $now->copy()->subMonth()->toDateString(),
            'notes' => 'Overdue bulan lalu.',
        ]);
        $this->schedule($admin, $machine, [
            'preventive_type' => 'PM1',
            'plan_date' => $now->toDateString(),
            'notes' => 'Jadwal bulan ini.',
        ]);

        PreventiveReport::query()->create([
            'user_id' => $technician->id,
            'pic_name' => $technician->name,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'category' => 'PM1',
            'work_date' => $now->toDateString(),
            'work_shift' => 'shift_1',
            'items' => ['heater' => 'ok'],
            'notes' => 'Catch-up',
            'result' => 'ok',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertDontSee('Overdue bulan lalu.')
            ->assertSee('Jadwal bulan ini.');

        PreventiveReport::query()->create([
            'user_id' => $technician->id,
            'pic_name' => $technician->name,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'category' => 'PM1',
            'work_date' => $now->toDateString(),
            'work_shift' => 'shift_1',
            'items' => ['heater' => 'ok'],
            'notes' => 'Bulan ini',
            'result' => 'ok',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertDontSee('Overdue bulan lalu.')
            ->assertDontSee('Jadwal bulan ini.');
    }

    public function test_admin_can_update_schedule_with_machine_snapshot(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $other = MachineName::query()->create([
            'machine_number' => 12,
            'name' => 'Sumitomo SE180HS',
            'brand' => 'Sumitomo',
            'tonnage' => 180,
            'machine_type' => 'Injection',
            'is_active' => true,
        ]);
        $schedule = $this->schedule($admin, $machine, [
            'preventive_type' => 'PM1',
            'notes' => 'Jadwal awal.',
        ]);
        $planDate = now()->timezone('Asia/Jakarta')->addDays(3)->toDateString();

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertSee('Edit')
            ->assertSee('Hapus');

        $this->actingAs($admin)
            ->patch(route('admin.preventive-schedules.update', $schedule), [
                'machine_name_id' => $other->id,
                'plan_date' => $planDate,
                'preventive_type' => 'PM2',
                'notes' => 'Jadwal diubah ke mesin lain.',
            ])
            ->assertRedirect(route('admin.preventive.index'))
            ->assertSessionHas('success', 'Jadwal preventive berhasil diperbarui.');

        $schedule->refresh();
        $this->assertSame($admin->id, $schedule->user_id);
        $this->assertSame($other->id, $schedule->machine_name_id);
        $this->assertSame(12, $schedule->machine_number);
        $this->assertSame('Sumitomo 180T', $schedule->machine_name);
        $this->assertSame('Injection', $schedule->machine_type);
        $this->assertSame($planDate, $schedule->plan_date->toDateString());
        $this->assertSame('PM2', $schedule->preventive_type);
        $this->assertSame('Jadwal diubah ke mesin lain.', $schedule->notes);
        $this->assertSame(0, PreventiveReport::query()->count());
    }

    public function test_update_requires_machine_plan_date_and_type(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $schedule = $this->schedule($admin, $machine);

        $this->actingAs($admin)
            ->from(route('admin.preventive.index'))
            ->patch(route('admin.preventive-schedules.update', $schedule), [
                'editing_id' => $schedule->id,
            ])
            ->assertRedirect(route('admin.preventive.index'))
            ->assertSessionHasErrors(['machine_name_id', 'plan_date', 'preventive_type']);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertSee('isEditing: true', false)
            ->assertSee('action="'.route('admin.preventive-schedules.update', $schedule).'"', false);
    }

    public function test_update_notes_reject_unsafe_plain_text(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $schedule = $this->schedule($admin, $machine, ['notes' => 'Catatan aman.']);
        $planDate = $schedule->plan_date->toDateString();

        $this->actingAs($admin)
            ->from(route('admin.preventive.index'))
            ->patch(route('admin.preventive-schedules.update', $schedule), [
                'editing_id' => $schedule->id,
                'machine_name_id' => $machine->id,
                'plan_date' => $planDate,
                'preventive_type' => 'PM1',
                'notes' => 'Cek <script>alert(1)</script>',
            ])
            ->assertRedirect(route('admin.preventive.index'))
            ->assertSessionHasErrors('notes');

        $this->assertSame('Catatan aman.', $schedule->fresh()->notes);
    }

    public function test_admin_can_store_schedule_with_machine_snapshot(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $planDate = now()->timezone('Asia/Jakarta')->addDays(7)->toDateString();

        $this->actingAs($admin)
            ->post(route('admin.preventive-schedules.store'), [
                'rows' => [[
                    'machine_name_id' => $machine->id,
                    'plan_date' => $planDate,
                    'preventive_type' => 'PM1',
                    'notes' => 'Jadwal PM awal bulan.',
                ]],
            ])
            ->assertRedirect(route('admin.preventive.index'));

        $schedule = PreventiveSchedule::query()->first();
        $this->assertNotNull($schedule);
        $this->assertSame($admin->id, $schedule->user_id);
        $this->assertSame($machine->id, $schedule->machine_name_id);
        $this->assertSame(7, $schedule->machine_number);
        $this->assertSame('Haitian 250T', $schedule->machine_name);
        $this->assertSame('Injection', $schedule->machine_type);
        $this->assertSame($planDate, $schedule->plan_date->toDateString());
        $this->assertSame('PM1', $schedule->preventive_type);
        $this->assertSame('Jadwal PM awal bulan.', $schedule->notes);
        $this->assertSame(0, PreventiveReport::query()->count());
    }

    public function test_admin_can_store_multiple_schedule_rows_and_skip_empty(): void
    {
        $admin = User::factory()->admin()->create();
        $first = $this->machine();
        $second = MachineName::query()->create([
            'machine_number' => 12,
            'name' => 'Sumitomo SE180HS',
            'brand' => 'Sumitomo',
            'tonnage' => 180,
            'machine_type' => 'Injection',
            'is_active' => true,
        ]);
        $firstDate = now()->timezone('Asia/Jakarta')->addDays(1)->toDateString();
        $secondDate = now()->timezone('Asia/Jakarta')->addDays(2)->toDateString();

        $this->actingAs($admin)
            ->post(route('admin.preventive-schedules.store'), [
                'rows' => [
                    [
                        'machine_name_id' => $first->id,
                        'plan_date' => $firstDate,
                        'preventive_type' => 'PM1',
                        'notes' => 'Baris 1',
                    ],
                    [
                        'machine_name_id' => '',
                        'plan_date' => '',
                        'preventive_type' => '',
                        'notes' => '',
                    ],
                    [
                        'machine_name_id' => $second->id,
                        'plan_date' => $secondDate,
                        'preventive_type' => 'PM2',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.preventive.index'))
            ->assertSessionHas('success', '2 jadwal preventive berhasil disimpan.');

        $this->assertSame(2, PreventiveSchedule::query()->count());
        $this->assertDatabaseHas('preventive_schedules', [
            'machine_name_id' => $first->id,
            'preventive_type' => 'PM1',
            'notes' => 'Baris 1',
        ]);
        $this->assertDatabaseHas('preventive_schedules', [
            'machine_name_id' => $second->id,
            'preventive_type' => 'PM2',
            'notes' => null,
        ]);
    }

    public function test_notes_are_optional(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $planDate = now()->timezone('Asia/Jakarta')->toDateString();

        $this->actingAs($admin)
            ->post(route('admin.preventive-schedules.store'), [
                'rows' => [[
                    'machine_name_id' => $machine->id,
                    'plan_date' => $planDate,
                    'preventive_type' => 'PM2',
                ]],
            ])
            ->assertRedirect(route('admin.preventive.index'));

        $this->assertDatabaseHas('preventive_schedules', [
            'machine_name_id' => $machine->id,
            'preventive_type' => 'PM2',
            'notes' => null,
        ]);
    }

    public function test_store_requires_machine_plan_date_and_type(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.preventive.index'))
            ->post(route('admin.preventive-schedules.store'), [])
            ->assertRedirect(route('admin.preventive.index'))
            ->assertSessionHasErrors(['rows']);
    }

    public function test_notes_reject_unsafe_plain_text(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $planDate = now()->timezone('Asia/Jakarta')->toDateString();

        $this->actingAs($admin)
            ->from(route('admin.preventive.index'))
            ->post(route('admin.preventive-schedules.store'), [
                'rows' => [[
                    'machine_name_id' => $machine->id,
                    'plan_date' => $planDate,
                    'preventive_type' => 'PM1',
                    'notes' => 'Cek <script>alert(1)</script>',
                ]],
            ])
            ->assertRedirect(route('admin.preventive.index'))
            ->assertSessionHasErrors('rows.0.notes');

        $this->assertSame(0, PreventiveSchedule::query()->count());
    }

    public function test_technician_cannot_open_or_store_schedule(): void
    {
        $technician = User::factory()->create();
        $machine = $this->machine();

        $this->actingAs($technician)
            ->get(route('admin.preventive.index'))
            ->assertRedirect(route('home'));

        $this->actingAs($technician)
            ->post(route('admin.preventive-schedules.store'), [
                'rows' => [[
                    'machine_name_id' => $machine->id,
                    'plan_date' => now()->timezone('Asia/Jakarta')->toDateString(),
                    'preventive_type' => 'PM1',
                ]],
            ])
            ->assertRedirect(route('home'));

        $this->assertSame(0, PreventiveSchedule::query()->count());
    }

    public function test_technician_cannot_update_schedule(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create();
        $machine = $this->machine();
        $schedule = $this->schedule($admin, $machine);
        $originalDate = $schedule->plan_date->toDateString();

        $this->actingAs($technician)
            ->patch(route('admin.preventive-schedules.update', $schedule), [
                'machine_name_id' => $machine->id,
                'plan_date' => now()->timezone('Asia/Jakarta')->addDays(1)->toDateString(),
                'preventive_type' => 'PM2',
                'notes' => 'Tidak boleh diubah teknisi.',
            ])
            ->assertRedirect(route('home'));

        $schedule->refresh();
        $this->assertSame('PM1', $schedule->preventive_type);
        $this->assertSame($originalDate, $schedule->plan_date->toDateString());
        $this->assertNull($schedule->notes);
    }

    public function test_admin_can_delete_schedule(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $schedule = PreventiveSchedule::query()->create([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => $machine->machine_type,
            'plan_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'preventive_type' => 'PM1',
            'notes' => null,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.preventive-schedules.destroy', $schedule))
            ->assertRedirect(route('admin.preventive.index'));

        $this->assertDatabaseMissing('preventive_schedules', ['id' => $schedule->id]);
    }

    public function test_injection_schedule_rejects_non_injection_type(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $planDate = now()->timezone('Asia/Jakarta')->addDays(1)->toDateString();

        $this->actingAs($admin)
            ->from(route('admin.preventive.index'))
            ->post(route('admin.preventive-schedules.store'), [
                'schedule_group' => 'injection',
                'rows' => [[
                    'machine_name_id' => $machine->id,
                    'plan_date' => $planDate,
                    'preventive_type' => 'HOPPER',
                ]],
            ])
            ->assertRedirect(route('admin.preventive.index'))
            ->assertSessionHasErrors('rows.0.preventive_type');

        $this->assertSame(0, PreventiveSchedule::query()->count());
    }

    public function test_admin_can_store_standalone_hopper_schedule(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = $this->machine();
        $planDate = now()->timezone('Asia/Jakarta')->addDays(1)->toDateString();

        $this->actingAs($admin)
            ->post(route('admin.preventive-schedules.store'), [
                'schedule_group' => 'HOPPER',
                'rows' => [[
                    'machine_name_id' => $machine->id,
                    'plan_date' => $planDate,
                    'preventive_type' => 'HOPPER',
                ]],
            ])
            ->assertRedirect(route('admin.preventive.index'));

        $this->assertDatabaseHas('preventive_schedules', [
            'machine_name_id' => $machine->id,
            'preventive_type' => 'HOPPER',
        ]);
    }

    public function test_schedule_types_come_from_active_checklist_categories(): void
    {
        $admin = User::factory()->admin()->create();
        PreventiveChecklistItem::query()->create([
            'category' => 'PM3',
            'group_key' => 'electrical',
            'group_label' => 'Electrical',
            'item_type' => 'condition',
            'item_key' => 'heater',
            'label' => 'Heater',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.preventive.index'))
            ->assertOk()
            ->assertSee('PM3')
            ->assertDontSee('>PM1</option>', false);
    }

    private function machine(): MachineName
    {
        return MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Haitian Mars 250T',
            'brand' => 'Haitian',
            'tonnage' => 250,
            'machine_type' => 'Injection',
            'is_active' => true,
        ]);
    }

    private function schedule(User $admin, MachineName $machine, array $overrides = []): PreventiveSchedule
    {
        return PreventiveSchedule::query()->create(array_merge([
            'user_id' => $admin->id,
            'machine_name_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'machine_name' => $machine->viewLabel(),
            'machine_type' => $machine->machine_type,
            'plan_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'preventive_type' => 'PM1',
            'notes' => null,
        ], $overrides));
    }
}
