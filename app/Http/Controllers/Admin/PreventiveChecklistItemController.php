<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PreventiveChecklistXlsxExport;
use App\Http\Controllers\Controller;
use App\Models\PreventiveChecklistItem;
use App\Support\PreventiveCategory;
use App\Support\PreventiveConveyorChecklist;
use App\Support\PreventiveMachineChecklist;
use App\Support\PreventivePm1Checklist;
use Database\Seeders\PreventiveChecklistSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PreventiveChecklistItemController extends Controller
{
    public function index(Request $request): View
    {
        $category = $this->selectedCategory($request);
        $this->ensureCategoryImported($category);

        return view('admin.preventive-checklist.index', [
            'categories' => $this->categoryOptions(),
            'category' => $category,
            'groupKeys' => PreventiveChecklistItem::query()
                ->where('category', $category)
                ->orderBy('group_key')
                ->distinct()
                ->pluck('group_key'),
            'items' => PreventiveChecklistItem::query()
                ->where('category', $category)
                ->orderBy('group_sort_order')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'itemTypes' => $this->itemTypes(),
        ]);
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $category = $this->selectedCategory($request);
        $this->ensureCategoryImported($category);

        $items = PreventiveChecklistItem::query()
            ->where('category', $category)
            ->orderBy('group_sort_order')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            return redirect()
                ->route('admin.preventive-checklist.index', ['category' => $category])
                ->withErrors(['export' => 'Belum ada check sheet untuk diunduh.']);
        }

        $export = new PreventiveChecklistXlsxExport($category);
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
        $maxGroup = (int) PreventiveChecklistItem::query()
            ->where('category', $validated['category'])
            ->max('group_sort_order');
        $maxSort = (int) PreventiveChecklistItem::query()
            ->where('category', $validated['category'])
            ->where('group_key', $validated['group_key'])
            ->max('sort_order');

        PreventiveChecklistItem::query()->create([
            ...$validated,
            'group_sort_order' => $this->groupSortOrder($validated['category'], $validated['group_key'], $maxGroup),
            'sort_order' => $maxSort + 1,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.preventive-checklist.index', ['category' => $validated['category']])
            ->with('success', 'Item check sheet berhasil ditambahkan.');
    }

    public function update(Request $request, PreventiveChecklistItem $preventiveChecklistItem): RedirectResponse
    {
        $validated = $this->validatedPayload($request, $preventiveChecklistItem);

        $preventiveChecklistItem->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.preventive-checklist.index', ['category' => $preventiveChecklistItem->category])
            ->with('success', 'Item check sheet berhasil diperbarui.');
    }

    public function destroy(PreventiveChecklistItem $preventiveChecklistItem): RedirectResponse
    {
        $category = $preventiveChecklistItem->category;
        $preventiveChecklistItem->delete();

        return redirect()
            ->route('admin.preventive-checklist.index', ['category' => $category])
            ->with('success', 'Item check sheet berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedPayload(Request $request, ?PreventiveChecklistItem $item = null): array
    {
        $uniqueKey = Rule::unique('preventive_checklist_items', 'item_key')->where(
            fn ($query) => $query->where('category', $request->input('category'))
        );
        if ($item) {
            $uniqueKey->ignore($item->id);
        }

        $validated = $request->validate([
            'category' => ['required', 'string', Rule::in(array_keys($this->categoryOptions()))],
            'group_key' => ['required', 'string', 'max:80'],
            'group_label' => ['required', 'string', 'max:150'],
            'item_type' => ['required', 'string', Rule::in(array_keys($this->itemTypes()))],
            'layout' => ['nullable', 'string', 'max:30'],
            'item_key' => ['nullable', 'string', 'max:80', 'regex:/^[a-z0-9_]+$/', $uniqueKey],
            'label' => ['required', 'string', 'max:255'],
            'standard' => ['nullable', 'string', 'max:255'],
            'has_input' => ['sometimes', 'boolean'],
            'input_placeholder' => ['nullable', 'string', 'max:100'],
            'input_suffix' => ['nullable', 'string', 'max:30'],
        ], [
            'item_key.regex' => 'Kode item hanya boleh huruf kecil, angka, dan underscore.',
            'item_key.unique' => 'Kode item sudah dipakai di kategori ini.',
        ]);

        $validated['group_key'] = Str::slug($validated['group_key'], '_');
        $validated['item_key'] = ($validated['item_key'] ?? '')
            ?: Str::slug($validated['label'], '_');
        $validated['has_input'] = $request->boolean('has_input');
        $validated['layout'] = ($validated['layout'] ?? null) ?: null;
        $validated['standard'] = $validated['standard'] ?? '';
        $validated['input_placeholder'] = $validated['input_placeholder'] ?? null;
        $validated['input_suffix'] = $validated['input_suffix'] ?? null;

        return $validated;
    }

    private function groupSortOrder(string $category, string $groupKey, int $maxGroup): int
    {
        $existing = PreventiveChecklistItem::query()
            ->where('category', $category)
            ->where('group_key', $groupKey)
            ->value('group_sort_order');

        return $existing !== null ? (int) $existing : $maxGroup + 1;
    }

    private function ensureCategoryImported(string $category): void
    {
        if (PreventiveChecklistItem::query()->where('category', $category)->exists()) {
            return;
        }

        $groups = $category === 'CONVEYOR'
            ? PreventiveConveyorChecklist::builtinGroups()
            : PreventiveMachineChecklist::builtinGroups($category);

        (new PreventiveChecklistSeeder)->importCategory($category, $groups);
    }

    private function selectedCategory(Request $request): string
    {
        $category = (string) $request->query('category', 'PM1');
        $options = $this->categoryOptions();

        return array_key_exists($category, $options) ? $category : 'PM1';
    }

    /** @return array<string, string> */
    private function categoryOptions(): array
    {
        $options = PreventiveCategory::labels();
        $options['CONVEYOR'] = 'Preventive Conveyor';

        return $options;
    }

    /** @return array<string, string> */
    private function itemTypes(): array
    {
        return [
            PreventivePm1Checklist::TYPE_CONDITION => 'Kondisi (Good / No Good)',
            PreventivePm1Checklist::TYPE_ACTION => 'Tindakan (Dilakukan / Belum)',
            PreventivePm1Checklist::TYPE_MEASUREMENT => 'Pengukuran (isian angka)',
        ];
    }
}
