<?php

namespace App\Exports;

use App\Support\EquipmentCategory;
use App\Support\XlsxExporter;
use Illuminate\Support\Collection;

class EquipmentXlsxExport
{
    public function __construct(private readonly string $category) {}

    /**
     * @param  Collection<int, object{number: string, type: string, is_active: bool}>  $items
     */
    public function buildExporter(Collection $items): XlsxExporter
    {
        $exporter = new XlsxExporter;
        $exporter->setSheetName($this->sheetName());
        $label = EquipmentCategory::label($this->category);

        $exporter->addRow(['Kategori', $label]);
        $exporter->addRow([]);
        $exporter->addRow(['No', 'Nomor', 'Type', 'Status']);

        foreach ($items->values() as $index => $item) {
            $exporter->addRow([
                $index + 1,
                $item->number,
                $item->type,
                $item->is_active ? 'Aktif' : 'Nonaktif',
            ]);
        }

        return $exporter;
    }

    public function filename(): string
    {
        $slug = preg_replace('/[^A-Za-z0-9_-]+/', '-', $this->category) ?: 'equipment';

        return 'master-data-equipment-'.$slug.'-'.now()->timezone('Asia/Jakarta')->format('Ymd-His').'.xlsx';
    }

    private function sheetName(): string
    {
        return mb_substr('Master Data '.EquipmentCategory::label($this->category), 0, 31);
    }
}
