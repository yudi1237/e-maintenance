<?php

namespace Tests\Feature;

use App\Models\FindingRecap;
use App\Models\OperatorReport;
use App\Models\PreventiveReport;
use App\Models\Robot;
use App\Models\User;
use App\Support\PreventiveStatus;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PreventiveApprovalDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDashboardSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_fills_admin_dashboard_like_a_running_app(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThanOrEqual(8, OperatorReport::query()->count());
        $this->assertGreaterThanOrEqual(11, PreventiveReport::query()->count());
        $this->assertGreaterThanOrEqual(4, FindingRecap::query()->count());
        $this->assertSame(60, Robot::query()->count());
        $this->assertSame('R14', PreventiveReport::query()->where('category', 'ROBOT')->value('robot_number'));
        $this->assertTrue(
            PreventiveReport::query()->where('status', PreventiveStatus::CLOSE_BY_TECHNICIAN)->exists()
        );
        $this->assertTrue(
            PreventiveReport::query()->where('status', PreventiveStatus::APPROVE_PRODUKSI_INJECTION)->exists()
        );
        $this->assertTrue(
            PreventiveReport::query()->where('status', PreventiveStatus::CLOSE)->exists()
        );
        $this->assertSame(
            6,
            PreventiveReport::query()->where('notes', 'like', PreventiveApprovalDemoSeeder::MARKER.'%')->count()
        );

        $admin = User::query()->where('npk', '100001')->first();
        $this->assertNotNull($admin);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Preventive hari ini')
            ->assertDontSee('Tidak ada tiket OP yang sedang berjalan.')
            ->assertDontSee('Belum ada hasil preventive mesin.')
            ->assertSee('Cover Dashboard Avanza')
            ->assertSee('Andi PIC')
            ->assertSee('Budi PIC')
            ->assertSee('Heater band zona 1')
            ->assertSee('Oil cooler')
            ->assertSee('PM1')
            ->assertSee('Close by teknisi')
            ->assertSee('Approve Produksi Injection')
            ->assertSee('Maintenance')
            ->assertSee('Menunggu approve Injection')
            ->assertSee('Menunggu approve Maintenance');

        $this->actingAs($admin)
            ->get(route('admin.sparepart-history.index'))
            ->assertOk()
            ->assertSee('Seal clamping 50x70x10')
            ->assertSee('Heater band zona 1')
            ->assertSee('Filter oli hidrolik')
            ->assertSee('Andi PIC');

        $stats = $this->actingAs($admin)
            ->getJson(route('admin.dashboard.stats'))
            ->assertOk()
            ->json();

        $this->assertGreaterThan(0, $stats['total']);
        $this->assertGreaterThan(0, $stats['active_machines']);
        $this->assertGreaterThan(0, $stats['closed']);
        $this->assertNotEmpty($stats['charts']['performer']['labels']);
        $this->assertNotEmpty($stats['charts']['technician_mttr']['labels']);
    }
}
