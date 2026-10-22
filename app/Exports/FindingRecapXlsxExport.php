<?php

namespace App\Exports;

use App\Models\FindingRecap;
use App\Support\XlsxExporter;
use Illuminate\Support\Collection;

class FindingRecapXlsxExport
{
    /**
     * @param  Collection<int, FindingRecap>  $recaps
     */
    public function buildExporter(Collection $recaps): XlsxExporter
    {
        $exporter = new XlsxExporter;
        $exporter->setSheetName('Log Temuan');

        $exporter->addRow([
            'Tanggal',
            'Due Date',
            'No Mesin',
            'Nama Mesin',
            'Nama Equipment',
            'Status',
            'Catatan',
        ]);

        foreach ($recaps as $recap) {
            $exporter->addRow([
                $recap->found_date?->format('d/m/Y'),
                $recap->due_date?->format('d/m/Y'),
                $recap->machine_number,
                $recap->machine_name_display,
                $recap->equipment_name,
                $recap->status_label,
                $recap->notesExportText(),
            ]);
        }

        return $exporter;
    }

    public function filename(): string
    {
        return 'log-temuan-'.now()->timezone('Asia/Jakarta')->format('Ymd-His').'.xlsx';
    }
}
