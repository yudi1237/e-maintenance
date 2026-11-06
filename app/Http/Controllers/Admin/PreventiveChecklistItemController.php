<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PreventiveChecklistXlsxExport;
use App\Http\Controllers\Controller;
use App\Models\PreventiveCategoryRecord;
use App\Models\PreventiveChecklistItem;
use App\Support\PreventiveCategory;
use App\Support\PreventiveConveyorChecklist;
use App\Support\PreventiveMachineChecklist;
use App\Support\PreventivePm1Checklist;
use Database\Seeders\PreventiveChecklistSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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

    public function storeCategory(Request $request): RedirectResponse
    {
        PreventiveCategoryRecord::ensureBuiltin();

        $label = trim((string) $request->validate([
            'label' => ['required', 'string', 'max:150'],
        ])['label']);

        if ($this->labelExists($label)) {
            throw ValidationException::withMessages([
                'label' => 'Kategori preventive sudah ada.',
            ]);
        }

        $code = $this->codeFromLabel($label);
        PreventiveCategoryRecord::query()->create([
            'code' => $code,
            'label' => $label,
            'sort_order' => (int) PreventiveCategoryRecord::query()->max('sort_order') + 1,
        ]);

        return redirect()
            ->route('admin.preventive-checklist.index', ['category' => $code])
            ->with('success', 'Kategori preventive berhasil ditambahkan.');
    }

    public function updateCategory(Request $request): RedirectResponse
    {
        PreventiveCategoryRecord::ensureBuiltin();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30'],
            'edit_label' => ['required', 'string', 'max:150'],
        ]);
        $label = trim($validated['edit_label']);
        $record = PreventiveCategoryRecord::query()->where('code', $validated['code'])->firstOrFail();

        if ($this->labelExists($label, $record->id)) {
            throw ValidationException::withMessages([
                'edit_label' => 'Nama kategori preventive sudah dipakai.',
            ]);
        }

        $record->update(['label' => $label]);

        return redirect()
            ->route('admin.preventive-checklist.index', ['category' => $record->code])
            ->with('success', 'Nama kategori preventive berhasil diubah.');
    }

    public function destroyCategory(Request $request): RedirectResponse
    {
        $code = (string) $request->validate([
            'code' => ['required', 'string', 'max:30'],
        ])['code'];
        $record = PreventiveCategoryRecord::query()->where('code', $code)->firstOrFail();
        $label = $record->label;

        DB::transaction(function () use ($record): void {
            PreventiveChecklistItem::query()->where('category', $record->code)->delete();
            $record->delete();
        });

        $next = PreventiveCategoryRecord::query()->orderBy('sort_order')->orderBy('id')->value('code');

        return redirect()
            ->route('admin.preventive-checklist.index', $next ? ['category' => $next] : [])
            ->with('success', 'Kategori preventive '.$label.' berhasil dihapus.');
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
        if ($category === '' || PreventiveChecklistItem::query()->where('category', $category)->exists()) {
            return;
        }

        if (! in_array($category, PreventiveCategory::values(), true) && $category !== 'CONVEYOR') {
            return;
        }

        $groups = $category === 'CONVEYOR'
            ? PreventiveConveyorChecklist::builtinGroups()
            : PreventiveMachineChecklist::builtinGroups($category);

        (new PreventiveChecklistSeeder)->importCategory($category, $groups);
    }

    private function selectedCategory(Request $request): string
    {
        $options = $this->categoryOptions();
        if ($options === []) {
            return '';
        }

        $category = (string) $request->query('category', '');
        if ($category !== '' && array_key_exists($category, $options)) {
            return $category;
        }

        return (string) array_key_first($options);
    }

    /** @return array<string, string> */
    private function categoryOptions(): array
    {
        PreventiveCategoryRecord::ensureBuiltin();

        return PreventiveCategoryRecord::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('label', 'code')
            ->all();
    }

    private function labelExists(string $label, ?int $ignoreId = null): bool
    {
        return PreventiveCategoryRecord::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->whereRaw('lower(label) = ?', [mb_strtolower($label)])
            ->exists();
    }

    private function codeFromLabel(string $label): string
    {
        $code = strtoupper(Str::slug($label, '_'));
        $code = trim(substr($code, 0, 30), '_');
        if ($code === '') {
            throw ValidationException::withMessages([
                'label' => 'Nama kategori preventive tidak valid.',
            ]);
        }

        $base = $code;
        $suffix = 2;
        while (PreventiveCategoryRecord::query()->where('code', $code)->exists()) {
            $tail = '_'.$suffix;
            $code = substr($base, 0, 30 - strlen($tail)).$tail;
            $suffix++;
        }

        return $code;
    }

    /** @return array<string, string> */
    private function itemTypes(): array
    {
        return [
            PreventivePm1Checklist::TYPE_CONDITION => 'Kondisi (Good / No Good / N/A)',
            PreventivePm1Checklist::TYPE_ACTION => 'Tindakan (Dilakukan / Belum)',
            PreventivePm1Checklist::TYPE_MEASUREMENT => 'Pengukuran (isian angka)',
        ];
    }
}
