<?php

namespace Tests\Feature;

use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTechnicianTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_tambah_teknisi_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.technicians.index'))
            ->assertOk()
            ->assertSee('Tambah PIC Teknisi')
            ->assertSee('PIN (4–6 digit)');
    }

    public function test_admin_can_create_pic_technician_with_pin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.technicians.store'), [
                'name' => 'Andi PIC',
                'npk' => '510001',
                'pin' => '1234',
                'pin_confirmation' => '1234',
            ])
            ->assertRedirect();

        $pic = Technician::query()->where('npk', '510001')->first();
        $this->assertNotNull($pic);
        $this->assertSame('Andi PIC', $pic->name);
        $this->assertTrue($pic->pinMatches('1234'));
        $this->assertFalse($pic->pinMatches('0000'));
    }

    public function test_produksi_injection_cannot_manage_technicians(): void
    {
        $produksi = User::factory()->produksiInjection()->create();

        $this->actingAs($produksi)
            ->get(route('admin.technicians.index'))
            ->assertForbidden();
    }
}
