<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\MachineName;
use App\Models\Robot;
use App\Models\User;
use App\Support\EquipmentCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMasterDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_edit_machine_master_data(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = MachineName::query()->create($this->machinePayload());

        $this->actingAs($admin)
            ->get(route('admin.machine-names.index'))
            ->assertOk()
            ->assertSee('Edit')
            ->assertSee('Tambah Mesin')
            ->assertSee('Form Master Data Mesin')
            ->assertSee('No Asset Mesin')
            ->assertDontSee('+ Tambah Baris')
            ->assertSee(route('admin.machine-names.edit', $machine), false);

        $this->actingAs($admin)
            ->get(route('admin.machine-names.edit', $machine))
            ->assertOk()
            ->assertSee('Edit Master Data Mesin 8')
            ->assertSee('Haitian');

        $this->actingAs($admin)
            ->put(route('admin.machine-names.update', $machine), array_merge($this->machinePayload(), [
                'brand' => 'Sumitomo',
                'machine_model' => 'SE180EV',
                'tonnage' => 180,
            ]))
            ->assertRedirect(route('admin.machine-names.index'));

        $machine->refresh();
        $this->assertSame('Sumitomo', $machine->brand);
        $this->assertSame('SE180EV', $machine->machine_model);
        $this->assertSame('Sumitomo SE180EV 180T', $machine->name);
    }

    public function test_machine_qr_shows_spec_caption_below_code(): void
    {
        $admin = User::factory()->admin()->create();
        $machine = MachineName::query()->create($this->machinePayload());

        $this->actingAs($admin)
            ->get(route('admin.machine-names.index'))
            ->assertOk()
            ->assertDontSee('No Mesin: 8')
            ->assertSee('No Asset: ASSET-008')
            ->assertSee('Brand: Haitian')
            ->assertSee('Tonage: 160T')
            ->assertSee('Machine Model: Mars 160');

        $download = $this->actingAs($admin)
            ->get(route('admin.machines.qr.download', $machine->machine_number))
            ->assertOk();

        $png = $download->getContent();
        $info = getimagesizefromstring($png);
        $this->assertIsArray($info);
        $this->assertSame('image/png', $info['mime']);
        $this->assertGreaterThan($info[0], $info[1]);
        $this->assertSame([
            'No Asset: ASSET-008',
            'Brand: Haitian',
            'Tonage: 160T',
            'Machine Model: Mars 160',
        ], $machine->qrCaptionLines());
    }

    public function test_admin_can_export_machine_master_data_excel(): void
    {
        $admin = User::factory()->admin()->create();
        MachineName::query()->create($this->machinePayload());

        $this->actingAs($admin)
            ->get(route('admin.machine-names.index'))
            ->assertOk()
            ->assertSee('Download Excel');

        $response = $this->actingAs($admin)
            ->get(route('admin.machine-names.export'))
            ->assertOk();

        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            (string) $response->headers->get('content-type')
        );
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));
        $this->assertNotEmpty($response->streamedContent());
    }

    public function test_admin_can_replace_and_delete_machine_photos(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $machine = MachineName::query()->create($this->machinePayload());

        $this->actingAs($admin)
            ->put(route('admin.machine-names.update', $machine), array_merge($this->machinePayload(), [
                'photos' => [UploadedFile::fake()->image('mesin-a.jpg')],
            ]))
            ->assertRedirect(route('admin.machine-names.index'));

        $machine->refresh();
        $this->assertCount(1, $machine->machine_photos);
        Storage::disk('public')->assertExists($machine->machine_photos[0]);

        $this->actingAs($admin)
            ->get(route('admin.machine-names.edit', $machine))
            ->assertOk()
            ->assertSee('Ganti')
            ->assertSee('Hapus');

        $this->actingAs($admin)
            ->put(route('admin.machine-names.photos.update', [$machine, 0]), [
                'photo' => UploadedFile::fake()->image('mesin-b.jpg'),
            ])
            ->assertRedirect();

        $machine->refresh();
        $this->assertCount(1, $machine->machine_photos);
        Storage::disk('public')->assertExists($machine->machine_photos[0]);

        $this->actingAs($admin)
            ->delete(route('admin.machine-names.photos.destroy', [$machine, 0]))
            ->assertRedirect();

        $this->assertSame([], $machine->fresh()->machine_photos);
    }

    public function test_admin_can_edit_user_and_reset_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'name' => 'Teknisi Lama',
            'npk' => '200099',
            'password' => 'password',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Edit Pengguna')
            ->assertSee('Password baru (opsional)');

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Teknisi Baru',
                'npk' => '200098',
                'role' => User::ROLE_TECHNICIAN,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect();

        $user->refresh();
        $this->assertSame('Teknisi Baru', $user->name);
        $this->assertSame('200098', $user->npk);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_dashboard_does_not_show_master_data_robot_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Master Data Equipment')
            ->assertDontSee('Nomor Robot')
            ->assertDontSee('Type Robot');
    }

    public function test_admin_can_manage_robots_from_menu(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.robots.index'))
            ->assertOk()
            ->assertSee('Master Data Equipment')
            ->assertSee('Input Master Data Robot')
            ->assertSee('Nomor')
            ->assertSee('Type')
            ->assertSee('Dehum')
            ->assertSee('Hopper')
            ->assertSee('Mold Temperature Control')
            ->assertSee('Mold')
            ->assertDontSee('Mold Temperature Control Mold')
            ->assertSee('Download Excel')
            ->assertSee(route('admin.robots.export', ['category' => EquipmentCategory::ROBOT]), false);

        $this->actingAs($admin)
            ->from(route('admin.robots.index'))
            ->post(route('admin.robots.store'), [
                'robot_number' => 'R01',
                'robot_type' => 'Yushin YC-150',
            ])
            ->assertRedirect(route('admin.robots.index'));

        $robot = Robot::query()->where('robot_number', 'R01')->first();
        $this->assertNotNull($robot);
        $this->assertSame('Yushin YC-150', $robot->robot_type);

        $this->actingAs($admin)
            ->put(route('admin.robots.update', $robot), [
                'robot_number' => 'R01',
                'robot_type' => 'Yushin YC-200',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $this->assertSame('Yushin YC-200', $robot->fresh()->robot_type);
    }

    public function test_admin_can_manage_equipment_master_tabs(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.robots.index', ['category' => EquipmentCategory::DEHUM]))
            ->assertOk()
            ->assertSee('Input Master Data Dehum')
            ->assertSee('Nomor')
            ->assertSee('Type')
            ->assertSee('Download Excel')
            ->assertSee(route('admin.robots.export', ['category' => EquipmentCategory::DEHUM]), false);

        $this->actingAs($admin)
            ->from(route('admin.robots.index', ['category' => EquipmentCategory::DEHUM]))
            ->post(route('admin.robots.store'), [
                'category' => EquipmentCategory::DEHUM,
                'number' => 'DH-01',
                'type' => 'Moretto D10',
            ])
            ->assertRedirect(route('admin.robots.index', ['category' => EquipmentCategory::DEHUM]));

        $equipment = Equipment::query()
            ->where('category', EquipmentCategory::DEHUM)
            ->where('number', 'DH-01')
            ->first();
        $this->assertNotNull($equipment);
        $this->assertSame('Moretto D10', $equipment->type);

        $this->actingAs($admin)
            ->put(route('admin.robots.equipments.update', $equipment), [
                'number' => 'DH-01',
                'type' => 'Moretto D20',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.robots.index', ['category' => EquipmentCategory::DEHUM]));

        $this->assertSame('Moretto D20', $equipment->fresh()->type);

        $this->actingAs($admin)
            ->get(route('admin.robots.index', ['category' => EquipmentCategory::MTC_MOLD]))
            ->assertOk()
            ->assertSee('Input Master Data Mold')
            ->assertSee('Mold')
            ->assertDontSee('Mold Temperature Control Mold')
            ->assertSee(route('admin.robots.export', ['category' => EquipmentCategory::MTC_MOLD]), false);
    }

    public function test_admin_can_export_equipment_master_excel_per_category(): void
    {
        $admin = User::factory()->admin()->create();

        Robot::query()->create([
            'robot_number' => 'R01',
            'robot_type' => 'Yushin YC-150',
            'is_active' => true,
        ]);
        Equipment::query()->create([
            'category' => EquipmentCategory::DEHUM,
            'number' => 'DH-01',
            'type' => 'Moretto D10',
            'is_active' => true,
        ]);

        $robotExport = $this->actingAs($admin)
            ->get(route('admin.robots.export', ['category' => EquipmentCategory::ROBOT]))
            ->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            (string) $robotExport->headers->get('content-type')
        );
        $this->assertStringContainsString('master-data-equipment-robot-', (string) $robotExport->headers->get('content-disposition'));
        $this->assertStringStartsWith('PK', $robotExport->streamedContent());

        $dehumExport = $this->actingAs($admin)
            ->get(route('admin.robots.export', ['category' => EquipmentCategory::DEHUM]))
            ->assertOk();
        $this->assertStringContainsString('master-data-equipment-dehum-', (string) $dehumExport->headers->get('content-disposition'));
        $this->assertNotEmpty($dehumExport->streamedContent());

        $this->actingAs($admin)
            ->from(route('admin.robots.index', ['category' => EquipmentCategory::HOPPER]))
            ->get(route('admin.robots.export', ['category' => EquipmentCategory::HOPPER]))
            ->assertRedirect(route('admin.robots.index', ['category' => EquipmentCategory::HOPPER]));
    }

    /**
     * @return array<string, mixed>
     */
    private function machinePayload(): array
    {
        return [
            'machine_number' => 8,
            'asset_number' => 'ASSET-008',
            'screw_diameter_mm' => 40,
            'brand' => 'Haitian',
            'machine_model' => 'Mars 160',
            'tonnage' => 160,
            'clamping_type' => 'Toggle',
            'machine_type' => 'Injection',
            'tie_bar_distance_mm' => '450 x 450',
            'platen_size_mm' => '600 x 600',
            'mold_thickness_min_mm' => 150,
            'mold_thickness_max_mm' => 500,
            'clamp_stroke_mm' => 400,
            'power_pump_kw' => 15.5,
            'power_heater_kw' => 8.2,
            'power_total_kw' => 23.7,
            'shot_weight_small_gr' => 120,
            'shot_weight_med_gr' => 180,
            'manufacture_year' => 2020,
            'name' => 'Haitian Mars 160 160T',
            'is_active' => true,
        ];
    }
}
