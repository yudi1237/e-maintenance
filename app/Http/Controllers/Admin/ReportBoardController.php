<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaOperatorReport;
use App\Models\FindingRecap;
use App\Models\MachineName;
use App\Models\MaintenanceReport;
use App\Models\OperatorReport;
use App\Support\FindingRecapStatus;
use App\Support\InjectionMachine;
use App\Support\RepairStatus;
use App\Support\TablePagination;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportBoardController extends Controller
{
    public function machines(Request $request): View
    {
        $filters = $this->ticketFilters($request);

        $tickets = OperatorReport::query()->forDashboard();
        if (! empty($filters['start_date'])) {
            $tickets->whereDate('stop_date', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $tickets->whereDate('stop_date', '<=', $filters['end_date']);
        }
        if (! empty($filters['machine_number'])) {
            $tickets->where('machine_number', (int) $filters['machine_number']);
        }

        $today = now()->timezone('Asia/Jakarta')->format('Y-m-d');
        $oldest = MaintenanceReport::query()->orderBy('report_date')->first();
        $newest = MaintenanceReport::query()->orderByDesc('report_date')->first();

        return view('admin.reports.op-mesin', [
            'tickets' => $tickets->paginate(TablePagination::perPage())->withQueryString(),
            'machineNumbers' => MachineName::query()
                ->operational()
                ->active()
                ->whereNotNull('machine_number')
                ->orderBy('machine_number')
                ->pluck('machine_number'),
            'exportStartDate' => $filters['start_date'] ?? ($oldest?->report_date->format('Y-m-d') ?? $today),
            'exportEndDate' => $filters['end_date'] ?? ($newest?->report_date->format('Y-m-d') ?? $today),
            'ticketMachineNumber' => $filters['machine_number'] ?? null,
            'statusLabels' => RepairStatus::labels(),
        ]);
    }

    public function areas(): View
    {
        return view('admin.reports.op-area', [
            'areaTickets' => AreaOperatorReport::query()
                ->board()
                ->paginate(10, ['*'], 'area_page')
                ->withQueryString(),
        ]);
    }

    public function findings(): View
    {
        return view('admin.reports.findings', [
            'recaps' => FindingRecap::query()
                ->with(['machineName', 'statusNotes.user'])
                ->priorityByDueDate()
                ->paginate(TablePagination::perPage())
                ->withQueryString(),
            'statusLabels' => FindingRecapStatus::labels(),
        ]);
    }

    /**
     * @return array{start_date?: string, end_date?: string, machine_number?: int}
     */
    private function ticketFilters(Request $request): array
    {
        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'machine_number' => ['nullable', 'integer', 'min:1', 'max:'.InjectionMachine::count()],
        ]);

        return array_filter(
            $filters,
            static fn ($value) => $value !== null && $value !== ''
        );
    }
}
