<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FindingRecap;
use App\Models\MachineName;
use App\Models\MaintenanceReport;
use App\Models\OperatorReport;
use App\Models\PreventiveReport;
use App\Models\PreventiveSchedule;
use App\Support\DashboardAnalytics;
use App\Support\FindingRecapStatus;
use App\Support\InjectionMachine;
use App\Support\PreventiveStatus;
use App\Support\RepairStatus;
use App\Support\TablePagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $summary = $this->dashboardSummary();
        $machines = $this->masterMachines();
        $machineNumbers = $machines->pluck('machine_number')->all();
        $now = now()->timezone('Asia/Jakarta');
        [$pmYear, $pmMonth] = DashboardAnalytics::resolvePmPeriod($now->year, $now->month, $now);
        $ticketFilters = $this->incomingTicketFilters($request);
        $exportRange = $this->exportDateRange();

        $ticketsQuery = OperatorReport::query()->forDashboard();
        if (! empty($ticketFilters['start_date'])) {
            $ticketsQuery->whereDate('stop_date', '>=', $ticketFilters['start_date']);
        }
        if (! empty($ticketFilters['end_date'])) {
            $ticketsQuery->whereDate('stop_date', '<=', $ticketFilters['end_date']);
        }
        if (! empty($ticketFilters['machine_number'])) {
            $ticketsQuery->where('machine_number', (int) $ticketFilters['machine_number']);
        }

        return view('admin.dashboard', [
            'machines' => $machines,
            'machineNumbers' => $machineNumbers,
            'machineStats' => $this->machineStats($machineNumbers),
            'machineTotal' => count($machineNumbers),
            'totalReportsToday' => $summary['total_reports_today'],
            'activeMachinesCount' => $summary['active_machines'],
            'closedReportsToday' => $summary['closed_reports_today'],
            'todayLabel' => $summary['today_label'],
            'approvalRoleCaption' => $summary['approval_role_caption'],
            'charts' => DashboardAnalytics::charts(),
            'pmStatus' => DashboardAnalytics::pmStatus($pmYear, $pmMonth),
            'pmYears' => DashboardAnalytics::pmYearOptions($now),
            'pmMonths' => DashboardAnalytics::monthNames(),
            'pmYear' => $pmYear,
            'pmMonth' => $pmMonth,
            'exportStartDate' => $ticketFilters['start_date'] ?? $exportRange['start'],
            'exportEndDate' => $ticketFilters['end_date'] ?? $exportRange['end'],
            'ticketMachineNumber' => $ticketFilters['machine_number'] ?? null,
            'tickets' => $ticketsQuery
                ->paginate(TablePagination::perPage())
                ->withQueryString(),
            'preventiveReports' => $this->dashboardPreventiveReports($request),
            'findingRecaps' => FindingRecap::query()
                ->with(['machineName', 'statusNotes.user'])
                ->priorityByDueDate()
                ->paginate(TablePagination::perPage(), ['*'], 'recap_page')
                ->withQueryString(),
            'findingRecapStatusLabels' => FindingRecapStatus::labels(),
            'preventiveToday' => $this->preventiveTodayByMachine($machineNumbers),
            'statusLabels' => RepairStatus::labels(),
        ]);
    }

    private function dashboardPreventiveReports(Request $request)
    {
        $query = PreventiveReport::query()->with('machineName');
        $orderSql = PreventiveStatus::dashboardOrderSql($request->user());
        if ($orderSql !== null) {
            $query->orderByRaw($orderSql);
        }

        return $query
            ->latestFirst()
            ->paginate(TablePagination::perPage(), ['*'], 'preventive_page')
            ->withQueryString();
    }

    /**
     * @return array{start_date?: string, end_date?: string, machine_number?: int}
     */
    private function incomingTicketFilters(Request $request): array
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

    private function exportDateRange(): array
    {
        $today = now()->timezone('Asia/Jakarta')->format('Y-m-d');

        $oldest = MaintenanceReport::query()->orderBy('report_date')->first();
        $newest = MaintenanceReport::query()->orderByDesc('report_date')->first();

        return [
            'start' => $oldest?->report_date->format('Y-m-d') ?? $today,
            'end' => $newest?->report_date->format('Y-m-d') ?? $today,
        ];
    }

    public function machine(Request $request, int $machineNumber): View
    {
        abort_unless(InjectionMachine::isValid($machineNumber), 404);

        $machine = MachineName::query()
            ->where('machine_number', $machineNumber)
            ->first();

        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $reportsQuery = MaintenanceReport::with(['user', 'machineName', 'damageCategory', 'operatorReport'])
            ->where('machine_number', $machineNumber);

        if (! empty($filters['start_date'])) {
            $reportsQuery->whereDate('report_date', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $reportsQuery->whereDate('report_date', '<=', $filters['end_date']);
        }

        $reports = $reportsQuery
            ->latest()
            ->paginate(TablePagination::perPage())
            ->withQueryString();

        $preventiveQuery = PreventiveReport::query()
            ->with('machineName')
            ->where('machine_number', $machineNumber);

        if (! empty($filters['start_date'])) {
            $preventiveQuery->whereDate('work_date', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $preventiveQuery->whereDate('work_date', '<=', $filters['end_date']);
        }

        $preventiveReports = $preventiveQuery
            ->latest('work_date')
            ->latest('id')
            ->paginate(TablePagination::perPage(), ['*'], 'preventive_page')
            ->withQueryString();

        return view('admin.machine-detail', [
            'machineNumber' => $machineNumber,
            'machine' => $machine,
            'reports' => $reports,
            'reportCount' => MaintenanceReport::where('machine_number', $machineNumber)->count(),
            'filteredReportCount' => $reports->total(),
            'preventiveReports' => $preventiveReports,
            'preventiveCount' => PreventiveReport::where('machine_number', $machineNumber)->count(),
            'filteredPreventiveCount' => $preventiveReports->total(),
            'startDate' => $filters['start_date'] ?? null,
            'endDate' => $filters['end_date'] ?? null,
            'statusLabels' => RepairStatus::labels(),
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $summary = $this->dashboardSummary();
        $machineNumbers = $this->masterMachines()->pluck('machine_number')->all();
        $now = now()->timezone('Asia/Jakarta');
        [$pmYear, $pmMonth] = DashboardAnalytics::resolvePmPeriod(
            $request->query('pm_year', $now->year),
            $request->has('pm_month') ? $request->query('pm_month') : $now->month,
            $now,
        );

        return response()->json([
            'machines' => $this->machineStats($machineNumbers),
            'preventive_today' => $this->preventiveTodayByMachine($machineNumbers),
            'total' => $summary['total_reports_today'],
            'active_machines' => $summary['active_machines'],
            'closed' => $summary['closed_reports_today'],
            'today_label' => $summary['today_label'],
            'updated_at' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s'),
            'charts' => DashboardAnalytics::charts(),
            'pm_status' => DashboardAnalytics::pmStatus($pmYear, $pmMonth),
        ]);
    }

    /**
     * @return array{
     *     total_reports_today: int,
     *     active_machines: int,
     *     closed_reports_today: int,
     *     today_label: string,
     *     approval_role_caption: string
     * }
     */
    private function dashboardSummary(): array
    {
        $today = now()->timezone('Asia/Jakarta');
        $todayDate = $today->toDateString();
        $user = auth()->user();
        $pendingStatus = RepairStatus::pendingApprovalStatusFor($user);

        $activeMachinesQuery = OperatorReport::query();
        if ($pendingStatus !== null) {
            $activeMachinesQuery->where('status', $pendingStatus);
        } else {
            $activeMachinesQuery->where('status', '!=', RepairStatus::CLOSE);
        }

        return [
            'total_reports_today' => OperatorReport::query()->whereDate('stop_date', $todayDate)->count()
                + MaintenanceReport::query()->today()->whereNull('operator_report_id')->count(),
            'active_machines' => (int) $activeMachinesQuery->distinct()->count('machine_number'),
            'closed_reports_today' => MaintenanceReport::query()
                ->today()
                ->whereIn('status_perbaikan', RepairStatus::approvedStatusesFor($user))
                ->count(),
            'today_label' => $today->format('d/m/Y'),
            'approval_role_caption' => RepairStatus::approvalRoleCaption($user),
        ];
    }

    private function masterMachines()
    {
        return MachineName::query()
            ->active()
            ->whereNotNull('machine_number')
            ->orderBy('machine_number')
            ->get();
    }

    /**
     * @param  list<int>  $machineNumbers
     * @return array<int, array{op: int, findings: int, pm_due: string|null, alert: string}>
     */
    private function machineStats(array $machineNumbers): array
    {
        $opRows = OperatorReport::query()
            ->where('status', '!=', RepairStatus::CLOSE)
            ->selectRaw('machine_number, status, COUNT(*) as total')
            ->groupBy('machine_number', 'status')
            ->get();

        $opTotals = [];
        $opActive = [];
        $opPending = [];
        foreach ($opRows as $row) {
            $number = (int) $row->machine_number;
            $total = (int) $row->total;
            $opTotals[$number] = ($opTotals[$number] ?? 0) + $total;
            if (in_array($row->status, RepairStatus::machineStopValues(), true)) {
                $opActive[$number] = ($opActive[$number] ?? 0) + $total;
            }
            if (in_array($row->status, RepairStatus::pendingMaintenanceCloseValues(), true)) {
                $opPending[$number] = ($opPending[$number] ?? 0) + $total;
            }
        }

        $findingCounts = FindingRecap::query()
            ->where('status', '!=', FindingRecapStatus::CLOSE)
            ->selectRaw('machine_number, COUNT(*) as total')
            ->groupBy('machine_number')
            ->pluck('total', 'machine_number');

        $pmDueDates = $this->nearestPmDueDates($machineNumbers);

        $stats = [];
        foreach ($machineNumbers as $number) {
            $number = (int) $number;
            $findings = (int) ($findingCounts[$number] ?? 0);
            $stats[$number] = [
                'op' => (int) ($opTotals[$number] ?? 0),
                'findings' => $findings,
                'pm_due' => $pmDueDates[$number] ?? null,
                'alert' => RepairStatus::machineCardAlert(
                    (int) ($opActive[$number] ?? 0),
                    (int) ($opPending[$number] ?? 0),
                    $findings
                ),
            ];
        }

        return $stats;
    }

    /**
     * @param  list<int>  $machineNumbers
     * @return array<int, string>
     */
    private function nearestPmDueDates(array $machineNumbers): array
    {
        if ($machineNumbers === []) {
            return [];
        }

        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $open = PreventiveSchedule::query()
            ->open()
            ->whereIn('machine_number', $machineNumbers)
            ->get(['machine_number', 'plan_date']);

        $dates = [];
        foreach ($machineNumbers as $number) {
            $forMachine = $open->filter(
                fn (PreventiveSchedule $schedule): bool => (int) $schedule->machine_number === (int) $number
            );

            if ($forMachine->isEmpty()) {
                continue;
            }

            $upcoming = $forMachine
                ->filter(fn (PreventiveSchedule $schedule): bool => $schedule->plan_date->toDateString() >= $today)
                ->sortBy(fn (PreventiveSchedule $schedule): string => $schedule->plan_date->toDateString())
                ->first();

            if ($upcoming) {
                $dates[(int) $number] = $upcoming->plan_date->format('d/m/Y');
                continue;
            }

            $overdue = $forMachine
                ->filter(fn (PreventiveSchedule $schedule): bool => $schedule->plan_date->toDateString() < $today)
                ->sortByDesc(fn (PreventiveSchedule $schedule): string => $schedule->plan_date->toDateString())
                ->first();

            if ($overdue) {
                $dates[(int) $number] = $overdue->plan_date->format('d/m/Y');
            }
        }

        return $dates;
    }

    /**
     * @param  list<int>  $machineNumbers
     * @return array<int, list<string>>
     */
    private function preventiveTodayByMachine(array $machineNumbers): array
    {
        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $map = [];
        foreach ($machineNumbers as $number) {
            $map[(int) $number] = [];
        }

        if ($machineNumbers === []) {
            return $map;
        }

        $rows = PreventiveReport::query()
            ->whereDate('work_date', $today)
            ->whereIn('machine_number', $machineNumbers)
            ->orderBy('id')
            ->get(['machine_number', 'category']);

        foreach ($rows as $row) {
            $number = (int) $row->machine_number;
            $category = $row->category_label;
            if ($category === '-' || $category === '') {
                continue;
            }
            if (! in_array($category, $map[$number], true)) {
                $map[$number][] = $category;
            }
        }

        return $map;
    }
}
