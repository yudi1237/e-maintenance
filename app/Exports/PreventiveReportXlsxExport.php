<?php

namespace App\Exports;

use App\Models\PreventiveReport;
use App\Support\PreventiveCategory;
use App\Support\XlsxExporter;

class PreventiveReportXlsxExport
{
    public function buildExporter(PreventiveReport $report): XlsxExporter
    {
        $exporter = new XlsxExporter;
        $exporter->setSheetName($this->sheetName($report));
        $readings = $report->readings ?? [];
        $answers = $report->items ?? [];
        $remarks = $report->item_remarks ?? [];

        $exporter->addRow(['Mesin', $report->machine_number]);
        $exporter->addRow(['Nama Mesin', $report->machine_name_display]);
        $exporter->addRow(['Kategori Preventive', $report->schedule_group_label]);
        $exporter->addRow(['Tipe Preventive', $report->category_label]);
        $exporter->addRow(['Tanggal', $report->work_date?->format('d/m/Y')]);
        $exporter->addRow(['Shift', $report->work_shift_label]);
        $exporter->addRow(['Anggota Tim', $report->team_members_label]);
        $exporter->addRow(['PIC', $report->pic_name]);
        $exporter->addRow(['Hasil', $report->result_label]);
        $exporter->addRow(['Status Approval', $report->status_label]);
        if (PreventiveCategory::isUtility($report->category)) {
            $exporter->addRow([PreventiveCategory::numberLabel($report->category), $report->equipment_label]);
        }
        $exporter->addRow(['Catatan', $report->notes]);
        $exporter->addRow(['Evidence', implode("\n", $report->evidence_urls)]);
        $exporter->addRow([]);
        $exporter->addRow(['Grup', 'No', 'Item Pemeriksaan', 'Standar', 'Isi Manual', 'Hasil', 'Remarks']);

        foreach ($report->checklistGroups() as $group) {
            $groupLabel = (string) ($group['label'] ?? '');
            $groupAnswers = $group['answers'] ?? [];
            $no = 1;

            foreach ($group['items'] as $key => $item) {
                $label = is_array($item) ? (string) ($item['label'] ?? $key) : (string) $item;
                $standard = is_array($item) ? (string) ($item['standard'] ?? '') : '';
                $suffix = is_array($item) ? (string) ($item['input']['suffix'] ?? '') : '';
                $hasInput = is_array($item) && ! empty($item['input']);
                $reading = $readings[$key] ?? null;
                $manual = '';
                if ($hasInput || (($group['layout'] ?? '') === 'voltage')) {
                    $manual = filled($reading) ? (string) $reading : '';
                    if ($manual !== '' && $suffix !== '') {
                        $manual .= ' '.$suffix;
                    } elseif ($manual !== '' && ($group['layout'] ?? '') === 'voltage') {
                        $manual .= str_contains((string) $key, 'power_supply') ? ' VDC' : ' VAC';
                    }
                }

                $result = '-';
                if ($groupAnswers !== []) {
                    $value = $answers[$key] ?? null;
                    $result = $groupAnswers[$value] ?? '-';
                }

                $exporter->addRow([
                    $groupLabel,
                    $no,
                    $label,
                    $standard !== '' ? $standard : '—',
                    $manual !== '' ? $manual : '—',
                    $result,
                    filled($remarks[$key] ?? null) ? (string) $remarks[$key] : '—',
                ]);
                $no++;
            }
        }

        return $exporter;
    }

    public function filename(PreventiveReport $report): string
    {
        $date = $report->work_date?->format('Ymd') ?: now()->timezone('Asia/Jakarta')->format('Ymd');
        $type = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $report->category) ?: 'PM';

        return 'preventive-mesin-'.$report->machine_number.'-'.$type.'-'.$date.'.xlsx';
    }

    private function sheetName(PreventiveReport $report): string
    {
        return mb_substr('PM Mesin '.$report->machine_number.' '.$report->category_label, 0, 31);
    }
}
