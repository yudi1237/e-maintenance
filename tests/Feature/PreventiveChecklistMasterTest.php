<?php

namespace Tests\Feature;

use App\Models\PreventiveChecklistItem;
use App\Models\User;
use App\Support\PreventiveMachineChecklist;
use Database\Seeders\PreventiveChecklistSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreventiveChecklistMasterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_preventive_checksheet_items(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Master Data Preventive')
            ->assertSee(route('admin.preventive-checklist.index'), false);

        $this->actingAs($admin)
            ->get(route('admin.preventive-checklist.index', ['category' => 'HOPPER']))
            ->assertOk()
            ->assertSee('Master Data Preventive')
            ->assertSee('Tambah Item Check Sheet')
            ->assertSee('Download Check Sheet')
            ->assertSee(route('admin.preventive-checklist.export', ['category' => 'HOPPER']), false)
            ->assertSee('Kode grup')
            ->assertSee('Nama item')
            ->assertDontSee('+ Tambah Baris')
            ->assertDontSee('rel="next"', false)
            ->assertDontSee('?page=', false);

        $this->actingAs($admin)
            ->from(route('admin.preventive-checklist.index', ['category' => 'HOPPER']))
            ->post(route('admin.preventive-checklist.store'), [
                'category' => 'HOPPER',
                'group_key' => 'air_blower',
                'group_label' => 'Sistem Udara & Blower',
                'item_type' => 'condition',
                'label' => 'Cek Filter Tambahan',
                'standard' => 'Bersih',
            ])
            ->assertRedirect(route('admin.preventive-checklist.index', ['category' => 'HOPPER']));

        $item = PreventiveChecklistItem::query()->where('item_key', 'cek_filter_tambahan')->first();
        $this->assertNotNull($item);
        $this->assertSame('Cek Filter Tambahan', $item->label);

        $this->actingAs($admin)
            ->put(route('admin.preventive-checklist.update', $item), [
                'category' => 'HOPPER',
                'group_key' => 'air_blower',
                'group_label' => 'Sistem Udara & Blower',
                'item_type' => 'condition',
                'item_key' => 'cek_filter_tambahan',
                'label' => 'Cek Filter Tambahan Revisi',
                'standard' => 'Sangat bersih',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $this->assertSame('Cek Filter Tambahan Revisi', $item->fresh()->label);
        $this->assertContains('Cek Filter Tambahan Revisi', PreventiveMachineChecklist::items('HOPPER'));

        $this->actingAs($admin)
            ->delete(route('admin.preventive-checklist.destroy', $item))
            ->assertRedirect();

        $this->assertDatabaseMissing('preventive_checklist_items', ['id' => $item->id]);
    }

    public function test_seeder_imports_builtin_checksheets(): void
    {
        $this->seed(PreventiveChecklistSeeder::class);

        $this->assertGreaterThan(10, PreventiveChecklistItem::query()->count());
        $this->assertTrue(
            PreventiveChecklistItem::query()->where('category', 'PM1')->where('item_key', 'thermocouple')->exists()
        );

        $admin = User::factory()->admin()->create();
        $lastPm1 = PreventiveChecklistItem::query()
            ->where('category', 'PM1')
            ->orderBy('group_sort_order')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->last();

        $this->actingAs($admin)
            ->get(route('admin.preventive-checklist.index', ['category' => 'PM1']))
            ->assertOk()
            ->assertSee($lastPm1->label)
            ->assertDontSee('rel="next"', false);
        $this->assertSame(
            array_keys(PreventiveMachineChecklist::builtinGroups('HOPPER')[0]['items']),
            array_keys(PreventiveMachineChecklist::groups('HOPPER')[0]['items'])
        );
    }

    public function test_admin_can_download_check_sheet_excel(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.preventive-checklist.export', ['category' => 'PM1']))
            ->assertOk();

        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            (string) $response->headers->get('content-type')
        );
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('check-sheet-PM1-', (string) $response->headers->get('content-disposition'));
        $this->assertNotEmpty($response->streamedContent());
    }
}
