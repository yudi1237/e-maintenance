<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateReportStatusRequest;
use App\Models\MaintenanceReport;
use App\Models\OperatorReport;
use App\Support\RepairRecord;
use App\Support\RepairStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MaintenanceReportController extends Controller
{
    public function show(MaintenanceReport $report): View
    {
        $report->load(['user', 'machineName', 'damageCategory', 'operatorReport']);

        return view('admin.report-show', [
            'report' => $report,
            'statusLabels' => RepairStatus::labels(),
        ]);
    }

    public function updateStatus(UpdateReportStatusRequest $request, MaintenanceReport $report): RedirectResponse
    {
        $validated = $request->validated();

        $payload = [
            'status_perbaikan' => $validated['status_perbaikan'],
            'tindakan_perbaikan' => $validated['tindakan_perbaikan'] ?? $report->tindakan_perbaikan,
        ];

        if (array_key_exists('damage_cause_category', $validated) && filled($validated['damage_cause_category'])) {
            $payload['damage_cause_category'] = $validated['damage_cause_category'];
        }

        $report->update($payload);

        if ($report->operator_report_id) {
            OperatorReport::query()->whereKey($report->operator_report_id)->update([
                'status' => $validated['status_perbaikan'],
            ]);
        }

        return back()->with('success', 'Status laporan Mesin '.$report->machine_number.' diperbarui menjadi '.$report->status_perbaikan_label.'.');
    }

    public function destroy(MaintenanceReport $report): RedirectResponse
    {
        $machineNumber = $report->machine_number;
        RepairRecord::deleteReport($report);

        return back()->with('success', 'Riwayat problem Mesin '.$machineNumber.' berhasil dihapus.');
    }
}
