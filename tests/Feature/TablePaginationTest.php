<?php

namespace Tests\Feature;

use App\Models\MachineName;
use App\Models\OperatorReport;
use App\Models\User;
use App\Support\RepairStatus;
use App\Support\TablePagination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TablePaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_tables_show_ten_rows_and_page_numbers(): void
    {
        $this->assertSame(10, TablePagination::perPage());

        $admin = User::factory()->admin()->create();
        $machine = MachineName::query()->create([
            'machine_number' => 1,
            'name' => 'Mesin Uji Pagination',
            'is_active' => true,
        ]);

        foreach (range(1, 12) as $number) {
            OperatorReport::query()->create([
                'operator_name' => 'Operator '.$number,
                'npk' => sprintf('77%04d', $number),
                'item_name' => sprintf('Item-tiket-%02d', $number),
                'machine_name_id' => $machine->id,
                'machine_number' => $machine->machine_number,
                'stop_date' => '2026-09-08',
                'stop_time' => '08:00',
                'problem' => sprintf('Pagination-problem-%02d', $number),
                'status' => RepairStatus::OPEN,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Pagination-problem-12')
            ->assertDontSee('Pagination-problem-01')
            ->assertSee('aria-label="Halaman 2"', false);

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['page' => 2]))
            ->assertOk()
            ->assertSee('Pagination-problem-01')
            ->assertDontSee('Pagination-problem-12');

        foreach (range(1, 12) as $number) {
            MachineName::query()->create([
                'machine_number' => $number + 1,
                'name' => sprintf('Mesin-Halaman-%02d', $number),
                'machine_model' => sprintf('Model-Halaman-%02d', $number),
                'is_active' => true,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.machine-names.index'))
            ->assertOk()
            ->assertSee('Model-Halaman-01')
            ->assertDontSee('Model-Halaman-12')
            ->assertSee('aria-label="Halaman 2"', false);

        $this->actingAs($admin)
            ->get(route('admin.machine-names.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Model-Halaman-12');
    }
}
