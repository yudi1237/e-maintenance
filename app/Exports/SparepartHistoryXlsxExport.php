<?php

namespace App\Exports;

use App\Support\XlsxExporter;
use Illuminate\Support\Collection;

class SparepartHistoryXlsxExport
{
    /**
     * @param  Collection<int, array{tanggal: string, sparepart: string, qty: int, machine_number: int, pic_name: string}>  $rows
     */
    public function buildExporter(Collection $rows): XlsxExporter
    {
        $exporter = new XlsxExporter;
        $exporter->setSheetName('History Spare Part');
        $exporter->addRow([
            'No',
            'Tanggal',
            'Sparepart',
            'Qty',
            'Mesin No',
            'PIC Teknisi',
        ]);

        $no = 1;
        foreach ($rows as $row) {
            $exporter->addRow([
                $no,
                $row['tanggal'],
                $row['sparepart'],
                $row['qty'],
                $row['machine_number'],
                $row['pic_name'],
            ]);
            $no++;
        }

        return $exporter;
    }

    public function filename(): string
    {
        return 'history-spare-part-'.now()->timezone('Asia/Jakarta')->format('Ymd').'.xlsx';
    }
}
