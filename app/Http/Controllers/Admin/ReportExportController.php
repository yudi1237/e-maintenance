<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MaintenanceReportXlsxExport;
use App\Http\Controllers\Controller;
use App\Models\MaintenanceReport;
use App\Support\InjectionMachine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        abort_unless($request->user()?->seesFullDashboard(), 403);

        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'machine_number' => ['nullable', 'integer', 'min:1', 'max:'.InjectionMachine::count()],
        ]);

        $query = MaintenanceReport::query()
            ->with(['user', 'machineName', 'damageCategory'])
            ->whereDate('report_date', '>=', $validated['start_date'])
            ->whereDate('report_date', '<=', $validated['end_date'])
            ->orderBy('report_date')
            ->orderBy('machine_number');

        if (! empty($validated['machine_number'])) {
            $query->where('machine_number', $validated['machine_number']);
        }

        $count = (clone $query)->count();

        if ($count === 0) {
            return redirect()
                ->route('admin.dashboard')
                ->withErrors([
                    'export' => 'Tidak ada laporan pada periode '.$validated['start_date'].' s/d '.$validated['end_date'].'. Perluas rentang tanggal.',
                ])
                ->withInput();
        }

        $reports = $query->get();
        $export = new MaintenanceReportXlsxExport;
        $filename = $export->filename($validated['start_date'], $validated['end_date']);

        return response()->streamDownload(function () use ($export, $reports) {
            $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');

            if ($tmp === false) {
                throw new \RuntimeException('Tidak dapat membuat file sementara untuk export Excel.');
            }

            $path = $tmp.'.xlsx';

            if (! rename($tmp, $path)) {
                @unlink($tmp);
                throw new \RuntimeException('Tidak dapat menyiapkan file export Excel.');
            }

            try {
                $export->buildExporter($reports)->writeToFile($path);
                readfile($path);
            } finally {
                @unlink($path);
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
