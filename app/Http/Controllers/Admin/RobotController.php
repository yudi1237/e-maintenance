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
    public function index(Request $request): View
    {
        $category = $this->resolvedCategory($request);

        return view('admin.robots.index', [
            'category' => $category,
            'categories' => EquipmentCategory::labels(),
            'formTitle' => EquipmentCategory::formTitle($category),
            'items' => $this->items($category),
        ]);
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $category = $this->resolvedCategory($request);
        $items = $this->exportRows($category);

        if ($items->isEmpty()) {
            return redirect()
                ->route('admin.robots.index', $this->indexQuery($category))
                ->withErrors(['export' => 'Belum ada master data '.EquipmentCategory::label($category).' untuk diunduh.']);
        }

        $export = new EquipmentXlsxExport($category);
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
        $category = $this->resolvedCategory($request);
        $validated = $this->validatedPayload($request, $category);

        if (EquipmentCategory::isRobot($category)) {
            Robot::query()->create([
                'robot_number' => $validated['number'],
                'robot_type' => $validated['type'],
                'is_active' => true,
            ]);
        } else {
            Equipment::query()->create([
                'category' => $category,
                'number' => $validated['number'],
                'type' => $validated['type'],
                'is_active' => true,
            ]);
        }

        return redirect()
            ->route('admin.robots.index', $this->indexQuery($category))
            ->with('success', EquipmentCategory::formTitle($category).' berhasil ditambahkan.');
    }

    public function update(Request $request, Robot $robot): RedirectResponse
    {
        $validated = $this->validatedPayload($request, EquipmentCategory::ROBOT, $robot);

        $robot->update([
            'robot_number' => $validated['number'],
            'robot_type' => $validated['type'],
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
        $validated = $this->validatedPayload($request, $equipment->category, null, $equipment);

        $equipment->update([
            'number' => $validated['number'],
            'type' => $validated['type'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.robots.index', ['category' => $equipment->category])
            ->with('success', EquipmentCategory::formTitle($equipment->category).' berhasil diperbarui.');
    }

    public function destroyEquipment(Equipment $equipment): RedirectResponse
    {
        $category = $equipment->category;
        $equipment->delete();

        return redirect()
            ->route('admin.robots.index', ['category' => $category])
            ->with('success', EquipmentCategory::formTitle($category).' berhasil dihapus.');
    }

    /**
     * @return array{number: string, type: string}
     */
    private function validatedPayload(Request $request, string $category, ?Robot $robot = null, ?Equipment $equipment = null): array
    {
        $number = $request->input('number', $request->input('robot_number'));
        $type = $request->input('type', $request->input('robot_type'));
        $request->merge([
            'number' => $number,
            'type' => $type,
        ]);

        $uniqueNumber = EquipmentCategory::isRobot($category)
            ? Rule::unique('robots', 'robot_number')
            : Rule::unique('equipments', 'number')->where('category', $category);

        if ($robot) {
            $uniqueNumber->ignore($robot->id);
        }

        if ($equipment) {
            $uniqueNumber->ignore($equipment->id);
        }

        $validated = $request->validate([
            'number' => ['required', 'string', 'max:50', $uniqueNumber],
            'type' => ['required', 'string', 'max:150'],
            'is_active' => ['sometimes', 'boolean'],
        ], [
            'number.required' => 'Nomor wajib diisi.',
            'number.unique' => 'Nomor sudah terdaftar.',
            'type.required' => 'Type wajib diisi.',
        ]);

        return [
            'number' => $validated['number'],
            'type' => $validated['type'],
        ];
    }

    private function resolvedCategory(Request $request): string
    {
        $category = (string) $request->input('category', EquipmentCategory::ROBOT);

        return in_array($category, EquipmentCategory::values(), true)
            ? $category
            : EquipmentCategory::ROBOT;
    }

    /**
     * @return array<string, string>
     */
    private function indexQuery(string $category): array
    {
        return EquipmentCategory::isRobot($category) ? [] : ['category' => $category];
    }

    /**
     * @return Collection<int, object{number: string, type: string, is_active: bool}>
     */
    private function exportRows(string $category): Collection
    {
        if (EquipmentCategory::isRobot($category)) {
            return Robot::query()
                ->orderBy('robot_number')
                ->get()
                ->map(fn (Robot $robot) => (object) [
                    'number' => $robot->robot_number,
                    'type' => $robot->robot_type,
                    'is_active' => (bool) $robot->is_active,
                ]);
        }

        return Equipment::query()
            ->forCategory($category)
            ->orderBy('number')
            ->get()
            ->map(fn (Equipment $item) => (object) [
                'number' => $item->number,
                'type' => $item->type,
                'is_active' => (bool) $item->is_active,
            ]);
    }

    private function items(string $category)
    {
        if (EquipmentCategory::isRobot($category)) {
            return Robot::query()
                ->orderBy('robot_number')
                ->paginate(TablePagination::perPage(), ['*'], 'items')
                ->withQueryString()
                ->through(fn (Robot $robot) => (object) [
                    'id' => $robot->id,
                    'number' => $robot->robot_number,
                    'type' => $robot->robot_type,
                    'is_active' => $robot->is_active,
                    'update_url' => route('admin.robots.update', $robot),
                    'destroy_url' => route('admin.robots.destroy', $robot),
                ]);
        }

        return Equipment::query()
            ->forCategory($category)
            ->orderBy('number')
            ->paginate(TablePagination::perPage(), ['*'], 'items')
            ->withQueryString()
            ->through(fn (Equipment $item) => (object) [
                'id' => $item->id,
                'number' => $item->number,
                'type' => $item->type,
                'is_active' => $item->is_active,
                'update_url' => route('admin.robots.equipments.update', $item),
                'destroy_url' => route('admin.robots.equipments.destroy', $item),
            ]);
    }
}
