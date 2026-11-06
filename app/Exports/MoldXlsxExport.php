<?php

namespace App\Exports;

use App\Models\Mold;
use App\Support\XlsxExporter;
use Illuminate\Support\Collection;

class MoldXlsxExport
{
    /**
     * @param  Collection<int, Mold>  $molds
     */
    public function buildExporter(Collection $molds): XlsxExporter
    {
        $exporter = new XlsxExporter;
        $exporter->setSheetName('Master Data Mold');
        $exporter->addRow(['No', 'Nama Mold', 'Customer', 'HxVxT (mm)', 'Status']);

        foreach ($molds->values() as $index => $mold) {
            $exporter->addRow([
                $index + 1,
                $mold->name,
                $mold->customer,
                $mold->dimension,
                $mold->is_active ? 'Aktif' : 'Nonaktif',
            ]);
        }

        return $exporter;
    }

    public function filename(): string
    {
        return 'master-data-mold-'.now()->timezone('Asia/Jakarta')->format('Ymd-His').'.xlsx';
    }
}
