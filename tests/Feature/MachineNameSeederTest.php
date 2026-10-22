<?php

namespace Tests\Feature;

use App\Models\MachineName;
use App\Models\User;
use App\Support\InjectionMachine;
use Database\Seeders\MachineNameSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MachineNameSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_sixty_active_demo_machines(): void
    {
        $this->seed(MachineNameSeeder::class);

        $this->assertSame(60, InjectionMachine::count());
        $this->assertSame(60, MachineName::query()->count());
        $this->assertSame(60, MachineName::query()->active()->count());
        $this->assertSame(range(1, 60), MachineName::query()->orderBy('machine_number')->get()->pluck('machine_number')->all());
        $this->assertSame('Mesin Demo 1', MachineName::query()->where('machine_number', 1)->value('name'));
        $this->assertSame('Mesin Demo 60', MachineName::query()->where('machine_number', 60)->value('name'));
        $this->assertTrue((bool) MachineName::query()->where('machine_number', 60)->value('is_active'));
    }

    public function test_seeder_is_idempotent_and_does_not_overwrite_real_machines(): void
    {
        MachineName::query()->create([
            'machine_number' => 7,
            'name' => 'Haitian Mars 250T',
            'brand' => 'Haitian',
            'machine_model' => 'Mars III',
            'tonnage' => 250,
            'asset_number' => 'ASSET-007',
            'is_active' => true,
        ]);

        $this->seed(MachineNameSeeder::class);
        $this->seed(MachineNameSeeder::class);

        $this->assertSame(60, MachineName::query()->count());

        $real = MachineName::query()->where('machine_number', 7)->first();
        $this->assertSame('Haitian Mars 250T', $real->name);
        $this->assertSame('Haitian', $real->brand);
        $this->assertSame('Mars III', $real->machine_model);
        $this->assertSame('ASSET-007', $real->asset_number);

        $this->assertSame(1, MachineName::query()->where('name', 'Mesin Demo 1')->count());
        $this->assertSame(0, MachineName::query()->where('machine_number', 7)->where('name', 'Mesin Demo 7')->count());
    }

    public function test_master_data_dashboard_and_preventive_picker_list_all_sixty_machines(): void
    {
        $this->seed(MachineNameSeeder::class);

        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.machine-names.index'))
            ->assertOk()
            ->assertSee('Mesin Demo 1')
            ->assertDontSee('Mesin Demo 60')
            ->assertSee('aria-label="Halaman 2"', false)
            ->assertSee('aria-label="Halaman 6"', false);

        $this->actingAs($admin)
            ->get(route('admin.machine-names.index', ['page' => 6]))
            ->assertOk()
            ->assertSee('Mesin Demo 60')
            ->assertDontSee('Mesin Demo 1');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Peta 60 Mesin Injection')
            ->assertSee('Demo 300T')
            ->assertSee(route('admin.machines.show', 60), false);

        $this->actingAsWithPic($technician)
            ->get(route('preventive.create'))
            ->assertOk()
            ->assertSee('Demo 120T')
            ->assertSee('Demo 300T')
            ->assertSee(route('preventive.categories', 60), false);
    }
}
