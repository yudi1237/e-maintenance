<?php

namespace App\Exports;

use App\Models\PreventiveChecklistItem;
use App\Support\PreventiveCategory;
use App\Support\PreventivePm1Checklist;
use App\Support\XlsxExporter;
use Illuminate\Support\Collection;

class PreventiveChecklistXlsxExport
{
    public function __construct(private readonly string $category) {}

    /**
     * @param  Collection<int, PreventiveChecklistItem>  $items
     */
    public function buildExporter(Collection $items): XlsxExporter
    {
        $exporter = new XlsxExporter;
        $exporter->setSheetName($this->sheetName());
        $typeLabels = $this->itemTypes();

        $exporter->addRow([
            'No',
            'Kode grup',
            'Nama grup',
            'Nama item',
            'Standar',
            'Tipe jawaban',
            'Kode item',
            'Layout',
            'Ada isian angka',
            'Placeholder isian',
            'Satuan',
            'Status',
        ]);

        foreach ($items->values() as $index => $item) {
            $exporter->addRow([
                $index + 1,
                $item->group_key,
                $item->group_label,
                $item->label,
                $item->standard,
                $typeLabels[$item->item_type] ?? $item->item_type,
                $item->item_key,
                $item->layout,
                $item->has_input ? 'Ya' : 'Tidak',
                $item->input_placeholder,
                $item->input_suffix,
                $item->is_active ? 'Aktif' : 'Nonaktif',
            ]);
        }

        return $exporter;
    }

    public function filename(): string
    {
        $slug = preg_replace('/[^A-Za-z0-9_-]+/', '-', $this->category) ?: 'check-sheet';

        return 'check-sheet-'.$slug.'-'.now()->timezone('Asia/Jakarta')->format('Ymd-His').'.xlsx';
    }

    private function sheetName(): string
    {
        $label = $this->category === 'CONVEYOR'
            ? 'Preventive Conveyor'
            : PreventiveCategory::label($this->category);

        return 'Check Sheet '.$label;
    }

    /**
     * @return array<string, string>
     */
    private function itemTypes(): array
    {
        return [
            PreventivePm1Checklist::TYPE_CONDITION => 'Kondisi (Good / No Good)',
            PreventivePm1Checklist::TYPE_ACTION => 'Tindakan (Dilakukan / Belum)',
            PreventivePm1Checklist::TYPE_MEASUREMENT => 'Pengukuran (isian angka)',
        ];
    }
}
