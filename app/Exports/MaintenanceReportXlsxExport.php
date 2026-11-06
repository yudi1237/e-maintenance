<?php

namespace App\Exports;

use App\Models\MaintenanceReport;
use App\Support\XlsxExporter;
use Illuminate\Support\Collection;

class MaintenanceReportXlsxExport
{
    /**
     * @param  Collection<int, MaintenanceReport>  $reports
     */
    public function buildExporter(Collection $reports): XlsxExporter
    {
        $exporter = new XlsxExporter;
        $exporter->setSheetName('Laporan Maintenance');

        $exporter->addRow([
            'Tanggal',
            'Nomor Mesin',
            'Nama Mesin (Master)',
            'Nama Mesin (Manual)',
            'Nama Mesin (Lengkap)',
            'Kategori Kerusakan',
            'Mold/Assy/Utility',
            'PIC',
            'Shift',
            'Mulai Perbaikan',
            'Selesai Perbaikan',
            'Masalah',
            'Analisa Penyebab',
            'Kategori Penyebab Kerusakan',
            'Line Stop Mesin',
            'Sub Kerusakan',
            'Tindakan Perbaikan',
            'Penggunaan Sparepart',
            'Yang Mengerjakan',
            'Ada OP',
            'Status Perbaikan',
            'Evidence',
            'Dibuat',
        ]);

        foreach ($reports as $report) {
            $exporter->addRow($this->rowFromReport($report));
        }

        return $exporter;
    }

    public function filename(string $startDate, string $endDate): string
    {
        return 'laporan-maintenance-'.$startDate.'-'.$endDate.'.xlsx';
    }

    /**
     * @return list<scalar|null>
     */
    private function rowFromReport(MaintenanceReport $report): array
    {
        return [
            $report->report_date->format('d/m/Y'),
            $report->machine_number,
            $report->exportMachineNameMaster(),
            $report->exportMachineNameManual(),
            $report->exportMachineName(),
            $report->damage_category_display,
            $report->mold_assy_utility ?? '',
            $report->pic_name,
            $report->work_shift_label,
            $report->repair_started_at->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            $report->repair_finished_at->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            $report->masalah ?? '',
            $report->analisa_penyebab_kerusakan ?? '',
            $report->damage_cause_category_label === '—' ? '' : $report->damage_cause_category_label,
            $report->line_stop_mesin_label,
            $report->sub_damage_category_label,
            $report->tindakan_perbaikan ?? '',
            $report->penggunaan_sparepart ?? '',
            $report->workers_label,
            $report->isStep2Completed() ? $report->ada_op_label : '',
            $report->status_perbaikan_label,
            $report->evidence_url ?? '',
            $report->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
        ];
    }
}
