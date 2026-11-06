<?php

namespace App\Exports;

use App\Support\XlsxExporter;
use Illuminate\Support\Collection;

class EquipmentXlsxExport
{
    /**
     * @param  Collection<int, object{number: string, type_label: string, specification: string, area_label: string, is_active: bool}>  $items
     */
    public function buildExporter(Collection $items): XlsxExporter
    {
        $exporter = new XlsxExporter;
        $exporter->setSheetName('Master Data Equipment');

        $exporter->addRow(['No', 'Nomor Equipment', 'Tipe Equipment', 'Spesifikasi', 'Area', 'Status']);

        foreach ($items->values() as $index => $item) {
            $exporter->addRow([
                $index + 1,
                $item->number,
                $item->type_label,
                $item->specification,
                $item->area_label,
                $item->is_active ? 'Aktif' : 'Nonaktif',
            ]);
        }

        return $exporter;
    }

    public function filename(): string
    {
        return 'master-data-equipment-'.now()->timezone('Asia/Jakarta')->format('Ymd-His').'.xlsx';
    }
}
