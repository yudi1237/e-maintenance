<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EquipmentXlsxExport;
use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Robot;
use App\Support\EquipmentCategory;
use App\Support\TablePagination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RobotController extends Controller
{
    public function index(): View
    {
        return view('admin.robots.index', [
            'types' => EquipmentCategory::typeOptions(),
            'areas' => EquipmentCategory::areaOptions(),
            'items' => $this->items(),
        ]);
    }

    public function export(): StreamedResponse|RedirectResponse
    {
        $items = $this->exportRows();

        if ($items->isEmpty()) {
            return redirect()
                ->route('admin.robots.index')
                ->withErrors(['export' => 'Belum ada master data equipment untuk diunduh.']);
        }

        $export = new EquipmentXlsxExport;
        $filename = $export->filename();

        return response()->streamDownload(function () use ($export, $items) {
            $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
            if ($tmp === false) {
                throw new \RuntimeException('Tidak dapat membuat file sementara untuk export Excel.');
            }

            $path = $tmp.'.xlsx';
            if (! rename($tmp, $path)) {
                @unlink($tmp);
                throw new \RuntimeException('Tidak dapat menyiapkan file export Excel.');
            }

            try {
                $export->buildExporter($items)->writeToFile($path);
                readfile($path);
            } finally {
                @unlink($path);
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $equipment = Equipment::query()->create([
            'category' => $validated['category'],
            'number' => $validated['number'],
            'type' => $validated['specification'],
            'specification' => $validated['specification'],
            'area' => $validated['area'],
            'is_active' => true,
        ]);

        $this->syncRobot($equipment);

        return redirect()
            ->route('admin.robots.index')
            ->with('success', 'Master data equipment berhasil ditambahkan.');
    }

    public function update(Request $request, Robot $robot): RedirectResponse
    {
        $validated = $request->validate([
            'robot_number' => ['required', 'string', 'max:50', Rule::unique('robots', 'robot_number')->ignore($robot->id)],
            'robot_type' => ['required', 'string', 'max:150'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $robot->update([
            'robot_number' => $validated['robot_number'],
            'robot_type' => $validated['robot_type'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.robots.index')
            ->with('success', 'Master data robot berhasil diperbarui.');
    }

    public function destroy(Robot $robot): RedirectResponse
    {
        $robot->delete();

        return redirect()
            ->route('admin.robots.index')
            ->with('success', 'Master data robot berhasil dihapus.');
    }

    public function updateEquipment(Request $request, Equipment $equipment): RedirectResponse
    {
        $previousCategory = $equipment->category;
        $previousNumber = $equipment->number;
        $validated = $this->validatedPayload($request, $equipment);

        $equipment->update([
            'category' => $validated['category'],
            'number' => $validated['number'],
            'type' => $validated['specification'],
            'specification' => $validated['specification'],
            'area' => $validated['area'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->syncRobot($equipment->fresh(), $previousCategory, $previousNumber);

        return redirect()
            ->route('admin.robots.index')
            ->with('success', 'Master data equipment berhasil diperbarui.');
    }

    public function destroyEquipment(Equipment $equipment): RedirectResponse
    {
        $this->removeRobot($equipment->category, $equipment->number);
        $equipment->delete();

        return redirect()
            ->route('admin.robots.index')
            ->with('success', 'Master data equipment berhasil dihapus.');
    }

    /**
     * @return array{category: string, number: string, specification: string, area: string}
     */
    private function validatedPayload(Request $request, ?Equipment $equipment = null): array
    {
        $uniqueNumber = Rule::unique('equipments', 'number')->where(
            'category',
            (string) $request->input('category')
        );

        if ($equipment) {
            $uniqueNumber->ignore($equipment->id);
        }

        $validated = $request->validate([
            'number' => ['required', 'string', 'max:50', $uniqueNumber],
            'category' => ['required', 'string', Rule::in(EquipmentCategory::typeValues())],
            'specification' => ['required', 'string', 'max:200'],
            'area' => ['required', 'string', Rule::in(EquipmentCategory::areaValues())],
            'is_active' => ['sometimes', 'boolean'],
        ], [
            'number.required' => 'Nomor equipment wajib diisi.',
            'number.unique' => 'Nomor equipment sudah terdaftar untuk tipe ini.',
            'category.required' => 'Tipe equipment wajib dipilih.',
            'category.in' => 'Tipe equipment tidak valid.',
            'specification.required' => 'Spesifikasi wajib diisi.',
            'area.required' => 'Area wajib dipilih.',
            'area.in' => 'Area tidak valid.',
        ]);

        return [
            'category' => $validated['category'],
            'number' => $validated['number'],
            'specification' => $validated['specification'],
            'area' => $validated['area'],
        ];
    }

    private function syncRobot(Equipment $equipment, ?string $previousCategory = null, ?string $previousNumber = null): void
    {
        if ($previousCategory === EquipmentCategory::ROBOT && ($previousCategory !== $equipment->category || $previousNumber !== $equipment->number)) {
            $this->removeRobot($previousCategory, (string) $previousNumber);
        }

        if ($equipment->category !== EquipmentCategory::ROBOT) {
            return;
        }

        Robot::query()->updateOrCreate(
            ['robot_number' => $equipment->number],
            [
                'robot_type' => $equipment->specification ?: $equipment->type,
                'is_active' => $equipment->is_active,
            ],
        );
    }

    private function removeRobot(?string $category, ?string $number): void
    {
        if ($category !== EquipmentCategory::ROBOT || $number === null || $number === '') {
            return;
        }

        Robot::query()->where('robot_number', $number)->delete();
    }

    /**
     * @return Collection<int, object>
     */
    private function exportRows(): Collection
    {
        return Equipment::query()
            ->orderBy('number')
            ->orderBy('category')
            ->get()
            ->map(fn (Equipment $item) => (object) [
                'number' => $item->number,
                'type_label' => $item->category_label,
                'specification' => $item->specification ?: $item->type,
                'area_label' => $item->area_label,
                'is_active' => (bool) $item->is_active,
            ]);
    }

    private function items()
    {
        return Equipment::query()
            ->orderBy('number')
            ->orderBy('category')
            ->paginate(TablePagination::perPage(), ['*'], 'items')
            ->withQueryString()
            ->through(fn (Equipment $item) => (object) [
                'id' => $item->id,
                'number' => $item->number,
                'category' => $item->category,
                'specification' => $item->specification ?: $item->type,
                'area' => $item->area,
                'is_active' => $item->is_active,
                'update_url' => route('admin.robots.equipments.update', $item),
                'destroy_url' => route('admin.robots.equipments.destroy', $item),
            ]);
    }
}
