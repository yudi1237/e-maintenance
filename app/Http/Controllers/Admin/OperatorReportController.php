<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperatorReport;
use App\Support\RepairRecord;
use App\Support\RepairStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OperatorReportController extends Controller
{
    public function show(OperatorReport $operatorReport): View
    {
        $operatorReport->load(['machineName', 'claimedBy', 'claimedTechnician', 'maintenanceReport.machineName', 'maintenanceReport.damageCategory']);

        return view('admin.operator-report-show', [
            'ticket' => $operatorReport,
            'report' => $operatorReport->maintenanceReport,
            'statusLabels' => RepairStatus::labels(),
        ]);
    }

    public function destroy(OperatorReport $operatorReport): RedirectResponse
    {
        RepairRecord::deleteTicket($operatorReport);

        return back()->with('success', 'Tiket OP #'.$operatorReport->id.' berhasil dihapus.');
    }
}
