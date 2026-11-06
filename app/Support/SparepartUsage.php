<?php

namespace App\Support;

use App\Models\MaintenanceReport;
use Illuminate\Foundation\Http\FormRequest;

class SparepartUsage
{
    public static function prepare(FormRequest $request): void
    {
        if ($request->input('penggunaan_sparepart_choice') !== 'sparepart') {
            $request->merge(['sparepart_items' => []]);

            return;
        }

        $items = collect($request->input('sparepart_items', []))
            ->map(function ($row) {
                $qty = is_array($row) ? ($row['qty'] ?? null) : null;

                return [
                    'name' => trim((string) (is_array($row) ? ($row['name'] ?? '') : '')),
                    'qty' => $qty === '' || $qty === null ? null : $qty,
                ];
            })
            ->filter(fn (array $row) => $row['name'] !== '' || $row['qty'] !== null)
            ->values()
            ->all();

        $request->merge(['sparepart_items' => $items]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(FormRequest $request): array
    {
        $sparepartChosen = $request->input('penggunaan_sparepart_choice') === 'sparepart';

        return [
            'penggunaan_sparepart_choice' => ['required', 'in:sparepart,none'],
            'sparepart_items' => [$sparepartChosen ? 'required' : 'nullable', 'array', $sparepartChosen ? 'min:1' : 'nullable'],
            'sparepart_items.*.name' => [$sparepartChosen ? 'required' : 'nullable', 'string', 'max:255'],
            'sparepart_items.*.qty' => [$sparepartChosen ? 'required' : 'nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'penggunaan_sparepart_choice.required' => 'Pilih pemakaian sparepart.',
            'penggunaan_sparepart_choice.in' => 'Pilih Sparepart atau Tidak ada.',
            'sparepart_items.required' => 'Isi minimal satu nama sparepart dan qty.',
            'sparepart_items.min' => 'Isi minimal satu nama sparepart dan qty.',
            'sparepart_items.*.name.required' => 'Nama sparepart wajib diisi.',
            'sparepart_items.*.qty.required' => 'Qty sparepart wajib diisi.',
            'sparepart_items.*.qty.integer' => 'Qty sparepart harus angka.',
            'sparepart_items.*.qty.min' => 'Qty sparepart minimal 1.',
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{text: string, items: ?array<int, array{name: string, qty: int}>}
     */
    public static function persisted(array $validated): array
    {
        return MaintenanceReport::persistSparepart(
            (string) ($validated['penggunaan_sparepart_choice'] ?? 'none'),
            $validated['sparepart_items'] ?? []
        );
    }
}
