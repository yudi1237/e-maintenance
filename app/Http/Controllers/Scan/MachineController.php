<?php

namespace App\Http\Controllers\Scan;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOperatorReportRequest;
use App\Models\FindingRecap;
use App\Models\MachineName;
use App\Models\MaintenanceReport;
use App\Models\OperatorReport;
use App\Models\PreventiveReport;
use App\Support\FindingRecapStatus;
use App\Support\InjectionMachine;
use App\Support\MachineQrCode;
use App\Support\RepairStatus;
use App\Support\TablePagination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MachineController extends Controller
{
    public function show(int $machineNumber, string $token): View
    {
        $this->assertScanAccess($machineNumber, $token);

        $machine = MachineName::query()
            ->operational()
            ->where('machine_number', $machineNumber)
            ->first();

        $operatorTickets = OperatorReport::query()
            ->with(['machineName', 'claimedTechnician', 'claimedBy'])
            ->where('machine_number', $machineNumber)
            ->whereDoesntHave('maintenanceReport')
            ->orderByDesc('stop_date')
            ->orderByDesc('id')
            ->get();

        $problemReports = MaintenanceReport::query()
            ->with(['machineName', 'damageCategory'])
            ->where('machine_number', $machineNumber)
            ->latest()
            ->paginate(TablePagination::perPage(), ['*'], 'problem_page')
            ->withQueryString();

        $preventiveReports = PreventiveReport::query()
            ->with('machineName')
            ->where('machine_number', $machineNumber)
            ->latest('work_date')
            ->latest('id')
            ->paginate(TablePagination::perPage(), ['*'], 'pm_page')
            ->withQueryString();

        $recaps = FindingRecap::query()
            ->with(['machineName', 'statusNotes.user'])
            ->where('machine_number', $machineNumber)
            ->where('status', FindingRecapStatus::CLOSE)
            ->latest('found_date')
            ->latest('id')
            ->paginate(TablePagination::perPage(), ['*'], 'recap_page')
            ->withQueryString();

        return view('scan.hub', [
            'machineNumber' => $machineNumber,
            'token' => $token,
            'machine' => $machine,
            'operatorTickets' => $operatorTickets,
            'problemReports' => $problemReports,
            'preventiveReports' => $preventiveReports,
            'recaps' => $recaps,
            'statusLabels' => RepairStatus::labels(),
        ]);
    }

    public function preventive(Request $request, int $machineNumber, string $token): View
    {
        $this->assertScanAccess($machineNumber, $token);

        $machine = MachineName::query()
            ->operational()
            ->where('machine_number', $machineNumber)
            ->first();

        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $reportsQuery = PreventiveReport::query()
            ->with('machineName')
            ->where('machine_number', $machineNumber);

        if (! empty($filters['start_date'])) {
            $reportsQuery->whereDate('work_date', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $reportsQuery->whereDate('work_date', '<=', $filters['end_date']);
        }

        $reports = $reportsQuery
            ->latest('work_date')
            ->latest('id')
            ->paginate(TablePagination::perPage())
            ->withQueryString();

        return view('scan.preventive', [
            'machineNumber' => $machineNumber,
            'token' => $token,
            'machine' => $machine,
            'reports' => $reports,
            'reportCount' => PreventiveReport::where('machine_number', $machineNumber)->count(),
            'filteredReportCount' => $reports->total(),
            'startDate' => $filters['start_date'] ?? null,
            'endDate' => $filters['end_date'] ?? null,
        ]);
    }

    public function preventiveShow(int $machineNumber, string $token, PreventiveReport $preventiveReport): View
    {
        $this->assertScanAccess($machineNumber, $token);
        abort_unless((int) $preventiveReport->machine_number === $machineNumber, 404);

        $preventiveReport->load('machineName');

        return view('scan.preventive-show', [
            'machineNumber' => $machineNumber,
            'token' => $token,
            'report' => $preventiveReport,
        ]);
    }

    public function info(Request $request, int $machineNumber, string $token): View
    {
        $this->assertScanAccess($machineNumber, $token);

        $machine = MachineName::query()
            ->operational()
            ->where('machine_number', $machineNumber)
            ->first();

        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $operatorTicketsQuery = OperatorReport::query()
            ->with(['machineName', 'claimedTechnician', 'claimedBy'])
            ->where('machine_number', $machineNumber)
            ->whereDoesntHave('maintenanceReport');

        $reportsQuery = MaintenanceReport::query()
            ->with(['machineName', 'damageCategory'])
            ->where('machine_number', $machineNumber);

        if (! empty($filters['start_date'])) {
            $operatorTicketsQuery->whereDate('stop_date', '>=', $filters['start_date']);
            $reportsQuery->whereDate('report_date', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $operatorTicketsQuery->whereDate('stop_date', '<=', $filters['end_date']);
            $reportsQuery->whereDate('report_date', '<=', $filters['end_date']);
        }

        $operatorTickets = $operatorTicketsQuery
            ->orderByDesc('stop_date')
            ->orderByDesc('id')
            ->get();

        $reports = $reportsQuery
            ->latest()
            ->paginate(TablePagination::perPage())
            ->withQueryString();

        return view('scan.machine', [
            'machineNumber' => $machineNumber,
            'token' => $token,
            'machine' => $machine,
            'operatorTickets' => $operatorTickets,
            'reports' => $reports,
            'reportCount' => MaintenanceReport::where('machine_number', $machineNumber)->count()
                + OperatorReport::query()
                    ->where('machine_number', $machineNumber)
                    ->whereDoesntHave('maintenanceReport')
                    ->count(),
            'filteredReportCount' => $reports->total() + $operatorTickets->count(),
            'startDate' => $filters['start_date'] ?? null,
            'endDate' => $filters['end_date'] ?? null,
            'statusLabels' => RepairStatus::labels(),
        ]);
    }

    public function createOperatorReport(int $machineNumber, string $token): View
    {
        $this->assertScanAccess($machineNumber, $token);

        $scannedMachine = MachineName::query()
            ->operational()
            ->where('machine_number', $machineNumber)
            ->first();

        $now = now()->timezone('Asia/Jakarta');

        return view('scan.input-op', [
            'machineNumber' => $machineNumber,
            'token' => $token,
            'scannedMachine' => $scannedMachine,
            'defaultDate' => $now->format('Y-m-d'),
            'defaultTime' => $now->format('H:i'),
        ]);
    }

    public function storeOperatorReport(StoreOperatorReportRequest $request, int $machineNumber, string $token): RedirectResponse
    {
        $validated = $request->validated();
        $machine = MachineName::query()
            ->operational()
            ->where('machine_number', $machineNumber)
            ->first();

        OperatorReport::create([
            'operator_name' => $validated['operator_name'],
            'npk' => '',
            'item_name' => $validated['item_name'],
            'machine_name_id' => $machine?->id,
            'machine_number' => $machineNumber,
            'stop_date' => $validated['stop_date'],
            'stop_time' => $validated['stop_time'],
            'problem' => $validated['problem'],
            'chronology' => $validated['chronology'] ?? null,
            'status' => RepairStatus::OPEN,
        ]);

        if ($request->user()?->isOperator()) {
            return redirect()
                ->route('operator.home')
                ->with('op_created', true);
        }

        return redirect()
            ->route('scan.machines.show', [$machineNumber, $token])
            ->with('success', 'Input OP berhasil dikirim.');
    }

    public function report(int $machineNumber, string $token, MaintenanceReport $report): View
    {
        $this->assertScanAccess($machineNumber, $token);
        abort_unless((int) $report->machine_number === $machineNumber, 404);

        $report->load(['machineName', 'damageCategory']);

        return view('scan.report', [
            'machineNumber' => $machineNumber,
            'token' => $token,
            'report' => $report,
            'statusLabels' => RepairStatus::labels(),
        ]);
    }

    public function findingRecaps(int $machineNumber, string $token): View
    {
        $this->assertScanAccess($machineNumber, $token);

        $machine = MachineName::query()
            ->operational()
            ->where('machine_number', $machineNumber)
            ->first();

        $recaps = FindingRecap::query()
            ->with(['machineName', 'statusNotes.user'])
            ->where('machine_number', $machineNumber)
            ->where('status', FindingRecapStatus::CLOSE)
            ->latest('found_date')
            ->latest('id')
            ->paginate(TablePagination::perPage());

        return view('scan.finding-recaps', [
            'machineNumber' => $machineNumber,
            'token' => $token,
            'machine' => $machine,
            'recaps' => $recaps,
        ]);
    }

    private function assertScanAccess(int $machineNumber, string $token): void
    {
        abort_unless(InjectionMachine::isValid($machineNumber), 404);
        abort_unless(MachineQrCode::isValidToken($machineNumber, $token), 404);
    }
}
