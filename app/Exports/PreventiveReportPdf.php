<?php

namespace App\Exports;

use App\Models\PreventiveReport;
use App\Support\PreventiveCategory;
use App\Support\PreventiveEvidence;
use App\Support\PreventiveStatus;
use Dompdf\Dompdf;
use Dompdf\Options;

class PreventiveReportPdf
{
    public function output(PreventiveReport $report): string
    {
        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('pdf.preventive-report', [
            'report' => $report,
            'rows' => $this->rows($report),
            'signatures' => $this->signatures($report),
            'equipment' => $this->equipmentLine($report),
            'documentNo' => PreventiveCategory::documentNumber($report->category),
            'logo' => $this->logoDataUri(),
            'evidences' => PreventiveEvidence::pdfDataUris($report->evidence_paths),
        ])->render(), 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    public function logoDataUri(): ?string
    {
        $path = public_path('images/logo-dpp-letterhead.png');
        if (! is_file($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        return $contents === false
            ? null
            : 'data:image/png;base64,'.base64_encode($contents);
    }

    public function filename(PreventiveReport $report): string
    {
        $date = $report->work_date?->format('Ymd') ?: now()->timezone('Asia/Jakarta')->format('Ymd');
        $type = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $report->category) ?: 'PM';

        return 'preventive-mesin-'.$report->machine_number.'-'.$type.'-'.$date.'.pdf';
    }

    /**
     * @return list<array{group: string, no: int, label: string, standard: string, manual: string, result: string, remark: string}>
     */
    public function rows(PreventiveReport $report): array
    {
        $readings = $report->readings ?? [];
        $answers = $report->items ?? [];
        $remarks = $report->item_remarks ?? [];
        $rows = [];

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

                $result = '—';
                if ($groupAnswers !== []) {
                    $value = $answers[$key] ?? null;
                    $result = $groupAnswers[$value] ?? '—';
                }

                $rows[] = [
                    'group' => $groupLabel,
                    'no' => $no,
                    'label' => $label,
                    'standard' => $standard !== '' ? $standard : '—',
                    'manual' => $manual !== '' ? $manual : '—',
                    'result' => $result,
                    'remark' => filled($remarks[$key] ?? null) ? (string) $remarks[$key] : '—',
                ];
                $no++;
            }
        }

        return $rows;
    }

    /**
     * @return list<array{title: string, role: string, name: string, signed: bool, date: string}>
     */
    public function signatures(PreventiveReport $report): array
    {
        $report->loadMissing(['injectionApprover', 'qualityApprover', 'maintenanceApprover']);
        $status = $report->status ?: PreventiveStatus::CLOSE_BY_TECHNICIAN;
        $injectionSigned = in_array($status, [
            PreventiveStatus::APPROVE_PRODUKSI_INJECTION,
            PreventiveStatus::APPROVE_QUALITY,
            PreventiveStatus::CLOSE,
        ], true);
        $qualitySigned = in_array($status, [
            PreventiveStatus::APPROVE_QUALITY,
            PreventiveStatus::CLOSE,
        ], true);
        $maintenanceSigned = $status === PreventiveStatus::CLOSE;
        $approvalDate = $report->updated_at?->timezone('Asia/Jakarta')->format('d.m.Y') ?? '';

        return [
            [
                'title' => 'Dibuat oleh',
                'role' => 'TEKNISI',
                'name' => (string) $report->pic_name,
                'signed' => true,
                'date' => $report->work_date?->format('d.m.Y') ?? '',
            ],
            [
                'title' => 'Diketahui oleh',
                'role' => 'PRODUKSI INJECTION',
                'name' => (string) ($report->injectionApprover?->name ?? ''),
                'signed' => $injectionSigned,
                'date' => $injectionSigned ? $approvalDate : '',
            ],
            [
                'title' => 'Diperiksa oleh',
                'role' => 'QUALITY',
                'name' => (string) ($report->qualityApprover?->name ?? ''),
                'signed' => $qualitySigned,
                'date' => $qualitySigned ? $approvalDate : '',
            ],
            [
                'title' => 'Disetujui oleh',
                'role' => 'MAINTENANCE',
                'name' => (string) ($report->maintenanceApprover?->name ?? ''),
                'signed' => $maintenanceSigned,
                'date' => $maintenanceSigned ? $approvalDate : '',
            ],
        ];
    }

    public function equipmentLine(PreventiveReport $report): ?array
    {
        if (! PreventiveCategory::isUtility($report->category)) {
            return null;
        }

        return [
            PreventiveCategory::numberLabel($report->category),
            $report->equipment_label,
        ];
    }
}
